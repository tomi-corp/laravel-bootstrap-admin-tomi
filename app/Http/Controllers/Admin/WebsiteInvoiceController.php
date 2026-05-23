<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Nasabah;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WebsiteInvoiceController extends Controller
{
    // Halaman list invoice
    public function index()
    {
        $invoices = Nasabah::select(
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
                'invoice' => 'INV-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
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
        $items = Nasabah::where('position', 'credam')
            ->where('status', 'approve')
            ->whereMonth('mulai_pinjaman', $bulan)
            ->whereYear('mulai_pinjaman', $tahun)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'title' => 'Invoice Website',
            'tanggal' => date('d M Y'),
            'invoice' => 'INV-' . $bulan . $tahun,
            'customer' => 'Tokomikro',
            'items' => $items,
        ];

        $pdf = Pdf::loadView('admin.bta.website.invoice.index', $data);

        return $pdf->stream($data['invoice'] . '.pdf');
    }
}
