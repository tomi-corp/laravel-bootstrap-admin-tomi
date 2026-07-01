<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Pinjaman\ActionPinjamRequest;
use App\Http\Requests\User\Pinjaman\CekMinimalMaximalRequest;
use App\Http\Requests\User\Pinjaman\CekPegawaiRequest;
use App\Http\Requests\User\Pinjaman\GetTenorRequest;
use App\Http\Requests\User\Pinjaman\OTPpinjamanRequest;
use App\Http\Requests\User\Pinjaman\ShowBerkasHistoryRequest;
use App\Http\Requests\User\Pinjaman\SimulationRequest;
use App\Http\Requests\User\Pinjaman\TandaTanganRequest;
use App\Http\Resources\User\Pinjaman\DataKaryawanResource;
use App\Http\Resources\User\Pinjaman\HistroyPinjamanResource;
use App\Http\Resources\User\Pinjaman\TenorPinjamanResource;
use App\Models\cost;
use App\Models\jatuh_tempo;
use App\Models\ListPerusahaan;
use App\Models\pinjaman;
use App\Models\tenor_pinjaman;
use App\Models\User;
use App\Models\verif_otp_pengajuan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PinjamanController extends Controller
{
    private function simulation($data)
    {
        $tenor = tenor_pinjaman::where('id', $data['id_tenor_pinjaman'])
            ->with('amount_pinjaman')
            ->first();

        $biaya_admin = $tenor->biaya_admin / 100 * $data['nominal'];
        $biaya_admin_tomi = $tenor->fee_tomi / 100 * $data['nominal'];
        $biaya_asuransi = $tenor->asuransi / 100 * $data['nominal'];
        $biaya_provisi = $tenor->provisi / 100 * $data['nominal'];

        $biaya_layanan = $tenor->biaya_layanan;
        $iuran_anggota = $tenor->amount_pinjaman->iuran_anggota * $tenor->tenor;
        $nilai_yang_diterima = $data['nominal'] - $biaya_admin - $biaya_layanan - $iuran_anggota - $biaya_asuransi - $biaya_provisi;

        $rate = $tenor->rate / 100;
        $angsuran_perbulan = $data['nominal'] + $data['nominal'] * $rate * $tenor->tenor;
        $perbulan = $angsuran_perbulan / $tenor->tenor;
        $angsuran_perbulan_ceil = ceil($perbulan / 1000) * 1000;

        $returnData = [
            'angsuran_perbulan' => $angsuran_perbulan_ceil,
            'rate_persen' => $tenor->rate . "%",
            'asuransi_persen' => $tenor->asuransi . "%",
            'asuransi' => $biaya_asuransi,
            'provisi_persen' => $tenor->provisi . "%",
            'provisi' => $biaya_provisi,
            'biaya_admin_persen' => $tenor->biaya_admin . "%",
            'tenor' => $tenor->tenor,
            'biaya_admin' => $biaya_admin,
            'biaya_admin_tomi' => $biaya_admin_tomi,
            'biaya_layanan' => $biaya_layanan,
            'iuran_anggota' => $iuran_anggota,
            'jumlah_diterima' => $nilai_yang_diterima,
        ];
        return $returnData;
    }

    private function setProfessionalFeeToZero(pinjaman $pinjaman): void
    {
        $columns = array_flip(Schema::getColumnListing($pinjaman->getTable()));

        foreach (
            [
                'professional_fee_1',
                'professional_fee_2',
                'profesional_fee_1',
                'profesional_fee_2',
                'jumlah_profesional_1',
                'jumlah_profesional_2',
                'ksp_fee',
            ] as $column
        ) {
            if (isset($columns[$column])) {
                $pinjaman->{$column} = 0;
            }
        }
    }

    // public function maximalMinimal(CekMinimalMaximalRequest $req)
    // {
    //     $nip = explode("-", $req->nip);
    //     $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
    //         ->with(['karyawanSingle' => function ($q) use ($nip) {
    //             $q->where('nip', '=', $nip[1]);
    //             $q->first();
    //         }, 'amount_pinjaman'])->first();


    //     if (isset($perusahaan)) {
    //         // $pendapatan = 70 / 100 * $perusahaan->karyawanSingle->penghasilan;
    //         if ($perusahaan->karyawanSingle->max_plafon > $perusahaan->amount_pinjaman->maximal) {
    //             $maksimal = $perusahaan->amount_pinjaman->maximal;
    //         } elseif ($perusahaan->karyawanSingle->max_plafon < $perusahaan->amount_pinjaman->maximal) {
    //             $maksimal = $perusahaan->karyawanSingle->max_plafon;
    //         } elseif ($perusahaan->karyawanSingle->max_plafon == $perusahaan->amount_pinjaman->maximal) {
    //             $maksimal = $perusahaan->amount_pinjaman->maximal;
    //         }

    //         return response()->json([
    //             'status' => 200,
    //             'maximal' => "Rp  " . number_format($maksimal),
    //             'minimal' => "Rp  " . number_format($perusahaan->amount_pinjaman->minimal),
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Data NIP tidak ditemukan',
    //         ], 400);
    //     }
    // }

    public function maximalMinimal(CekMinimalMaximalRequest $req)
    {
        // Memecah NIP berdasarkan tanda "-" 
        // Contoh: "ABC-12345" → ["ABC", "12345"]
        $nip = explode("-", $req->nip);

        // Ambil data perusahaan berdasarkan unique_code (bagian pertama NIP)
        // Sekaligus eager load relasi:
        // - karyawanSingle (difilter berdasarkan NIP bagian kedua)
        // - amount_pinjaman
        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                // Filter karyawan berdasarkan NIP
                $q->where('nip', '=', $nip[1]);

                // first() di sini sebenarnya tidak diperlukan karena with() sudah handle relasi
                $q->first();
            }, 'amount_pinjaman'])->first();

        // Cek apakah data perusahaan ditemukan
        if (isset($perusahaan)) {

            // Membandingkan maksimal plafon karyawan dengan maksimal pinjaman perusahaan
            if ($perusahaan->karyawanSingle->max_plafon > $perusahaan->amount_pinjaman->maximal) {
                // Jika plafon karyawan lebih besar → pakai maksimal perusahaan
                $maksimal = $perusahaan->amount_pinjaman->maximal;
            } elseif ($perusahaan->karyawanSingle->max_plafon < $perusahaan->amount_pinjaman->maximal) {
                // Jika plafon karyawan lebih kecil → pakai plafon karyawan
                $maksimal = $perusahaan->karyawanSingle->max_plafon;
            } elseif ($perusahaan->karyawanSingle->max_plafon == $perusahaan->amount_pinjaman->maximal) {
                // Jika sama → pakai salah satu (di sini pakai maksimal perusahaan)
                $maksimal = $perusahaan->amount_pinjaman->maximal;
            }

            // Mengembalikan response JSON sukses
            return response()->json([
                'status' => 200,
                // Format angka ke format Rupiah
                'maximal' => "Rp  " . number_format($maksimal),
                'minimal' => "Rp  " . number_format($perusahaan->amount_pinjaman->minimal),
            ], 200);
        } else {
            // Jika data perusahaan tidak ditemukan
            return response()->json([
                'status' => 400,
                'message' => 'Data NIP tidak ditemukan',
            ], 400);
        }
    }

    public function getTenor(GetTenorRequest $req)
    {
        $nip = explode("-", $req->nip);
        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }, 'amount_pinjaman'])
            ->first();

        // $pendapatan = 70 / 100 * $perusahaan->karyawanSingle->penghasilan;


        $tenor = tenor_pinjaman::where('id_perusahaan', $perusahaan->id)
            ->where(function ($q) use ($req) {
                $q->where('minimal_nominal', '<=', $req->nominal)
                    ->where('maximal_nominal', '>=', $req->nominal);
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

        // $user = User::where('id', auth('api')->user()->id)->first();

        // if (auth('api')->user()->verified_privy == "n") {
        //     if (isset($user->nik) && isset($user->nama_lengkap) && isset($user->selfie) && isset($user->ktp)) {
        //         if ($user->register_token_privy == null) {
        //             // tampilkan consent privy
        //             return response()->json([
        //                 'status' => 400,
        //                 'message' => 'Tampilkan consent privy untuk register'
        //             ], 400);
        //         } else {
        //             if ($user->resend_privy == 1) {
        //                 $reason = json_decode($user->reason_privy);
        //                 return response()->json([
        //                     'status' => 400,
        //                     'reason' => $reason->reason,
        //                     'message' => 'Harap perbarui kembali data anda'
        //                 ], 400);
        //             } elseif ($user->resend_privy == 0 && $user->reason_privy != null) {
        //                 $reason = json_decode($user->reason_privy);
        //                 return response()->json([
        //                     'status' => 400,
        //                     'reason' => $reason->reason,
        //                     'message' => 'Harap hubungi customer service untuk merubah data anda'
        //                 ], 400);
        //             } else {
        //                 return response()->json([
        //                     'status' => 400,
        //                     'message' => 'Harap menunggu, data anda sedang proses verifikasi'
        //                 ], 400);
        //             }
        //         }
        //     } else {
        //         return response()->json([
        //             'status' => 400,
        //             'message' => 'Harap lengkapi data anda telebih dahulu',
        //         ], 400);
        //     }
        // } else {
        //     if ($user->register_token_privy != null && $user->id_privy == null) {
        //         $checkStatusPrivy = checkStatus();
        //         if ($checkStatusPrivy['data']['privy_id']) {
        //             $user->id_privy = $checkStatusPrivy['data']['privy_id'];
        //             $user->save();
        //         }
        //     }
        $simulation = $this->simulation($data);
        return response()->json([
            'status' => 200,
            'message' => 'success get simulation',
            'rate_persen' => $simulation['rate_persen'],
            'asuransi_persen' => $simulation['asuransi_persen'],
            'provisi_persen' => $simulation['provisi_persen'],
            'asuransi' => "Rp  " .  number_format($simulation['asuransi']),
            'provisi' => "Rp  " .  number_format($simulation['provisi']),
            'biaya_admin_persen' => $simulation['biaya_admin_persen'],
            'tenor' => $simulation['tenor'] . " bulan",
            'angsuran_perbulan' => "Rp  " .  number_format($simulation['angsuran_perbulan']),
            'biaya_admin' => "Rp  " .  number_format($simulation['biaya_admin']),
            'biaya_layanan' => "Rp  " .  number_format($simulation['biaya_layanan']),
            'iuran_anggota' => "Rp  " .  number_format($simulation['iuran_anggota']),
            'jumlah_diterima' => "Rp  " .  number_format($simulation['jumlah_diterima']),
        ], 200);
        // }
    }

    // public function actionPinjaman(ActionPinjamRequest $req)
    // {
    //     $user = User::where('id', auth('api')->user()->id)
    //         ->with('kyc')
    //         ->first();

    //     $nip = explode("-", $req->nip);

    //     $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
    //         ->with(['karyawanSingle' => function ($q) use ($nip) {
    //             $q->where('nip', '=', $nip[1]);
    //             $q->first();
    //         }, 'amount_pinjaman', 'lender'])
    //         ->first();


    //     if (!isset($perusahaan) && !isset($perusahaan->karyawanSingle)) {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Data NIP tidak ditemukan',
    //         ], 400);
    //     } else {
    //         // $pendapatan = 70 / 100 * $perusahaan->karyawanSingle->penghasilan;
    //         if ($perusahaan->karyawanSingle->max_plafon > $perusahaan->amount_pinjaman->maximal) {
    //             $maksimal = $perusahaan->amount_pinjaman->maximal;
    //         } elseif ($perusahaan->karyawanSingle->max_plafon < $perusahaan->amount_pinjaman->maximal) {
    //             $maksimal = $perusahaan->karyawanSingle->max_plafon;
    //         }

    //         $tenor = tenor_pinjaman::where('id_perusahaan', $perusahaan->id)
    //             ->where('id', $req->id_tenor_pinjaman)
    //             ->with('amount_pinjaman')
    //             ->orderBy('tenor')
    //             ->first();

    //         if (!($tenor->minimal_nominal <= $req->nominal) && !($maksimal <= $tenor->maximal_nominal)) {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Maksimal nominal pinjaman Rp ' . number_format($maksimal),
    //             ], 400);
    //         } elseif (auth('api')->user()->verified_privy == "n") {

    //             if (isset($user->nik) && isset($user->nama_lengkap) && isset($user->dob) && isset($user->kyc->selfie) && isset($user->kyc->ktp)) {
    //                 if ($user->register_token_privy == null) {
    //                     // tampilkan consent privy
    //                     return response()->json([
    //                         'status' => 400,
    //                         'message' => 'Tampilkan consent privy untuk register'
    //                     ], 400);
    //                 } else {
    //                     if ($user->resend_privy == 1) {
    //                         $reason = json_decode($user->reason_privy);
    //                         return response()->json([
    //                             'status' => 400,
    //                             'reason' => $reason->reason,
    //                             'message' => $reason->reason . ', Harap perbarui kembali data anda'
    //                         ], 400);
    //                     } elseif ($user->resend_privy == 0 && $user->reason_privy != null) {
    //                         $reason = json_decode($user->reason_privy);
    //                         return response()->json([
    //                             'status' => 400,
    //                             'reason' => $reason->reason,
    //                             'message' =>  $reason->reason . ', Harap hubungi customer service untuk merubah data anda'
    //                         ], 400);
    //                     } else {
    //                         return response()->json([
    //                             'status' => 400,
    //                             'message' => 'Harap menunggu, data anda sedang proses verifikasi'
    //                         ], 400);
    //                     }
    //                 }
    //             } else {
    //                 if (!isset($user->nik) || !isset($user->nama_lengkap) && !isset($user->dob)) {
    //                     return response()->json([
    //                         'status' => 400,
    //                         'message' => 'Harap lengkapi data anda telebih dahulu',
    //                     ], 400);
    //                 } else {
    //                     if ($user->kyc == null) {
    //                         return response()->json([
    //                             'status' => 400,
    //                             'message' => 'Maaf, proses tidak dapat dilanjutkan. Kami memerlukan pembaruan foto selfie dan KTP Anda.',
    //                         ], 400);
    //                     }
    //                 }
    //             }
    //         } else {

    //             if ($user->kyc == null) {
    //                 return response()->json([
    //                     'status' => 400,
    //                     'message' => 'Maaf, proses tidak dapat dilanjutkan. Kami memerlukan pembaruan foto selfie dan KTP Anda.',
    //                 ], 400);
    //             }

    //             if ($user->register_token_privy != null && $user->id_privy == null) {
    //                 $checkStatusPrivy = checkStatus();
    //                 if ($checkStatusPrivy['data']['privy_id']) {
    //                     $user->id_privy = $checkStatusPrivy['data']['privy_id'];
    //                     $user->save();
    //                 }
    //             }


    //             $cekPinjamanData = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
    //                 ->where(function ($q) {
    //                     $q->orWhere('status', 'pending')
    //                         ->orWhere('status', 'on_proses')
    //                         ->orWhere('status', 'approve');
    //                 })
    //                 ->where('id_karyawan', $perusahaan->karyawanSingle->id)
    //                 ->where('status_otp', 'success')
    //                 ->first();

    //             $cekPinjamanUser = pinjaman::where('user_id', auth('api')->user()->id)
    //                 ->where(function ($q) {
    //                     $q->orWhere('status', 'pending')
    //                         ->orWhere('status', 'on_proses')
    //                         ->orWhere('status', 'approve');
    //                 })
    //                 ->where('id_karyawan', $perusahaan->karyawanSingle->id)
    //                 ->where('status_otp', 'success')
    //                 ->first();

    //             // where('user_id', auth('api')->user()->id)
    //             $cekPinjamanPendingOTP = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
    //                 ->where('status', 'on_proses')
    //                 ->where('status_otp', 'pending')
    //                 ->first();


    //             // pinjaman aktif
    //             if (isset($cekPinjamanData) || isset($cekPinjamanUser)) {
    //                 return response()->json([
    //                     'status' => 400,
    //                     'message' => 'Mohon maaf, saat ini Anda masih memiliki pinjaman aktif. Terima kasih',
    //                 ], 400);
    //             }

    //             // pembaruan data karyawan tiap tanggal 1
    //             if ($perusahaan->karyawanSingle->updated_at && Carbon::parse($perusahaan->karyawanSingle->updated_at)->lessThan(Carbon::now()->startOfMonth())) {
    //                 return response()->json([
    //                     'status' => 400,
    //                     'message' => 'Kami melihat pembaruan data Anda sudah melewati batas waktu yang ditentukan, Mohon bantuannya untuk segera menghubungi Human Capital (HC) Perusahaan Anda agar mereka dapat memproses dan mengirimkan data terbaru Anda ke aplikasi TOMI. Kami siap membantu menyelesaikan prosesnya setelah data dari HC kami terima. Terima kasih!',
    //                 ], 400);
    //             }

    //             if (isset($cekPinjamanPendingOTP)) {
    //                 return response()->json([
    //                     'status' => 200,
    //                     'pinjaman' => false,
    //                     'message' => 'Anda memiliki validasi otp yang belum diselesaikan',
    //                 ], 200);
    //             }

    //             while (true) {
    //                 $code = "#LOAN-" . Carbon::now()->format('Y-m-d') . "-" . auth('api')->user()->id . Carbon::now()->format('s') . rand(100, 999);
    //                 $pinjaman = pinjaman::where('id_pinjaman', $code)->first();
    //                 if (!isset($pinjaman)) {
    //                     break;
    //                 }
    //             }

    //             $codeOTP = rand(1000, 9999);

    //             $otp = new verif_otp_pengajuan();
    //             $otp->user_id = auth('api')->user()->id;
    //             $otp->failed = 0;
    //             $otp->otp = $codeOTP;

    //             $otp->expired = Carbon::now()->addHour(24);

    //             $pinjaman = new pinjaman();
    //             $pinjaman->user_id = auth('api')->user()->id;
    //             $pinjaman->id_perusahaan = $perusahaan->id;
    //             $pinjaman->id_karyawan = $perusahaan->karyawanSingle->id;
    //             $pinjaman->id_lender = $perusahaan->id_lender;
    //             $pinjaman->id_kyc = $user->kyc->id;
    //             $pinjaman->id_pinjaman = $code;
    //             $pinjaman->nominal = $req->nominal;
    //             $pinjaman->tenor = $tenor->tenor;
    //             $pinjaman->rate = $tenor->rate;
    //             $pinjaman->asuransi = $tenor->asuransi;
    //             $pinjaman->provisi = $tenor->provisi;
    //             $pinjaman->biaya_admin =  $tenor->biaya_admin;
    //             $pinjaman->status = 'on_proses';
    //             $pinjaman->status_otp = 'pending';

    //             $data = [
    //                 'id_tenor_pinjaman' => $tenor->id,
    //                 'nominal' => $req->nominal,
    //             ];

    //             $simulation = $this->simulation($data);
    //             $pinjaman->total_biaya_admin = $simulation['biaya_admin'];
    //             $pinjaman->biaya_admin_ksp = $simulation['biaya_admin'];
    //             $pinjaman->dpp_adm_ksp = round($pinjaman->biaya_admin_ksp / 111 * 100);
    //             $pinjaman->ppn_adm_ksp = round($pinjaman->dpp_adm_ksp * 11 / 100);
    //             $pinjaman->biaya_admin_pandu = $simulation['biaya_admin_tomi'];
    //             $pinjaman->dpp_adm_pandu = round($pinjaman->biaya_admin_pandu / 111 * 100);
    //             $pinjaman->ppn_adm_pandu = round($pinjaman->dpp_adm_pandu * 11 / 100);
    //             $pinjaman->pendapatan_bunga = $req->nominal * $tenor->rate / 100 * $tenor->tenor;

    //             $pinjaman->dpp_bunga = round($pinjaman->pendapatan_bunga / 111 * 100);
    //             $pinjaman->ppn_bunga = round($pinjaman->dpp_bunga * 11 / 100);


    //             $pinjaman->total_angsuran_perbulan = $simulation['angsuran_perbulan'];
    //             $pinjaman->jumlah_yang_diterima = $simulation['jumlah_diterima'];
    //             $pinjaman->jumlah_asuransi = $simulation['asuransi'];
    //             $pinjaman->jumlah_provisi = $simulation['provisi'];
    //             $this->setProfessionalFeeToZero($pinjaman);

    //             $pinjaman->iuran_anggota = $simulation['iuran_anggota'];
    //             $pinjaman->biaya_layanan = $simulation['biaya_layanan'];

    //             $cost = cost::get()->toArray();
    //             $totalCost = $cost[3]['biaya'] + $cost[1]['biaya'];
    //             $pinjaman->cost_ksp = $totalCost;
    //             $pinjaman->description_cost_ksp = "Biaya disbursment + PK Otomation";

    //             sendOTPWhatsapp($perusahaan->karyawanSingle->whatsapp, $codeOTP);
    //             $pinjaman->save();


    //             // jatuh tempo langsung tercreate sesuai tenor
    //             $tanggal_sekarang = Carbon::now();
    //             $oppca = 1;
    //             while (true) {
    //                 $jatuh_tempo = new jatuh_tempo();
    //                 $jatuh_tempo->id_pinjaman = $pinjaman->id;
    //                 $jatuh_tempo->pembayaran = $oppca;
    //                 if ($oppca == 1) {
    //                     if ($tanggal_sekarang->format('d') <= $perusahaan->payment_date - $perusahaan->h_min_jatuh_tempo) {
    //                         $jatuh_tempo->jatuh_tempo =  $tanggal_sekarang->day($perusahaan->payment_date);
    //                     } else {
    //                         $jatuh_tempo->jatuh_tempo =  $tanggal_sekarang->day($perusahaan->payment_date)->addMonth(1);
    //                     }
    //                 } else {
    //                     $jatuh_tempo_last = jatuh_tempo::where('id_pinjaman', $pinjaman->id)
    //                         ->orderBy('id', 'desc')
    //                         ->first();
    //                     $jatuh_tempo->jatuh_tempo =  Carbon::parse($jatuh_tempo_last->jatuh_tempo)
    //                         ->addMonth(1);
    //                 }

    //                 $jatuh_tempo->status = 'belum';
    //                 $jatuh_tempo->save();
    //                 if ($oppca == $tenor->tenor) {
    //                     break;
    //                 }
    //                 $oppca++;
    //             }

    //             $otp->pinjaman_id = $pinjaman->id;
    //             $otp->save();


    //             $nomorWa = $perusahaan->karyawanSingle->whatsapp;

    //             $mask = substr($nomorWa, 0, 4)
    //                 . str_repeat('x', strlen($nomorWa) - 7)
    //                 . substr($nomorWa, -3);

    //             return response()->json([
    //                 'status' => 200,
    //                 'pinjaman' => true,
    //                 'message' => 'success, harap periksa OTP yang telah dikirimkan melalui whatsapp ' . $mask,
    //             ], 200);
    //         }
    //     }
    // }

    // Function untuk proses pengajuan pinjaman
    public function actionPinjaman(ActionPinjamRequest $req)
    {
        // Mengambil data user yang sedang login dari guard API
        $user = User::where('id', auth('api')->user()->id)
            // Mengambil relasi KYC user
            ->with('kyc')
            // Mengambil satu data user pertama
            ->first();

        // Memecah NIP berdasarkan tanda "-"
        // Contoh: ABCD-12345
        // $nip[0] = ABCD
        // $nip[1] = 12345
        $nip = explode("-", $req->nip);

        // Mencari data perusahaan berdasarkan unique_code dari NIP
        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            // Mengambil relasi karyawan, amount pinjaman, dan lender
            ->with([
                // Mengambil satu karyawan berdasarkan NIP
                'karyawanSingle' => function ($q) use ($nip) {
                    // Filter karyawan berdasarkan NIP
                    $q->where('nip', '=', $nip[1]);

                    // Ini sebenarnya tidak terlalu berguna di dalam with()
                    // Karena with() tetap mengambil relasi, bukan return first di sini
                    $q->first();
                },

                // Mengambil setting amount pinjaman perusahaan
                'amount_pinjaman',

                // Mengambil data lender perusahaan
                'lender'
            ])
            // Mengambil satu data perusahaan
            ->first();

        // Validasi jika perusahaan dan karyawan tidak ditemukan
        if (!isset($perusahaan) && !isset($perusahaan->karyawanSingle)) {

            // Return error jika data NIP tidak ditemukan
            return response()->json([
                'status' => 400,
                'message' => 'Data NIP tidak ditemukan',
            ], 400);
        } else {

            // Rumus lama maksimal pinjaman berdasarkan 70% penghasilan
            // $pendapatan = 70 / 100 * $perusahaan->karyawanSingle->penghasilan;

            // Jika max plafon karyawan lebih besar dari maksimal perusahaan
            if ($perusahaan->karyawanSingle->max_plafon > $perusahaan->amount_pinjaman->maximal) {

                // Maka maksimal pinjaman mengikuti batas maksimal perusahaan
                $maksimal = $perusahaan->amount_pinjaman->maximal;
            } elseif ($perusahaan->karyawanSingle->max_plafon < $perusahaan->amount_pinjaman->maximal) {

                // Jika max plafon karyawan lebih kecil dari maksimal perusahaan
                // Maka maksimal pinjaman mengikuti max plafon karyawan
                $maksimal = $perusahaan->karyawanSingle->max_plafon;
            }

            // Mengambil tenor pinjaman berdasarkan perusahaan dan tenor yang dipilih user
            $tenor = tenor_pinjaman::where('id_perusahaan', $perusahaan->id)
                // Filter berdasarkan id tenor dari request
                ->where('id', $req->id_tenor_pinjaman)
                // Mengambil relasi amount pinjaman
                ->with('amount_pinjaman')
                // Urutkan berdasarkan tenor
                ->orderBy('tenor')
                // Ambil satu tenor
                ->first();

            // Validasi nominal pinjaman
            if (!($tenor->minimal_nominal <= $req->nominal) && !($maksimal <= $tenor->maximal_nominal)) {

                // Return error jika nominal melebihi batas
                return response()->json([
                    'status' => 400,
                    'message' => 'Maksimal nominal pinjaman Rp ' . number_format($maksimal),
                ], 400);
            } elseif (auth('api')->user()->verified_privy == "n") {

                // Jika user belum verified Privy

                // Cek apakah data dasar user dan file KYC sudah lengkap
                if (
                    isset($user->nik) &&
                    isset($user->nama_lengkap) &&
                    isset($user->dob) &&
                    isset($user->kyc->selfie) &&
                    isset($user->kyc->ktp)
                ) {

                    // Jika user belum memiliki token register Privy
                    if ($user->register_token_privy == null) {

                        // Minta frontend menampilkan consent Privy
                        return response()->json([
                            'status' => 400,
                            'message' => 'Tampilkan consent privy untuk register'
                        ], 400);
                    } else {

                        // Jika data Privy perlu dikirim ulang
                        if ($user->resend_privy == 1) {

                            // Decode alasan dari Privy
                            $reason = json_decode($user->reason_privy);

                            // Return pesan agar user memperbarui data
                            return response()->json([
                                'status' => 400,
                                'reason' => $reason->reason,
                                'message' => $reason->reason . ', Harap perbarui kembali data anda'
                            ], 400);
                        } elseif ($user->resend_privy == 0 && $user->reason_privy != null) {

                            // Jika data tidak bisa dikirim ulang dan ada alasan dari Privy
                            $reason = json_decode($user->reason_privy);

                            // Return pesan agar user hubungi customer service
                            return response()->json([
                                'status' => 400,
                                'reason' => $reason->reason,
                                'message' => $reason->reason . ', Harap hubungi customer service untuk merubah data anda'
                            ], 400);
                        } else {

                            // Jika sedang proses verifikasi Privy
                            return response()->json([
                                'status' => 400,
                                'message' => 'Harap menunggu, data anda sedang proses verifikasi'
                            ], 400);
                        }
                    }
                } else {

                    // Jika data user belum lengkap

                    // Cek apakah NIK, nama lengkap, atau tanggal lahir belum ada
                    if (!isset($user->nik) || !isset($user->nama_lengkap) && !isset($user->dob)) {

                        // Return pesan agar user melengkapi data
                        return response()->json([
                            'status' => 400,
                            'message' => 'Harap lengkapi data anda telebih dahulu',
                        ], 400);
                    } else {

                        // Jika data KYC belum ada
                        if ($user->kyc == null) {

                            // Return pesan agar user upload selfie dan KTP
                            return response()->json([
                                'status' => 400,
                                'message' => 'Maaf, proses tidak dapat dilanjutkan. Kami memerlukan pembaruan foto selfie dan KTP Anda.',
                            ], 400);
                        }
                    }
                }
            } else {

                // Jika user sudah verified Privy

                // Validasi apakah data KYC user ada
                if ($user->kyc == null) {

                    // Return error jika KYC kosong
                    return response()->json([
                        'status' => 400,
                        'message' => 'Maaf, proses tidak dapat dilanjutkan. Kami memerlukan pembaruan foto selfie dan KTP Anda.',
                    ], 400);
                }

                // Jika user sudah punya token register Privy tetapi belum punya id_privy
                if ($user->register_token_privy != null && $user->id_privy == null) {

                    // Cek status Privy
                    $checkStatusPrivy = checkStatus();

                    // Jika Privy mengembalikan privy_id
                    if ($checkStatusPrivy['data']['privy_id']) {

                        // Simpan privy_id ke user
                        $user->id_privy = $checkStatusPrivy['data']['privy_id'];

                        // Simpan perubahan user
                        $user->save();
                    }
                }

                // Mengecek apakah karyawan masih punya pinjaman aktif
                $cekPinjamanData = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
                    // Filter status pinjaman aktif
                    ->where(function ($q) {
                        $q->orWhere('status', 'pending')
                            ->orWhere('status', 'on_proses')
                            ->orWhere('status', 'approve');
                    })
                    // Filter ulang berdasarkan karyawan
                    ->where('id_karyawan', $perusahaan->karyawanSingle->id)
                    // Hanya yang OTP-nya sudah sukses
                    ->where('status_otp', 'success')
                    // Ambil satu data
                    ->first();

                // Mengecek apakah user yang login masih punya pinjaman aktif
                $cekPinjamanUser = pinjaman::where('user_id', auth('api')->user()->id)
                    // Filter status pinjaman aktif
                    ->where(function ($q) {
                        $q->orWhere('status', 'pending')
                            ->orWhere('status', 'on_proses')
                            ->orWhere('status', 'approve');
                    })
                    // Filter berdasarkan karyawan
                    ->where('id_karyawan', $perusahaan->karyawanSingle->id)
                    // Hanya yang OTP-nya sukses
                    ->where('status_otp', 'success')
                    // Ambil satu data
                    ->first();

                // Mengecek apakah ada pengajuan pinjaman yang OTP-nya belum selesai
                $cekPinjamanPendingOTP = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
                    // Status masih on proses
                    ->where('status', 'on_proses')
                    // OTP masih pending
                    ->where('status_otp', 'pending')
                    // Ambil satu data
                    ->first();

                // Jika masih ada pinjaman aktif dari karyawan atau user
                if (isset($cekPinjamanData) || isset($cekPinjamanUser)) {

                    // Return error karena user masih punya pinjaman aktif
                    return response()->json([
                        'status' => 400,
                        'message' => 'Mohon maaf, saat ini Anda masih memiliki pinjaman aktif. Terima kasih',
                    ], 400);
                }

                // Cek apakah data karyawan sudah lewat dari awal bulan ini
                if (
                    $perusahaan->karyawanSingle->updated_at &&
                    Carbon::parse($perusahaan->karyawanSingle->updated_at)->lessThan(Carbon::now()->startOfMonth())
                ) {

                    // Return pesan agar user menghubungi HC perusahaan
                    return response()->json([
                        'status' => 400,
                        'message' => 'Kami melihat pembaruan data Anda sudah melewati batas waktu yang ditentukan, Mohon bantuannya untuk segera menghubungi Human Capital (HC) Perusahaan Anda agar mereka dapat memproses dan mengirimkan data terbaru Anda ke aplikasi TOMI. Kami siap membantu menyelesaikan prosesnya setelah data dari HC kami terima. Terima kasih!',
                    ], 400);
                }

                // Jika ada OTP pinjaman yang belum diselesaikan
                if (isset($cekPinjamanPendingOTP)) {

                    // Return response bahwa user harus menyelesaikan OTP dulu
                    return response()->json([
                        'status' => 200,
                        'pinjaman' => false,
                        'message' => 'Anda memiliki validasi otp yang belum diselesaikan',
                    ], 200);
                }

                // Loop untuk membuat kode pinjaman unik
                while (true) {

                    // Generate kode pinjaman
                    $code = "#LOAN-" .
                        Carbon::now()->format('Y-m-d') .
                        "-" .
                        auth('api')->user()->id .
                        Carbon::now()->format('s') .
                        rand(100, 999);

                    // Cek apakah kode pinjaman sudah ada di database
                    $pinjaman = pinjaman::where('id_pinjaman', $code)->first();

                    // Jika kode belum ada, hentikan loop
                    if (!isset($pinjaman)) {
                        break;
                    }
                }

                // Generate kode OTP 4 digit
                $codeOTP = rand(1000, 9999);

                // Membuat object OTP baru
                $otp = new verif_otp_pengajuan();

                // Set user pemilik OTP
                $otp->user_id = auth('api')->user()->id;

                // Set jumlah gagal input OTP menjadi 0
                $otp->failed = 0;

                // Set kode OTP
                $otp->otp = $codeOTP;

                // Set masa berlaku OTP 24 jam
                $otp->expired = Carbon::now()->addHour(24);

                // Membuat object pinjaman baru
                $pinjaman = new pinjaman();

                // Set user yang mengajukan pinjaman
                $pinjaman->user_id = auth('api')->user()->id;

                // Set perusahaan karyawan
                $pinjaman->id_perusahaan = $perusahaan->id;

                // Set id karyawan
                $pinjaman->id_karyawan = $perusahaan->karyawanSingle->id;

                // Set lender dari perusahaan
                $pinjaman->id_lender = $perusahaan->id_lender;

                // Set id KYC user
                $pinjaman->id_kyc = $user->kyc->id;

                // Set kode pinjaman
                $pinjaman->id_pinjaman = $code;

                // Set nominal pinjaman dari request
                $pinjaman->nominal = $req->nominal;

                // Set tenor pinjaman
                $pinjaman->tenor = $tenor->tenor;

                // Set rate bunga
                $pinjaman->rate = $tenor->rate;

                // Set biaya asuransi
                $pinjaman->asuransi = $tenor->asuransi;

                // Set biaya provisi
                $pinjaman->provisi = $tenor->provisi;

                // Set biaya admin
                $pinjaman->biaya_admin = $tenor->biaya_admin;

                // Set status pinjaman menjadi on_proses
                $pinjaman->status = 'on_proses';

                // Set status OTP menjadi pending
                $pinjaman->status_otp = 'pending';

                // Data untuk simulasi perhitungan pinjaman
                $data = [
                    // Id tenor pinjaman
                    'id_tenor_pinjaman' => $tenor->id,

                    // Nominal pinjaman
                    'nominal' => $req->nominal,
                ];

                // Memanggil function simulation untuk menghitung biaya pinjaman
                $simulation = $this->simulation($data);

                // Set total biaya admin dari hasil simulasi
                $pinjaman->total_biaya_admin = $simulation['biaya_admin'];

                // Set biaya admin KSP
                $pinjaman->biaya_admin_ksp = $simulation['biaya_admin'];

                // Hitung DPP admin KSP
                $pinjaman->dpp_adm_ksp = round($pinjaman->biaya_admin_ksp / 111 * 100);

                // Hitung PPN admin KSP
                $pinjaman->ppn_adm_ksp = round($pinjaman->dpp_adm_ksp * 11 / 100);

                // Set biaya admin Pandu/TOMI
                $pinjaman->biaya_admin_pandu = $simulation['biaya_admin_tomi'];

                // Hitung DPP admin Pandu/TOMI
                $pinjaman->dpp_adm_pandu = round($pinjaman->biaya_admin_pandu / 111 * 100);

                // Hitung PPN admin Pandu/TOMI
                $pinjaman->ppn_adm_pandu = round($pinjaman->dpp_adm_pandu * 11 / 100);

                // Hitung pendapatan bunga
                $pinjaman->pendapatan_bunga = $req->nominal * $tenor->rate / 100 * $tenor->tenor;

                // Hitung DPP bunga
                $pinjaman->dpp_bunga = round($pinjaman->pendapatan_bunga / 111 * 100);

                // Hitung PPN bunga
                $pinjaman->ppn_bunga = round($pinjaman->dpp_bunga * 11 / 100);

                // Set total angsuran per bulan dari simulasi
                $pinjaman->total_angsuran_perbulan = $simulation['angsuran_perbulan'];

                // Set jumlah uang yang diterima user
                $pinjaman->jumlah_yang_diterima = $simulation['jumlah_diterima'];

                // Set jumlah asuransi
                $pinjaman->jumlah_asuransi = $simulation['asuransi'];

                // Set jumlah provisi
                $pinjaman->jumlah_provisi = $simulation['provisi'];

                // Set professional fee menjadi 0
                $this->setProfessionalFeeToZero($pinjaman);

                // Set iuran anggota
                $pinjaman->iuran_anggota = $simulation['iuran_anggota'];

                // Set biaya layanan
                $pinjaman->biaya_layanan = $simulation['biaya_layanan'];

                // Mengambil semua data biaya/cost
                $cost = cost::get()->toArray();

                // Menjumlahkan cost index 3 dan index 1
                $totalCost = $cost[3]['biaya'] + $cost[1]['biaya'];

                // Set cost KSP
                $pinjaman->cost_ksp = $totalCost;

                // Set keterangan cost KSP
                $pinjaman->description_cost_ksp = "Biaya disbursment + PK Otomation";

                // Mengirim OTP ke WhatsApp karyawan
                sendOTPWhatsapp($perusahaan->karyawanSingle->whatsapp, $codeOTP);

                // Simpan data pinjaman ke database
                $pinjaman->save();

                // Mengambil tanggal sekarang
                $tanggal_sekarang = Carbon::now();

                // Counter pembayaran pertama
                $oppca = 1;

                // Loop untuk membuat jadwal jatuh tempo sesuai tenor
                while (true) {

                    // Membuat object jatuh tempo baru
                    $jatuh_tempo = new jatuh_tempo();

                    // Set id pinjaman
                    $jatuh_tempo->id_pinjaman = $pinjaman->id;

                    // Set pembayaran ke berapa
                    $jatuh_tempo->pembayaran = $oppca;

                    // Jika pembayaran pertama
                    if ($oppca == 1) {

                        // Cek apakah tanggal sekarang masih sebelum batas H-min jatuh tempo
                        if ($tanggal_sekarang->format('d') <= $perusahaan->payment_date - $perusahaan->h_min_jatuh_tempo) {

                            // Set jatuh tempo bulan ini sesuai payment_date perusahaan
                            $jatuh_tempo->jatuh_tempo = $tanggal_sekarang->day($perusahaan->payment_date);
                        } else {

                            // Jika sudah lewat batas, jatuh tempo masuk bulan depan
                            $jatuh_tempo->jatuh_tempo = $tanggal_sekarang
                                ->day($perusahaan->payment_date)
                                ->addMonth(1);
                        }
                    } else {

                        // Jika bukan pembayaran pertama

                        // Ambil data jatuh tempo terakhir dari pinjaman ini
                        $jatuh_tempo_last = jatuh_tempo::where('id_pinjaman', $pinjaman->id)
                            ->orderBy('id', 'desc')
                            ->first();

                        // Set jatuh tempo berikutnya satu bulan setelah jatuh tempo terakhir
                        $jatuh_tempo->jatuh_tempo = Carbon::parse($jatuh_tempo_last->jatuh_tempo)
                            ->addMonth(1);
                    }

                    // Set status jatuh tempo belum dibayar
                    $jatuh_tempo->status = 'belum';

                    // Simpan data jatuh tempo
                    $jatuh_tempo->save();

                    // Jika jumlah pembayaran sudah sama dengan tenor, hentikan loop
                    if ($oppca == $tenor->tenor) {
                        break;
                    }

                    // Naikkan counter pembayaran
                    $oppca++;
                }

                // Set id pinjaman ke data OTP
                $otp->pinjaman_id = $pinjaman->id;

                // Simpan data OTP ke database
                $otp->save();

                // Ambil nomor WhatsApp karyawan
                $nomorWa = $perusahaan->karyawanSingle->whatsapp;

                // Masking nomor WhatsApp
                // Contoh: 081234567890 menjadi 0812xxxxx890
                $mask = substr($nomorWa, 0, 4)
                    . str_repeat('x', strlen($nomorWa) - 7)
                    . substr($nomorWa, -3);

                // Return response sukses pengajuan pinjaman
                return response()->json([
                    'status' => 200,
                    'pinjaman' => true,
                    'message' => 'success, harap periksa OTP yang telah dikirimkan melalui whatsapp ' . $mask,
                ], 200);
            }
        }
    }

    public function actionPinjamanOTP(OTPpinjamanRequest $req)
    {
        $verif = verif_otp_pengajuan::where('user_id', auth('api')->user()->id)
            ->where('otp', $req->otp)
            ->with(['pinjaman' => function ($q) {
                $q->with([
                    'perusahaan' => function ($p) {
                        $p->with(['lender' => function ($o) {
                            $o->with('bank');
                        }]);
                    },
                    'data_karyawan' => function ($w) {
                        $w->with('bank');
                    },
                    'jatuh_tempo' => function ($jt) {
                        $jt->orderBy('jatuh_tempo', 'asc');
                    }
                ]);
            }, 'user'])
            ->first();

        if (!isset($verif)) {
            $verifFailed = verif_otp_pengajuan::where('user_id', auth('api')->user()->id)->first();
            if (isset($verifFailed)) {
                if ($verifFailed->failed >= 3) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Anda telah 3x melakukan kesalahan pada memasukan kode OTP, Silahkan hubungi customer service kami',
                    ], 400);
                }
                $verifFailed->failed = $verifFailed->failed + 1;
                $verifFailed->save();
                return response()->json([
                    'status' => 400,
                    'message' => 'OTP tidak benar',
                ], 400);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Silahkan request OTP kembali',
                ], 400);
            }
        } else {
            if ($verif->failed >= 3) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Anda telah 3x melakukan kesalahan pada memasukan kode OTP, Silahkan hubungi customer service kami',
                ], 400);
            }
            if ($verif->expired < Carbon::now()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'OTP kadaluarsa',
                ], 400);
            } else {
                Carbon::setLocale('id');
                $waktu = Carbon::now();
                $user = User::where('id', auth('api')->user()->id)->first();
                $time = $waktu->format('Y-m-d');
                $hari = $waktu->isoFormat('dddd');
                $carbonSekarang = $waktu->format('d/m/Y');
                $carbonTahun = $waktu->format('Y');
                $bulanAngka = (int) $waktu->format('m');
                $bulanRomawi = [
                    1  => 'I',
                    2  => 'II',
                    3  => 'III',
                    4  => 'IV',
                    5  => 'V',
                    6  => 'VI',
                    7  => 'VII',
                    8  => 'VIII',
                    9  => 'IX',
                    10 => 'X',
                    11 => 'XI',
                    12 => 'XII',
                ];
                $bulan_romawi = $bulanRomawi[$bulanAngka];

                $pinjamanCount = pinjaman::count();
                // $rekeningTomi = rekening_tomi::with('bank')->first();

                $random = rand(1000, 9999);
                $usia =  Carbon::parse($verif->user->dob)->age;

                $fileName = $user->nik . '_' . $time . '_' . $random . '.pdf';

                $disk = Storage::disk('local');
                $html = $disk->get('template/pkk.html');


                $htmlTabelAngsuran = '<table class="table-pinjaman">
                        <thead>
                            <tr class="table-pinjaman__row">
                                <th class="table-pinjaman__header">Angsuran ke -</th>
                                <th class="table-pinjaman__header">Nilai Angsuran</th>
                                <th class="table-pinjaman__header">Tanggal Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody>';

                foreach ($verif->pinjaman->jatuh_tempo as $index => $jt) {
                    $isEven = ($index + 1) % 2 == 0;
                    $classRow = $isEven ? 'table-pinjaman__row--even' : '';
                    $nilaiAngsuran = 'Rp ' . number_format($verif->pinjaman->total_angsuran_perbulan);

                    $htmlTabelAngsuran .= '
                            <tr class="table-pinjaman__row ' . $classRow . '">
                                <td class="table-pinjaman__cell">' . ($index + 1) . '</td>
                                <td class="table-pinjaman__cell table-pinjaman__cell--nilai">' . $nilaiAngsuran . '</td>
                                <td class="table-pinjaman__cell">' . Carbon::parse($jt['jatuh_tempo'])->format('d/m/Y') . '</td>
                            </tr>';
                }

                $htmlTabelAngsuran .= '</tbody></table>';

                $snapshotPerusahaan = $verif->pinjaman->perusahaan;

                $dataPengganti = [
                    'nomor' => $pinjamanCount,
                    'hari' => $hari,
                    'tanggal_sekarang' => $carbonSekarang,
                    'ct' => $carbonTahun,
                    'bulan_romawi' => $bulan_romawi,
                    'id_pinjaman' => $verif->pinjaman->id_pinjaman,
                    't' => $snapshotPerusahaan->payment_date,
                    'pinjaman_jatuh_tempo' => $htmlTabelAngsuran,

                    'pihak_kedua' => '<b>' . $verif->pinjaman->data_karyawan->nama_karyawan . '</b>' . ',' . $usia . ' tahun, ' . 'beralamat di ' . $verif->pinjaman->data_karyawan->alamat . ' pemegang kartu tanda penduduk nomor ' . $verif->pinjaman->data_karyawan->nik . ' karyawan pada ' . $snapshotPerusahaan->nama . ' dengan NIP ' . $verif->pinjaman->data_karyawan->nip,
                    'plafond'    => 'Rp  ' . number_format($verif->pinjaman->nominal),
                    'plafond_terbilang'    => ' (' . terbilang($verif->pinjaman->nominal) . ' Rupiah)',
                    'jangka' => $verif->pinjaman->tenor . ' bulan',
                    'rate' => $verif->pinjaman->rate . '%',
                    'angsuran_perbulan' => 'Rp  ' . number_format($verif->pinjaman->total_angsuran_perbulan),
                    'angsuran_perbulan_terbilang' =>  ' (' . terbilang($verif->pinjaman->total_angsuran_perbulan) . ' Rupiah)',
                    'administrasi' => 'Rp  ' . number_format($verif->pinjaman->total_biaya_admin),
                    'asuransi' => 'Rp  ' . number_format($verif->pinjaman->jumlah_asuransi),
                    'provisi' => 'Rp  ' . number_format($verif->pinjaman->jumlah_provisi),
                    'biaya_layanan' => 'Rp  ' . number_format($verif->pinjaman->biaya_layanan),
                    'iuran_anggota' => 'Rp  ' . number_format($verif->pinjaman->iuran_anggota),
                    'rekening_pencairan' => $verif->pinjaman->data_karyawan->nomor_bank,
                    'bank_pencairan' => $verif->pinjaman->data_karyawan->bank->code_bank,
                    'atas_nama_rekening_pencairan' => $verif->pinjaman->data_karyawan->nama_rekening,
                    'jumlah_pencairan' => 'Rp  ' . number_format($verif->pinjaman->jumlah_yang_diterima),
                    'terbilang_jumlah_pencairan' => ' (' . terbilang($verif->pinjaman->jumlah_yang_diterima) . ' Rupiah)',
                    'bankLender' => $snapshotPerusahaan->lender->bank->code_bank,
                    'rekeningLender' =>  $snapshotPerusahaan->lender->no_rekening,
                    'atasNamaBankLender' =>  $snapshotPerusahaan->lender->nama_rekening,
                    'penerima_pinjaman' => strtoupper($user->nama_lengkap)
                ];


                foreach ($dataPengganti as $key => $value) {
                    $html = str_replace("{{ \$$key }}", $value, $html);
                }

                // Buat PDF dari HTML
                $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

                // Simpan ke file
                $outputPdfPath = 'assets/pk/' . $fileName;
                $disk->put($outputPdfPath, $pdf->output());

                // ttdPK
                // $hasilTTD = ttdPK($user, $fileName, $verif);
                // dd($hasilTTD);

                // dd($documentTTD);
                // masukin data dari tanda tangan ke db pinjaman 
                // habis itu get file yg sudah ditanda tangan
                $pinjaman = pinjaman::where('id', $verif->pinjaman->id)->first();
                // $pinjaman->referrence_number = $hasilTTD['data']['reference_number'];
                // $pinjaman->document_token = $hasilTTD['data']['document_token'];
                $pinjaman->document_name = $fileName;
                $pinjaman->status_otp = "success";
                // untuk ambil document yg sudah ditanda tanganin
                $pinjaman->save();
                $verif->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'OTP benar, Silahkan tanda tanganin berkas untuk melanjutkan',
                ], 200);


                // {{base_url}}/web/api/v2/doc-signing/status

                // Storage::disk('local')->put('assets/pk/' . $fileName, base64_decode($responseBody->data->));


                // {{base_url}}/web/api/v2/doc-signing/status


                //     $pinjaman = pinjaman::where('id', $verif->pinjaman_id)
                //         ->with(['data_karyawan' => function ($q) {
                //             $q->with('bank');
                //         }])->first();
            }
        }
    }

    public function showberkas()
    {
        $pinjaman = pinjaman::where('user_id', auth('api')->user()->id)
            ->where('status_otp', 'success')
            ->where('ttd', 'n')
            ->where('status', 'on_proses')
            ->first();

        if (!isset($pinjaman)) {
            return response()->json([
                'status' => 200,
                'message' => 'Anda tidak memiliki pinjaman yang harus ditanda tangani',
            ], 200);
        } else {
            $path = 'assets/pk/' . $pinjaman->document_name;
            $pdfContent = Storage::get($path);
            $base64Pdf = base64_encode($pdfContent);
            $document = 'data:application/pdf;base64,' . $base64Pdf;
            return response()->json([
                'status' => 200,
                'message' => '',
                'document' => $document
            ], 200);
        }
    }


    public function showBerkasHistory(ShowBerkasHistoryRequest $req)
    {
        // 
        $user = User::where('id', auth('api')->user()->id)
            ->first();
        $pinjaman  = pinjaman::where('id', $req->id_pinjaman)
            ->where('user_id', $user->id)
            ->first();
        if (isset($pinjaman)) {
            if ($pinjaman->document_privy == 'y') {
                $path = 'assets/pk/' . $pinjaman->document_name;
                $pdfContent = Storage::get($path);
                $base64Pdf = base64_encode($pdfContent);
                $document = 'data:application/pdf;base64,' . $base64Pdf;
                return response()->json([
                    'status' => 200,
                    'message' => '',
                    'document' => $document
                ], 200);
            } else {
                $documentTTD = getDocumentPK($user, $pinjaman->referrence_number, $pinjaman->document_token);
                if (!isset($documentTTD['data']['signed_document'])) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Mohon tunggu, Document sedang proses penanda tanganan, pinjaman dapat diproses ketahap selanjutnya jika anda sudah melihat document yang sudah anda tanda tangani.',
                    ], 400);
                } else {
                    Storage::disk('local')->put('assets/pk/' . $pinjaman->document_name, base64_decode(Str::after($documentTTD['data']['signed_document'], 'data:application/pdf;base64,')));
                    $pinjaman->document_privy = 'y';
                    $pinjaman->save();

                    $path = 'assets/pk/' . $pinjaman->document_name;
                    $pdfContent = Storage::get($path);
                    $base64Pdf = base64_encode($pdfContent);
                    $document = 'data:application/pdf;base64,' . $base64Pdf;
                    return response()->json([
                        'status' => 200,
                        'message' => '',
                        'document' => $document
                    ], 200);
                }
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Pinjaman tidak tersedia',
            ], 400);
        }
    }

    public function tandaTangan(TandaTanganRequest $req)
    {
        $user  = User::where('id', auth('api')->user()->id)->first();
        $pinjaman = pinjaman::where('user_id', auth('api')->user()->id)
            ->where('status_otp', 'success')
            ->where('ttd', 'n')
            ->where('status', 'on_proses')
            ->first();

        if (!isset($pinjaman)) {
            return response()->json([
                'status' => 200,
                'message' => 'Anda tidak memiliki pinjaman yang harus ditanda tangani',
            ], 200);
        } else {

            if (Hash::check($req->password, $user->password)) {
                // Log authentikasi, timestamps otentikasi saat input tanda tangan (OTP), privyid, preference number.
                // mulai tanda tangan
                $hasilTTD = ttdPK($user, $pinjaman->document_name, $pinjaman->id);
                // dd($hasilTTD);
                // masukin data dari tanda tangan ke db pinjaman 
                // habis itu get file yg sudah ditanda tangan
                $pinjaman->referrence_number = $hasilTTD['data']['reference_number'];
                $pinjaman->document_token = $hasilTTD['data']['document_token'];
                $pinjaman->ttd = 'y';
                $pinjaman->status = 'pending';
                $pinjaman->save();
                Log::channel('privylog')->info('correct password | Timestamp :' . Carbon::now() . '| privy_id : ' . $user->id_privy . '| referrence_number_user : ' . $user->reference_number_privy . '| referrence_number_document : ' . $hasilTTD['data']['reference_number'] . '| document_token : ' . $hasilTTD['data']['document_token']);
                // untuk ambil document yg sudah ditanda tanganin
                // $documentTTD = getDocumentPK($user, $pinjaman->referrence_number, $pinjaman->document_token);
                // if (!isset($documentTTD['data']['signed_document'])) {
                //     return response()->json([
                //         'status' => 400,
                //         'message' => 'Mohon tunggu, Document sedang proses penanda tanganan',
                //     ], 400);
                // } else {
                // Storage::disk('local')->put('assets/pk/' . $pinjaman->document_name, base64_decode(Str::after($documentTTD['data']['signed_document'], 'data:application/pdf;base64,')));
                return response()->json([
                    'status' => 200,
                    'message' => 'Verifikasi tanda tangan berhasil! Dana Anda segera dicairkan dalam 1x24 Jam Kerja. Jika ada pertanyaan lebih lanjut, jangan ragu untuk menghubungi kami.',
                ], 200);
                // }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Password tidak sesuai',
                ], 400);
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

        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }])->first();


        if (isset($perusahaan) && isset($perusahaan->karyawanSingle)) {
            $hasil = true;
            $tanggal_bergabung = Carbon::parse($perusahaan->karyawanSingle->tanggal_bergabung);
            $tanggal_sekarang = Carbon::now();

            $diff = intval($tanggal_bergabung->floatDiffInMonths($tanggal_sekarang));

            if ($perusahaan->karyawanSingle->status_sp != 0) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Mohon maaf, peminjaman hanya dapat dilakukan dengan karyawan berstatus bebas surat peringatan',
                ], 400);
            } elseif ($perusahaan->karyawanSingle->status == "tetap") {
                $hasil = true;
            } elseif ($diff < 6) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Mohon maaf, hanya karyawan dengan status tetap / masa kerja > 6 Bulan untuk melakukan peminjaman',
                ], 400);
            } else {
                $hasil = true;
            }
        }

        if ($hasil == false) {
            return response()->json([
                'status' => 400,
                'message' => 'Mohon maaf, data Anda belum ditemukan di aplikasi Tomi. Silakan hubungi HRD Anda agar data Anda dapat segera didaftarkan.',
            ], 400);
        } else {

            // $cekPinjamanData = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
            //     ->where(function ($q) {
            //         $q->orWhere('status', 'exported')
            //             ->orWhere('status', 'on_proses')
            //             ->orWhere('status', 'approve');
            //     })
            //     ->where('id_karyawan', $perusahaan->karyawanSingle->id)
            //     ->where('status_otp', 'success')
            //     ->first();

            // $cekPinjamanUser = pinjaman::where('user_id', auth('api')->user()->id)
            //     ->where(function ($q) {
            //         $q->orWhere('status', 'exported')
            //             ->orWhere('status', 'on_proses')
            //             ->orWhere('status', 'approve');
            //     })
            //     ->where('id_karyawan', $perusahaan->karyawanSingle->id)
            //     ->where('status_otp', 'success')
            //     ->first();


            // where('user_id', auth('api')->user()->id)
            $cekPinjamanPendingOTP = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
                ->where('status', 'on_proses')
                ->where('status_otp', 'pending')
                ->first();

            // if (isset($cekPinjamanData) || isset($cekPinjamanUser)) {
            //     return response()->json([
            //         'status' => 400,
            //         'message' => 'anda memiliki pinjaman yang belum selesai',
            //     ], 400);
            // }

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
            $verifOTP = verif_otp_pengajuan::where('pinjaman_id', $pinjaman->id)->first();
            if (isset($verifOTP)) {
                $verifOTP->failed = 0;
                $verifOTP->otp = $codeOTP;
                $verifOTP->expired = Carbon::now()->addHour(23);
                $verifOTP->save();
            } else {
                $verifOTP = new verif_otp_pengajuan();
                $verifOTP->pinjaman_id = $pinjaman->id;
                $verifOTP->user_id = auth('api')->user()->id;
                $verifOTP->failed = 0;
                $verifOTP->otp = $codeOTP;
                $verifOTP->expired = Carbon::now()->addHour(23);
                $verifOTP->save();
            }

            sendOTPWhatsapp($pinjaman->data_karyawan->whatsapp, $codeOTP);
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
