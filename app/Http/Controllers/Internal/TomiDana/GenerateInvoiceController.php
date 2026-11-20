<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Imports\TomiDana\GenerateInvoiceImport;
use App\Models\invoice_generate_manual;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Generate invoice TomiDana: upload Excel pencairan, ringkas per perusahaan, lalu cetak invoice PDF.
 * Port dari Generate Invoice KspDDT.
 */
class GenerateInvoiceController extends Controller
{
    use ScopesCompanies;

    private const STORAGE_DIR = 'invoices/generate';

    public function index()
    {
        $selectedCompany = request('nama_perusahaan');

        $invoices = $this->invoices()
            ->when($selectedCompany, fn ($q) => $q->where('nama_perusahaan', $selectedCompany))
            ->latest()
            ->get();

        $companies = $this->invoices()
            ->where('nama_perusahaan', '!=', '')
            ->distinct()
            ->orderBy('nama_perusahaan')
            ->pluck('nama_perusahaan');

        return view('internal.tomi-dana.generate-invoice.index', compact('invoices', 'companies', 'selectedCompany'));
    }

    public function store(Request $request)
    {
        $request->validate(['invoiceFile' => 'required|file|mimes:xlsx,xls|max:10240']);
        $file = $request->file('invoiceFile');
        $filename = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            .'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs(self::STORAGE_DIR, $filename, 'local');

        try {
            $import = new GenerateInvoiceImport;
            Excel::import($import, Storage::disk('local')->path($path));
            $summaries = $import->getSummaries();

            if (empty($summaries)) {
                Storage::disk('local')->delete($path);

                return back()->with('error', 'Tidak ada data yang bisa diproses. Pastikan kolom NAMA PERUSAHAAN terisi.');
            }

            DB::transaction(function () use ($summaries, $filename) {
                foreach ($summaries as $summary) {
                    invoice_generate_manual::create([
                        'nama_perusahaan' => $summary['nama_perusahaan'],
                        'filename' => $filename,
                        'jumlah_peminjam' => $summary['jumlah_peminjam'],
                        'total_pinjaman' => $summary['total_pinjaman'],
                        'total_dibayarkan' => $summary['total_dibayarkan'],
                        'total_fee' => $summary['total_fee'],
                    ]);
                }
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            Log::error('Generate Invoice TomiDana Error: '.$e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memproses file: '.$e->getMessage());
        }

        return redirect()
            ->route('internal.tomi-dana.generate-invoice')
            ->with('success', count($summaries).' invoice perusahaan berhasil dibuat dari file yang diupload.');
    }

    public function pdf(int $id)
    {
        $invoice = $this->invoices()->findOrFail($id);
        $relativePath = self::STORAGE_DIR.'/'.$invoice->filename;

        if (blank($invoice->filename) || ! Storage::disk('local')->exists($relativePath)) {
            return redirect()
                ->route('internal.tomi-dana.generate-invoice')
                ->with('error', 'File Excel sumber tidak ditemukan di storage: '.$relativePath);
        }

        $rows = Excel::toArray([], Storage::disk('local')->path($relativePath))[0] ?? [];
        if (empty($rows)) {
            return redirect()->route('internal.tomi-dana.generate-invoice')->with('error', 'Data Excel tidak ditemukan.');
        }

        $headers = array_map(fn ($header) => Str::snake(Str::lower(preg_replace('/\s+/', ' ', trim((string) $header)))), array_shift($rows));
        $companyName = trim((string) $invoice->nama_perusahaan);

        $items = [];
        foreach ($rows as $row) {
            $mapped = [];
            foreach ($headers as $colIndex => $header) {
                $mapped[$header] = $row[$colIndex] ?? null;
            }

            if (trim((string) GenerateInvoiceImport::pick($mapped, GenerateInvoiceImport::COMPANY_KEYS, '')) !== $companyName) {
                continue;
            }

            $items[] = [
                'no' => count($items) + 1,
                'tanggal_pengajuan' => $this->formatExcelDate($mapped['tanggal_pengajuan'] ?? null),
                'nama' => $mapped['nama'] ?? '-',
                'no_ktp' => $mapped['no_ktp'] ?? '-',
                'penempatan_kerja' => $mapped['penempatan_kerja'] ?? '-',
                'jumlah_pinjaman' => GenerateInvoiceImport::toNumber(GenerateInvoiceImport::pick($mapped, GenerateInvoiceImport::LOAN_KEYS)),
                'fee_tomi' => GenerateInvoiceImport::toNumber(GenerateInvoiceImport::pick($mapped, GenerateInvoiceImport::FEE_KEYS)),
                'total_dibayarkan' => GenerateInvoiceImport::toNumber(GenerateInvoiceImport::pick($mapped, GenerateInvoiceImport::PAID_KEYS)),
            ];
        }

        $summary = ['total_fee' => array_sum(array_column($items, 'fee_tomi'))];
        $rekening = null;

        return Pdf::loadView('internal.tomi-dana.generate-invoice.pdf', compact('invoice', 'items', 'summary', 'rekening'))
            ->setPaper('a4', 'landscape')
            ->stream('invoice_'.Str::slug($invoice->nama_perusahaan).'.pdf');
    }

    /**
     * Invoice yang boleh dilihat admin. Invoice hanya menyimpan nama perusahaan (teks),
     * jadi admin PIC dibatasi lewat nama perusahaannya.
     */
    private function invoices(): Builder
    {
        $query = invoice_generate_manual::query();
        $admin = auth('admin')->user();

        if ($admin && $admin->type === 'pic') {
            $query->whereIn('nama_perusahaan', $this->visibleCompanies()->pluck('nama'));
        }

        return $query;
    }

    private function formatExcelDate($value): string
    {
        if (is_null($value) || $value === '') {
            return '-';
        }

        try {
            return is_numeric($value)
                ? ExcelDate::excelToDateTimeObject($value)->format('d/m/Y')
                : Carbon::parse(trim((string) $value))->format('d/m/Y');
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
