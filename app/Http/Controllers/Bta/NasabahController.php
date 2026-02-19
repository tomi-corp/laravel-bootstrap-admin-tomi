<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bta\Nasabah\NasabahInsertRequest;
use App\Models\bank;
use App\Models\dati;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\nasabah;
use App\Models\provinsi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\FuncCall;
use Yajra\DataTables\Facades\DataTables;

class NasabahController extends Controller
{

    private function terbilang($nilai)
    {
        // Handle nilai negatif
        $minus = '';
        if ($nilai < 0) {
            $minus = 'Minus ';
            $nilai = abs($nilai);
        }

        // Handle nilai nol
        if ($nilai == 0) {
            return 'Nol';
        }

        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp = "";

        if ($nilai < 12) {
            $temp = $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->terbilang($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $puluh = floor($nilai / 10);
            $satuan = $nilai % 10;
            $temp = $this->terbilang($puluh) . " Puluh";
            if ($satuan > 0) {
                $temp .= " " . $this->terbilang($satuan);
            }
        } else if ($nilai < 200) {
            $temp = "Seratus";
            $sisa = $nilai % 100;
            if ($sisa > 0) {
                $temp .= " " . $this->terbilang($sisa);
            }
        } else if ($nilai < 1000) {
            $ratus = floor($nilai / 100);
            $sisa = $nilai % 100;
            $temp = $this->terbilang($ratus) . " Ratus";
            if ($sisa > 0) {
                $temp .= " " . $this->terbilang($sisa);
            }
        } else if ($nilai < 2000) {
            $temp = "Seribu";
            $sisa = $nilai % 1000;
            if ($sisa > 0) {
                $temp .= " " . $this->terbilang($sisa);
            }
        } else if ($nilai < 1000000) {
            $ribu = floor($nilai / 1000);
            $sisa = $nilai % 1000;
            $temp = $this->terbilang($ribu) . " Ribu";
            if ($sisa > 0) {
                $temp .= " " . $this->terbilang($sisa);
            }
        } else if ($nilai < 1000000000) {
            $juta = floor($nilai / 1000000);
            $sisa = $nilai % 1000000;
            $temp = $this->terbilang($juta) . " Juta";
            if ($sisa > 0) {
                $temp .= " " . $this->terbilang($sisa);
            }
        } else if ($nilai < 1000000000000) {
            $miliar = floor($nilai / 1000000000);
            $sisa = $nilai % 1000000000;
            $temp = $this->terbilang($miliar) . " Miliar";
            if ($sisa > 0) {
                $temp .= " " . $this->terbilang($sisa);
            }
        } else if ($nilai < 1000000000000000) {
            $triliun = floor($nilai / 1000000000000);
            $sisa = $nilai % 1000000000000;
            $temp = $this->terbilang($triliun) . " Triliun";
            if ($sisa > 0) {
                $temp .= " " . $this->terbilang($sisa);
            }
        }

        return $minus . trim($temp);
    }

    public function nasabahRevisi($no_ktp)
    {
        // Di dalam method edit/update view
        $nasabah = Nasabah::where('no_ktp', $no_ktp)
            ->with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'bank', 'dati'])
            ->where('position', 'analyst')
            ->where('status', 'revisi')
            ->first();

        if (!$nasabah) {
            return abort(404);
        }

