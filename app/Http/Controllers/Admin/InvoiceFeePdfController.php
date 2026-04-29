<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class InvoiceFeePdfController extends Controller
{
    public function show($id)
    {
        $response = Http::get("http://localhost:8000/api/invoice-excel/{$id}");

        if (!$response->successful()) {
            abort(404, 'Data invoice tidak ditemukan.');
        }

        $result = $response->json();

        return view('admin.invoice-fee.show', compact('result', 'id'));
    }

    public function downloadPdf($id)
    {
        $response = Http::withBasicAuth(
            config('koperasi.identity'),
            config('koperasi.password')
        )->get(config('koperasi.api_url') . "/api/invoice-excel/{$id}");

        if (!$response->successful()) {
            abort($response->status(), 'Data invoice tidak ditemukan atau akses ditolak.');
        }

        $result = $response->json();

        $invoiceData = $result['invoice'] ?? [];

        if (!isset($invoiceData['created_at']) || empty($invoiceData['created_at'])) {
            $invoiceData['created_at'] = null;
        }

        $invoice = (object) $invoiceData;
        $rows = $result['data'] ?? [];
        $items = collect($rows)->values()->map(function ($row, $index) {
            $jumlahPinjaman = (float) ($row['jumlah_pinjaman'] ?? 0);

            // Fee Tomi = 1,75% dari jumlah pinjaman
            $feeTomi = round($jumlahPinjaman * 0.0175, 0);

            // Jumlah Tagihan = jumlah pinjaman + 7% dari jumlah pinjaman
            $jumlahTagihan = round($jumlahPinjaman * 1.07, 0);

            return [
                'no' => $index + 1,
                'tanggal_pengajuan' => $this->excelDateToNormal($row['tanggal_pengajuan'] ?? null),
                'nama' => $row['nama'] ?? '-',
                'no_ktp' => $row['no_ktp'] ?? '-',
                'penempatan_kerja' => $row['penempatan_kerja'] ?? '-',
                'jumlah_pinjaman' => $jumlahPinjaman,
                'jumlah_tagihan' => $jumlahTagihan,
                'fee_tomi' => $feeTomi,
            ];
        })->toArray();

        $totalJumlahPinjaman = collect($items)->sum('jumlah_pinjaman');
        $totalJumlahFee = collect($items)->sum('fee_tomi');
        $totalJumlahTagihan = collect($items)->sum('jumlah_tagihan');

        $summary = [
            'total_fee' => $totalJumlahFee,
            'total_jumlah_pinjaman' => $totalJumlahPinjaman,
            'total_jumlah_tagihan' => $totalJumlahTagihan,
        ];

        $rekening = (object) [
            'atas_nama' => 'PT. TOMI DIGITAL INDONESIA',
            'nomor_rekening' => '1234567890',
            'bank' => (object) [
                'code_bank' => 'MANDIRI',
            ],
        ];

        $pdf = Pdf::loadView('admin.invoice-fee.pdf', [
            'invoice' => $invoice,
            'items' => $items,
            'summary' => $summary,
            'rekening' => $rekening,
            'totalJumlahPinjaman' => $totalJumlahPinjaman,
            'totalJumlahFee' => $totalJumlahFee,
            'totalJumlahTagihan' => $totalJumlahTagihan,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('invoice-fee-' . $id . '.pdf');
    }

    private function excelDateToNormal($value): string
    {
        if (empty($value)) {
            return '-';
        }

        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $value)->format('d M Y');
        }

        return (string) $value;
    }


    public function downloadTransferRequestPdf($id)
    {
        $response = Http::withBasicAuth(
            config('koperasi.identity'),
            config('koperasi.password')
        )->get(config('koperasi.api_url') . "/api/invoice-excel/{$id}");

        if (!$response->successful()) {
            abort($response->status(), 'Data invoice tidak ditemukan atau akses ditolak.');
        }

        $result = $response->json();

        $invoiceData = $result['invoice'] ?? [];

        if (!isset($invoiceData['created_at']) || empty($invoiceData['created_at'])) {
            $invoiceData['created_at'] = now();
        }

        $invoice = (object) $invoiceData;
        $rows = $result['data'] ?? [];

        $items = collect($rows)->values()->map(function ($row, $index) {
            $jumlahPinjaman = (float) ($row['jumlah_pinjaman'] ?? 0);

            // Fee Tomi = 1,75% dari jumlah pinjaman
            $feeTomi = round($jumlahPinjaman * 0.0175, 0);

            // Jumlah Tagihan = jumlah pinjaman + fee tomi
            $jumlahTagihan = $jumlahPinjaman + $feeTomi;

            return [
                'no' => $index + 1,
                'tanggal_pengajuan' => $this->excelDateToNormal($row['tanggal_pengajuan'] ?? null),
                'nama' => $row['nama'] ?? '-',
                'no_ktp' => $row['no_ktp'] ?? '-',
                'jumlah_pinjaman' => $jumlahPinjaman,
                'jumlah_tagihan' => $jumlahTagihan,
                'fee_tomi' => $feeTomi,
            ];
        })->toArray();

        $totalJumlahPinjaman = collect($items)->sum('jumlah_pinjaman');
        $totalJumlahFee = collect($items)->sum('fee_tomi');
        $totalJumlahTagihan = collect($items)->sum('jumlah_tagihan');

        $summary = [
            'total_fee' => $totalJumlahFee,
            'total_jumlah_pinjaman' => $totalJumlahPinjaman,
            'total_jumlah_tagihan' => $totalJumlahTagihan,
        ];

        $rekening = (object) [
            'atas_nama' => 'PT. TOMI DIGITAL INDONESIA',
            'nomor_rekening' => '1234567890',
            'bank' => (object) [
                'code_bank' => 'MANDIRI',
            ],
        ];

        $pdf = Pdf::loadView('admin.invoice-fee.transfer_request_pdf', [
            'invoice' => $invoice,
            'items' => $items,
            'summary' => $summary,
            'rekening' => $rekening,
            'totalJumlahPinjaman' => $totalJumlahPinjaman,
            'totalJumlahFee' => $totalJumlahFee,
            'totalJumlahTagihan' => $totalJumlahTagihan,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('invoice-fee-' . $id . '.pdf');
    }
}
