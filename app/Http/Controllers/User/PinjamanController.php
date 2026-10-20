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
use App\Models\PrivyDigitalIdSession;
use App\Models\rekening_tomi;
use App\Models\tenor_pinjaman;
use App\Models\User;
use App\Models\verif_otp_pengajuan;
use App\Services\PrivyDigitalIdClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PinjamanController extends Controller
{
    private const KYC_EXPIRED_DAYS = 180;

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
            'rate_persen' => $tenor->rate.'%',
            'asuransi_persen' => $tenor->asuransi.'%',
            'asuransi' => $biaya_asuransi,
            'provisi_persen' => $tenor->provisi.'%',
            'provisi' => $biaya_provisi,
            'biaya_admin_persen' => $tenor->biaya_admin.'%',
            'tenor' => $tenor->tenor,
            'biaya_admin' => $biaya_admin,
            'biaya_admin_tomi' => $biaya_admin_tomi,
            'biaya_layanan' => $biaya_layanan,
            'iuran_anggota' => $iuran_anggota,
            'jumlah_diterima' => $nilai_yang_diterima,
        ];

        return $returnData;
    }

    private function kycExpiredResponse(User $user)
    {
        // Dinonaktifkan sementara agar KYC lama tetap bisa digunakan untuk pengajuan pinjaman.
        return null;

        if ($user->kyc == null || $user->kyc->created_at == null) {
            return null;
        }

        if (Carbon::parse($user->kyc->created_at)->lessThan(Carbon::now()->subDays(self::KYC_EXPIRED_DAYS))) {
            return response()->json([
                'status' => 400,
                'message' => 'Maaf, KYC Anda sudah lebih dari 180 hari. Mohon perbarui foto selfie dan KTP Anda untuk melanjutkan proses pinjaman.',
            ], 400);
        }

        return null;
    }

    private function isKaryawanTetap($karyawan): bool
    {
        return strtolower(trim((string) $karyawan?->status)) === 'tetap';
    }

    private function privyVerificationRequiredResponse(string $message, ?string $reason = null, ?User $user = null)
    {
        $response = [
            'status' => 400,
            'message' => $message,
            'action_required' => 'privy_verification',
            'privy_verification_required' => true,
            'next_endpoint' => '/v1/api/privy/digital-id/session',
        ];

        if ($reason !== null) {
            $response['reason'] = $reason;
        }

        return response()->json($response, 400);
    }

    private function createPrivyDigitalIdSession(User $user): ?array
    {
        if (empty($user->nik) || empty($user->nama_lengkap) || empty($user->dob) || empty($user->email) || empty($user->nohp)) {
            return null;
        }

        $activeSession = PrivyDigitalIdSession::where('user_id', $user->id)
            ->whereIn('status', ['created', 'pending', 'processing'])
            ->whereNotNull('session_url')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();

        if ($activeSession) {
            return [
                'session_id' => $activeSession->privy_session_id,
                'session_url' => $activeSession->session_url,
                'expires_at' => $activeSession->expires_at?->toISOString(),
            ];
        }

        $referenceNumber = 'TOMIDIGITALID'.$user->id.now('Asia/Jakarta')->format('YmdHis');

        try {
            $session = app(PrivyDigitalIdClient::class)->createSession();

            PrivyDigitalIdSession::create([
                'user_id' => $user->id,
                'reference_number' => $referenceNumber,
                'privy_session_id' => $session['session_id'],
                'session_url' => $session['session_url'],
                'status' => 'created',
                'expires_at' => $session['expires_at'] ? Carbon::parse($session['expires_at'])->toDateTimeString() : null,
            ]);

            return [
                'session_id' => $session['session_id'],
                'session_url' => $session['session_url'],
                'expires_at' => $session['expires_at'],
            ];
        } catch (\Throwable $e) {
            Log::warning('Privy Digital ID session creation from loan request failed', [
                'user_id' => $user->id,
                'reference_number' => $referenceNumber,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function hasVerifiedPrivy(User $user): bool
    {
        if ($user->hasLegacyPrivyVerification()) {
            return true;
        }

        $verifiedSession = $user->verifiedDigitalIdSession();

        if (! $verifiedSession) {
            return false;
        }

        $user->id_privy = $user->id_privy ?: $verifiedSession->privy_id;
        $user->verified_privy = 'y';
        $user->save();

        return true;
    }

    private function renderPkHtml(pinjaman $pinjaman, User $user)
    {

        Carbon::setLocale('id');
        $waktu = Carbon::now();
        $hari = $waktu->isoFormat('dddd');
        $carbonSekarang = $waktu->format('d/m/Y');
        $carbonTahun = $waktu->format('Y');
        $bulanAngka = (int) $waktu->format('m');
        $bulanRomawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        $disk = Storage::disk('local');
        $html = $disk->get('template/pkk.html');
        $tenorConfig = tenor_pinjaman::where('id_perusahaan', $pinjaman->id_perusahaan)
            ->where('tenor', $pinjaman->tenor)
            ->first();
        $rate = (float) data_get($tenorConfig, 'rate', $pinjaman->rate ?? 0);
        $tenorBulan = max((int) $pinjaman->tenor, 1);
        $totalDenganBunga = $pinjaman->nominal + ($pinjaman->nominal * ($rate / 100) * $tenorBulan);
        $angsuranPerbulan = ceil(($totalDenganBunga / $tenorBulan) / 1000) * 1000;
        $rateText = rtrim(rtrim(number_format($rate, 2, ',', '.'), '0'), ',').' %';

        $htmlTabelAngsuran = '<table class="table-pinjaman">
                <thead>
                    <tr class="table-pinjaman__row">
                        <th class="table-pinjaman__header">Angsuran ke -</th>
                        <th class="table-pinjaman__header">Nilai Angsuran</th>
                        <th class="table-pinjaman__header">Tanggal Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($pinjaman->jatuh_tempo as $index => $jt) {
            $isEven = ($index + 1) % 2 == 0;
            $classRow = $isEven ? 'table-pinjaman__row--even' : '';
            $nilaiAngsuran = 'Rp '.number_format($angsuranPerbulan);

            $htmlTabelAngsuran .= '
                    <tr class="table-pinjaman__row '.$classRow.'">
                        <td class="table-pinjaman__cell">'.($index + 1).'</td>
                        <td class="table-pinjaman__cell table-pinjaman__cell--nilai">'.$nilaiAngsuran.'</td>
                        <td class="table-pinjaman__cell">'.Carbon::parse($jt['jatuh_tempo'])->format('d/m/Y').'</td>
                    </tr>';
        }

        $htmlTabelAngsuran .= '</tbody></table>';

        $usia = Carbon::parse($user->dob)->age;
        $dataKaryawan = $pinjaman->data_karyawan;
        $perusahaan = $pinjaman->perusahaan ?: $dataKaryawan->perusahaan;
        $bank = $dataKaryawan->bank;
        $lender = $perusahaan?->lender;

        $dataPengganti = [
            'nomor' => pinjaman::count(),
            'hari' => $hari,
            'tanggal_sekarang' => $carbonSekarang,
            'ct' => $carbonTahun,
            'bulan_romawi' => $bulanRomawi[$bulanAngka],
            'id_pinjaman' => $pinjaman->id_pinjaman,
            't' => data_get($perusahaan, 'payment_date', '-'),
            'pinjaman_jatuh_tempo' => $htmlTabelAngsuran,

            'pihak_kedua' => '<b>'.$dataKaryawan->nama_karyawan.'</b>'.','.$usia.' tahun, '.'beralamat di '.$dataKaryawan->alamat.' pemegang kartu tanda penduduk nomor '.$dataKaryawan->nik.' karyawan pada '.data_get($perusahaan, 'nama', '-').' dengan NIP '.$dataKaryawan->nip,
            'plafond' => 'Rp  '.number_format($pinjaman->nominal),
            'plafond_terbilang' => ' ('.terbilang($pinjaman->nominal).' Rupiah)',
            'jangka' => $pinjaman->tenor.' bulan',
            'rate' => $rateText,
            'angsuran_perbulan' => 'Rp  '.number_format($angsuranPerbulan),
            'angsuran_perbulan_terbilang' => ' ('.terbilang($angsuranPerbulan).' Rupiah)',
            'administrasi' => 'Rp  '.number_format($pinjaman->total_biaya_admin),
            'asuransi' => 'Rp  '.number_format($pinjaman->jumlah_asuransi),
            'provisi' => 'Rp  '.number_format($pinjaman->jumlah_provisi),
            'biaya_layanan' => 'Rp  '.number_format($pinjaman->biaya_layanan),
            'iuran_anggota' => 'Rp  '.number_format($pinjaman->iuran_anggota),
            'rekening_pencairan' => $dataKaryawan->nomor_bank,
            'bank_pencairan' => data_get($bank, 'code_bank', '-'),
            'atas_nama_rekening_pencairan' => $dataKaryawan->nama_rekening,
            'jumlah_pencairan' => 'Rp  '.number_format($pinjaman->jumlah_yang_diterima),
            'terbilang_jumlah_pencairan' => ' ('.terbilang($pinjaman->jumlah_yang_diterima).' Rupiah)',
            'bankLender' => data_get($lender, 'bank.code_bank', '-'),
            'rekeningLender' => data_get($lender, 'no_rekening', '-'),
            'atasNamaBankLender' => data_get($lender, 'nama_rekening', '-'),
            'penerima_pinjaman' => strtoupper($user->nama_lengkap),
        ];

        foreach ($dataPengganti as $key => $value) {
            $html = str_replace("{{ \$$key }}", $value, $html);
        }

        return $html;
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
        $nip = explode('-', $req->nip);

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
                'maximal' => 'Rp  '.number_format($maksimal),
                'minimal' => 'Rp  '.number_format($perusahaan->amount_pinjaman->minimal),
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
        $nip = explode('-', $req->nip);
        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }])
            ->first();

        // $pendapatan = 70 / 100 * $perusahaan->karyawanSingle->penghasilan;

        if ($req->nominal > $perusahaan->amount_pinjaman->maximal) {
            $maksimal = $perusahaan->amount_pinjaman->maximal;
        } elseif ($req->nominal > $perusahaan->karyawanSingle->max_plafon) {
            $maksimal = $perusahaan->karyawanSingle->max_plafon;
        } else {
            $maksimal = $req->nominal;
        }

        $range = [
            'maximal' => $maksimal,
            'minimal' => $perusahaan->amount_pinjaman->minimal,
        ];

        $tenor = tenor_pinjaman::where('id_perusahaan', $perusahaan->id)
            ->where(function ($q) use ($range, $req) {
                $q->where('minimal_nominal', '>=', $range['minimal'])
                    ->where('minimal_nominal', '<=', $req->nominal);
            })
            ->with('amount_pinjaman')
            ->orderBy('tenor')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'success get tenor',
            'produk' => TenorPinjamanResource::collection($tenor),
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
            'asuransi' => 'Rp  '.number_format($simulation['asuransi']),
            'provisi' => 'Rp  '.number_format($simulation['provisi']),
            'biaya_admin_persen' => $simulation['biaya_admin_persen'],
            'tenor' => $simulation['tenor'].' bulan',
            'angsuran_perbulan' => 'Rp  '.number_format($simulation['angsuran_perbulan']),
            'biaya_admin' => 'Rp  '.number_format($simulation['biaya_admin']),
            'biaya_layanan' => 'Rp  '.number_format($simulation['biaya_layanan']),
            'iuran_anggota' => 'Rp  '.number_format($simulation['iuran_anggota']),
            'jumlah_diterima' => 'Rp  '.number_format($simulation['jumlah_diterima']),
        ], 200);
        // }
    }

    public function actionPinjaman(ActionPinjamRequest $req)
    {
        /**
         * ☠️═══════════════════════════════════════════════════════════════════════☠️
         *                     ☠️ FINAL BOSS - PRODUCTION CORE ☠️
         * ☠️═══════════════════════════════════════════════════════════════════════☠️
         *
         * ⚠️ BERHENTI.
         *
         * Jika tujuanmu membuka function ini hanya karena ingin
         * "rapihin dikit" atau "biar lebih clean",
         * tutup editor sekarang.
         *
         * =======================================================================
         *                     ☠️ AREA DENGAN RISIKO TERTINGGI ☠️
         * =======================================================================
         *
         * Function ini adalah jantung proses pengajuan pinjaman.
         *
         * Hampir seluruh proses bisnis melewati tempat ini.
         *
         * Sedikit perubahan dapat menyebabkan:
         *
         * ☠️ Pengajuan gagal.
         * ☠️ OTP tidak terkirim.
         * ☠️ Simulasi pinjaman salah.
         * ☠️ Data pinjaman tidak tersimpan.
         * ☠️ Jadwal jatuh tempo tidak terbentuk.
         * ☠️ User tidak bisa mencairkan pinjaman.
         * ☠️ Dan yang paling menakutkan...
         *     Semua terlihat normal sampai Production digunakan pelanggan.
         *
         * =======================================================================
         *                    ☠️ SEBELUM MENYENTUH FUNCTION INI ☠️
         * =======================================================================
         *
         * Tanyakan pada dirimu sendiri:
         *
         * □ Apakah saya memahami seluruh flow bisnisnya?
         * □ Apakah saya tahu setiap return 400 dibuat untuk kondisi apa?
         * □ Apakah saya sudah membaca semua requirement bisnis?
         * □ Apakah saya sudah menyiapkan rollback?
         * □ Apakah saya sudah melakukan testing end-to-end?
         *
         * Jika ada satu saja jawaban "Tidak"...
         *
         * Jangan lanjutkan.
         *
         * =======================================================================
         *                     ☠️ HUKUM LEGACY CODE ☠️
         * =======================================================================
         *
         * Legacy Code bukan berarti kode ini buruk.
         *
         * Legacy Code berarti:
         *
         * - Sudah digunakan oleh ribuan transaksi.
         * - Sudah melewati banyak perubahan requirement.
         * - Sudah menyimpan banyak cerita yang tidak ada di dokumentasi.
         *
         * Jangan pernah menghapus sesuatu hanya karena
         * "kelihatannya tidak dipakai."
         *
         * Production sering memiliki pendapat yang berbeda.
         *
         * =======================================================================
         *                     ☠️ SATU BARIS, SERIBU DAMPAK ☠️
         * =======================================================================
         *
         * Ingat...
         *
         * Bug di sini tidak selalu muncul hari ini.
         * Kadang muncul minggu depan.
         * Kadang muncul saat jam kerja.
         * Kadang muncul pukul 02:00 pagi.
         *
         * Dan Production...
         * Selalu memilih waktu yang paling tidak tepat.
         *
         * =======================================================================
         *                         ☠️ LAST WARNING ☠️
         * =======================================================================
         *
         * Jika setelah mengubah function ini:
         *
         * ☠️ Finance mulai bertanya.
         * ☠️ Customer Service mulai menerima banyak komplain.
         * ☠️ QA mengirim pesan "Mas, ada yang aneh."
         * ☠️ Senior Developer berjalan ke arah mejamu.
         *
         * Ketahuilah...
         *
         * Boss Fight telah dimulai.
         *
         * Semoga commit-mu kecil.
         * Semoga rollback-mu mudah.
         * Dan semoga Production tidak pernah mengingat namamu.
         *
         *                        ☠️ ENTER AT YOUR OWN RISK ☠️
         */
        $user = User::where('id', auth('api')->user()->id)
            ->with('kyc')
            ->first();

        if (! $this->hasVerifiedPrivy($user)) {
            return $this->privyVerificationRequiredResponse('Silakan verifikasi identitas Privy terlebih dahulu sebelum mengajukan pinjaman.');
        }

        $nip = explode('-', $req->nip);

        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }, 'amount_pinjaman', 'lender'])
            ->first();

        if (! isset($perusahaan) || ! isset($perusahaan->karyawanSingle)) {
            return response()->json([
                'status' => 400,
                'message' => 'Data NIP tidak ditemukan',
            ], 400);
        } else {
            if (! $this->isKaryawanTetap($perusahaan->karyawanSingle)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Mohon maaf, pengajuan pinjaman Anda belum dapat diproses. Silakan hubungi Human Capital (HC) Perusahaan Anda untuk informasi lebih lanjut.',
                ], 400);
            }

            // $pendapatan = 70 / 100 * $perusahaan->karyawanSingle->penghasilan;
            if ($perusahaan->karyawanSingle->max_plafon > $perusahaan->amount_pinjaman->maximal) {
                $maksimal = $perusahaan->amount_pinjaman->maximal;
            } elseif ($perusahaan->karyawanSingle->max_plafon < $perusahaan->amount_pinjaman->maximal) {
                $maksimal = $perusahaan->karyawanSingle->max_plafon;
            }

            $tenor = tenor_pinjaman::where('id_perusahaan', $perusahaan->id)
                ->where('id', $req->id_tenor_pinjaman)
                ->with('amount_pinjaman')
                ->orderBy('tenor')
                ->first();

            if (! ($tenor->minimal_nominal <= $req->nominal) && ! ($maksimal <= $tenor->maximal_nominal)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Maksimal nominal pinjaman Rp '.number_format($maksimal),
                ], 400);
            } else {

                // KYC lama menjadi sumber utama. Untuk user yang sudah selesai
                // verifikasi lewat Privy Digital ID, gunakan sesi verified sebagai
                // fallback karena flow tersebut tidak selalu membuat record user_kyc.
                $kyc = $user->kyc;
                $privyDigitalIdSession = $kyc
                    ? null
                    : $user->verifiedDigitalIdSession();

                // Nasabah terverifikasi Privy (users.verified_privy + users.id_privy) tidak wajib
                // memiliki KYC lama: identitas & liveness sudah diverifikasi oleh Privy.
                if ($kyc === null && $privyDigitalIdSession === null && ! $user->hasLegacyPrivyVerification()) {
                    return $this->privyVerificationRequiredResponse('Maaf, proses tidak dapat dilanjutkan. Kami memerlukan pembaruan foto selfie dan KTP Anda.');
                }

                $kycExpiredResponse = $this->kycExpiredResponse($user);
                if ($kycExpiredResponse) {
                    return $kycExpiredResponse;
                }


                $cekPinjamanData = pinjaman::where('id_karyawan', $perusahaan->karyawanSingle->id)
                    ->where(function ($q) {
                        $q->orWhere('status', 'pending')
                            ->orWhere('status', 'on_proses')
                            ->orWhere('status', 'approve');
                    })
                    ->where('id_karyawan', $perusahaan->karyawanSingle->id)
                    ->where('status_otp', 'success')
                    ->first();

                $cekPinjamanUser = pinjaman::where('user_id', auth('api')->user()->id)
                    ->where(function ($q) {
                        $q->orWhere('status', 'pending')
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

                // pinjaman aktif
                if (isset($cekPinjamanData) || isset($cekPinjamanUser)) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Mohon maaf, saat ini Anda masih memiliki pinjaman aktif. Terima kasih',
                    ], 400);
                }

                // pembaruan data karyawan tiap tanggal 1
                if ($perusahaan->karyawanSingle->updated_at && Carbon::parse($perusahaan->karyawanSingle->updated_at)->lessThan(Carbon::now()->startOfMonth())) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Kami melihat pembaruan data Anda sudah melewati batas waktu yang ditentukan, Mohon bantuannya untuk segera menghubungi Human Capital (HC) Perusahaan Anda agar mereka dapat memproses dan mengirimkan data terbaru Anda ke aplikasi TOMI. Kami siap membantu menyelesaikan prosesnya setelah data dari HC kami terima. Terima kasih!',
                    ], 400);
                }

                if (isset($cekPinjamanPendingOTP)) {
                    return response()->json([
                        'status' => 200,
                        'pinjaman' => false,
                        'message' => 'Anda memiliki validasi otp yang belum diselesaikan',
                    ], 200);
                }

                while (true) {
                    $code = '#LOAN-'.Carbon::now()->format('Y-m-d').'-'.auth('api')->user()->id.Carbon::now()->format('s').rand(100, 999);
                    $pinjaman = pinjaman::where('id_pinjaman', $code)->first();
                    if (! isset($pinjaman)) {
                        break;
                    }
                }

                $codeOTP = rand(1000, 9999);

                $otp = new verif_otp_pengajuan;
                $otp->user_id = auth('api')->user()->id;
                $otp->failed = 0;
                $otp->otp = $codeOTP;

                $otp->expired = Carbon::now()->addHour(24);

                $pinjaman = new pinjaman;
                $pinjaman->user_id = auth('api')->user()->id;
                $pinjaman->id_perusahaan = $perusahaan->id;
                $pinjaman->id_karyawan = $perusahaan->karyawanSingle->id;
                $pinjaman->id_lender = $perusahaan->id_lender;
                $pinjaman->id_kyc = $kyc?->id;
                $pinjaman->id_pinjaman = $code;
                $pinjaman->nominal = $req->nominal;
                $pinjaman->tenor = $tenor->tenor;
                $pinjaman->rate = $tenor->rate;
                $pinjaman->asuransi = $tenor->asuransi;
                $pinjaman->provisi = $tenor->provisi;
                $pinjaman->biaya_admin = $tenor->biaya_admin;
                $pinjaman->status = 'on_proses';
                $pinjaman->status_otp = 'pending';

                $data = [
                    'id_tenor_pinjaman' => $tenor->id,
                    'nominal' => $req->nominal,
                ];

                $simulation = $this->simulation($data);
                $pinjaman->total_biaya_admin = $simulation['biaya_admin'];
                $pinjaman->biaya_admin_ksp = $simulation['biaya_admin'];
                $pinjaman->dpp_adm_ksp = round($pinjaman->biaya_admin_ksp / 111 * 100);
                $pinjaman->ppn_adm_ksp = round($pinjaman->dpp_adm_ksp * 11 / 100);
                $pinjaman->biaya_admin_pandu = $simulation['biaya_admin_tomi'];
                $pinjaman->dpp_adm_pandu = round($pinjaman->biaya_admin_pandu / 111 * 100);
                $pinjaman->ppn_adm_pandu = round($pinjaman->dpp_adm_pandu * 11 / 100);
                $pinjaman->pendapatan_bunga = $req->nominal * $tenor->rate / 100 * $tenor->tenor;

                $pinjaman->dpp_bunga = round($pinjaman->pendapatan_bunga / 111 * 100);
                $pinjaman->ppn_bunga = round($pinjaman->dpp_bunga * 11 / 100);

                $pinjaman->total_angsuran_perbulan = $simulation['angsuran_perbulan'];
                $pinjaman->jumlah_yang_diterima = $simulation['jumlah_diterima'];
                $pinjaman->jumlah_asuransi = $simulation['asuransi'];
                $pinjaman->jumlah_provisi = $simulation['provisi'];
                $pinjaman->profesional_fee_1 = $tenor->profesional_fee_1 ?? 0;
                $pinjaman->profesional_fee_2 = $tenor->profesional_fee_2 ?? 0;
                $pinjaman->jumlah_profesional_1 = $pinjaman->profesional_fee_1 / 100 * $req->nominal;
                $pinjaman->jumlah_profesional_2 = $pinjaman->profesional_fee_2 / 100 * $req->nominal;
                $pinjaman->ksp_fee = ($tenor->ksp ?? 0) / 100 * $req->nominal;

                $pinjaman->iuran_anggota = $simulation['iuran_anggota'];
                $pinjaman->biaya_layanan = $simulation['biaya_layanan'];

                $cost = cost::get()->toArray();
                $totalCost = $cost[3]['biaya'] + $cost[1]['biaya'];
                $pinjaman->cost_ksp = $totalCost;
                $pinjaman->description_cost_ksp = 'Biaya disbursment + PK Otomation';

                sendOTPWhatsapp($perusahaan->karyawanSingle->whatsapp, $codeOTP);
                $pinjaman->save();

                // jatuh tempo langsung tercreate sesuai tenor
                $tanggal_sekarang = Carbon::now();
                $oppca = 1;
                while (true) {
                    $jatuh_tempo = new jatuh_tempo;
                    $jatuh_tempo->id_pinjaman = $pinjaman->id;
                    $jatuh_tempo->pembayaran = $oppca;
                    if ($oppca == 1) {
                        if ($tanggal_sekarang->format('d') <= $perusahaan->payment_date - $perusahaan->h_min_jatuh_tempo) {
                            $jatuh_tempo->jatuh_tempo = $tanggal_sekarang->day($perusahaan->payment_date);
                        } else {
                            $jatuh_tempo->jatuh_tempo = $tanggal_sekarang->day($perusahaan->payment_date)->addMonth(1);
                        }
                    } else {
                        $jatuh_tempo_last = jatuh_tempo::where('id_pinjaman', $pinjaman->id)
                            ->orderBy('id', 'desc')
                            ->first();
                        $jatuh_tempo->jatuh_tempo = Carbon::parse($jatuh_tempo_last->jatuh_tempo)
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

                $nomorWa = $perusahaan->karyawanSingle->whatsapp;

                $mask = substr($nomorWa, 0, 4)
                    .str_repeat('x', strlen($nomorWa) - 7)
                    .substr($nomorWa, -3);

                return response()->json([
                    'status' => 200,
                    'pinjaman' => true,
                    'message' => 'success, harap periksa OTP yang telah dikirimkan melalui whatsapp '.$mask,
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
                    'data_karyawan' => function ($w) {
                        $w->with('bank');
                        $w->with(['perusahaan' => function ($p) {
                            $p->with(['lender' => function ($o) {
                                $o->with('bank');
                            }]);
                        }]);
                    },
                    'jatuh_tempo' => function ($jt) {
                        $jt->orderBy('jatuh_tempo', 'asc');
                    },
                ]);
            }, 'user'])
            ->first();

        if (! isset($verif)) {
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
                    1 => 'I',
                    2 => 'II',
                    3 => 'III',
                    4 => 'IV',
                    5 => 'V',
                    6 => 'VI',
                    7 => 'VII',
                    8 => 'VIII',
                    9 => 'IX',
                    10 => 'X',
                    11 => 'XI',
                    12 => 'XII',
                ];
                $bulan_romawi = $bulanRomawi[$bulanAngka];

                $pinjamanCount = pinjaman::count();
                // $rekeningTomi = rekening_tomi::with('bank')->first();

                $random = rand(1000, 9999);
                $usia = Carbon::parse($verif->user->dob)->age;

                $fileName = $user->nik.'_'.$time.'_'.$random.'.pdf';

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
                    $nilaiAngsuran = 'Rp '.number_format($verif->pinjaman->total_angsuran_perbulan);

                    $htmlTabelAngsuran .= '
                            <tr class="table-pinjaman__row '.$classRow.'">
                                <td class="table-pinjaman__cell">'.($index + 1).'</td>
                                <td class="table-pinjaman__cell table-pinjaman__cell--nilai">'.$nilaiAngsuran.'</td>
                                <td class="table-pinjaman__cell">'.Carbon::parse($jt['jatuh_tempo'])->format('d/m/Y').'</td>
                            </tr>';
                }

                $htmlTabelAngsuran .= '</tbody></table>';

                $dataPengganti = [
                    'nomor' => $pinjamanCount,
                    'hari' => $hari,
                    'tanggal_sekarang' => $carbonSekarang,
                    'ct' => $carbonTahun,
                    'bulan_romawi' => $bulan_romawi,
                    'id_pinjaman' => $verif->pinjaman->id_pinjaman,
                    't' => $verif->pinjaman->data_karyawan->perusahaan->payment_date,
                    'pinjaman_jatuh_tempo' => $htmlTabelAngsuran,

                    'pihak_kedua' => '<b>'.$verif->pinjaman->data_karyawan->nama_karyawan.'</b>'.','.$usia.' tahun, '.'beralamat di '.$verif->pinjaman->data_karyawan->alamat.' pemegang kartu tanda penduduk nomor '.$verif->pinjaman->data_karyawan->nik.' karyawan pada '.$verif->pinjaman->data_karyawan->perusahaan->nama.' dengan NIP '.$verif->pinjaman->data_karyawan->nip,
                    'plafond' => 'Rp  '.number_format($verif->pinjaman->nominal),
                    'plafond_terbilang' => ' ('.terbilang($verif->pinjaman->nominal).' Rupiah)',
                    'jangka' => $verif->pinjaman->tenor.' bulan',
                    'rate' => $verif->pinjaman->rate.'%',
                    'angsuran_perbulan' => 'Rp  '.number_format($verif->pinjaman->total_angsuran_perbulan),
                    'angsuran_perbulan_terbilang' => ' ('.terbilang($verif->pinjaman->total_angsuran_perbulan).' Rupiah)',
                    'administrasi' => 'Rp  '.number_format($verif->pinjaman->total_biaya_admin),
                    'asuransi' => 'Rp  '.number_format($verif->pinjaman->jumlah_asuransi),
                    'provisi' => 'Rp  '.number_format($verif->pinjaman->jumlah_provisi),
                    'biaya_layanan' => 'Rp  '.number_format($verif->pinjaman->biaya_layanan),
                    'iuran_anggota' => 'Rp  '.number_format($verif->pinjaman->iuran_anggota),
                    'rekening_pencairan' => $verif->pinjaman->data_karyawan->nomor_bank,
                    'bank_pencairan' => $verif->pinjaman->data_karyawan->bank->code_bank,
                    'atas_nama_rekening_pencairan' => $verif->pinjaman->data_karyawan->nama_rekening,
                    'jumlah_pencairan' => 'Rp  '.number_format($verif->pinjaman->jumlah_yang_diterima),
                    'terbilang_jumlah_pencairan' => ' ('.terbilang($verif->pinjaman->jumlah_yang_diterima).' Rupiah)',
                    'bankLender' => $verif->pinjaman->data_karyawan->perusahaan->lender->bank->code_bank,
                    'rekeningLender' => $verif->pinjaman->data_karyawan->perusahaan->lender->no_rekening,
                    'atasNamaBankLender' => $verif->pinjaman->data_karyawan->perusahaan->lender->nama_rekening,
                    'penerima_pinjaman' => strtoupper($user->nama_lengkap),
                ];

                foreach ($dataPengganti as $key => $value) {
                    $html = str_replace("{{ \$$key }}", $value, $html);
                }

                // Buat PDF dari HTML
                $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

                // Simpan ke file
                $outputPdfPath = 'assets/pk/'.$fileName;
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
                $pinjaman->status_otp = 'success';
                // untuk ambil document yg sudah ditanda tanganin
                $pinjaman->save();
                $verif->delete();

                if (! $this->hasVerifiedPrivy($user)) {
                    return $this->privyVerificationRequiredResponse('User wajib verifikasi Privy terlebih dahulu', null, $user);
                }

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

        if (! isset($pinjaman)) {
            return response()->json([
                'status' => 200,
                'message' => 'Anda tidak memiliki pinjaman yang harus ditanda tangani',
            ], 200);
        } else {
            $path = 'assets/pk/'.$pinjaman->document_name;
            $pdfContent = Storage::get($path);
            $base64Pdf = base64_encode($pdfContent);
            $document = 'data:application/pdf;base64,'.$base64Pdf;

            return response()->json([
                'status' => 200,
                'message' => '',
                'document' => $document,
            ], 200);
        }
    }

    public function previewPkPdf($id = null)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $query = pinjaman::where('user_id', $user->id)
            ->with([
                'jatuh_tempo',
                'perusahaan.lender.bank',
                'data_karyawan.bank',
                'data_karyawan.perusahaan.lender.bank',
            ]);

        if ($id != null) {
            $pinjaman = $query->where('id', $id)->first();
        } else {
            $pinjaman = $query->where('status', 'on_proses')
                ->latest()
                ->first();
        }

        if (! isset($pinjaman)) {
            return response()->json([
                'status' => 404,
                'message' => 'Pinjaman tidak tersedia',
            ], 404);
        }

        if (! isset($pinjaman->data_karyawan)) {
            return response()->json([
                'status' => 422,
                'message' => 'Data karyawan pinjaman tidak tersedia',
            ], 422);
        }

        $html = $this->renderPkHtml($pinjaman, $user);
        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        $fileName = 'PK_'.$pinjaman->id_pinjaman.'.pdf';

        return $pdf->stream($fileName);
    }

    public function showBerkasHistory(ShowBerkasHistoryRequest $req)
    {
        //
        $user = User::where('id', auth('api')->user()->id)
            ->first();
        $pinjaman = pinjaman::where('id', $req->id_pinjaman)
            ->where('user_id', $user->id)
            ->first();
        if (isset($pinjaman)) {
            if ($pinjaman->document_privy == 'y') {
                $path = 'assets/pk/'.$pinjaman->document_name;
                $pdfContent = Storage::get($path);
                $base64Pdf = base64_encode($pdfContent);
                $document = 'data:application/pdf;base64,'.$base64Pdf;

                return response()->json([
                    'status' => 200,
                    'message' => '',
                    'document' => $document,
                ], 200);
            } else {
                $documentTTD = getDocumentPK($user, $pinjaman->referrence_number, $pinjaman->document_token);
                if (! isset($documentTTD['data']['signed_document'])) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Mohon tunggu, Document sedang proses penanda tanganan, pinjaman dapat diproses ketahap selanjutnya jika anda sudah melihat document yang sudah anda tanda tangani.',
                    ], 400);
                } else {
                    Storage::disk('local')->put('assets/pk/'.$pinjaman->document_name, base64_decode(Str::after($documentTTD['data']['signed_document'], 'data:application/pdf;base64,')));
                    $pinjaman->document_privy = 'y';
                    $pinjaman->save();

                    $path = 'assets/pk/'.$pinjaman->document_name;
                    $pdfContent = Storage::get($path);
                    $base64Pdf = base64_encode($pdfContent);
                    $document = 'data:application/pdf;base64,'.$base64Pdf;

                    return response()->json([
                        'status' => 200,
                        'message' => '',
                        'document' => $document,
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
        $user = User::where('id', auth('api')->user()->id)->first();
        $pinjaman = pinjaman::where('user_id', auth('api')->user()->id)
            ->where('status_otp', 'success')
            ->where('ttd', 'n')
            ->where('status', 'on_proses')
            ->first();

        if (! isset($pinjaman)) {
            return response()->json([
                'status' => 200,
                'message' => 'Anda tidak memiliki pinjaman yang harus ditanda tangani',
            ], 200);
        } else {

            if (Hash::check($req->password, $user->password)) {
                // Log authentikasi, timestamps otentikasi saat input tanda tangan (OTP), privyid, preference number.
                // mulai tanda tangan
                $hasilTTD = ttdPK($user, $pinjaman->document_name, $pinjaman->id);

                if (($hasilTTD['error'] ?? false) === true) {
                    return response()->json([
                        'status' => $hasilTTD['status'],
                        'message' => $hasilTTD['message'],
                    ], $hasilTTD['status']);
                }

                // dd($hasilTTD);
                // masukin data dari tanda tangan ke db pinjaman
                // habis itu get file yg sudah ditanda tangan
                $pinjaman->referrence_number = $hasilTTD['data']['reference_number'];
                $pinjaman->document_token = $hasilTTD['data']['document_token'];
                $pinjaman->ttd = 'y';
                $pinjaman->status = 'pending';
                $pinjaman->save();
                Log::channel('privylog')->info('correct password | Timestamp :'.Carbon::now().'| privy_id : '.$user->id_privy.'| referrence_number_user : '.$user->reference_number_privy.'| referrence_number_document : '.$hasilTTD['data']['reference_number'].'| document_token : '.$hasilTTD['data']['document_token']);

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
            'history_pinjaman' => HistroyPinjamanResource::collection($history),
        ], 200);
    }

    public function cekPegawai(CekPegawaiRequest $req)
    {
        $hasil = false;
        $nip = explode('-', $req->nip);

        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }])->first();

        if (isset($perusahaan) && isset($perusahaan->karyawanSingle)) {
            $hasil = true;

            if ($perusahaan->karyawanSingle->status_sp != 0) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Mohon maaf, peminjaman hanya dapat dilakukan dengan karyawan berstatus bebas surat peringatan',
                ], 400);
            } elseif (! $this->isKaryawanTetap($perusahaan->karyawanSingle)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Mohon maaf, pengajuan pinjaman Anda belum dapat diproses. Silakan hubungi Human Capital (HC) Perusahaan Anda untuk informasi lebih lanjut.',
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
                'data' => new DataKaryawanResource($perusahaan),
            ], 200);
        }
    }

    public function actionPinjamanOTPUlang()
    {
        $pinjaman = pinjaman::where('user_id', auth('api')->user()->id)
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
                $verifOTP = new verif_otp_pengajuan;
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
