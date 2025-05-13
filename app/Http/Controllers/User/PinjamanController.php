<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Pinjaman\ActionPinjamRequest;
use App\Http\Requests\User\Pinjaman\SimulationRequest;
use App\Http\Resources\User\Pinjaman\HistroyPinjamanResource;
use App\Http\Resources\User\Pinjaman\ProdukPinjamanResource;
use App\Http\Resources\User\Pinjaman\TenorPinjamanResource;
use App\Models\pinjaman;
use App\Models\produk_pinjaman;
use App\Models\tenor_pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PinjamanController extends Controller
{
    private function simulation($data)
    {
        $tenor = tenor_pinjaman::where('id', $data['id_tenor_pinjaman'])->first();
        $biayaAdmin = $data['nominal'] * $tenor->biaya_admin / 100;
        $returnData = [
            'biaya_admin' => $biayaAdmin,
            'potongan_perbulan' => $data['nominal'] / $tenor->tenor,
            'jumlah_diterima' => $data['nominal'] - $biayaAdmin,
            'tenor' => $tenor->tenor,
        ];
        return $returnData;
    }

    public function getTenor()
    {
        $tenor = tenor_pinjaman::orderBy('tenor')->get();
        return response()->json([
            'status' => 200,
            'message' => 'success get tenor',
            'produk' => TenorPinjamanResource::collection($tenor)
        ], 200);
    }

    public function getSimulation(SimulationRequest $req)
    {
        $data = [
            'id_tenor_pinjaman' => $req->id_tenor_pinjaman,
            'nominal' => $req->nominal,
        ];
        $simulation = $this->simulation($data);
        return response()->json([
            'status' => 200,
            'message' => 'success get simulation',
            'biaya_admin' => "Rp" .  number_format($simulation['biaya_admin']),
            'potongan_perbulan' => "Rp" .  number_format($simulation['potongan_perbulan']),
            'jumlah_diterima' => "Rp" .  number_format($simulation['jumlah_diterima']),
        ], 200);
    }

    public function actionPinjaman(ActionPinjamRequest $req)
    {
        $cekPinjaman = pinjaman::where('user_id', auth('api')->user()->id)->where('status', 'pending')->first();
        if (isset($cekPinjaman)) {
            return response()->json([
                'status' => 400,
                'message' => 'anda memiliki pinjaman yang belum diproses',
            ], 400);
        }
        $tenor = tenor_pinjaman::where('id', $req->id_tenor_pinjaman)->first();
        $pinjaman = new pinjaman();
        $pinjaman->user_id = auth('api')->user()->id;
        $pinjaman->id_pinjaman = "#LOAN-" . Carbon::now()->format('Y-m-d') . "-" . auth('api')->user()->id . Carbon::now()->format('s');
        $pinjaman->nominal = $req->nominal;
        $pinjaman->tenor = $tenor->tenor;
        $pinjaman->biaya_admin = $tenor->biaya_admin;
        $pinjaman->tujuan = $req->tujuan;
        $pinjaman->status = 'pending';
        $pinjaman->save();
        return response()->json([
            'status' => 200,
            'message' => 'success',
        ], 200);
    }

    public function historyPinjam()
    {
        $history = pinjaman::where('user_id', auth('api')->user()->id)->with('pembayaran_pinjaman')->get();
        return response()->json([
            'status' => 200,
            'message' => 'success',
            'history_pinjaman' => HistroyPinjamanResource::collection($history)
        ], 200);
    }
}
