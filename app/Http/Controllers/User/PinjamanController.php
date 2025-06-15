<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Pinjaman\ActionPinjamRequest;
use App\Http\Requests\User\Pinjaman\CekMinimalMaximalRequest;
use App\Http\Requests\User\Pinjaman\CekPegawaiRequest;
use App\Http\Requests\User\Pinjaman\GetTenorRequest;
use App\Http\Requests\User\Pinjaman\OTPpinjamanRequest;
use App\Http\Requests\User\Pinjaman\SimulationRequest;
use App\Http\Resources\User\Pinjaman\DataKaryawanResource;
use App\Http\Resources\User\Pinjaman\HistroyPinjamanResource;
use App\Http\Resources\User\Pinjaman\ProdukPinjamanResource;
use App\Http\Resources\User\Pinjaman\TenorPinjamanResource;
use App\Models\jatuh_tempo;
use App\Models\list_data_ksp;
use App\Models\ListPerusahaan;
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
        $tenor = tenor_pinjaman::where('id', $data['id_tenor_pinjaman'])
            ->with('amount_pinjaman')
            ->first();

        $biaya_admin = $tenor->biaya_admin / 100 * $data['nominal'];
        $biaya_layanan = $tenor->biaya_layanan;
        $iuran_anggota = $tenor->amount_pinjaman->iuran_anggota * $tenor->tenor;
        $nilai_yang_diterima = $data['nominal'] - $biaya_admin - $biaya_layanan - $iuran_anggota;

        $rate = $tenor->rate / 100;
        $angsuran_perbulan = $data['nominal'] + $data['nominal'] * $rate * $tenor->tenor;
        $perbulan = $angsuran_perbulan / $tenor->tenor;
        $angsuran_perbulan_ceil = ceil($perbulan / 1000) * 1000;

        $returnData = [
            'angsuran_perbulan' => $angsuran_perbulan_ceil,
            'tenor' => $tenor->tenor,
            'biaya_admin' => $biaya_admin,
            'biaya_layanan' => $biaya_layanan,
            'iuran_anggota' => $iuran_anggota,
            'jumlah_diterima' => $nilai_yang_diterima,
        ];
        return $returnData;
    }

    public function maximalMinimal(CekMinimalMaximalRequest $req)
    {
        $nip = explode("-", $req->nip);
        $perusahaan = ListPerusahaan::where('id', substr($nip[0], 4))
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }, 'amount_pinjaman'])->first();

        if (isset($perusahaan)) {
            $pendapatan = 70 / 100 * $perusahaan->karyawanSingle->penghasilan;
            if ($pendapatan > $perusahaan->amount_pinjaman->maximal) {
                $maksimal = $perusahaan->amount_pinjaman->maximal;
            } elseif ($pendapatan < $perusahaan->amount_pinjaman->maximal) {
                $maksimal = $pendapatan;
            }

            return response()->json([
                'status' => 200,
                'maximal' => "Rp  " . number_format($maksimal),
                'minimal' => "Rp  " . number_format($perusahaan->amount_pinjaman->minimal),
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Data NIP tidak ditemukan',
            ], 400);
        }
    }

    public function getTenor(GetTenorRequest $req)
    {
        $nip = explode("-", $req->nip);
        $perusahaan = ListPerusahaan::where('id', substr($nip[0], 4))
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }])
            ->first();

        $pendapatan = 70 / 100 * $perusahaan->karyawanSingle->penghasilan;


        if ($req->nominal > $perusahaan->amount_pinjaman->maximal) {
            $maksimal = $perusahaan->amount_pinjaman->maximal;
        } elseif ($req->nominal > $pendapatan) {
            $maksimal = $pendapatan;
        } else {
            $maksimal = $req->nominal;
        }

        $range = [
            'maximal' => $maksimal,
            'minimal' => $perusahaan->amount_pinjaman->minimal
        ];

        $tenor = tenor_pinjaman::where('id_perusahaan', $perusahaan->id)
            ->where(function ($query) use ($range, $req) {
                $query->where('minimal_nominal', '<=', $req->nominal);
                $query->where('maximal_nominal', '>=',  $range['maximal']);
            })
            ->with('amount_pinjaman')
            ->orderBy('tenor')
            ->get();

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
            'tenor' => $simulation['tenor'] . " bulan",
            'angsuran_perbulan' => "Rp  " .  number_format($simulation['angsuran_perbulan']),
            'biaya_admin' => "Rp  " .  number_format($simulation['biaya_admin']),
            'biaya_layanan' => "Rp  " .  number_format($simulation['biaya_layanan']),
            'iuran_anggota' => "Rp  " .  number_format($simulation['iuran_anggota']),
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
        $data_karyawan = list_data_ksp::where('nip', $req->nip)
            ->with('perusahaan')
            ->first();

        $codeOTP = rand(1000, 9999);

        $otp = new verif_otp_pengajuan();
        $otp->user_id = auth('api')->user()->id;
        $otp->otp = $codeOTP;

        $otp->expired = Carbon::now()->addHour(12);

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

        $jatuh_tempo = new jatuh_tempo();
        $jatuh_tempo->id_pinjaman = $pinjaman->id;
        $jatuh_tempo->pembayaran = 1;
        $jatuh_tempo->jatuh_tempo =   Carbon::now()->day($data_karyawan->perusahaan->payment_date)->addMonth(1);
        $jatuh_tempo->save();

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

    public function cekPegawai(CekPegawaiRequest $req)
    {
        $hasil = false;
        $nip = explode("-", $req->nip);
        $perusahaan = ListPerusahaan::where('id', substr($nip[0], 4))
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }])->first();

        if (isset($perusahaan)) {
            if (substr($perusahaan->id_code_perusahaan, -4)  == substr($nip[0], 0, 4)) {
                $hasil = true;
            }
        }

        if ($hasil == false) {
            return response()->json([
                'status' => 400,
                'message' => 'Mohon maaf, data Anda belum ditemukan di aplikasi Tomi. Silakan hubungi HRD Anda agar data Anda dapat segera didaftarkan.',
            ], 400);
        } else {

            $cekPinjaman = pinjaman::where('user_id', auth('api')->user()->id)
                ->orWhere('status', 'on_proses')
                ->orWhere('status', 'exported')
                ->orWhere('status', 'approve')
                ->where('status_otp', 'success')
                ->first();


            $cekPinjamanPendingOTP = pinjaman::where('user_id', auth('api')->user()->id)
                ->where('status', 'on_proses')
                ->where('status_otp', 'pending')
                ->first();

            if (isset($cekPinjaman)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'anda memiliki pinjaman yang belum selesai',
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
                'data' => new DataKaryawanResource($perusahaan)
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
