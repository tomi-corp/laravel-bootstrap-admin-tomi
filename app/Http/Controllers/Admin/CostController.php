<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ksp\Cost\CostAddRequest;
use App\Models\cost;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CostController extends Controller
{
    public function costIndex()
    {
        $cost = cost::get()->toArray();
        $data = [
            'identity_verifikasi' => $cost[0]['biaya'],
            'disbursement_peminjam' => $cost[1]['biaya'],
        ];
        return view('admin.ksp.cost.index', compact('data'));
    }

    public function costAdd(CostAddRequest $req)
    {
        $cost_identy = cost::where('type', 'identity_verification')->first();
        $cost_identy->biaya = $req->identity_verification;
        $cost_identy->save();

        $cost_disbursment = cost::where('type', 'disbursement_peminjam')->first();
        $cost_disbursment->biaya = $req->disbursement_peminjam;
        $cost_disbursment->save();

        return response()->json([
            'status' => 200,
            'message' => 'Biaya berhasil di ubah',
        ], 200);
    }
}
