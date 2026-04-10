<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GenerateInvoice\GenerateInvoiceRequest;
use App\Imports\InvoiceGenerateDataMentah;
use App\Imports\InvoiceGenerateImport;
use App\Models\invoice_generate_manual;
use App\Models\rekening_pandu;
use App\Models\rekening_tomi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;


class GenerateInvoiceController extends Controller
{
    public function index()
    {
        return view("admin.generate-invoice.index");
    }

    public function stats()
    {
        $stats = invoice_generate_manual::selectRaw('
            COUNT(*) as total_invoice,
            SUM(total_pinjaman) as total_pinjaman,
            SUM(total_dibayarkan) as total_dibayarkan,
            SUM(total_fee) as fee_tomi
        ')->first();

        return response()->json([
            'total_invoice' => $stats->total_invoice ?? 0,
            'total_pinjaman' => $stats->total_pinjaman ?? 0,
            'total_dibayarkan' => $stats->total_dibayarkan ?? 0,
            'fee_tomi' => $stats->fee_tomi ?? 0,
        ]);
    }

    public function downloadTemplateInvoice()
    {
        $path = '/template/tempate_invoice_generate.xlsx';
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File tidak tersedia.');
        }
        return Storage::disk('local')->download($path);
    }

    public function generateInvoice(GenerateInvoiceRequest $request)
    {
        try {
            $file = $request->file('invoiceFile');

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); // 'xlsx' atau 'xls'
            $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

            $path = $file->storeAs('invoices/generate', $filename, 'local');

            $fullPath = Storage::disk('local')->path($path);
            if (!file_exists($fullPath)) {
                throw new \Exception("File tidak ditemukan di path: {$fullPath}");
            }
            $import = new InvoiceGenerateImport();
            // ✅ FIX 5: Execute import dengan full path yang memiliki ekstensi
            Excel::import($import, $fullPath);
            $summary = $import->getSummary();


            // Simpan ke database
            $invoiceRecord = invoice_generate_manual::create([
                'nama_perusahaan' => $summary['nama_perusahaan'],
                'filename' => $filename,
                'jumlah_peminjam' => $summary['jumlah_peminjam'],
                'total_pinjaman' => $summary['total_pinjaman'],
                'total_dibayarkan' => $summary['total_dibayarkan'],
                'total_fee' => $summary['total_fee'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil digenerate!',
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data Excel gagal: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadExcel($id)
    {
        $invoice = invoice_generate_manual::where('id', $id)->first();
        $path = 'invoices/generate/' . $invoice->filename;
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File tidak tersedia.');
        }
        return Storage::disk('local')->download($path);
    }

    public function generateInvoiceDownload($id)
    {
        $invoice = invoice_generate_manual::where('id', $id)->first();
        $filename = $invoice->filename;
        $path = 'invoices/generate/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $fullPath = storage_path('app/' . $path);


        $importMentah = new InvoiceGenerateDataMentah();

        Excel::import($importMentah, $fullPath);

        $summary = $importMentah->getSummary();
        $items = $importMentah->getItems();
        $rekening = rekening_pandu::first();

        // 4. Load View
        $pdf = Pdf::loadView('admin.generate-invoice.pinjaman', [
            'invoice'  => $invoice,
            'items'    => $items,
            'rekening' => $rekening,
            'summary'  => $summary
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Invoice-' . $invoice->nama_perusahaan . '.pdf');
    }

    public function generateFee($id)
    {
        $invoice = invoice_generate_manual::where('id', $id)->first();
        $filename = $invoice->filename;
        $path = 'invoices/generate/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $fullPath = storage_path('app/' . $path);


        $importMentah = new InvoiceGenerateDataMentah();

        Excel::import($importMentah, $fullPath);

        $summary = $importMentah->getSummary();
        $items = $importMentah->getItems();
        $rekening = rekening_tomi::first();

        // 4. Load View
        $pdf = Pdf::loadView('admin.generate-invoice.fee', [
            'invoice'  => $invoice,
            'items'    => $items,
            'rekening' => $rekening,
            'summary'  => $summary
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Invoice-' . $invoice->nama_perusahaan . '.pdf');
    }

    public function generateTable()
    {
        $data = invoice_generate_manual::get();

        return DataTables::of($data)
            ->addColumn('jumlah_peminjam', function ($row) {
                return number_format((int) $row->jumlah_peminjam, 0, ',', '.');
            })
            ->addColumn('total_pinjaman', function ($row) {
                return 'Rp. ' . number_format((float) $row->total_pinjaman, 0, ',', '.');
            })
            ->addColumn('total_dibayarkan', function ($row) {
                return 'Rp. ' . number_format((float) $row->total_dibayarkan, 0, ',', '.');
            })
            ->addColumn('total_fee', function ($row) {
                return 'Rp. ' . number_format((float) $row->total_fee, 0, ',', '.');
            })
            ->addColumn('datetime', function ($row) {
                $datetime = Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
                return $datetime;
            })
            ->addColumn('action', function ($row) {
                // Generate URL download secara dinamis
                $urlDownloadPerusahaan = route('admin.generate.invoice.fee', ['id' => $row->id]);
                $urlDownloadExcel = route('admin.generate.invoice.download.excel', ['id' => $row->id]);
                return '

                    <a href="' . $urlDownloadPerusahaan . '" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-file-eye"></i>  Invoice Fee Tomi
                    </a>

                    <a href="' . $urlDownloadExcel . '" class="btn btn-sm btn-success">
                        <i class="mdi mdi-file-excel"></i> Excel
                    </a>
                ';
            })
            ->rawColumns(['file', 'action'])
            ->toJson();
    }
}
