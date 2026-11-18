<?php

namespace App\Exports;

use App\Services\Insight\InsightPeriod;
use App\Services\Insight\InsightService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DashboardReportSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly string $scope,
        private readonly string $sheetTitle,
        private readonly InsightService $insight,
        private readonly InsightPeriod $period,
    ) {
    }

    public function collection(): Collection
    {
        if ($this->scope === 'tomidana') {
            return $this->tomiDanaRows();
        }

        $data = $this->scope === 'overview'
            ? $this->insight->overview($this->period, null)
            : $this->insight->product($this->scope, $this->period, null);

        $rows = collect($data['kpis'] ?? [])->map(fn (array $kpi) => [
            $kpi['label'],
            $kpi['value'],
            $kpi['format'] ?? 'num',
            $this->period->label,
        ]);

        if ($this->scope === 'overview') {
            $rows = $rows->concat(
                collect($data['products'] ?? [])->flatMap(fn (array $product) => [
                    [$product['name'] . ' - Dana tersalurkan', $product['disbursed'], 'rp', $this->period->label],
                    [$product['name'] . ' - NOA penyaluran', $product['count'], 'num', $this->period->label],
                    [$product['name'] . ' - Sisa pembiayaan', $product['outstanding'], 'rp', $this->period->label],
                ])
            );
        }

        return $rows;
    }

    private function tomiDanaRows(): Collection
    {
        $installments = DB::table('jatuh_tempos')
            ->select([
                'id_pinjaman',
                DB::raw("MIN(CASE WHEN status != 'lunas' OR status IS NULL THEN jatuh_tempo END) as next_due_date"),
                DB::raw('COUNT(*) as installment_count'),
                DB::raw("SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as paid_installments"),
                DB::raw("MAX(CASE WHEN status = 'lunas' THEN updated_at END) as last_payment_date"),
            ])
            ->groupBy('id_pinjaman');

        $disbursements = DB::table('disbursements')
            ->select([
                'pinjaman_id',
                DB::raw('MAX(transfer_date) as realized_date'),
                DB::raw('MAX(created_at) as disbursement_date'),
            ])
            ->groupBy('pinjaman_id');

        return DB::table('pinjamen as p')
            ->leftJoin('list_perusahaans as lp', 'lp.id', '=', 'p.id_perusahaan')
            ->leftJoin('list_karyawans as k', 'k.id', '=', 'p.id_karyawan')
            ->leftJoinSub($disbursements, 'd', 'd.pinjaman_id', '=', 'p.id')
            ->leftJoinSub($installments, 'jt', 'jt.id_pinjaman', '=', 'p.id')
            ->select([
                'p.created_at',
                'p.status',
                'p.nominal',
                'p.total_angsuran_perbulan',
                'p.tenor',
                'p.rate',
                'p.pendapatan_bunga',
                'p.biaya_admin',
                'p.biaya_admin_ksp',
                'p.total_biaya_admin',
                'p.biaya_admin_pandu',
                'p.biaya_layanan',
                'p.iuran_anggota',
                'p.cost_tomi',
                'lp.nama as company_name',
                'k.nama_karyawan as employee_name',
                'd.realized_date',
                'd.disbursement_date',
                'jt.next_due_date',
                'jt.installment_count',
                'jt.paid_installments',
                'jt.last_payment_date',
            ])
            ->whereNull('p.deleted_at')
            ->whereBetween('p.created_at', [$this->period->start(), $this->period->end()])
            ->orderBy('p.created_at')
            ->get()
            ->map(fn ($row) => $this->mapTomiDanaRow($row));
    }

    private function mapTomiDanaRow(object $row): array
    {
        $totalInstallments = (int) ($row->installment_count ?? 0);
        $paidInstallments = min((int) ($row->paid_installments ?? 0), $totalInstallments);
        $remainingInstallments = max($totalInstallments - $paidInstallments, 0);
        $monthlyInstallment = (float) ($row->total_angsuran_perbulan ?? 0);
        $paymentStatus = null;

        if ($totalInstallments > 0) {
            $paymentStatus = $paidInstallments >= $totalInstallments
                ? 'Lunas'
                : (($row->next_due_date && CarbonImmutable::parse($row->next_due_date)->isBefore(CarbonImmutable::today()))
                    ? 'Terlambat'
                    : 'Belum Lunas');
        }

        return [
            'TomiDana',
            $row->company_name,
            $row->employee_name,
            $this->dateValue($row->created_at),
            $this->dateValue($row->realized_date ?? $row->disbursement_date),
            $this->dateValue($row->next_due_date),
            $row->status,
            $row->nominal,
            $row->total_angsuran_perbulan,
            $row->tenor,
            $totalInstallments > 0 ? min($paidInstallments + 1, $totalInstallments) : null,
            $remainingInstallments,
            $remainingInstallments * $monthlyInstallment,
            $this->dateValue($row->last_payment_date),
            $paymentStatus,
            $row->rate,
            $row->pendapatan_bunga,
            null,
            null,
            null,
            null,
            null,
            null,
            $row->biaya_admin,
            $row->biaya_admin_ksp,
            $row->total_biaya_admin,
            $row->biaya_admin_pandu,
            $row->biaya_admin_pandu,
            null,
            null,
            null,
            null,
            $row->biaya_layanan,
            $row->iuran_anggota,
            $row->cost_tomi,
        ];
    }

    private function dateValue(?string $value): ?string
    {
        return $value ? CarbonImmutable::parse($value)->toDateString() : null;
    }

    public function headings(): array
    {
        if ($this->scope === 'overview') {
            return [
                'Produk',
                'Lender',
                'Perusahaan',
                'Nama',
                'Tanggal Transaksi',
                'Tanggal Realisasi',
                'Tanggal Jatuh Tempo Pembayaran',
                'Status Pinjaman',
                'Nominal',
                'Angsuran Perbulan',
                'Tenor',
                'Angsuran ke',
                'Sisa Tenor',
                'Sisa Outstanding',
                'Tanggal Pembayaran',
                'Status Pembayaran',
                'Rate (%)',
                'Rate (Rp)',
                'Rate LENDER (%)',
                'Pendapatan Bunga LENDER (%)',
                'Pendapatan Bunga LENDER (Rp)',
                'Rate TOMI (%)',
                'Pendapatan Bunga TOMI (%)',
                'Pendapatan Bunga TOMI (Rp)',
                'Biaya Admin (%)',
                'Biaya Admin LENDER (%)',
                'Biaya Admin LENDER (Rp)',
                'Biaya Admin TOMI (%)',
                'Biaya Admin TOMI (Rp)',
                'Biaya Admin Referal 1 (%)',
                'Biaya Admin Referal 1 (Rp)',
                'Biaya Admin Referal 2 (%)',
                'Biaya Admin Referal 2 (Rp)',
                'Biaya Layanan TOMI',
                'Iuran Anggota',
                'Total Pendapatan TOMI (Rp)',
            ];
        }

        if (in_array($this->scope, ['tomidana', 'tomigaji', 'tomiinvoice'], true)) {
            return [
                'Produk',
                'Perusahaan',
                'Nama',
                'Tanggal Transaksi',
                'Tanggal Realisasi',
                'Tanggal Jatuh Tempo Pembayaran',
                'Status Pinjaman',
                'Nominal',
                'Angsuran Perbulan',
                'Tenor',
                'Angsuran ke',
                'Sisa Tenor',
                'Sisa Outstanding',
                'Tanggal Pembayaran',
                'Status Pembayaran',
                'Rate (%)',
                'Rate (Rp)',
                'Rate LENDER (%)',
                'Pendapatan Bunga LENDER (%)',
                'Pendapatan Bunga LENDER (Rp)',
                'Rate TOMI (%)',
                'Pendapatan Bunga TOMI (%)',
                'Pendapatan Bunga TOMI (Rp)',
                'Biaya Admin (%)',
                'Biaya Admin LENDER (%)',
                'Biaya Admin LENDER (Rp)',
                'Biaya Admin TOMI (%)',
                'Biaya Admin TOMI (Rp)',
                'Biaya Admin Referal 1 (%)',
                'Biaya Admin Referal 1 (Rp)',
                'Biaya Admin Referal 2 (%)',
                'Biaya Admin Referal 2 (Rp)',
                'Biaya Layanan TOMI',
                'Iuran Anggota',
                'Total Pendapatan TOMI (Rp)',
            ];
        }

        return ['Metrik', 'Nilai', 'Format', 'Periode'];
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }
}
