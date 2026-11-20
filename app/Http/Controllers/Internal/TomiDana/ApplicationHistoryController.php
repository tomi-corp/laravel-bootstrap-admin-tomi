<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\pinjaman;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Riwayat pengajuan pinjaman TomiDana (semua perusahaan yang boleh diakses admin).
 * Hanya pengajuan yang sudah tervalidasi WhatsApp (status_otp = success).
 */
class ApplicationHistoryController extends Controller
{
    use ScopesCompanies;

    /**
     * Filter tabel: key filter => status pinjaman.
     */
    private const FILTERS = [
        'aktif' => ['approve'],
        'lunas' => ['done'],
        'diproses' => ['on_proses', 'pending'],
        'ditolak' => ['reject'],
    ];

    private const STATUS_BADGES = [
        'on_proses' => ['DIPROSES', 'info'],
        'pending' => ['PENDING', 'warning'],
        'approve' => ['AKTIF', 'primary'],
        'reject' => ['DITOLAK', 'danger'],
        'done' => ['LUNAS', 'success'],
    ];

    public function index()
    {
        return view('internal.tomi-dana.application-history.index');
    }

    public function statistic()
    {
        $summary = fn (?array $statuses) => $this->applications()
            ->when($statuses, fn ($q) => $q->whereIn('status', $statuses))
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(nominal), 0) as nominal')
            ->first();

        $format = fn ($row) => [
            'total' => number_format((int) $row->total, 0, ',', '.'),
            'nominal' => 'Rp. '.number_format((float) $row->nominal, 0, ',', '.'),
        ];

        return response()->json([
            'status' => 200,
            'pengajuan' => $format($summary(null)),
            'aktif' => $format($summary(self::FILTERS['aktif'])),
            'lunas' => $format($summary(self::FILTERS['lunas'])),
        ]);
    }

    public function table(Request $req)
    {
        $pinjamanTable = (new pinjaman)->getTable();

        $query = $this->applications()
            ->select([
                "{$pinjamanTable}.*",
                'list_karyawans.nama_karyawan as karyawan_nama',
                'list_karyawans.nip as karyawan_nip',
                'list_perusahaans.nama as perusahaan_nama',
                'lenders.nama as lender_nama',
            ])
            ->leftJoin('list_karyawans', 'list_karyawans.id', '=', "{$pinjamanTable}.id_karyawan")
            ->leftJoin('list_perusahaans', 'list_perusahaans.id', '=', "{$pinjamanTable}.id_perusahaan")
            ->leftJoin('lenders', 'lenders.id', '=', "{$pinjamanTable}.id_lender");

        if ($statuses = self::FILTERS[$req->input('filter')] ?? null) {
            $query->whereIn("{$pinjamanTable}.status", $statuses);
        }

        $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');
        $upper = fn ($value) => e(strtoupper((string) ($value ?? '-')));

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('tanggal', fn ($row) => Carbon::parse($row->created_at)->format('Y-m-d H:i'))
            ->addColumn('id_pinjaman', fn ($row) => $upper($row->id_pinjaman))
            ->addColumn('nama_karyawan', fn ($row) => '<b>'.$upper($row->karyawan_nama).'</b><br><small>'.e($row->karyawan_nip ?? '-').'</small>')
            ->addColumn('perusahaan', fn ($row) => $upper($row->perusahaan_nama))
            ->addColumn('lender', fn ($row) => $upper($row->lender_nama))
            ->addColumn('nominal', fn ($row) => $rupiah($row->nominal))
            ->addColumn('tenor', fn ($row) => number_format((int) $row->tenor, 0, ',', '.').' BULAN')
            ->addColumn('angsuran_perbulan', fn ($row) => $rupiah($row->total_angsuran_perbulan))
            ->addColumn('jumlah_yang_diterima', fn ($row) => $rupiah($row->jumlah_yang_diterima))
            ->addColumn('status_badge', function ($row) {
                [$label, $color] = self::STATUS_BADGES[$row->status] ?? [strtoupper((string) $row->status), 'secondary'];

                return '<span class="badge badge-'.$color.'">'.e($label).'</span>';
            })
            ->filterColumn('nama_karyawan', fn ($q, $keyword) => $q->where(fn ($w) => $w
                ->where('list_karyawans.nama_karyawan', 'like', "%{$keyword}%")
                ->orWhere('list_karyawans.nip', 'like', "%{$keyword}%")))
            ->filterColumn('perusahaan', fn ($q, $keyword) => $q->where('list_perusahaans.nama', 'like', "%{$keyword}%"))
            ->filterColumn('lender', fn ($q, $keyword) => $q->where('lenders.nama', 'like', "%{$keyword}%"))
            ->filterColumn('id_pinjaman', fn ($q, $keyword) => $q->where("{$pinjamanTable}.id_pinjaman", 'like', "%{$keyword}%"))
            ->orderColumn('tanggal', "{$pinjamanTable}.created_at $1")
            ->orderColumn('nominal', "{$pinjamanTable}.nominal $1")
            ->rawColumns(['id_pinjaman', 'nama_karyawan', 'perusahaan', 'lender', 'status_badge'])
            ->only([
                'DT_RowIndex',
                'tanggal',
                'id_pinjaman',
                'nama_karyawan',
                'perusahaan',
                'lender',
                'nominal',
                'tenor',
                'angsuran_perbulan',
                'jumlah_yang_diterima',
                'status_badge',
            ])
            ->toJson();
    }

    private function applications(): Builder
    {
        $pinjamanTable = (new pinjaman)->getTable();

        return pinjaman::query()
            ->whereIn("{$pinjamanTable}.id_perusahaan", $this->visibleCompanies()->select('list_perusahaans.id'))
            ->where("{$pinjamanTable}.status_otp", 'success');
    }
}
