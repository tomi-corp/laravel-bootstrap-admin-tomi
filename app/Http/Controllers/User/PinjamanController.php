<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Pinjaman\ActionPinjamRequest;
use App\Http\Requests\User\Pinjaman\OTPpinjamanRequest;
use App\Http\Requests\User\Pinjaman\SimulationRequest;
use App\Http\Resources\User\Pinjaman\DataKaryawanResource;
use App\Http\Resources\User\Pinjaman\HistroyPinjamanResource;
use App\Http\Resources\User\Pinjaman\ProdukPinjamanResource;
use App\Http\Resources\User\Pinjaman\TenorPinjamanResource;
use App\Models\list_data_ksp;
use App\Models\pinjaman;
use App\Models\produk_pinjaman;
use App\Models\tenor_pinjaman;
use App\Models\verif_otp_pengajuan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PinjamanController extends Controller
{
    private function simulation($data)
    {
        $tenor = tenor_pinjaman::where('id', $data['id_tenor_pinjaman'])->first();

        $rateee = $data['nominal']  * (($tenor->rate / 100 * $tenor->tenor) + $tenor->biaya_admin / 100);
        $rateeFinal = $rateee + $tenor->iuran_anggota;

        $potonganBulanan = ceil(($data['nominal'] / $tenor->tenor) / 10000) * 10000;

        $returnData = [
            'biaya_admin' => $rateeFinal,
            'potongan_perbulan' => $potonganBulanan,
            'jumlah_diterima' => $data['nominal'] - $rateeFinal,
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
            'biaya_admin' => "Rp  " .  number_format($simulation['biaya_admin']),
            'potongan_perbulan' => "Rp  " .  number_format($simulation['potongan_perbulan']),
            'jumlah_diterima' => "Rp  " .  number_format($simulation['jumlah_diterima']),
        ], 200);
    }

    public function actionPinjaman(ActionPinjamRequest $req)
    {
        $cekPinjaman = pinjaman::where('user_id', auth('api')->user()->id)
            ->where('status', 'on_proses')
            ->where('status_otp', 'success')
            ->first();


        $cekPinjamanPendingOTP = pinjaman::where('user_id', auth('api')->user()->id)
            ->where('status', 'on_proses')
            ->where('status_otp', 'pending')
            ->first();

        if (isset($cekPinjaman)) {
            return response()->json([
                'status' => 400,
                'message' => 'anda memiliki pinjaman yang belum diproses',
            ], 400);
        }

        if (isset($cekPinjamanPendingOTP)) {
            return response()->json([
                'status' => 200,
                'pinjaman' => false,
                'message' => 'anda memiliki validasi otp yang belum diselesaikan',
            ], 200);
        }

        // send otp

        while (true) {
            $code = "#LOAN-" . Carbon::now()->format('Y-m-d') . "-" . auth('api')->user()->id . Carbon::now()->format('s') . rand(100, 999);
            $pinjaman = pinjaman::where('id_pinjaman', $code)->first();
            if (!isset($pinjaman)) {
                break;
            }
        }

        $tenor = tenor_pinjaman::where('id', $req->id_tenor_pinjaman)->first();
        $data_karyawan = list_data_ksp::where('nip', $req->nip)->first();

        $codeOTP = rand(1000, 9999);

        $otp = new verif_otp_pengajuan();
        $otp->user_id = auth('api')->user()->id;
        $otp->otp = $codeOTP;

        $otp->expired = Carbon::now()->addMinutes(30);


        $pinjaman = new pinjaman();
        $pinjaman->user_id = auth('api')->user()->id;
        $pinjaman->id_perusahaan = $data_karyawan->id_list_perusahaan;
        $pinjaman->id_list_data_ksp = $data_karyawan->id;
        $pinjaman->id_pinjaman = $code;
        $pinjaman->nominal = $req->nominal;
        $pinjaman->tenor = $tenor->tenor;
        $pinjaman->rate = $tenor->rate;
        $pinjaman->biaya_admin = $tenor->biaya_admin;
        $pinjaman->iuran_anggota = $tenor->iuran_anggota;
        $pinjaman->status = 'on_proses';
        $pinjaman->status_otp = 'pending';

        $data = [
            'id_tenor_pinjaman' => $tenor->id,
            'nominal' => $req->nominal,
        ];

        $simulation = $this->simulation($data);
        $pinjaman->total_biaya_admin = $simulation['biaya_admin'];
        $pinjaman->total_angsuran_perbulan = $simulation['potongan_perbulan'];
        $pinjaman->jumlah_yang_diterima = $simulation['jumlah_diterima'];


        sendOTPWhatsapp($data_karyawan->whatsapp, $codeOTP);
        $pinjaman->save();
        $otp->pinjaman_id = $pinjaman->id;
        $otp->save();
        return response()->json([
            'status' => 200,
            'pinjaman' => true,
            'message' => 'success, harap periksa OTP yang telah dikirimkan melalui whatsapp',
        ], 200);
    }

    public function actionPinjamanOTP(OTPpinjamanRequest $req)
    {
        $verif = verif_otp_pengajuan::where('user_id',  auth('api')->user()->id)
            ->where('otp', $req->otp)
            ->first();

        if (!isset($verif)) {
            return response()->json([
                'status' => 400,
                'message' => 'OTP tidak benar',
            ], 400);
        } else {
            if ($verif->expired < Carbon::now()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'OTP kadaluarsa',
                ], 400);
            } else {
                $pinjaman = pinjaman::where('id', $verif->pinjaman_id)->first();
                $pinjaman->status_otp = "success";
                $pinjaman->save();
                $verif->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'verifikasi selesai, kami akan meneruskan data anda kepada pihak koperasi',
                ], 200);
            }
        }
    }

    public function historyPinjam()
    {
        $history = pinjaman::where('user_id', auth('api')->user()->id)
            ->with('pembayaran_pinjaman')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'history_pinjaman' => HistroyPinjamanResource::collection($history)
        ], 200);
    }

    public function cekPegawai(Request $req)
    {
        $list = list_data_ksp::where('nip', $req->nip)
            ->where('approve_satu', 'approve')
            ->where('approve_dua', 'approve')
            ->with('perusahaan')
            ->first();

        if (!isset($list)) {
            return response()->json([
                'status' => 400,
                'message' => 'Mohon maaf, data Anda belum ditemukan di aplikasi Tomi. Silakan hubungi HRD Anda agar data Anda dapat segera didaftarkan.',
            ], 400);
        } else {

            $cekPinjaman = pinjaman::where('user_id', auth('api')->user()->id)
                ->where('status', 'on_proses')
                ->where('status_otp', 'success')
                ->first();


            $cekPinjamanPendingOTP = pinjaman::where('user_id', auth('api')->user()->id)
                ->where('status', 'on_proses')
                ->where('status_otp', 'pending')
                ->first();

            if (isset($cekPinjaman)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'anda memiliki pinjaman yang belum diproses',
                ], 400);
            }

            if (isset($cekPinjamanPendingOTP)) {
                return response()->json([
                    'status' => 200,
                    'pinjaman' => false,
                    'message' => 'anda memiliki validasi otp yang belum diselesaikan',
                ], 200);
            }


            return response()->json([
                'status' => 200,
                'data' => new DataKaryawanResource($list)
            ], 200);
        }
    }

    public function actionPinjamanOTPUlang()
    {
        $pinjaman  = pinjaman::where('user_id', auth('api')->user()->id)
            ->where('status_otp', 'pending')
            ->with('data_karyawan')
            ->first();

        if (isset($pinjaman)) {
            $codeOTP = rand(1000, 9999);
            sendOTPWhatsapp($pinjaman->data_karyawan->whatsapp, $codeOTP);
            $verifOTP = verif_otp_pengajuan::where('pinjaman_id', $pinjaman->id)->first();
            $verifOTP->otp = $codeOTP;
            $verifOTP->expired = Carbon::now()->addHour(23);
            $verifOTP->save();
            return response()->json([
                'status' => 200,
                'message' => 'OTP berhasil dikirim',
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Data pengajuan anda tidak ditemukan',
            ], 400);
        }
    }
}
