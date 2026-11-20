<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\CompanyReferral;
use App\Models\CompanyPks;
use App\Models\Feature;
use App\Models\ListPerusahaan;
use App\Models\pinjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

/**
 * Biaya referal TomiDana: rekap fee Referal 1 / Referal 2 per perusahaan ber-PKS TomiDana per bulan.
 * Belum ada tabel invoice referal, jadi tagihan dihitung langsung dari pinjaman yang dicairkan pada bulan tersebut.
 */
class ReferralFeeController extends Controller
{
    use ScopesCompanies;

    private const FEATURE_CODE = 'tomidana';

    /** Kolom yang bisa dipilih saat download PDF. */
    public const PDF_COLUMNS = [
        'referal_1' => 'REFERAL 1',
        'referal_2' => 'REFERAL 2',
    ];

    /** Fee referal per pinjaman: nominal rupiah tersimpan, atau persen x nominal untuk data lama. */
    private const FEE_1_SQL = 'COALESCE(p.jumlah_profesional_1, COALESCE(p.profesional_fee_1, 0) / 100 * COALESCE(p.nominal, 0), 0)';

    private const FEE_2_SQL = 'COALESCE(p.jumlah_profesional_2, COALESCE(p.profesional_fee_2, 0) / 100 * COALESCE(p.nominal, 0), 0)';

    public function index()
    {
        return view('internal.tomi-dana.referral-fee.index', [
            'currentPeriod' => Carbon::now()->isoFormat('MMMM Y'),
        ]);
    }

    /**
     * @param  string  $type  current = bulan berjalan, history = bulan-bulan sebelumnya
     */
    public function table(string $type)
    {
        $currentPeriod = Carbon::now()->format('Y-m');

        $query = DB::query()
            ->fromSub($this->monthlySummary(), 's')
            ->join('list_perusahaans as lp', 'lp.id', '=', 's.company_id')
            ->select('s.*', 'lp.nama as company_name', 'lp.id_code_perusahaan')
            ->when($type === 'current', fn ($q) => $q->where('s.period', $currentPeriod))
            ->when($type === 'history', fn ($q) => $q->where('s.period', '<', $currentPeriod));

        $referrals = $this->referralNames();
        $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');
        $referralName = fn ($row, int $position) => e(Str::upper($referrals[$row->company_id][$position] ?? '-'));

        return DataTables::query($query)
            ->addIndexColumn()
            ->addColumn('period_label', fn ($row) => Str::upper(Carbon::createFromFormat('Y-m', $row->period)->isoFormat('MMMM Y')))
            ->addColumn('invoice_number', fn ($row) => e($this->invoiceNumber($row->id_code_perusahaan, $row->period)))
            ->addColumn('company', fn ($row) => '<b>'.e(Str::upper($row->company_name)).'</b>')
            ->addColumn('referral_1', fn ($row) => $referralName($row, 1))
            ->addColumn('referral_2', fn ($row) => $referralName($row, 2))
            ->addColumn('total_loans', fn ($row) => '<span class="badge badge-success">'.number_format((int) $row->total_loans, 0, ',', '.').'</span>')
            ->addColumn('fee_1', fn ($row) => $rupiah($row->fee_1))
            ->addColumn('fee_2', fn ($row) => $rupiah($row->fee_2))
            ->addColumn('total_fee', fn ($row) => '<b>'.$rupiah($row->fee_1 + $row->fee_2).'</b>')
            ->addColumn('download', fn ($row) => '<a href="javascript:void(0)" class="btn-download-invoice" data-company="'.$row->company_id.'" data-period="'.$row->period.'"><i class="mdi mdi-download"></i></a>')
            ->filterColumn('company', fn ($q, $keyword) => $q->where('lp.nama', 'like', "%{$keyword}%"))
            ->orderColumn('period_label', 's.period $1')
            ->orderColumn('company', 'lp.nama $1')
            ->rawColumns(['invoice_number', 'company', 'referral_1', 'referral_2', 'total_loans', 'total_fee', 'download'])
            ->only(['DT_RowIndex', 'period_label', 'invoice_number', 'company', 'referral_1', 'referral_2', 'total_loans', 'fee_1', 'fee_2', 'total_fee', 'download'])
            ->toJson();
    }

