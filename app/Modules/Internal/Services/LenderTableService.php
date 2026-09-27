<?php

namespace App\Modules\Internal\Services;

use App\Models\lender;
use App\Modules\Internal\Repositories\LenderRepository;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class LenderTableService
{
    public function __construct(private readonly LenderRepository $lenders)
    {
    }

    public function data(): JsonResponse
    {
        return DataTables::eloquent($this->lenders->tableQuery())
            ->addIndexColumn()
            ->addColumn('bank_name', fn (lender $lender) => $lender->bank?->code_bank ?? '-')
            ->addColumn('province_name', fn (lender $lender) => $lender->provinsi?->provinsi ?? '-')
            ->addColumn('city_name', fn (lender $lender) => $lender->kabupaten?->kabupaten ?? '-')
            ->addColumn('district_name', fn (lender $lender) => $lender->kecamatan?->kecamatan ?? '-')
            ->addColumn('village_name', function (lender $lender) {
                if (! $lender->desa) {
                    return '-';
                }

                return trim(($lender->desa->desa ?? '-').' '.($lender->desa->kodepos ?? ''));
            })
            ->addColumn('status_badge', function (lender $lender) {
                $class = $lender->status === 'active'
                    ? 'bg-emerald-100 text-emerald-700 ring-emerald-600/20'
                    : 'bg-amber-100 text-amber-700 ring-amber-600/20';

                return '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold uppercase ring-1 ring-inset '.$class.'">'.strtoupper($lender->status).'</span>';
            })
            ->addColumn('action', function (lender $lender) {
                return '<div class="inline-flex items-center gap-2">'
                    .'<button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-blue-600 text-white transition hover:bg-blue-700" onclick="editLender('.$lender->id.')"><i class="fa-solid fa-pen"></i></button>'
                    .'<button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-red-600 text-white transition hover:bg-red-700" onclick="deleteLender('.$lender->id.')"><i class="fa-solid fa-trash-can"></i></button>'
                    .'</div>';
            })
            ->filterColumn('bank_name', fn ($query, $keyword) => $query->whereHas('bank', fn ($q) => $q->where('code_bank', 'like', "%{$keyword}%")))
            ->filterColumn('province_name', fn ($query, $keyword) => $query->whereHas('provinsi', fn ($q) => $q->where('provinsi', 'like', "%{$keyword}%")))
            ->filterColumn('city_name', fn ($query, $keyword) => $query->whereHas('kabupaten', fn ($q) => $q->where('kabupaten', 'like', "%{$keyword}%")))
            ->filterColumn('district_name', fn ($query, $keyword) => $query->whereHas('kecamatan', fn ($q) => $q->where('kecamatan', 'like', "%{$keyword}%")))
            ->filterColumn('village_name', fn ($query, $keyword) => $query->whereHas('desa', fn ($q) => $q->where('desa', 'like', "%{$keyword}%")->orWhere('kodepos', 'like', "%{$keyword}%")))
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}
