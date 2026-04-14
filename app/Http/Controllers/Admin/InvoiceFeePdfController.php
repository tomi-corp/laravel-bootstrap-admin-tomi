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

        $invoice = (object) ($result['invoice'] ?? []);
        $rows = $result['data'] ?? [];

        $items = collect($rows)->values()->map(function ($row, $index) {
            return [
                'no' => $index + 1,
                'tanggal_pengajuan' => $this->excelDateToNormal($row['tanggal_pengajuan'] ?? null),
                'nama' => $row['nama'] ?? '-',
                'no_ktp' => $row['no_ktp'] ?? '-',
                'jumlah_pinjaman' => (float) ($row['jumlah_pinjaman'] ?? 0),
                'fee_tomi' => (float) ($row['total_fee'] ?? 0),
            ];
        })->toArray();

        $summary = [
            'total_fee' => collect($rows)->sum(function ($item) {
                return (float) ($item['total_fee'] ?? 0);
            }),
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
}
