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
            'penerbitan_sertifikat_elektronik' => $cost[0]['biaya'],
            'pk_automation' => $cost[1]['biaya'],
            'wa_otp' => $cost[2]['biaya'],
            'disbursement' => $cost[3]['biaya'],
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
        $cost_identy = cost::where('type', 'penerbitan_sertifikat_elektronik')->first();
        $cost_identy->biaya = $req->penerbitan_sertifikat_elektronik;
        $cost_identy->save();

        $cost_disbursment = cost::where('type', 'pk_automation')->first();
        $cost_disbursment->biaya = $req->pk_automation;
        $cost_disbursment->save();

        $cost_disbursment = cost::where('type', 'wa_otp')->first();
        $cost_disbursment->biaya = $req->wa_otp;
        $cost_disbursment->save();

        $cost_disbursment = cost::where('type', 'disbursement')->first();
        $cost_disbursment->biaya = $req->disbursement;
        $cost_disbursment->save();

        return response()->json([
            'status' => 200,
            'message' => 'Biaya berhasil di ubah',
        ], 200);
    }
}
