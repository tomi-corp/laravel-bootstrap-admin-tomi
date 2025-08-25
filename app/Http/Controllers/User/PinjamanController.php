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
use Illuminate\Support\Str;
use PhpParser\Node\Expr\FuncCall;

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
            'rate_persen' => $tenor->rate . "%",
            'biaya_admin_persen' => $tenor->biaya_admin . "%",
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
        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
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
        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
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
            ->where(function ($q) use ($range, $req) {
                $q->where('minimal_nominal', '>=', $range['minimal'])
                    ->where('minimal_nominal', '<=',  $req->nominal);
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

    public function actionPinjaman(ActionPinjamRequest $req)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $nip = explode("-", $req->nip);

        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }, 'amount_pinjaman'])
            ->first();


        if (!isset($perusahaan) && !isset($perusahaan->karyawanSingle)) {
            return response()->json([
                'status' => 400,
                'message' => 'Data NIP tidak ditemukan',
            ], 400);
        } else {
            $pendapatan = 70 / 100 * $perusahaan->karyawanSingle->penghasilan;
            if ($pendapatan > $perusahaan->amount_pinjaman->maximal) {
                $maksimal = $perusahaan->amount_pinjaman->maximal;
            } elseif ($pendapatan < $perusahaan->amount_pinjaman->maximal) {
                $maksimal = $pendapatan;
            }

            $tenor = tenor_pinjaman::where('id_perusahaan', $perusahaan->id)
                ->where('id', $req->id_tenor_pinjaman)
                ->with('amount_pinjaman')
                ->orderBy('tenor')
                ->first();

            if (!($tenor->minimal_nominal <= $req->nominal) && !($maksimal <= $tenor->maximal_nominal)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Maksimal nominal pinjaman Rp ' . number_format($maksimal),
                ], 400);
            } elseif (auth('api')->user()->verified_privy == "n") {

                if (isset($user->nik) && isset($user->nama_lengkap) && isset($user->selfie) && isset($user->ktp)) {
                    if ($user->register_token_privy == null) {
                        // tampilkan consent privy
                        return response()->json([
                            'status' => 400,
                            'message' => 'Tampilkan consent privy untuk register'
                        ], 400);
                    } else {
                        if ($user->resend_privy == 1) {
                            $reason = json_decode($user->reason_privy);
                            return response()->json([
                                'status' => 400,
                                'reason' => $reason->reason,
                                'message' => 'Harap perbarui kembali data anda'
                            ], 400);
                        } elseif ($user->resend_privy == 0 && $user->reason_privy != null) {
                            $reason = json_decode($user->reason_privy);
                            return response()->json([
                                'status' => 400,
                                'reason' => $reason->reason,
                                'message' => 'Harap hubungi customer service untuk merubah data anda'
                            ], 400);
                        } else {
                            return response()->json([
                                'status' => 400,
                                'message' => 'Harap menunggu, data anda sedang proses verifikasi'
                            ], 400);
                        }
                    }
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Harap lengkapi data anda telebih dahulu',
                    ], 400);
                }
            } else {
                if ($user->register_token_privy != null && $user->id_privy == null) {
                    $checkStatusPrivy = checkStatus();
                    if ($checkStatusPrivy['data']['privy_id']) {
                        $user->id_privy = $checkStatusPrivy['data']['privy_id'];
                        $user->save();
                    }
                }


                $cekPinjamanData = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
                    ->where(function ($q) {
                        $q->orWhere('status', 'exported')
                            ->orWhere('status', 'on_proses')
                            ->orWhere('status', 'approve');
                    })
                    ->where('id_karyawan', $perusahaan->karyawanSingle->id)
                    ->where('status_otp', 'success')
                    ->first();

                $cekPinjamanUser = pinjaman::where('user_id', auth('api')->user()->id)
                    ->where(function ($q) {
                        $q->orWhere('status', 'exported')
                            ->orWhere('status', 'on_proses')
                            ->orWhere('status', 'approve');
                    })
                    ->where('id_karyawan', $perusahaan->karyawanSingle->id)
                    ->where('status_otp', 'success')
                    ->first();

                // where('user_id', auth('api')->user()->id)
                $cekPinjamanPendingOTP = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
                    ->where('status', 'on_proses')
                    ->where('status_otp', 'pending')
                    ->first();

                if (isset($cekPinjamanData) || isset($cekPinjamanUser)) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'anda memiliki pinjaman yang belum lunas atau terselesaikan',
                    ], 400);
                }

                if (isset($cekPinjamanPendingOTP)) {
                    return response()->json([
                        'status' => 200,
                        'pinjaman' => false,
                        'message' => 'anda memiliki validasi otp yang belum diselesaikan',
                    ], 200);
                }

                while (true) {
                    $code = "#LOAN-" . Carbon::now()->format('Y-m-d') . "-" . auth('api')->user()->id . Carbon::now()->format('s') . rand(100, 999);
                    $pinjaman = pinjaman::where('id_pinjaman', $code)->first();
                    if (!isset($pinjaman)) {
                        break;
                    }
                }

                $codeOTP = rand(1000, 9999);

                $otp = new verif_otp_pengajuan();
                $otp->user_id = auth('api')->user()->id;
                $otp->failed = 0;
                $otp->otp = $codeOTP;

                $otp->expired = Carbon::now()->addHour(24);

                $pinjaman = new pinjaman();
                $pinjaman->user_id = auth('api')->user()->id;
                $pinjaman->id_perusahaan = $perusahaan->id;
                $pinjaman->id_karyawan = $perusahaan->karyawanSingle->id;
                $pinjaman->id_pinjaman = $code;
                $pinjaman->nominal = $req->nominal;
                $pinjaman->tenor = $tenor->tenor;
                $pinjaman->rate = $tenor->rate;
                $pinjaman->biaya_admin =  $tenor->biaya_admin;
                $pinjaman->status = 'on_proses';
                $pinjaman->status_otp = 'pending';

                $data = [
                    'id_tenor_pinjaman' => $tenor->id,
                    'nominal' => $req->nominal,
                ];

                $simulation = $this->simulation($data);
                $pinjaman->total_biaya_admin = $simulation['biaya_admin'];
                $pinjaman->biaya_admin_ksp = $simulation['biaya_admin'] * 50 / 100;
                $pinjaman->dpp_adm_ksp = round($pinjaman->biaya_admin_ksp / 111 * 100);
                $pinjaman->ppn_adm_ksp = round($pinjaman->dpp_adm_ksp * 11 / 100);
                $pinjaman->biaya_admin_pandu = $simulation['biaya_admin'] * 50 / 100;
                $pinjaman->dpp_adm_pandu = round($pinjaman->biaya_admin_pandu / 111 * 100);
                $pinjaman->ppn_adm_pandu = round($pinjaman->dpp_adm_pandu * 11 / 100);
                $pinjaman->pendapatan_bunga = $req->nominal * $tenor->rate / 100 * $tenor->tenor;

                $pinjaman->dpp_bunga = round($pinjaman->pendapatan_bunga / 111 * 100);
                $pinjaman->ppn_bunga = round($pinjaman->dpp_bunga * 11 / 100);


                $pinjaman->total_angsuran_perbulan = $simulation['angsuran_perbulan'];
                $pinjaman->jumlah_yang_diterima = $simulation['jumlah_diterima'];
                $pinjaman->iuran_anggota = $simulation['iuran_anggota'];
                $pinjaman->biaya_layanan = $simulation['biaya_layanan'];

                $cost = cost::get()->toArray();
                $totalCost = $cost[3]['biaya'] + $cost[1]['biaya'];
                $pinjaman->cost_ksp = $totalCost;
                $pinjaman->description_cost_ksp = "Biaya disbursment + PK Otomation";

                sendOTPWhatsapp($perusahaan->karyawanSingle->whatsapp, $codeOTP);
                $pinjaman->save();


                // jatuh tempo langsung tercreate sesuai tenor
                $tanggal_sekarang = Carbon::now();
                $oppca = 1;
                while (true) {
                    $jatuh_tempo = new jatuh_tempo();
                    $jatuh_tempo->id_pinjaman = $pinjaman->id;
                    $jatuh_tempo->pembayaran = $oppca;
                    if ($oppca == 1) {
                        if ($tanggal_sekarang->format('d') < $perusahaan->payment_date - 5) {
                            $jatuh_tempo->jatuh_tempo =  $tanggal_sekarang->day($perusahaan->payment_date);
                        } else {
                            $jatuh_tempo->jatuh_tempo =  $tanggal_sekarang->day($perusahaan->payment_date)->addMonth(1);
                        }
                    } else {
                        $jatuh_tempo_last = jatuh_tempo::where('id_pinjaman', $pinjaman->id)
                            ->orderBy('id', 'desc')
                            ->first();
                        $jatuh_tempo->jatuh_tempo =  Carbon::parse($jatuh_tempo_last->jatuh_tempo)
                            ->addMonth(1);
                    }

                    $jatuh_tempo->status = 'belum';
                    $jatuh_tempo->save();
                    if ($oppca == $tenor->tenor) {
                        break;
                    }
                    $oppca++;
                }

                $otp->pinjaman_id = $pinjaman->id;
                $otp->save();
                return response()->json([
                    'status' => 200,
                    'pinjaman' => true,
                    'message' => 'success, harap periksa OTP yang telah dikirimkan melalui whatsapp',
                ], 200);
            }
        }
    }

    public function actionPinjamanOTP(OTPpinjamanRequest $req)
    {
        $verif = verif_otp_pengajuan::where('user_id',  auth('api')->user()->id)
            ->where('otp', $req->otp)
            ->with(['pinjaman' => function ($q) {
                $q->with(['data_karyawan' => function ($w) {
                    $w->with('bank');
                    $w->with('perusahaan');
                }]);
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
                $user = User::where('id', auth('api')->user()->id)->first();
                $time = Carbon::now()->format('Y-m-d');
                $random = rand(1000, 9999);
                $usia =  Carbon::parse($verif->user->dob)->age;

                $fileName = $user->nik . '_' . $time . '_' . $random . '.pdf';

                $disk = Storage::disk('local');
                $html = $disk->get('template/pkk.html');

                $dataPengganti = [
                    'pihak_kedua' => '<b>' . $verif->pinjaman->data_karyawan->nama_karyawan . '</b>' . ',' . $usia . ' tahun, ' . 'beralamat di ' . $verif->pinjaman->data_karyawan->alamat . ' pemegang kartu tanda penduduk nomor ' . $verif->pinjaman->data_karyawan->nik . ' karyawan pada ' . $verif->pinjaman->data_karyawan->perusahaan->nama . ' dengan NIP ' . $verif->pinjaman->data_karyawan->nip,
                    'plafond'    => 'Rp  ' . number_format($verif->pinjaman->nominal),
                    'plafond_terbilang'    => ' (' . terbilang($verif->pinjaman->nominal) . ' rupiah)',
                    'jangka' => $verif->pinjaman->tenor . ' bulan',
                    'rate' => $verif->pinjaman->rate . '%',
                    'angsuran_perbulan' => 'Rp  ' . number_format($verif->pinjaman->total_angsuran_perbulan),
                    'angsuran_perbulan_terbilang' =>  ' (' . terbilang($verif->pinjaman->total_angsuran_perbulan) . ' rupiah)',
                    'administrasi' => 'Rp  ' . number_format($verif->pinjaman->total_biaya_admin),
                    'biaya_layanan' => 'Rp  ' . number_format($verif->pinjaman->biaya_layanan),
                    'iuran_anggota' => 'Rp  ' . number_format($verif->pinjaman->iuran_anggota),
                    'rekening_pencairan' => $verif->pinjaman->data_karyawan->nomor_bank,
                    'bank_pencairan' => $verif->pinjaman->data_karyawan->bank->code_bank,
                    'atas_nama_rekening_pencairan' => $verif->pinjaman->data_karyawan->nama_rekening,
                    'jumlah_pencairan' => 'Rp  ' . number_format($verif->pinjaman->jumlah_yang_diterima),
                    'terbilang_jumlah_pencairan' => ' (' . terbilang($verif->pinjaman->jumlah_yang_diterima) . ' rupiah)',
                    'nomor_rekening_tomi' => $verif->pinjaman->data_karyawan->perusahaan->virtual_account_channel_properties_virtual_account_number,
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
            $user = User::where('id', auth('api')->user()->id)->first();
            // $documentTTD = getDocumentPK($user, $pinjaman->referrence_number, $pinjaman->document_token);
            $path = 'assets/pk/' . $pinjaman->document_name;
            $pdfContent = Storage::get($path);
            $base64Pdf = base64_encode($pdfContent);
            $document = 'data:application/pdf;base64,' . $base64Pdf;
            // if (!isset($documentTTD['data']['signed_document'])) {
            //     return response()->json([
            //         'status' => 400,
            //         'message' => 'Mohon tunggu, Document sedang disiapkan',
            //     ], 400);
            // } else {
            // Storage::disk('local')->put('assets/pk/' . $pinjaman->document_name, base64_decode(Str::after($documentTTD['data']['signed_document'], 'data:application/pdf;base64,')));
            return response()->json([
                'status' => 200,
                'message' => '',
                'document' => $document
            ], 200);
            // }
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
                        'message' => 'Mohon tunggu, Document sedang proses penanda tanganan',
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
                Log::channel('privylog')->info('correct password | Timestamp :' . Carbon::now() . '| privy_id : ' . $user->id_privy . '| referrence_number_user : ' . $user->reference_number_privy . '| referrence_number_document : ' . $pinjaman->referrence_number);
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

            $cekPinjamanData = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
                ->where(function ($q) {
                    $q->orWhere('status', 'exported')
                        ->orWhere('status', 'on_proses')
                        ->orWhere('status', 'approve');
                })
                ->where('id_karyawan', $perusahaan->karyawanSingle->id)
                ->where('status_otp', 'success')
                ->first();

            $cekPinjamanUser = pinjaman::where('user_id', auth('api')->user()->id)
                ->where(function ($q) {
                    $q->orWhere('status', 'exported')
                        ->orWhere('status', 'on_proses')
                        ->orWhere('status', 'approve');
                })
                ->where('id_karyawan', $perusahaan->karyawanSingle->id)
                ->where('status_otp', 'success')
                ->first();


            // where('user_id', auth('api')->user()->id)
            $cekPinjamanPendingOTP = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
                ->where('status', 'on_proses')
                ->where('status_otp', 'pending')
                ->first();

            if (isset($cekPinjamanData) || isset($cekPinjamanUser)) {
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
