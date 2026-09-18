<?php

namespace App\Modules\Internal\Services;

use App\Models\ListPerusahaan;
use App\Modules\Internal\Repositories\CompanyRepository;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class CompanyTableService
{
    public function __construct(private readonly CompanyRepository $companies)
    {
    }

    public function data(): JsonResponse
    {
        return DataTables::eloquent($this->companies->tableQuery())
            ->addIndexColumn()
            ->addColumn('lender_name', fn (ListPerusahaan $company) => optional($company->lender)->nama ?? '-')
            ->addColumn('province_name', fn (ListPerusahaan $company) => optional($company->provinsi)->provinsi ?? '-')
            ->addColumn('city_name', fn (ListPerusahaan $company) => optional($company->kabupaten)->kabupaten ?? '-')
            ->addColumn('district_name', fn (ListPerusahaan $company) => optional($company->kecamatan)->kecamatan ?? '-')
            ->addColumn('village_name', function (ListPerusahaan $company) {
                if (! $company->desa) {
                    return '-';
                }

                return trim(($company->desa->desa ?? '-').' '.($company->desa->kodepos ?? ''));
            })
            ->addColumn('status_badge', function (ListPerusahaan $company) {
                $class = $company->status === 'active' ? 'badge-success' : 'badge-warning';

                return '<span class="badge '.$class.'">'.strtoupper($company->status).'</span>';
            })
            ->addColumn('action', function (ListPerusahaan $company) {
                return '<button type="button" class="btn btn-sm btn-primary mr-1" onclick="editCompany('.$company->id.')"><i class="fa-solid fa-pen"></i></button>'
                    .'<button type="button" class="btn btn-sm btn-danger" onclick="deleteCompany('.$company->id.')"><i class="fa-solid fa-trash-can"></i></button>';
            })
            ->filterColumn('lender_name', fn ($query, $keyword) => $query->whereHas('lender', fn ($q) => $q->where('nama', 'like', "%{$keyword}%")))
            ->filterColumn('province_name', fn ($query, $keyword) => $query->whereHas('provinsi', fn ($q) => $q->where('provinsi', 'like', "%{$keyword}%")))
            ->filterColumn('city_name', fn ($query, $keyword) => $query->whereHas('kabupaten', fn ($q) => $q->where('kabupaten', 'like', "%{$keyword}%")))
            ->filterColumn('district_name', fn ($query, $keyword) => $query->whereHas('kecamatan', fn ($q) => $q->where('kecamatan', 'like', "%{$keyword}%")))
            ->filterColumn('village_name', fn ($query, $keyword) => $query->whereHas('desa', fn ($q) => $q->where('desa', 'like', "%{$keyword}%")->orWhere('kodepos', 'like', "%{$keyword}%")))
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}
