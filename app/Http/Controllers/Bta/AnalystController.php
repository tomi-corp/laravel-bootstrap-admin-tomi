<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bta\Analyst\UpdateAnalystRequest;
use App\Models\nasabah;
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
                'no_ktp',              // pastikan kolom ini ada di DB
                'nama_lengkap',
                'tanggal_lahir',
                'jenis_kelamin',
                'no_telp',
                'status_perkawinan',
                'nama_perusahaan',
                'status_pegawai',
                // tambahkan kolom lain jika perlu
            ])

            // opsional: filter hanya nasabah dengan status tertentu
            // ->where('status', 'pending')
            ->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('bta.analyst.review', $row->no_ktp) . '" target="_blank" class="btn btn-warning btn-sm">Review</a>
                ';
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
            ->first();

        if (!isset($nasabah)) {
            return abort(404);
        }

        // --- Baris baru untuk membuat URL aman ---
        $documents = [
            'ktp' => $nasabah->ktp,
            'npwp' => $nasabah->npwp,
            'id_card' => $nasabah->id_card,
            'kk' => $nasabah->kk,
            'selfie' => $nasabah->selfie,
            'slip_gaji' => $nasabah->slip_gaji,
            'sk_karyawan' => $nasabah->sk_karyawan,
            'form_slik' => $nasabah->form_slik,
            'form_kredit' => $nasabah->form_kredit,
            'buku_tabungan' => $nasabah->buku_tabungan,
            'form_checklist' => $nasabah->form_checklist,
            'mutasi_rekening' => $nasabah->mutasi_rekening,
            'form_bta' => $nasabah->form_bta,
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
        // --- Selesai ---

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
        $nasabah = Nasabah::findOrFail($request->nasabah_id);

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

        $nasabah->plafond           = $request->plafond;
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
        $nasabah->suku_bunga = $request->suku_bunga;
        $nasabah->tujuan_penggunaan = $request->tujuan_penggunaan;
        $nasabah->catatan_analyst = $request->catatan_analyst;

        $nasabah->status = $statusMap[$request->rekomendasi];
        $nasabah->analyst_id = auth('bta')->id();
        $nasabah->position = 'analyst';

        // 3. Update Status Dokumen (Mapping Field Request -> Database)
        $nasabah->status_npwp      = $request->status_npwp;
        $nasabah->note_npwp    = $request->note_npwp;

        $nasabah->status_ktp    = $request->status_ktp;
        $nasabah->note_ktp      = $request->note_ktp;
        $nasabah->status_id_card   = $request->status_id_card;
        $nasabah->note_id_card     = $request->note_id_card;
        $nasabah->status_kk     = $request->status_kk;
        $nasabah->note_kk       = $request->note_kk;
        $nasabah->status_slip_gaji   = $request->status_slip_gaji;
        $nasabah->note_slip_gaji     = $request->note_slip_gaji;
        $nasabah->status_sk_karyawan     = $request->status_sk_karyawan;
        $nasabah->note_sk_karyawan       = $request->note_sk_karyawan;
        $nasabah->status_mutasi_rekening = $request->status_mutasi_rekening;
        $nasabah->note_mutasi_rekening   = $request->note_mutasi_rekening;

        // Field tambahan yang ada di request JSON Anda
        $nasabah->status_form_slik     = $request->status_form_slik;
        $nasabah->note_form_slik       = $request->note_form_slik;
        $nasabah->status_form_bta      = $request->status_form_bta;
        $nasabah->note_form_bta        = $request->note_form_bta;

        $nasabah->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Analisis berhasil disimpan dengan status: ' . $nasabah->status
        ]);
    }
}
