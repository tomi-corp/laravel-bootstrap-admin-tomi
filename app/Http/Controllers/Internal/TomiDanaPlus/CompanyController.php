<?php

namespace App\Http\Controllers\Internal\TomiDanaPlus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\Feature;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

/**
 * Daftar perusahaan TomiDana Plus: hanya perusahaan yang punya PKS fitur TomiDana Plus.
 */
class CompanyController extends Controller
{
    use ScopesCompanies;

    public const FEATURE_CODE = 'tomidanaplus';

    public function index()
    {
        return view('internal.tomi-dana-plus.companies.index', [
            'feature' => $this->feature(),
        ]);
    }

    public function table()
    {
        $feature = $this->feature();
        $pksOfFeature = fn ($query) => $query->where('feature_id', $feature->id);

        $query = $this->visibleCompanies()
            ->select('list_perusahaans.*')
            ->whereHas('pks', $pksOfFeature)
            ->with(['lender:id,nama', 'pks' => fn ($query) => $pksOfFeature($query)->latest('end_date')]);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('lender', fn ($row) => Str::upper($row->lender?->nama ?? '-'))
            ->addColumn('pks_number', fn ($row) => Str::upper($row->pks->first()?->agreement_number ?? '-'))
            ->addColumn('pks_period', function ($row) {
                $pks = $row->pks->first();

                return $pks ? $pks->start_date?->format('d-m-Y').' s/d '.$pks->end_date?->format('d-m-Y') : '-';
            })
            ->addColumn('pks_status', function ($row) {
                $pks = $row->pks->first();

                return $pks && $pks->end_date?->lt(today())
                    ? '<span class="badge badge-secondary">BERAKHIR</span>'
                    : '<span class="badge badge-success">AKTIF</span>';
            })
            ->editColumn('status', fn ($row) => $row->status === 'active'
                ? '<span class="badge badge-success">AKTIF</span>'
                : '<span class="badge badge-warning">TIDAK AKTIF</span>')
            ->editColumn('nama', fn ($row) => Str::upper($row->nama))
            ->editColumn('pic_perusahaan', fn ($row) => Str::upper($row->pic_perusahaan))
            ->editColumn('pic_internal', fn ($row) => Str::upper($row->pic_internal))
            ->editColumn('email_perusahaan', fn ($row) => Str::lower((string) $row->email_perusahaan))
            ->filterColumn('lender', fn ($query, $keyword) => $query->whereHas('lender', fn ($q) => $q->where('nama', 'like', "%{$keyword}%")))
            ->filterColumn('pks_number', fn ($query, $keyword) => $query->whereHas('pks', fn ($q) => $pksOfFeature($q)->where('agreement_number', 'like', "%{$keyword}%")))
            ->rawColumns(['status', 'pks_status'])
            ->make(true);
    }

    private function feature(): Feature
    {
        return Feature::query()->where('code', self::FEATURE_CODE)->firstOrFail();
    }
}
