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
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
            'video_interview' => ['video_interview', 'status_video_interview', 'note_video_interview', 'Video Interview'],
            'video_asuransi' => ['video_asuransi', 'status_video_asuransi', 'note_video_asuransi', 'Video Asuransi'],
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
            'foto_akad_kredit',
            'pk_tanda_tangan',
            // tambahkan kolom lain jika perlu
        ])
            ->where('position', 'credam')
            ->where('status', 'pending')
            ->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->addColumn('foto_akad_status', function ($row) {
                return $row->foto_akad_kredit
                    ? '<span class="badge badge-success"><i class="mdi mdi-check-circle"></i> Terupload</span>'
                    : '<span class="badge badge-warning"><i class="mdi mdi-alert-circle"></i> Belum</span>';
            })
            ->addColumn('pk_tanda_tangan_status', function ($row) {
                return $row->pk_tanda_tangan
                    ? '<span class="badge badge-success"><i class="mdi mdi-check-circle"></i> Terupload</span>'
                    : '<span class="badge badge-warning"><i class="mdi mdi-alert-circle"></i> Belum</span>';
            })
            ->rawColumns(['foto_akad_status', 'pk_tanda_tangan_status'])
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

            // --- isi data seperti biasa ---
            $nasabah->nama_lengkap = strtoupper($request->nama_lengkap);
            $nasabah->nama_singkat = strtoupper($request->nama_singkat);
            $nasabah->jenis_kelamin = strtoupper($request->jenis_kelamin);
            $nasabah->tempat_lahir = strtoupper($request->tempat_lahir);
            $nasabah->tanggal_lahir = $request->tanggal_lahir;
            $nasabah->nama_ibu = strtoupper($request->nama_ibu);
            $nasabah->no_telp = $request->no_telp;
            $nasabah->agama = strtoupper($request->agama);
            $nasabah->pendidikan_terakhir = strtoupper($request->pendidikan);
            $nasabah->status_perkawinan = strtoupper($request->status_perkawinan);

            // pasangan
            $nasabah->nama_pasangan = strtoupper($request->nama_pasangan);
            $nasabah->tgl_lahir_pasangan = $request->tgl_lahir_pasangan;

            // alamat
            $nasabah->alamat_rumah = strtoupper($request->alamat_rumah);
            $nasabah->provinsi_id = $request->provinsi;
            $nasabah->kota_id = $request->kota;
            $nasabah->kecamatan_id = $request->kecamatan;
            $nasabah->kelurahan_id = $request->kelurahan;
            $nasabah->kode_pos = $request->kodePos;
            $nasabah->kode_dati_id = $request->kode_dati;

            // pekerjaan
            $nasabah->nama_perusahaan = strtoupper($request->nama_perusahaan);
            $nasabah->bidang_usaha = strtoupper($request->bidang_usaha);
            $nasabah->alamat_kerja = strtoupper($request->alamat_kerja);
            $nasabah->nip = $request->nip;
            $nasabah->status_pegawai = strtoupper($request->status_pegawai);
            $nasabah->tgl_akhir_kontrak = $request->tgl_akhir_kontrak;

            // bank
            $nasabah->bank_id = $request->bankSelect;
            $nasabah->nama_pemilik_rek = strtoupper($request->nama_pemilik_rek);
            $nasabah->no_rek = $request->no_rek;

            // kredit
            $nasabah->plafond = $request->plafond;
            $nasabah->tipe_bunga = $request->tipe_bunga;
            $nasabah->jangka_waktu = $request->jangka_waktu;
            $nasabah->suku_bunga = $request->suku_bunga;
            $nasabah->nilai_angsuran = $request->nilai_angsuran;
            $nasabah->biaya_bank = $request->biaya_bank;
            $nasabah->biaya_asuransi = $request->biaya_asuransi;
            $nasabah->biaya_koperasi = $request->biaya_koperasi;
            $nasabah->simpanan_koperasi = $request->simpanan_koperasi;
            $nasabah->tujuan_penggunaan = strtoupper($request->tujuan_penggunaan);
            $nasabah->gaji_bersih = $request->gaji_bersih;
            $nasabah->tanggal_pengajuan = $request->tanggal_pengajuan;
            $nasabah->mulai_pinjaman = $request->mulai_pinjaman;
            $nasabah->akhir_pinjaman = $request->akhir_pinjaman;
            $nasabah->dbr = $request->dbr;

            // identitas
            $nasabah->no_ktp = $request->no_ktp;
            $nasabah->no_npwp = $request->no_npwp;

            // file upload
            $fileMappings = [
                'upload_ktp' => 'ktp',
                'upload_id_card' => 'id_card',
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
                'upload_video_interview' => 'video_interview',
                'upload_video_asuransi'  => 'video_asuransi',
            ];

            foreach ($fileMappings as $inputName => $columnName) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $filename = time() . '_' . uniqid() . '_' . $columnName . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('bta', $filename);
                    $nasabah->{$columnName} = $filename;

                    if ($columnName === "video_interview" || $columnName === "video_asuransi") {
                        $nasabah->{"status_" . $columnName} = "pending";
                    }
                }
            }

            $nasabah->admin_id = auth('bta')->id();
            $nasabah->position = 'analyst';
            $nasabah->status = 'pending';

            $nasabah->save();

            return response()->json([
                'message' => 'Data nasabah berhasil disimpan.',
                'data' => $nasabah
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error database',
                'sql_state' => $e->errorInfo[0],
                'error_code' => $e->errorInfo[1],
                'error_message' => $e->errorInfo[2],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan tidak terduga',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function nasabahUpdate(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'tanggal_pengajuan' => 'required|date|after_or_equal:' . now()->subWeek()->toDateString() . '|before_or_equal:' . now()->addWeek()->toDateString(),
        ], [
            'tanggal_pengajuan.required' => 'Tanggal pengajuan wajib diisi.',
            'tanggal_pengajuan.date' => 'Tanggal pengajuan harus berupa tanggal yang valid.',
            'tanggal_pengajuan.after_or_equal' => 'Tanggal pengajuan tidak boleh kurang dari 1 minggu dari tanggal hari ini.',
            'tanggal_pengajuan.before_or_equal' => 'Tanggal pengajuan tidak boleh lebih dari 1 minggu dari tanggal hari ini.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $nasabah = Nasabah::where('id', $req->id)
            ->where('position', 'analyst')
            ->where('status', 'revisi')
            ->first();

        if (!$nasabah) {
            return response()->json(['message' => 'Data nasabah tidak ditemukan'], 404);
        }

        // === Identitas ===
        $nasabah->nama_lengkap = strtoupper($req->nama_lengkap);
        $nasabah->nama_singkat = strtoupper($req->nama_singkat);
        $nasabah->jenis_kelamin = $req->jenis_kelamin;
        $nasabah->tempat_lahir = strtoupper($req->tempat_lahir);
        $nasabah->tanggal_lahir = $req->tanggal_lahir;
        $nasabah->nama_ibu = strtoupper($req->nama_ibu);
        $nasabah->no_telp = $req->no_telp;
        $nasabah->agama = $req->agama;
        $nasabah->pendidikan_terakhir = $req->pendidikan_terakhir;
        $nasabah->status_perkawinan = $req->status_perkawinan;

        if ($req->status_perkawinan === 'Kawin') {
            $nasabah->nama_pasangan = strtoupper($req->nama_pasangan);
            $nasabah->tgl_lahir_pasangan = $req->tgl_lahir_pasangan;
        } else {
            $nasabah->nama_pasangan = null;
            $nasabah->tgl_lahir_pasangan = null;
        }

        // === Alamat ===
        $nasabah->alamat_rumah = strtoupper($req->alamat_rumah);
        $nasabah->provinsi_id = $req->provinsi_id;
        $nasabah->kota_id = $req->kota_id;
        $nasabah->kecamatan_id = $req->kecamatan_id;

        $kelurahan = Kelurahan::find($req->kelurahan_id);
        $nasabah->kelurahan_id = $req->kelurahan_id;
        $nasabah->kode_pos = $kelurahan ? $kelurahan->kodepos : $req->kode_pos;
        $nasabah->kode_dati_id = $req->kode_dati_id;

        // === Pekerjaan ===
        $nasabah->nama_perusahaan = strtoupper($req->nama_perusahaan);
        $nasabah->bidang_usaha = strtoupper($req->bidang_usaha);
        $nasabah->alamat_kerja = strtoupper($req->alamat_kerja);
        $nasabah->nip = $req->nip;
        $nasabah->status_pegawai = $req->status_pegawai;
        $nasabah->no_ktp = $req->no_ktp;
        $nasabah->no_npwp = $req->no_npwp;

        if ($req->status_pegawai === 'Karyawan Tidak Tetap' || $req->status_pegawai === 'Pensiun') {
            $nasabah->tgl_akhir_kontrak = $req->tgl_akhir_kontrak;
        } else {
            $nasabah->tgl_akhir_kontrak = null;
        }

        // === Bank ===
        $nasabah->bank_id = $req->bank_id;
        $nasabah->no_rek = $req->no_rek;
        $nasabah->nama_pemilik_rek = strtoupper($req->nama_pemilik_rek);

        // === KTA Multiguna ===
        $nasabah->plafond = $this->rupiahToNumber($req->plafond);
        $nasabah->persetujuan_analyst = $this->rupiahToNumber($req->persetujuan_analyst);
        $nasabah->tipe_bunga = $req->tipe_bunga;
        $nasabah->jangka_waktu = $req->jangka_waktu;
        $nasabah->suku_bunga = $req->suku_bunga;
        $nasabah->nilai_angsuran = $this->rupiahToNumber($req->nilai_angsuran);
        $nasabah->biaya_bank = $this->rupiahToNumber($req->biaya_bank);
        $nasabah->biaya_asuransi = $this->rupiahToNumber($req->biaya_asuransi);
        $nasabah->biaya_koperasi = $this->rupiahToNumber($req->biaya_koperasi);
        $nasabah->simpanan_koperasi = $this->rupiahToNumber($req->simpanan_koperasi);
        $nasabah->tujuan_penggunaan = $req->tujuan_penggunaan;
        $nasabah->gaji_bersih = $this->rupiahToNumber($req->gaji_bersih);
        $nasabah->tanggal_pengajuan = $req->tanggal_pengajuan;
        $nasabah->mulai_pinjaman = $req->mulai_pinjaman;
        $nasabah->akhir_pinjaman = $req->akhir_pinjaman;
        $nasabah->dbr = $req->dbr;

        $fileMappings = [
            'upload_ktp' => 'ktp',
            'upload_id_card' => 'id_card',
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
            'upload_video_interview' => 'video_interview',
            'upload_video_asuransi' => 'video_asuransi',
        ];

        foreach ($fileMappings as $inputName => $columnName) {
            if ($req->hasFile($inputName)) {
                $file = $req->file($inputName);

                $filename = time() . '_' . uniqid() . '_' . $columnName . '.' . $file->getClientOriginalExtension();

                // simpan file baru
                $file->storeAs('bta', $filename);

                // OPTIONAL: hapus file lama
                if (!empty($nasabah->{$columnName})) {
                    Storage::delete('bta/' . $nasabah->{$columnName});
                }

                // update kolom
                $nasabah->{$columnName} = $filename;
            }
        }

        // === Metadata ===
        $nasabah->admin_id = auth('bta')->user()->id;
        $nasabah->position = 'analyst';
        $nasabah->status = 'pending';

        $nasabah->save();

        return response()->json([
            'message' => 'Data nasabah berhasil disimpan.'
        ]);
    }

    private function rupiahToNumber($value): int
    {
        if (!$value) {
            return 0;
        }

        return (int) preg_replace('/[^0-9]/', '', $value);
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


        $plafond = $nasabah->persetujuan_analyst;
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

        if ($nasabah->status_pegawai !== "Pensiun") {
            $pdf = Pdf::loadView('bta.pdf.perjanjian-kredit', ['data' => $data]);
        } else {
            $pdf = Pdf::loadView('bta.pdf.perjanjian-kredit-pensiun', ['data' => $data]);
        }


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
