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
        private readonly ?Collection $cachedRows = null,
    ) {
    }

    public function collection(): Collection
    {
        if ($this->cachedRows !== null) {
            return $this->cachedRows;
        }

        if ($this->scope === 'tomidana') {
            return $this->tomiDanaRows();
        }

        if ($this->scope === 'tomigaji') {
            return $this->tomiGajiRows();
        }

        if ($this->scope === 'tomiinvoice') {
            return $this->tomiInvoiceRows();
        }

        if ($this->scope === 'overview') {
            return $this->tomiDanaRows()
                ->concat($this->tomiGajiRows())
                ->concat($this->tomiInvoiceRows())
                ->values();
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

    private function tomiInvoiceRows(): Collection
    {
        return DB::table('financing_request_invoices as f')
            ->leftJoin('clients as c', 'c.id', '=', 'f.client_id')
            ->leftJoin('vendors as v', 'v.id', '=', 'f.vendor_id')
            ->leftJoin('lenders as l', 'l.id', '=', 'f.lender_id')
            ->whereNull('f.deleted_at')
            ->where('f.analyst_decision', 'approved')
            ->whereBetween('f.approved_at', [$this->period->start(), $this->period->end()])
            ->orderBy('f.approved_at')
            ->orderBy('f.id')
            ->get([
                'f.id',
                'f.created_at',
                'f.approved_at',
                'f.analyst_decision',
                'f.status',
                'f.purchase_order_amount',
                'f.approved_financing_amount',
                'f.finance_invoice_term_date_1',
                'f.finance_invoice_term_date_2',
                'f.finance_invoice_term_date_3',
                'f.finance_invoice_term_amount_1',
                'f.finance_invoice_term_amount_2',
                'f.finance_invoice_term_amount_3',
                'f.finance_vendor_payment_proof_date_1',
                'f.finance_vendor_payment_proof_date_2',
                'f.finance_vendor_payment_proof_date_3',
                'c.name as client_name',
                'v.name as vendor_name',
                'l.nama as lender_name',
            ])
            ->map(function ($row) {
                $nominal = (float) ($row->approved_financing_amount ?? $row->purchase_order_amount ?? 0);
                $terms = collect([1, 2, 3])->map(fn (int $number) => [
                    'date' => $row->{"finance_invoice_term_date_{$number}"},
                    'amount' => (float) ($row->{"finance_invoice_term_amount_{$number}"} ?? 0),
                ]);
                $firstTermDate = $terms->pluck('date')->filter()->sort()->first();
                $billed = $terms->sum('amount');
                $paymentDate = collect([1, 2, 3])
                    ->map(fn (int $number) => $row->{"finance_vendor_payment_proof_date_{$number}"})
                    ->filter()
                    ->sort()
                    ->last();

                $values = [
                    'Tomi Invoice',
                $row->client_name,
                $row->vendor_name,
                    $this->dateValue($row->created_at),
                    $this->dateValue($row->approved_at),
                    $this->dateValue($firstTermDate),
                    $row->status ?: $row->analyst_decision,
                    $nominal,
                    null,
                    null,
                    null,
                    null,
                    max(0, $nominal - $billed),
                    $this->dateValue($paymentDate),
                    $billed >= $nominal && $nominal > 0 ? 'Lunas' : 'Belum Lunas',
                ];

                $values = array_pad($values, 34, null);
                $values[] = null;

                return $values;
            });
    }

    private function tomiGajiRows(): Collection
    {
        $dateDisbursed = 'COALESCE(sf.transfer_date, sf.created_at)';

        return DB::table('salary_financing_items as sfi')
            ->join('salary_financings as sf', 'sf.id', '=', 'sfi.salary_financing_id')
            ->leftJoin('lenders as l', 'l.id', '=', 'sf.lender_id')
            ->whereNull('sfi.deleted_at')
            ->whereIn('sf.status', InsightService::GAJI_DISBURSED)
            ->whereRaw("$dateDisbursed BETWEEN ? AND ?", [$this->period->start(), $this->period->end()])
            ->orderByRaw("$dateDisbursed")
            ->orderBy('sfi.id')
            ->get([
                'sf.id as financing_id',
                'sf.agreement_number',
                'sf.company_name',
                'sf.id_perusahaan',
                'l.nama as lender_name',
                'sf.status as financing_status',
                'sf.created_at as financing_created_at',
                'sf.transfer_date',
                'sf.payment_date as financing_payment_date',
                'sf.loan_amount',
                'sf.total_dibayarkan',
                'sf.total_fee',
                'sf.total_borrowers',
                'sfi.application_date',
                'sfi.name as employee_name',
                'sfi.nik',
                'sfi.nip',
                'sfi.work_placement',
                'sfi.income_amount',
                'sfi.payment_date as item_payment_date',
                'sfi.bank',
                'sfi.account_number',
                'sfi.account_holder_name',
            ])
            ->map(function ($row) {
                $values = [
                    'TomiGaji',
                    $row->company_name,
                    $row->employee_name,
                    $this->dateValue($row->application_date ?? $row->financing_created_at),
                    $this->dateValue($row->transfer_date ?? $row->financing_created_at),
                    null,
                    $row->financing_status,
                    $row->income_amount,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $this->dateValue($row->item_payment_date ?? $row->financing_payment_date),
                    $row->financing_status === 'lunas' ? 'Lunas' : 'Belum Lunas',
                ];

                $values = array_pad($values, 34, null);
                $values[] = $row->total_fee;

                return $values;
            });
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