        // Mapping konfigurasi file: [field_path, field_status, field_note, label]
        $fileConfigs = [
            'ktp' => ['ktp', 'status_ktp', 'note_ktp', 'KTP'],
            'npwp' => ['npwp', 'status_npwp', 'note_npwp', 'NPWP'],
            'selfie' => ['selfie', 'status_selfie', 'note_selfie', 'Selfie dengan KTP'],
            'kk' => ['kk', 'status_kk', 'note_kk', 'Kartu Keluarga'],
            'id_card' => ['id_card', 'status_id_card', 'note_id_card', 'ID Card'],
            'sk_karyawan' => ['sk_karyawan', 'status_sk_karyawan', 'note_sk_karyawan', 'SK Karyawan'],
            'slip_gaji' => ['slip_gaji', 'status_slip_gaji', 'note_slip_gaji', 'Slip Gaji'],
            'buku_tabungan' => ['buku_tabungan', 'status_buku_tabungan', 'note_buku_tabungan', 'Buku Tabungan'],
            'mutasi_rekening' => ['mutasi_rekening', 'status_mutasi_rekening', 'note_mutasi_rekening', 'Mutasi Rekening'],
            'form_slik' => ['form_slik', 'status_form_slik', 'note_form_slik', 'Form SLIK'],
            'form_kredit' => ['form_kredit', 'status_form_kredit', 'note_form_kredit', 'Form Kredit'],
            'form_bta' => ['form_bta', 'status_form_bta', 'note_form_bta', 'Form BTA'],
            'form_checklist' => ['form_checklist', 'status_form_checklist', 'note_form_checklist', 'Checklist Verifikasi'],
        ];

        $documents = [];
        $fileMeta = []; // Untuk dikirim ke view: status, note, canUpload

        // foreach ($fileConfigs as $key => [$pathField, $statusField, $noteField, $label]) {
        //     $documents[$key] = $nasabah->$pathField;

        //     $status = $nasabah->$statusField ?? 'pending';
        //     $note = $nasabah->$noteField;

        //     // Hanya bisa upload ulang jika status reject ATAU belum ada file sama sekali
        //     $canUpload = ($status === 'reject') || (empty($nasabah->$pathField) && $status !== 'approve');

        //     $fileMeta[$key] = [
        //         'label' => $label,
        //         'status' => $status,
        //         'note' => $note,
        //         'can_upload' => $canUpload,
        //         'path' => $nasabah->$pathField,
        //     ];
        // }

        // Di NasabahController@edit
        foreach ($fileConfigs as $key => [$pathField, $statusField, $noteField, $label]) {
            $documents[$key] = $nasabah->$pathField;
            $status = $nasabah->$statusField ?? 'pending';

            // Pastikan return BOOLEAN, bukan string
            $canUpload = ($status === 'reject') || (empty($nasabah->$pathField) && $status !== 'approve');
            $note = $nasabah->$noteField;

            $fileMeta[$key] = [
                'label' => $label,
                'status' => $status,
                'note' => $note,
                'can_upload' => (bool) $canUpload, // CAST ke boolean
                'path' => $nasabah->$pathField,
            ];
        }



        // Generate secure URLs seperti kode Anda sebelumnya
        $safeUrls = [];
        foreach ($documents as $key => $fileName) {
            if ($fileName) {
                $payload = [
                    'file_path' => 'bta/' . $fileName,
                    'expires_at' => now()->addHours(24)->timestamp,
                ];
                $encryptedToken = Crypt::encrypt(json_encode($payload));
                $tokenForUrl = base64_encode($encryptedToken);
                $safeUrls[$key] = route('bta.secure.file.download', ['token' => $tokenForUrl]);
            } else {
                $safeUrls[$key] = null;
            }
        }

        $provinsis = Provinsi::get();
        $banks = Bank::get();

