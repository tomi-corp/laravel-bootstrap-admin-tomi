<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bta\Analyst\UpdateAnalystRequest;
use App\Models\nasabah;
use Google\Service\CloudSearch\SafeUrlProto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Psy\Util\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;


class AnalystController extends Controller
{
    public function analystIndex()
    {
        return view('bta.analyst.index');
    }

    public function analystTable()
    {
        $nasabah = nasabah::where('position', 'analyst')
            ->where('status', 'pending')
            ->select([
                'id',
                'no_ktp',
                'nama_lengkap',
                'tanggal_lahir',
                'jenis_kelamin',
                'no_telp',
                'status_perkawinan',
                'nama_perusahaan',
                'status_pegawai',
                'analyst_id', // <-- tambahkan kolom ini
            ])
            ->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                // ambil login id
                $loginId = intval(auth('bta')->id());
                // ambil analyst id, cast ke int kalau tidak null
                $analystId = !is_null($row->analyst_id) ? intval($row->analyst_id) : null;

                // hanya bisa klik jika analyst_id null atau sama dengan login
                $canAccess = is_null($analystId) || $analystId === $loginId;

                if ($canAccess) {
                    return '
                <a href="' . route('bta.analyst.review', $row->no_ktp) . '" 
                   class="btn btn-success btn-sm">
                   <i class="mdi mdi-magnify"></i>
                </a>
            ';
                } else {
                    return '
                <button class="btn btn-secondary btn-sm" disabled
                        title="Hanya analyst yang sudah memeriksa data yang boleh mengakses.">
                    <i class="mdi mdi-magnify"></i>
                </button>
            ';
                }
            })
            ->rawColumns(['action'])
            ->toJson();
    }



    public function analystReview($noktp)
    {

        $nasabah = nasabah::where('no_ktp', $noktp)
            ->with([
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa',
                'bank',
                'dati'
            ])
            ->where('position', 'analyst')
            ->where('status', 'pending')
            ->first();


        if (!isset($nasabah)) {
            return abort(404);
        }

        $documents = [
            'ktp' => $nasabah->ktp,
            'npwp' => $nasabah->npwp,
            'id_card' => $nasabah->id_card,
            'kk' => $nasabah->kk,
            // 'selfie' => $nasabah->selfie,
            'slip_gaji' => $nasabah->slip_gaji,
            'sk_karyawan' => $nasabah->sk_karyawan,
            'form_slik' => $nasabah->form_slik,
            'form_kredit' => $nasabah->form_kredit,
            'buku_tabungan' => $nasabah->buku_tabungan,
            'form_checklist' => $nasabah->form_checklist,
            'mutasi_rekening' => $nasabah->mutasi_rekening,
            'form_bta' => $nasabah->form_bta,
            'video_interview' => $nasabah->video_interview,
            'video_asuransi' => $nasabah->video_asuransi,
        ];

        $safeUrls = [];
        foreach ($documents as $key => $fileName) {
            if ($fileName) {
                // 1. Buat payload yang ingin dienkripsi (path file dan expiry)
                $payload = [
                    'file_path' => 'bta/' . $fileName, // Sesuaikan prefix path jika berbeda
                    'expires_at' => now()->addHours(24)->timestamp, // Token berlaku 24 jam
                ];

                // 2. Enkripsi payload untuk menjadi token
                $encryptedToken = Crypt::encrypt(json_encode($payload));

                // 3. Encode agar aman untuk URL
                $tokenForUrl = base64_encode($encryptedToken);

                // 4. Generate URL ke route download yang aman
                $safeUrls[$key] = route('bta.secure.file.download', ['token' => $tokenForUrl]);
            } else {
                $safeUrls[$key] = null;
            }
        }


        return view('bta.analyst.review.index', compact('nasabah', 'safeUrls'));
    }

    public function downloadFile($token)
    {
        try {
            // 1. Decode token dari URL
            $decodedToken = base64_decode($token);

            // 2. Dekripsi token untuk mendapatkan payload
            $decryptedPayload = Crypt::decrypt($decodedToken);

            // 3. Parse JSON payload
            $payload = json_decode($decryptedPayload, true);

            if (!$payload || !isset($payload['file_path']) || !isset($payload['expires_at'])) {
                abort(404); // Payload tidak valid
            }

            // 4. Periksa apakah token sudah kadaluarsa
            if ($payload['expires_at'] < time()) {
                abort(404); // Token expired
            }

            $pathToFile = $payload['file_path'];

            // 5. Gunakan Storage facade untuk mengakses file
            if (!Storage::exists($pathToFile)) {
                abort(404); // File tidak ditemukan di storage
            }

            $fullPath = Storage::path($pathToFile);
            $mimeType = Storage::mimeType($pathToFile);

            // Return file sebagai response
            return response()->file($fullPath, ['Content-Type' => $mimeType]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Jika dekripsi gagal, artinya token tidak valid
            abort(404);
        } catch (\Exception $e) {
            // Tangani error lainnya
            abort(404);
        }
    }

    public function analystUpdate(UpdateAnalystRequest $request)
    // public function analystUpdate(Request $request)
    {
        $nasabah = Nasabah::findOrFail($request->id);

        // 1. Mapping Status Rekomendasi
        $statusMap = [
            'disetujui'    => 'approve',
            'ditolak'      => 'reject',
            'dikembalikan' => 'revisi',
        ];

        // 2. Update Data Utama & Keuangan
        // $nasabah->fill($request->only([
        //     'plafond',
        //     'nilai_angsuran',
        //     'biaya_bank',
        //     'biaya_asuransi',
        //     'biaya_koperasi',
        //     'simpanan_koperasi',
        //     'gaji_bersih',
        //     'mulai_pinjaman',
        //     'akhir_pinjaman',
        //     'dbr',
        //     'jangka_waktu',
        //     'suku_bunga',
        //     'tujuan_penggunaan',
        //     'catatan_analyst'
        // ]));

        $nasabah->persetujuan_analyst           = $request->plafond;
        $nasabah->nilai_angsuran    = $request->nilai_angsuran;
        $nasabah->biaya_bank        = $request->biaya_bank;
        $nasabah->biaya_asuransi    = $request->biaya_asuransi;
        $nasabah->biaya_koperasi    = $request->biaya_koperasi;
        $nasabah->simpanan_koperasi = $request->simpanan_koperasi;
        $nasabah->gaji_bersih       = $request->gaji_bersih;
        $nasabah->mulai_pinjaman = $request->mulai_pinjaman;
        $nasabah->akhir_pinjaman = $request->akhir_pinjaman;
        $nasabah->dbr = $request->dbr;
        $nasabah->jangka_waktu = $request->jangka_waktu;
        $nasabah->tipe_bunga = $request->tipe_bunga;
        $nasabah->suku_bunga = $request->suku_bunga;
        $nasabah->tujuan_penggunaan = $request->tujuan_penggunaan;
        $nasabah->catatan_analyst = $request->catatan_analyst;

        $nasabah->status = $statusMap[$request->rekomendasi];
        $nasabah->analyst_id = auth('bta')->id();
        $nasabah->position = 'analyst';

        // 3. Update Status Dokumen (Mapping Field Request -> Database)

        // KTP
        $nasabah->status_ktp = $request->status_ktp;
        if ($request->status_ktp == "approve") {
            $nasabah->note_ktp = null;
        } else {
            $nasabah->note_ktp = $request->note_ktp;
        }

        // NPWP
        $nasabah->status_npwp = $request->status_npwp;
        if ($request->status_npwp == "approve") {
            $nasabah->note_npwp = null;
        } else {
            $nasabah->note_npwp    = $request->note_npwp;
        }

        // ID CARD
        $nasabah->status_id_card   = $request->status_id_card;
        if ($request->status_id_card == "approve") {
            $nasabah->note_id_card = null;
        } else {
            $nasabah->note_id_card = $request->note_id_card;
        }

        // KK
        $nasabah->status_kk = $request->status_kk;
        if ($request->status_kk == "approve") {
            $nasabah->note_kk = null;
        } else {
            $nasabah->note_kk = $request->note_kk;
        }

        // SLIP GAJI
        $nasabah->status_slip_gaji   = $request->status_slip_gaji;
        if ($request->status_slip_gaji == "approve") {
            $nasabah->note_slip_gaji = null;
        } else {
            $nasabah->note_slip_gaji = $request->note_slip_gaji;
        }

        // SK KARYAWAN
        $nasabah->status_sk_karyawan = $request->status_sk_karyawan;
        if ($request->status_sk_karyawan == "approve") {
            $nasabah->note_sk_karyawan = null;
        } else {
            $nasabah->note_sk_karyawan = $request->note_sk_karyawan;
        }

        // SLIK
        $nasabah->status_form_slik = $request->status_form_slik;
        if ($request->status_form_slik == "approve") {
            $nasabah->note_form_slik = null;
        } else {
            $nasabah->note_form_slik = $request->note_form_slik;
        }

        // FORM CHECKLIST BTA
        $nasabah->status_form_bta = $request->status_form_bta;
        if ($request->status_form_bta == "approve") {
            $nasabah->note_form_bta = null;
        } else {
            $nasabah->note_form_bta = $request->note_form_bta;
        }

        // MUTASI REKENING
        $nasabah->status_mutasi_rekening = $request->status_mutasi_rekening;
        if ($request->status_mutasi_rekening == "approve") {
            $nasabah->note_mutasi_rekening = null;
        } else {
            $nasabah->note_mutasi_rekening = $request->note_mutasi_rekening;
        }

        // FORM CHECKLIST
        $nasabah->status_form_checklist = $request->status_form_checklist;
        if ($request->status_form_checklist == "approve") {
            $nasabah->note_form_checklist = null;
        } else {
            $nasabah->note_form_checklist = $request->note_form_checklist;
        }

        // BUKU TABUNGAN
        $nasabah->status_buku_tabungan = $request->status_buku_tabungan;
        if ($request->status_buku_tabungan == "approve") {
            $nasabah->note_buku_tabungan = null;
        } else {
            $nasabah->note_buku_tabungan = $request->note_buku_tabungan;
        }

        // FORM KREDIT
        $nasabah->status_form_kredit = $request->status_form_kredit;
        if ($request->status_form_kredit == "approve") {
            $nasabah->note_form_kredit = null;
        } else {
            $nasabah->note_form_kredit = $request->note_form_kredit;
        }

        //  SELFIE
        $nasabah->status_selfie = $request->status_selfie;
        if ($request->status_selfie == "approve") {
            $nasabah->note_selfie = null;
        } else {
            $nasabah->note_selfie = $request->note_selfie;
        }


        //  video asuransi
        $nasabah->status_video_asuransi = $request->status_video_asuransi;
        if ($request->status_video_asuransi == "approve") {
            $nasabah->note_video_asuransi = null;
        } else {
            $nasabah->note_video_asuransi = $request->note_video_asuransi;
        }

        //  video interview
        $nasabah->status_video_interview = $request->status_video_interview;
        if ($request->status_video_interview == "approve") {
            $nasabah->note_video_interview = null;
        } else {
            $nasabah->note_video_interview = $request->note_video_interview;
        }

        $nasabah->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Analisis berhasil disimpan dengan status: ' . $nasabah->status
        ]);
    }
}