    public function download(Request $request, int $company, string $period)
    {
        abort_unless(preg_match('/^\d{4}-\d{2}$/', $period), 404);
        abort_unless(in_array($company, $this->companyIds()->all(), true), 404);

        $columns = array_intersect_key(self::PDF_COLUMNS, array_flip((array) $request->get('columns', [])));
        if (empty($columns)) {
            $columns = self::PDF_COLUMNS;
        }

        $companyModel = ListPerusahaan::findOrFail($company);
        $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();

        $rows = $this->loansQuery()
            ->where('p.id_perusahaan', $company)
            ->whereBetween('d.created_at', [$start, $start->copy()->endOfMonth()])
            ->leftJoin('list_karyawans as k', 'k.id', '=', 'p.id_karyawan')
            ->select('p.id_pinjaman', 'p.nominal', 'p.created_at', 'd.created_at as disbursed_at', 'k.nama_karyawan', 'k.nip')
            ->selectRaw(self::FEE_1_SQL.' as fee_1')
            ->selectRaw(self::FEE_2_SQL.' as fee_2')
            ->orderBy('d.created_at')
            ->get()
            ->map(function ($row) use ($columns) {
                $fees = array_intersect_key(['referal_1' => (float) $row->fee_1, 'referal_2' => (float) $row->fee_2], $columns);

                return [
                    'tanggal' => Carbon::parse($row->disbursed_at)->format('d-m-Y'),
                    'id_pinjaman' => $row->id_pinjaman,
                    'nama_karyawan' => $row->nama_karyawan ?? '-',
                    'nip' => $row->nip ?? '-',
                    'nominal' => (float) $row->nominal,
                    'fees' => $fees,
                    'subtotal' => array_sum($fees),
                ];
            });

        $totals = [];
        foreach (array_keys($columns) as $key) {
            $totals[$key] = $rows->sum(fn ($row) => $row['fees'][$key] ?? 0);
        }

        $referrals = CompanyReferral::with(['referral.bank:id,code_bank'])
            ->where('company_id', $company)
            ->whereIn('position', $this->positionsFor($columns))
            ->orderBy('position')
            ->get();

        $pdf = Pdf::loadView('internal.tomi-dana.referral-fee.pdf', [
            'company' => $companyModel,
            'periodStart' => $start,
            'invoiceNumber' => $this->invoiceNumber($companyModel->id_code_perusahaan, $period),
            'columns' => $columns,
            'rows' => $rows,
            'totals' => $totals,
            'grandTotal' => array_sum($totals),
            'referrals' => $referrals,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('invoice-referal-'.Str::slug($companyModel->nama).'-'.$period.'.pdf');
    }

    /** Ringkasan per perusahaan per bulan pencairan. */
    private function monthlySummary(): Builder
    {
        return $this->loansQuery()
            ->selectRaw('p.id_perusahaan as company_id')
            ->selectRaw("DATE_FORMAT(d.created_at, '%Y-%m') as period")
            ->selectRaw('COUNT(DISTINCT p.id) as total_loans')
            ->selectRaw('SUM('.self::FEE_1_SQL.') as fee_1')
            ->selectRaw('SUM('.self::FEE_2_SQL.') as fee_2')
            ->groupBy('p.id_perusahaan', 'period');
    }

    /** Pinjaman yang sudah dicairkan milik perusahaan ber-PKS TomiDana yang boleh diakses admin. */
    private function loansQuery(): Builder
    {
        return DB::table((new pinjaman)->getTable().' as p')
            ->join('disbursements as d', 'd.pinjaman_id', '=', 'p.id')
            ->whereIn('p.status', ['approve', 'done'])
            ->whereNull('p.deleted_at')
            ->whereNull('d.deleted_at')
            ->whereIn('p.id_perusahaan', $this->companyIds());
    }

    private function companyIds(): Collection
    {
        return CompanyPks::query()
            ->where('feature_id', $this->featureId())
            ->whereIn('company_id', $this->visibleCompanies()->select('list_perusahaans.id'))
            ->distinct()
            ->pluck('company_id')
            ->map(fn ($id) => (int) $id);
    }

    /** Nama referral per perusahaan: [company_id => [posisi => nama]]. */
    private function referralNames(): array
    {
        $names = [];
        CompanyReferral::with('referral:id,name')
            ->whereIn('company_id', $this->companyIds())
            ->get()
            ->each(function (CompanyReferral $row) use (&$names) {
                $names[$row->company_id][$row->position] = $row->referral?->name;
            });

        return $names;
    }

    private function positionsFor(array $columns): array
    {
        return array_values(array_filter([
            isset($columns['referal_1']) ? 1 : null,
            isset($columns['referal_2']) ? 2 : null,
        ]));
    }

    private function invoiceNumber(?string $companyCode, string $period): string
    {
        return 'INV-REF-'.str_replace('-', '', $period).'-'.Str::upper(ltrim((string) $companyCode, '#'));
    }

    private function featureId(): int
    {
        return (int) Feature::where('code', self::FEATURE_CODE)->value('id');
    }
}
