<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TomiInvoiceController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        $query = DB::table('list_perusahaans')->orderByDesc('id');

        if ($admin->role === 'pic' || $admin->type === 'pic') {
            $query->where('id', $admin->company_id);
        }

        $perusahaans = $query
            ->get()
            ->map(fn($company) => $this->mapCompany($company));

        $invoiceSummary = $this->getInvoiceSummary();

        $perusahaans = $perusahaans->map(function (array $perusahaan) use ($invoiceSummary) {
            $summary = $invoiceSummary->get($this->normalizeCompanyName($perusahaan['nama']), [
                'total_invoice' => 0,
                'total_pinjaman' => 0,
                'total_dibayarkan' => 0,
                'total_fee' => 0,
            ]);

            return array_merge($perusahaan, $summary);
        });

        return view('admin.tomi-invoice.index', [
            'perusahaans' => $perusahaans,
            'totalPerusahaan' => $perusahaans->count(),
            'totalInvoice' => $perusahaans->sum('total_invoice'),
            'totalPinjaman' => $perusahaans->sum('total_pinjaman'),
            'totalDibayarkan' => $perusahaans->sum('total_dibayarkan'),
            'totalFee' => $perusahaans->sum('total_fee'),
        ]);
    }

    public function show($id)
    {
        $company = DB::table('list_perusahaans')
            ->where('id', $id)
            ->first();

        if (!$company) {
            abort(404, 'Data tidak ditemukan');
        }

        $perusahaan = $this->mapCompany($company);
        $details = $this->getInvoiceDetails($perusahaan['nama']);

        $perusahaan['details'] = $details;
        $perusahaan['total_invoice'] = $details->count();
        $perusahaan['total_pinjaman'] = $details->sum('total_pinjaman');
        $perusahaan['total_dibayarkan'] = $details->sum('total_dibayarkan');
        $perusahaan['total_fee'] = $details->sum('total_fee');

        return view('admin.tomi-invoice.show', compact('perusahaan'));
    }

    private function value(object $row, array $keys, mixed $default = null): mixed
    {
        foreach ($keys as $key) {
            $value = data_get($row, $key);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    }

    private function mapCompany(object $company): array
    {
        $status = strtolower((string) $this->value($company, ['status'], 'active'));
        $status = in_array($status, ['inactive', 'non_active', 'nonactive'], true) ? 'nonactive' : 'active';

        return [
            'id' => $this->value($company, ['id']),
            'nama' => $this->value($company, ['nama', 'nama_perusahaan', 'company_name', 'name'], '-'),
            'status' => $status,
            'max_plafon' => (float) $this->value($company, ['max_plafon', 'maximum_plafon', 'plafon_max', 'max_limit'], 0),
        ];
    }

    private function getInvoiceSummary()
    {
        if (!Schema::hasTable('invoice_generate_manuals')) {
            return collect();
        }

        return DB::table('invoice_generate_manuals')
            ->select(
                'nama_perusahaan',
                DB::raw('COUNT(*) as total_invoice'),
                DB::raw('COALESCE(SUM(total_pinjaman), 0) as total_pinjaman'),
                DB::raw('COALESCE(SUM(total_dibayarkan), 0) as total_dibayarkan'),
                DB::raw('COALESCE(SUM(total_fee), 0) as total_fee')
            )
            ->groupBy('nama_perusahaan')
            ->get()
            ->mapWithKeys(fn($item) => [
                $this->normalizeCompanyName($item->nama_perusahaan) => [
                    'total_invoice' => (int) $item->total_invoice,
                    'total_pinjaman' => (float) $item->total_pinjaman,
                    'total_dibayarkan' => (float) $item->total_dibayarkan,
                    'total_fee' => (float) $item->total_fee,
                ],
            ]);
    }

    private function getInvoiceDetails(string $companyName)
    {
        if (!Schema::hasTable('invoice_generate_manuals')) {
            return collect();
        }

        return DB::table('invoice_generate_manuals')
            ->where('nama_perusahaan', $companyName)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn($invoice) => [
                'id' => $invoice->id,
                'nama_perusahaan' => $invoice->nama_perusahaan ?? $companyName,
                'nomor_invoice' => 'INV-TOMI-' . str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT),
                'filename' => $invoice->filename ?? '-',
                'jumlah_peminjam' => (int) ($invoice->jumlah_peminjam ?? 0),
                'total_pinjaman' => (float) ($invoice->total_pinjaman ?? 0),
                'total_dibayarkan' => (float) ($invoice->total_dibayarkan ?? 0),
                'total_fee' => (float) ($invoice->total_fee ?? 0),
                'created_at' => $invoice->created_at ?? null,
            ]);
    }

    private function normalizeCompanyName(?string $name): string
    {
        return mb_strtolower(trim((string) $name));
    }
}
