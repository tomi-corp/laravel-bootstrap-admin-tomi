<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ksp\Cost\CostAddRequest;
use App\Http\Requests\Admin\Ksp\Cost\UpdateRekeningRequest;
use App\Models\bank;
use App\Models\cost;
use App\Models\rekening_tomi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CostController extends Controller
{
    public function costIndex()
    {
        $cost = cost::get()->toArray();
        $bank = bank::get();
        $bankTomi = rekening_tomi::first();

        $data = [
            'identity_verifikasi' => $cost[0]['biaya'],
            'disbursement_peminjam' => $cost[1]['biaya'],
            'disbursement_tomi' => $cost[2]['biaya'],
            'bank' => $bank,
            'bankTomi' => $bankTomi
        ];
        return view('admin.ksp.cost.index', compact('data'));
    }

    public function saveRekening(UpdateRekeningRequest $req)
    {
        $bankTomi = rekening_tomi::firstOrNew();
        $bankTomi->bank_id = $req->bank;
        $bankTomi->nomor_rekening = $req->noRekening;
        $bankTomi->atas_nama = $req->atasNamaRekening;
        $bankTomi->updated_by = auth('admin')->user()->id;
        $bankTomi->save();
        return response()->json([
            'status' => 200,
            'message' => 'Rekening Repayment Berhasil Dirubah',
        ], 200);
    }

    public function costAdd(CostAddRequest $req)
    {
        $cost_identy = cost::where('type', 'identity_verification')->first();
        $cost_identy->biaya = $req->identity_verification;
        $cost_identy->save();

        $cost_disbursment = cost::where('type', 'disbursement_peminjam')->first();
        $cost_disbursment->biaya = $req->disbursement_peminjam;
        $cost_disbursment->save();

        $cost_disbursment = cost::where('type', 'disbursement_tomi')->first();
        $cost_disbursment->biaya = $req->disbursement_tomi;
        $cost_disbursment->save();

        return response()->json([
            'status' => 200,
            'message' => 'Biaya berhasil di ubah',
        ], 200);
    }
}