        return view('bta.nasabah.edit', compact(
            'nasabah',
            'safeUrls',
            'provinsis',
            'banks',
            'fileMeta'
        ));
    }


    public function nasabahIndex()
    {
        return view('bta.nasabah.index');
    }

    public function nasabahDetail(Request $req)
    {
        $nasabah = nasabah::where('id', $req->id)
            ->with([
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa',
                'bank',
                'dati'
            ])
            ->first();


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

        return response()->json([
            'status' => 200,
            'data' => $nasabah,
            'file' => $safeUrls
        ], 200);
    }

    public function revisiTable()
    {
        $nasabah = nasabah::select([
            'id',
            'no_ktp',              // pastikan kolom ini ada di DB
            'nama_lengkap',
            'tanggal_lahir',
            'jenis_kelamin',
            'no_telp',
            'status_perkawinan',
            'nama_perusahaan',
            'status_pegawai',
            'status',
            'catatan_analyst',
            // tambahkan kolom lain jika perlu
        ])
            ->where('position', 'analyst')
            ->where('status', 'revisi')
            ->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                // return '<button class="btn btn-sm btn-primary mr-2" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i> </button>';
                $revisiUrl = route('bta.nasabah.revisi', $row->no_ktp);
                return '
                    <a href="' . $revisiUrl . '" class="btn btn-sm btn-primary mr-2" title="Revisi">
                        <i class="mdi mdi-pencil"></i> Revisi
                    </a>
                ';
            })

            ->toJson();
    }

    public function disetujuiTable()
    {
        $nasabah = nasabah::select([
            'id',
            'no_ktp',              // pastikan kolom ini ada di DB
            'nama_lengkap',
            'tanggal_lahir',
            'jenis_kelamin',
            'no_telp',
            'status_perkawinan',
            'nama_perusahaan',
            'status_pegawai',
            'status',
            'catatan_analyst',
            // tambahkan kolom lain jika perlu
        ])
            ->where('position', 'analyst')
            ->where('status', 'approve')
            ->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            // ->addColumn('action', function ($row) {
            //     return '
            //         <a href="' . route('bta.analyst.review', $row->no_ktp) . '" target="_blank" class="btn btn-warning btn-sm">Review</a>
            //     ';
            // })
            ->addColumn('action', function ($row) {
                return '
                <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-sm btn-info mr-2" onclick="download(' . $row->id . ')"><i class="mdi mdi-download"></i> </button>
                <button class="btn btn-sm btn-primary mr-2" onclick="uploadSetujuiAnalyst(' . $row->id . ')"><i class="mdi mdi-pencil"></i> </button>
                </div>';
                // return '<button class="btn btn-sm btn-primary mr-2" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i> </button>';
            })

            ->toJson();
    }


    public function ditolakTable()
    {
        $nasabah = nasabah::select([
            'id',
            'no_ktp',              // pastikan kolom ini ada di DB
            'nama_lengkap',
            'tanggal_lahir',
            'jenis_kelamin',
            'no_telp',
            'status_perkawinan',
            'nama_perusahaan',
            'status_pegawai',
            'status',
            'catatan_analyst',
            // tambahkan kolom lain jika perlu
        ])
            ->where('position', 'analyst')
            ->where('status', 'reject')
            ->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->toJson();
    }

    public function selesaiTable()
    {
        $nasabah = nasabah::select([
            'id',
            'no_ktp',              // pastikan kolom ini ada di DB
            'nama_lengkap',
            'tanggal_lahir',
            'jenis_kelamin',
            'no_telp',
            'status_perkawinan',
            'nama_perusahaan',
            'status_pegawai',
            'status',
            'catatan_analyst',
            // tambahkan kolom lain jika perlu
        ])
            ->where('position', 'credam')
            ->where('status', 'approve')
            ->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->toJson();
    }

    public function credamTable()
    {
        $nasabah = nasabah::select([
            'id',
            'no_ktp',              // pastikan kolom ini ada di DB
            'nama_lengkap',
            'tanggal_lahir',
            'jenis_kelamin',
            'no_telp',
            'status_perkawinan',
            'nama_perusahaan',
            'status_pegawai',
            'status',
            'catatan_analyst',
            // tambahkan kolom lain jika perlu
        ])
            ->where('position', 'credam')
            ->where('status', 'pending')
            ->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->toJson();
    }


    public function analystTable()
    {
        $nasabah = nasabah::select([
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
            ->where('position', 'analyst')
            ->where('status', 'pending')
            ->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->toJson();
    }

    public function nasabahInsert(NasabahInsertRequest $request)
    {
        try {
            $nasabah = new nasabah();

            // Assign data identitas
            $nasabah->nama_lengkap = $request->nama_lengkap;
            $nasabah->nama_singkat = $request->nama_singkat;
            $nasabah->jenis_kelamin = $request->jenis_kelamin;
            $nasabah->tempat_lahir = $request->tempat_lahir;
            $nasabah->tanggal_lahir = $request->tanggal_lahir;
            $nasabah->nama_ibu = $request->nama_ibu;
            $nasabah->no_telp = $request->no_telp;
            $nasabah->agama = $request->agama;
            $nasabah->pendidikan_terakhir = $request->pendidikan; // Catatan: Kolom di DB adalah pendidikan_terakhir
            $nasabah->status_perkawinan = $request->status_perkawinan;

            // Assign data pasangan
            $nasabah->nama_pasangan = $request->nama_pasangan;
            $nasabah->tgl_lahir_pasangan = $request->tgl_lahir_pasangan;

            // Assign data alamat
            $nasabah->alamat_rumah = $request->alamat_rumah;
            $nasabah->provinsi_id = $request->provinsi;
            $nasabah->kota_id = $request->kota;
            $nasabah->kecamatan_id = $request->kecamatan;
            $nasabah->kelurahan_id = $request->kelurahan;
            $nasabah->kode_pos = $request->kodePos;
            $nasabah->kode_dati_id = $request->kode_dati;

            // Assign data pekerjaan
            $nasabah->nama_perusahaan = $request->nama_perusahaan;
            $nasabah->bidang_usaha = $request->bidang_usaha;
            $nasabah->alamat_kerja = $request->alamat_kerja;
            $nasabah->nip = $request->nip;
            $nasabah->status_pegawai = $request->status_pegawai;
            $nasabah->tgl_akhir_kontrak = $request->tgl_akhir_kontrak;

            // Assign data bank
            $nasabah->bank_id = $request->bankSelect; // Catatan: Gunakan nama input yang sesuai
            $nasabah->nama_pemilik_rek = $request->nama_pemilik_rek;
            $nasabah->no_rek = $request->no_rek;

            // Assign data KTA Multiguna
            $nasabah->plafond = $request->plafond;
            $nasabah->jangka_waktu = $request->jangka_waktu;
            $nasabah->suku_bunga = $request->suku_bunga;
            $nasabah->nilai_angsuran = $request->nilai_angsuran;
            $nasabah->biaya_bank = $request->biaya_bank;
            $nasabah->biaya_asuransi = $request->biaya_asuransi;
            $nasabah->biaya_koperasi = $request->biaya_koperasi;
            $nasabah->simpanan_koperasi = $request->simpanan_koperasi;
            $nasabah->tujuan_penggunaan = $request->tujuan_penggunaan;
            $nasabah->gaji_bersih = $request->gaji_bersih;
            $nasabah->mulai_pinjaman = $request->mulai_pinjaman;
            $nasabah->akhir_pinjaman = $request->akhir_pinjaman;
            $nasabah->dbr = $request->dbr;

            // Assign nomor KTP dan NPWP (disimpan juga sebagai kolom terpisah)
            $nasabah->no_ktp = $request->no_ktp;
            $nasabah->no_npwp = $request->no_npwp;

            // --- 3. Proses Upload File ---
            // Definisikan mapping antara input form dan kolom di database
            $fileMappings = [
                'upload_ktp' => 'ktp',
                // 'upload_selfie' => 'selfie',
                'upload_npwp' => 'npwp',
                'upload_kk' => 'kk',
                'upload_sk' => 'sk_karyawan',
                'upload_slip' => 'slip_gaji',
                'upload_buku_tabungan' => 'buku_tabungan',
                'upload_mutasi' => 'mutasi_rekening',
                'upload_slik' => 'form_slik',
                'upload_kredit' => 'form_kredit',
                'upload_bta' => 'form_bta',
                'upload_checklist' => 'form_checklist',
            ];

            foreach ($fileMappings as $inputName => $columnName) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    // Generate nama file unik
                    $filename = time() . '_' . uniqid() . '_' . $columnName . '.' . $file->getClientOriginalExtension();
                    // Simpan ke storage (misalnya: public/nasabah_files/)
                    // $path = $file->storeAs('nasabah_files', $filename, 'public');
                    $file->storeAs('bta', $filename);
                    // Simpan path ke kolom database
                    $nasabah->{$columnName} = $filename;
                }
            }

            // Kolom lainnya
            $nasabah->admin_id = auth('bta')->id(); // Misalnya ID admin yang sedang login
            $nasabah->position = 'analyst'; // Atau sesuaikan
            $nasabah->status = 'pending'; // Status awal

            // Simpan ke database
            $nasabah->save();

            // --- 4. Return Response ---
            return response()->json([
                'message' => 'Data nasabah berhasil disimpan.',
                'data' => $nasabah // Opsional: kembalikan data yang disimpan
            ], 201); // Gunakan 201 Created untuk resource baru

        } catch (\Exception $e) {
            // Log error untuk debugging
            // \Log::error('Error inserting nasabah: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data ke database.'
            ], 500);
        }
    }

    public function nasabahUpdate(Request $req)
    {

        $nasabah = nasabah::where('no_ktp', $req->no_ktp)
            ->where('position', 'analyst')
            ->where('status', 'revisi')
            ->first();

        if (!$nasabah) {
            return response()->json(['message' => 'Data nasabah tidak ditemukan'], 404);
        }

        // === Identitas ===
        $nasabah->nama_lengkap = $req->nama_lengkap;
        $nasabah->nama_singkat = $req->nama_singkat;
        $nasabah->jenis_kelamin = $req->jenis_kelamin;
        $nasabah->tempat_lahir = $req->tempat_lahir;
        $nasabah->tanggal_lahir = $req->tanggal_lahir;
        $nasabah->nama_ibu = $req->nama_ibu;
        $nasabah->no_telp = $req->no_telp;
        $nasabah->agama = $req->agama;
        $nasabah->pendidikan_terakhir = $req->pendidikan_terakhir;
        $nasabah->status_perkawinan = $req->status_perkawinan;

        // === Pasangan ===
        if ($req->status_perkawinan === 'Kawin') {
            $nasabah->nama_pasangan = $req->nama_pasangan;
            $nasabah->tgl_lahir_pasangan = $req->tgl_lahir_pasangan;
        }

        // === Alamat ===
        $nasabah->alamat_rumah = $req->alamat_rumah;
        $nasabah->provinsi_id = $req->provinsi_id;
        $nasabah->kota_id = $req->kota_id;
        $nasabah->kecamatan_id = $req->kecamatan_id;

        // Ambil data kelurahan dengan aman
        $kelurahan = Kelurahan::where('id', $req->kelurahan_id)->first();
        $nasabah->kelurahan_id = $req->kelurahan_id;
        $nasabah->kode_pos = $kelurahan ? $kelurahan->kodepos : $nasabah->kode_pos; // Fallback ke nilai lama jika kelurahan tidak ditemukan
        $nasabah->kode_dati_id = $req->kode_dati_id;

        // === Pekerjaan ===
        $nasabah->nama_perusahaan = $req->nama_perusahaan;
        $nasabah->bidang_usaha = $req->bidang_usaha;
        $nasabah->alamat_kerja = $req->alamat_kerja;
        $nasabah->nip = $req->nip;
        $nasabah->status_pegawai = $req->status_pegawai;
        $nasabah->no_ktp = $req->no_ktp;
        $nasabah->no_npwp = $req->no_npwp;

        if ($req->status_pegawai === 'Karyawan Tidak Tetap') {
            $nasabah->tgl_akhir_kontrak = $req->tgl_akhir_kontrak;
        }

        // === Bank ===
        $nasabah->bank_id = $req->bank_id;
        $nasabah->no_rek = $req->no_rek;
        $nasabah->nama_pemilik_rek = $req->nama_pemilik_rek;

        // === LOGIKA UPLOAD FILE ===
        $tglInput = now()->format('Ymd');
        $namaLengkapSanitized = Str::slug($req->nama_lengkap, '_');
        $randomNumber = rand(1000, 9999);

        $fileFields = [
            'upload_ktp' => 'ktp',
            'upload_npwp' => 'npwp',
            'upload_selfie' => 'selfie',
            'upload_kk' => 'kk',
            'upload_idcard' => 'id_card',
            'upload_sk' => 'sk_karyawan',
            'upload_slip' => 'slip_gaji',
            'upload_bukutabungan' => 'buku_tabungan',
            'upload_mutasi' => 'mutasi_rekening',
            'upload_slik' => 'form_slik',
            'upload_kredit' => 'form_kredit',
            'upload_bta' => 'form_bta',
            'upload_checklist' => 'form_checklist'
        ];

        foreach ($fileFields as $requestField => $dbField) {
            // Cek apakah ada file yang diupload untuk field ini
            if ($req->hasFile($requestField)) {
                $file = $req->file($requestField);

                // Validasi file
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                $extension = strtolower($file->getClientOriginalExtension());

                if (!in_array($extension, $allowedExtensions)) {
                    return response()->json([
                        'message' => "Format file {$requestField} tidak valid. Hanya JPG, PNG, PDF yang diperbolehkan."
                    ], 422);
                }

                if ($file->getSize() > 5120 * 1024) { // 5 MB
                    return response()->json([
                        'message' => "Ukuran file {$requestField} melebihi batas maksimal 5 MB."
                    ], 422);
                }

                // Generate nama file unik
                $fileName = "{$tglInput}_{$namaLengkapSanitized}_{$randomNumber}_{$requestField}.{$extension}";

                // Simpan file ke storage
                $file->storeAs('bta', $fileName);
                // Update path file di database
                $nasabah->{$dbField} = $fileName;

                // === RESET STATUS & NOTE ===
                $statusField = "status_{$dbField}";
                $noteField = "note_{$dbField}";

                // Set status ke pending (karena file baru perlu direview ulang)
                $nasabah->{$statusField} = 'pending';

                // Hapus note revisi sebelumnya (karena file sudah diganti)
                $nasabah->{$noteField} = null;
            }
            // Jika TIDAK ada file yang diupload:
            // - Biarkan $nasabah->{$dbField} tetap (file lama)
            // - Biarkan status dan note tetap seperti sebelumnya
        }

        // === Metadata ===
        $nasabah->admin_id = auth('bta')->user()->id;
        $nasabah->position = 'analyst';
        $nasabah->status = 'pending'; // Status nasabah tetap revisi sampai analyst approve

        $nasabah->save();

        return response()->json([
            'message' => 'Data nasabah berhasil disimpan.'
        ]);
    }


    // public function downloadPdf(Request $request)
    // {
    //     $data = [
    //         'nomor' => $request->nomor ?? '001',
    //         'bulan' => $request->bulan ?? date('m'),
    //         'tahun' => $request->tahun ?? date('Y'),
    //         'nomor_rekening' => $request->nomor_rekening ?? '1234567890',

    //         'debitur_nama' => $request->debitur_nama ?? 'MUHAMMAD HIMAWAN ARDIANSYAH',
    //         'debitur_alamat' => $request->debitur_alamat ?? 'KP. PIPITAN RT 008/002 PIPITAN WALANTAKA KOTA SERANG',
    //         'debitur_ktp' => $request->debitur_ktp ?? '3604100405950714',

    //         'jumlah_pinjaman' => $request->jumlah_pinjaman ?? 50000000,
    //         'jumlah_pinjaman_terbilang' => $request->jumlah_pinjaman_terbilang ?? 'LIMA PULUH JUTA RUPIAH',
    //         'bunga' => $request->bunga ?? '12',
    //         'jangka_waktu' => $request->jangka_waktu ?? '24',
    //         'angsuran' => $request->angsuran ?? 2500000,
    //         'angsuran_terbilang' => $request->angsuran_terbilang ?? 'DUA JUTA LIMA RATUS RIBU RUPIAH',
    //         'tujuan_penggunaan' => $request->tujuan_penggunaan ?? 'Konsumtif',

    //         'jaminan_1' => $request->jaminan_1 ?? 'BPKB Kendaraan Bermotor',
    //         'jaminan_2' => $request->jaminan_2 ?? 'Sertifikat Tanah',
    //         'jaminan_3' => $request->jaminan_3 ?? '',
    //         'jaminan_4' => $request->jaminan_4 ?? '',

    //         'tempat_ttd' => $request->tempat_ttd ?? 'Bandung',
    //         'tanggal_ttd' => $request->tanggal_ttd ?? now(),
    //     ];


    //     $pdf = Pdf::loadView('bta.pdf.perjanjian-kredit', $data)
    //         ->setPaper('a4', 'portrait')
    //         ->setOption('margin-top', 25.4)   // 2.54 cm
    //         ->setOption('margin-bottom', 25.4) // 2.54 cm
    //         ->setOption('margin-left', 25.4)   // 2.54 cm
    //         ->setOption('margin-right', 25.4)  // 2.54 cm
    //         ->setOption('dpi', 300)
    //         ->setOption('isHtml5ParserEnabled', true)
    //         ->setOption('isFontSubsettingEnabled', true)
    //         ->setOption('isRemoteEnabled', true)
    //         ->setOption('isJavascriptEnabled', false);

    //     return $pdf->stream('PERJANJIAN_KREDIT_BTA_' . now()->format('YmdHis') . '.pdf');
    // }

    public function downloadPdf(Request $req)
    {

        $nasabah = nasabah::where('id', $req->id)
            ->with([
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa',
                'bank',
                'dati'
            ])
            ->where('position', 'analyst')
            ->where('status', 'approve')
            ->first();


        $plafond = $nasabah->plafond;
        $angsuran = $nasabah->nilai_angsuran;

        $data = [
            'no_surat'      => '__',
            'bulan'         => '__',
            'tahun'         => date('Y'),

            'nama_debitur'  => $nasabah->nama_lengkap,
            'alamat'        => $nasabah->alamat_rumah . ',' . $nasabah->desa->desa . ' ,' . $nasabah->kecamatan->kecamatan . ' ,' . $nasabah->kabupaten->kabupaten . ' ,' . $nasabah->provinsi->provinsi,
            'no_ktp'        => $nasabah->no_ktp,

            'plafond'       => $plafond,
            'terbilang_plafond' => $this->terbilang($plafond), // Generate otomatis

            'bunga'         => $nasabah->suku_bunga,
            'jangka'        =>  $nasabah->jangka_waktu,

            'angsuran'      => $angsuran,
            'terbilang_angsuran' => $this->terbilang($angsuran), // Generate otomatis

            'tujuan'        => $nasabah->tujuan_penggunaan,
            'agunan'        => null, // Kosongkan jika tidak ada, otomatis jadi garis

            'tanggal_ttd'   => Carbon::now()->translatedFormat('d F Y'),
        ];


        $pdf = Pdf::loadView('bta.pdf.perjanjian-kredit', ['data' => $data]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('PK_' . $data['nama_debitur'] . '.pdf');
    }


    public function nasabahUploadSetuju(Request $req)
    {
        $nasabah = nasabah::where('id', $req->uploadSetujuAnalyst)->first();
        $tglInput = now()->format('Ymd');
        $namaLengkapSanitized = Str::slug($req->nama_lengkap, '_');
        $randomNumber = rand(1000, 9999);

        $fileUploadKredit = $req->file('uploadFotoAkadKredit');
        $extension = $fileUploadKredit->getClientOriginalExtension();
        $fileNameAkadKredit = "{$tglInput}_{$namaLengkapSanitized}_{$randomNumber}_foto_akad_kredit.{$extension}";
        $fileUploadKredit->storeAs('bta', $fileNameAkadKredit);
        $nasabah->foto_akad_kredit = $fileNameAkadKredit;

        $fileTandaTangan = $req->file('uploadPkTertandaTangan');
        $extensionTandaTangan = $fileTandaTangan->getClientOriginalExtension();
        $fileNameTandaTangan = "{$tglInput}_{$namaLengkapSanitized}_{$randomNumber}_tanda_tangan_pk.{$extensionTandaTangan}";
        $fileTandaTangan->storeAs('bta', $fileNameTandaTangan);
        $nasabah->pk_tanda_tangan = $fileNameTandaTangan;

        $nasabah->position = 'credam';
        $nasabah->status = 'pending';
        $nasabah->save();
        return response()->json([
            'message' => 'Tanda tangan PK & Foto Akad Berhasil di Upload!'
        ]);
    }


    public function nasabahTambahIndex()
    {
        // 
        $provinsis = provinsi::get();
        $banks = bank::get();
        return view('bta.nasabah.tambah', compact('provinsis', 'banks'));
    }
}
