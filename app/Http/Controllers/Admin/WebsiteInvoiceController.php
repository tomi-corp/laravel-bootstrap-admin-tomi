<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\nasabah;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WebsiteInvoiceController extends Controller
{
    // Halaman list invoice
    public function index()
    {
        $invoices = nasabah::select(
            DB::raw('MONTH(mulai_pinjaman) as bulan'),
            DB::raw('YEAR(mulai_pinjaman) as tahun'),
            DB::raw('COUNT(*) as total')
        )
            ->where('position', 'credam')
            ->where('status', 'approve')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $invoices = $invoices->map(function ($item, $index) {

            $namaBulan = Carbon::create()
                ->month($item->bulan)
                ->translatedFormat('F');

            return [
                'id' => $index + 1,
                'invoice' => 'LW-' . Carbon::createFromDate((int) $item->tahun, (int) $item->bulan, 1)->format('Ymd'),
                'bulan' => $namaBulan . ' ' . $item->tahun,
                'total_nasabah' => $item->total,
                'bulan_angka' => $item->bulan,
                'tahun' => $item->tahun,
            ];
        });

        return view('admin.bta.website.index', compact('invoices'));
    }

    // Generate PDF
    public function generate($bulan, $tahun)
    {
        $tanggalInvoice = Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->startOfDay();
        $nomorInvoice = 'LW-' . $tanggalInvoice->format('Ymd');

        $items = nasabah::where('position', 'credam')
            ->where('status', 'approve')
            ->whereMonth('mulai_pinjaman', $bulan)
            ->whereYear('mulai_pinjaman', $tahun)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'title' => 'Invoice Website',
            'tanggal' => $tanggalInvoice->translatedFormat('d F Y'),
            'invoice' => [
                'nomor_invoice' => $nomorInvoice,
                'created_at' => $tanggalInvoice,
            ],
            'customer' => 'Tokomikro',
            'items' => $items,
        ];

        $pdf = Pdf::loadView('admin.bta.website.invoice.index', $data);

        return $pdf->stream($nomorInvoice . '.pdf');
    }
}
