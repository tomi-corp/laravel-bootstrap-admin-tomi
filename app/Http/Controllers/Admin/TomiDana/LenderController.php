<?php

namespace App\Http\Controllers\Admin\TomiDana;

use App\Http\Controllers\Controller;
use App\Models\lender;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LenderController extends Controller
{
    public function index()
    {
        return view('admin.tomi-dana.lender.index');
    }

    public function table()
    {
        $query = lender::with('bank')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('lender_features')
                    ->join('features', 'features.id', '=', 'lender_features.feature_id')
                    ->whereColumn('lender_features.lender_id', 'lenders.id')
                    ->whereRaw("REPLACE(LOWER(features.name), ' ', '') = ?", ['tomidana'])
                    ->whereNull('lender_features.deleted_at')
                    ->whereNull('features.deleted_at');
            })
            ->latest();

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('bank', fn ($row) => $row->bank?->code_bank ?? '-')
            ->addColumn('status_label', function ($row) {
                return $row->status === 'active'
                    ? '<span class="badge badge-success">ACTIVE</span>'
                    : '<span class="badge badge-warning">INACTIVE</span>';
            })
            ->addColumn('action', fn ($row) => '<a href="'.route('admin.tomi-dana.lender.pinjaman', ['lender_id' => $row->id]).'" class="btn btn-sm btn-info" title="Lihat pinjaman"><i class="mdi mdi-format-list-bulleted"></i></a>')
            ->rawColumns(['status_label', 'action'])
            ->toJson();
    }
}
