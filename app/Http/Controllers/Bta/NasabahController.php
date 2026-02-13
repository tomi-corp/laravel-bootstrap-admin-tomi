<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bta\Nasabah\NasabahInsertRequest;
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
            // ->addColumn('action', function ($row) {
            //     return '
            //         <a href="' . route('bta.analyst.review', $row->no_ktp) . '" target="_blank" class="btn btn-warning btn-sm">Review</a>
            //     ';
            // })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i> </button>';
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

    public function nasabahInsert(NasabahInsertRequest $req)
    {
        $nasabah = new nasabah();

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
        $provinsi = provinsi::where('provinsi', $req->provinsi)->first();
        $nasabah->provinsi_id = $provinsi->id;
        $kota = kota::where('provinsi', $req->provinsi)
            ->where('kabupaten', $req->kota)
            ->first();
        $nasabah->kota_id = $kota->id;
        $kecamatan = kecamatan::where('kabupaten', $req->kota)
            ->where('kecamatan', $req->kecamatan)
            ->first();
        $nasabah->kecamatan_id = $kecamatan->id;
        $kelurahan = kelurahan::where('kecamatan', $req->kecamatan)
            ->where('desa', $req->kelurahan)
            ->first();
        $nasabah->kelurahan_id = $kelurahan->id;
        $nasabah->kode_pos = $kelurahan->kodepos;
        $dati = dati::where('kabupaten', $req->kota)
            ->first();
        $nasabah->kode_dati_id = $dati->id;

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

        // === Simpan File dengan Nama Kustom ===
        $tglInput = now()->format('Ymd'); // 20260122
        $namaLengkapSanitized = Str::slug($req->nama_lengkap, '_'); // rizal_muhamad
        $randomNumber = rand(1000, 9999);

        $fileFields = [
            'ktp',
            'npwp',
            'selfie',
            'kk',
            'id_card',
            'sk_karyawan',
            'slip_gaji',
            'buku_tabungan',
            'mutasi_rekening',
            'form_slik',
            'form_kredit',
            'form_bta',
            'form_checklist'
        ];

        foreach ($fileFields as $field) {
            if ($req->hasFile($field)) {
                $file = $req->file($field);
                // $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                // Format: tgl_nama_random_originalname
                $fileName = "{$tglInput}_{$namaLengkapSanitized}_{$randomNumber}_{$field}.{$extension}";

                // Simpan ke folder: storage/app/public/nasabah/
                $file->storeAs('bta', $fileName);

                // Simpan path relatif ke database
                $nasabah->{$field} = $fileName;
            }
        }

        $nasabah->admin_id = auth('bta')->user()->id;
        $nasabah->position = 'analyst';
        $nasabah->status = 'pending';
        $nasabah->save();

        return response()->json([
            'message' => 'Data nasabah berhasil disimpan.'
        ]);
    }

    public function nasabahUpdate(Request $req)
    {

        $nasabah =  nasabah::where('id', $req->id)->first();

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
        $kelurahan = kelurahan::where('id', $req->kelurahan_id)
            ->first();
        $nasabah->kelurahan_id = $req->kelurahan_id;
        $nasabah->kode_pos = $kelurahan->kodepos;
        $nasabah->kode_dati_id = $req->kode_dati;

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

        // === Simpan File dengan Nama Kustom ===
        $tglInput = now()->format('Ymd'); // 20260122
        $namaLengkapSanitized = Str::slug($req->nama_lengkap, '_'); // rizal_muhamad
        $randomNumber = rand(1000, 9999);

        $fileFields = [
            'ktp' => 'ktp',
            'npwp' => 'npwp',
            'selfie' => 'selfie',
            'kk' => 'kk',
            'idcard' => 'id_card',
            'sk' => 'sk_karyawan',
            'slip' => 'slip_gaji',
            'bukutabungan' => 'buku_tabungan',
            'mutasi' => 'mutasi_rekening',
            'slik' => 'form_slik',
            'kredit' => 'form_kredit',
            'bta' => 'form_bta',
            'checklist' => 'form_checklist'
        ];

        foreach ($fileFields as $requestField => $dbField) {
            if ($req->hasFile($requestField)) {
                $file = $req->file($requestField);
                $extension = $file->getClientOriginalExtension();
                // Format: tgl_nama_random_originalname
                $fileName = "{$tglInput}_{$namaLengkapSanitized}_{$randomNumber}_{$requestField}.{$extension}";
                // Simpan ke folder: storage/app/public/nasabah/
                $file->storeAs('bta', $fileName);
                // Simpan path relatif ke database
                $nasabah->{$dbField} = $fileName;
                // Set status menjadi pending untuk field yang diupload
                $statusField = "status_{$dbField}";
                $nasabah->{$statusField} = 'pending';
            }
        }

        $nasabah->admin_id = auth('bta')->user()->id;
        $nasabah->position = 'admin';
        $nasabah->status = 'revisi';
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
            'no_rekening'   => $nasabah->no_rek,

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
}
