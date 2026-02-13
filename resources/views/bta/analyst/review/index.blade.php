@extends('bta.layout.app')
@section('title', 'Detail Nasabah - ' . $nasabah->nama_lengkap)
@section('content')

    <style>
        /* Gaya Modal Dokumen */
        .modal-document-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
        }

        .modal-document-frame {
            width: 100%;
            height: 100%;
            border: none;
        }

        .modal-document-image {
            max-width: 100%;
            max-height: 85vh;
            /* Tinggi maksimal gambar dalam modal */
            object-fit: contain;
        }

        .modal-document-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .modal-document-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-document-status .btn {
            margin-right: 5px;
        }

        .modal-document-notes {
            margin-top: 10px;
        }

        .modal-document-notes textarea {
            width: 100%;
            resize: vertical;
        }

        /* Gaya Toggle Status yang Lebih Baik */
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .status-badge.approve {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.reject {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-badge:hover {
            opacity: 0.8;
        }

        /* Gaya Viewer Dokumen Kecil */
        .thumbnail-viewer {
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
            background-color: #f9f9f9;
        }

        .thumbnail-viewer img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .thumbnail-viewer iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .document-actions {
            margin-top: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .document-status-display {
            font-size: 0.8rem;
        }

        /* Gaya tambahan untuk kondisi tersembunyi */
        .document-hidden {
            display: none !important;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Detail Nasabah</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title mb-4">{{ $nasabah->nama_lengkap }}</h4>
                <div class="row">
                    <!-- Sidebar Nav -->
                    <div class="col-sm-3">
                        <div class="nav flex-column nav-pills nav-pills-tab" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <a class="nav-link active show mb-2" id="v-pills-data-diri-tab" data-toggle="pill"
                                href="#v-pills-data-diri" role="tab" aria-controls="v-pills-data-diri"
                                aria-selected="true">Data Diri</a>
                            <a class="nav-link mb-2" id="v-pills-pekerjaan-tab" data-toggle="pill" href="#v-pills-pekerjaan"
                                role="tab" aria-controls="v-pills-pekerjaan" aria-selected="false">Pekerjaan</a>
                            <a class="nav-link mb-2" id="v-pills-kredit-tab" data-toggle="pill" href="#v-pills-kredit"
                                role="tab" aria-controls="v-pills-kredit" aria-selected="false">KTA Multiguna</a>
                            <a class="nav-link mb-2" id="v-pills-dokumen-tab" data-toggle="pill" href="#v-pills-dokumen"
                                role="tab" aria-controls="v-pills-dokumen" aria-selected="false">Dokumen</a>
                        </div>
                    </div>
                    <!-- Content Tabs -->
                    <div class="col-sm-9">
                        <!-- Combined Form -->
                        <form id="combinedForm">
                            @csrf
                            <input type="hidden" name="nasabah_id" value="{{ $nasabah->id }}">
                            <div class="tab-content pt-0">
                                <!-- DATA DIRI -->
                                <div class="tab-pane fade active show" id="v-pills-data-diri" role="tabpanel"
                                    aria-labelledby="v-pills-data-diri-tab">
                                    <div class="card shadow-none border">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Data Diri</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                                        <input type="text" class="form-control" id="nama_lengkap"
                                                            value="{{ $nasabah->nama_lengkap }}" readonly>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="nama_singkat" class="form-label">Nama Singkat</label>
                                                        <input type="text" class="form-control" id="nama_singkat"
                                                            value="{{ $nasabah->nama_singkat ?? '-' }}" readonly>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="no_ktp" class="form-label">No KTP</label>
                                                        <input type="text" class="form-control" id="no_ktp"
                                                            value="{{ $nasabah->no_ktp ?? '-' }}" readonly>
                                                    </div>


                                                    <div class="form-group mb-3">
                                                        <label for="agama" class="form-label">Agama</label>
                                                        <input type="text" class="form-control" id="agama"
                                                            value="{{ $nasabah->agama ?? '-' }}" readonly>
                                                    </div>


                                                    <div class="form-group mb-3">
                                                        <label for="no_ktp" class="form-label">No NPWP</label>
                                                        <input type="text" class="form-control" id="no_npwp"
                                                            value="{{ $nasabah->no_npwp ?? '-' }}" readonly>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                                        <input type="text" class="form-control" id="jenis_kelamin"
                                                            value="{{ $nasabah->jenis_kelamin }}" readonly>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="ttl" class="form-label">Tempat, Tanggal
                                                            Lahir</label>
                                                        <input type="text" class="form-control" id="ttl"
                                                            value="{{ $nasabah->tempat_lahir }}, {{ \Carbon\Carbon::parse($nasabah->tanggal_lahir)->format('d F Y') }}"
                                                            readonly>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="nama_ibu" class="form-label">Nama Ibu</label>
                                                        <input type="text" class="form-control" id="nama_ibu"
                                                            value="{{ $nasabah->nama_ibu }}" readonly>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="no_telp" class="form-label">No. Telepon</label>
                                                        <input type="text" class="form-control" id="no_telp"
                                                            value="{{ $nasabah->no_telp }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="status_perkawinan" class="form-label">Status
                                                            Perkawinan</label>
                                                        <input type="text" class="form-control" id="status_perkawinan"
                                                            value="{{ $nasabah->status_perkawinan }}" readonly>
                                                    </div>

                                                    @if ($nasabah->status_perkawinan === 'Kawin')
                                                        <div class="form-group mb-3">
                                                            <label for="nama_pasangan" class="form-label">Nama
                                                                Pasangan</label>
                                                            <input type="text" class="form-control" id="nama_pasangan"
                                                                value="{{ $nasabah->nama_pasangan }}" readonly>
                                                        </div>
                                                    @endif

                                                    <div class="form-group mb-3">
                                                        <label for="alamat" class="form-label">Alamat Rumah</label>
                                                        <textarea class="form-control" id="alamat" rows="2" readonly>{{ $nasabah->alamat_rumah }}</textarea>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="wilayah" class="form-label">Provinsi / Kota</label>
                                                        <input type="text" class="form-control" id="wilayah"
                                                            value="{{ optional($nasabah->provinsi)->provinsi ?? '-' }} / {{ optional($nasabah->kabupaten)->kabupaten ?? '-' }}"
                                                            readonly>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="form-group mb-3">
                                                                <label for="kode_pos" class="form-label">Kode Pos</label>
                                                                <input type="text" class="form-control" id="kode_pos"
                                                                    value="{{ $nasabah->kode_pos }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group mb-3">
                                                                <label for="kode_dati" class="form-label">Kode
                                                                    DATI</label>
                                                                <input type="text" class="form-control" id="kode_dati"
                                                                    value="{{ optional($nasabah->dati)->sandi ?? '-' }}"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="pendidikan" class="form-label">Pendidikan
                                                            Terakhir</label>
                                                        <input type="text" class="form-control" id="pendidikan"
                                                            value="{{ $nasabah->pendidikan_terakhir }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- PEKERJAAN -->
                                <div class="tab-pane fade" id="v-pills-pekerjaan" role="tabpanel"
                                    aria-labelledby="v-pills-pekerjaan-tab">
                                    <div class="card shadow-none border">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Pekerjaan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Nama Perusahaan</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $nasabah->nama_perusahaan }}" readonly>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Bidang Usaha</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $nasabah->bidang_usaha }}" readonly>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Alamat Kerja</label>
                                                        <textarea class="form-control" rows="2" readonly>{{ $nasabah->alamat_kerja }}</textarea>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">NIP</label>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $nasabah->nip ?? '-' }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Status Pegawai</label>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $nasabah->status_pegawai }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if ($nasabah->status_pegawai === 'Karyawan Tidak Tetap')
                                                        <div class="form-group mb-3">
                                                            <label class="form-label text-danger">Tgl Akhir Kontrak</label>
                                                            <input type="text" class="form-control"
                                                                value="{{ $nasabah->tgl_akhir_kontrak ? \Carbon\Carbon::parse($nasabah->tgl_akhir_kontrak)->format('d F Y') : '-' }}"
                                                                readonly>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Bank</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ optional($nasabah->bank)->code_bank ?? '-' }} - {{ optional($nasabah->bank)->description ?? '-' }}"
                                                            readonly>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label class="form-label">No. Rekening</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $nasabah->no_rek }}" readonly>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Nama Pemilik Rekening</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $nasabah->nama_pemilik_rek }}" readonly>
                                                    </div>

                                                    <div class="alert alert-info mt-4">
                                                        <small><i class="fas fa-info-circle"></i> Pastikan nama pemilik
                                                            rekening sesuai dengan nama di KTP nasabah untuk kelancaran
                                                            pencairan.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- KTA MULTIGUNA -->
                                <div class="tab-pane fade" id="v-pills-kredit" role="tabpanel"
                                    aria-labelledby="v-pills-kredit-tab">
                                    <div class="card shadow-none border">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Edit Produk Kredit: KTA Multiguna</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="plafond">Plafond:</label>
                                                        <input type="text" class="form-control rupiah-format"
                                                            name="plafond" id="plafond"
                                                            value="{{ old('plafond', number_format($nasabah->plafond, 0, ',', '.')) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="jangka_waktu">Jangka Waktu (bulan):</label>
                                                        <input type="number" class="form-control" name="jangka_waktu"
                                                            id="jangka_waktu"
                                                            value="{{ old('jangka_waktu', $nasabah->jangka_waktu) }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="diskon" class="form-label">Suku Bunga:</label>

                                                        <div class="input-group">

                                                            <input type="number" step="0.01" class="form-control"
                                                                id="diskon" placeholder="Masukkan angka"
                                                                aria-label="Diskon" aria-describedby="persen-addon"
                                                                name="suku_bunga" id="suku_bunga"
                                                                value="{{ old('suku_bunga', $nasabah->suku_bunga) }}">
                                                            <span class="input-group-text" id="persen-addon">%</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="nilai_angsuran">Nilai Angsuran (PMT):</label>
                                                        <input type="text" readonly class="form-control"
                                                            name="nilai_angsuran" id="nilai_angsuran"
                                                            value="{{ old('nilai_angsuran', number_format($nasabah->nilai_angsuran, 0, ',', '.')) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="biaya_bank">Biaya Bank:</label>
                                                        <input type="text" step="0.01"
                                                            class="form-control rupiah-format" name="biaya_bank"
                                                            id="biaya_bank"
                                                            value="{{ old('biaya_bank', $nasabah->biaya_bank) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="biaya_asuransi">Biaya Asuransi:</label>
                                                        <input type="text" step="0.01"
                                                            class="form-control rupiah-format" name="biaya_asuransi"
                                                            id="biaya_asuransi"
                                                            value="{{ old('biaya_asuransi', $nasabah->biaya_asuransi) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="biaya_koperasi">Biaya Koperasi:</label>
                                                        <input type="text" step="0.01"
                                                            class="form-control rupiah-format" name="biaya_koperasi"
                                                            id="biaya_koperasi"
                                                            value="{{ old('biaya_koperasi', $nasabah->biaya_koperasi) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="simpanan_koperasi">Simpanan Koperasi:</label>
                                                        <input type="text" step="0.01"
                                                            class="form-control rupiah-format" name="simpanan_koperasi"
                                                            id="simpanan_koperasi"
                                                            value="{{ old('simpanan_koperasi', $nasabah->simpanan_koperasi) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="tujuan_penggunaan">Tujuan Penggunaan:</label>
                                                        <select class="form-control" name="tujuan_penggunaan"
                                                            id="tujuan_penggunaan">
                                                            <option value="" disabled selected>-- Pilih Tujuan --
                                                            </option>

                                                            @php
                                                                $options = ['Konsumsi', 'Modal Kerja', 'investasi'];
                                                            @endphp

                                                            @foreach ($options as $option)
                                                                <option value="{{ $option }}"
                                                                    {{ old('tujuan_penggunaan', $nasabah->tujuan_penggunaan) == $option ? 'selected' : '' }}>
                                                                    {{ $option }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="gaji_bersih">Gaji Bersih:</label>
                                                        <input type="text" step="0.01"
                                                            class="form-control rupiah-format" name="gaji_bersih"
                                                            id="gaji_bersih"
                                                            value="{{ old('gaji_bersih', $nasabah->gaji_bersih) }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="mulai_pinjaman">Mulai Pinjaman:</label>
                                                        <input type="date" class="form-control" name="mulai_pinjaman"
                                                            id="mulai_pinjaman"
                                                            value="{{ old('mulai_pinjaman', $nasabah->mulai_pinjaman ? \Carbon\Carbon::parse($nasabah->mulai_pinjaman)->format('Y-m-d') : '') }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="akhir_pinjaman">Akhir Pinjaman:</label>
                                                        <input type="date" class="form-control" readonly
                                                            name="akhir_pinjaman" id="akhir_pinjaman"
                                                            value="{{ old('akhir_pinjaman', $nasabah->akhir_pinjaman ? \Carbon\Carbon::parse($nasabah->akhir_pinjaman)->format('Y-m-d') : '') }}">
                                                    </div>

                                                    {{-- <div class="form-group">
                                                        <label for="dbr">DBR (Debt Burden Ratio):</label>
                                                        <input type="number" step="0.01" class="form-control"
                                                            name="dbr" id="dbr"
                                                            value="{{ old('dbr', $nasabah->dbr) }}">
                                                        <small class="text-muted">* DBR dihitung otomatis berdasarkan Nilai
                                                            Angsuran dan Gaji Bersih</small>
                                                    </div> --}}


                                                    <div class="form-group">
                                                        <label for="dbr">DBR (Debt Burden Ratio):</label>
                                                        <input type="text" step="0.01" class="form-control "
                                                            name="dbr" id="dbr"
                                                            value="{{ old('dbr', $nasabah->dbr) }}">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- DOKUMEN -->
                                <div class="tab-pane fade" id="v-pills-dokumen" role="tabpanel"
                                    aria-labelledby="v-pills-dokumen-tab">
                                    <div class="card shadow-none border">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Dokumen</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Dokumen Loop -->
                                                <!-- Di dalam loop dokumen Anda -->
                                                @foreach ([
            ['field' => 'ktp', 'label' => 'KTP Pemohon'], ///dsasads
            ['field' => 'npwp', 'label' => 'NPWP Pemohon'],
            ['field' => 'id_card', 'label' => 'ID Card'],
            ['field' => 'kk', 'label' => 'Kartu Keluarga'],
            ['field' => 'selfie', 'label' => 'Selfie'],
            ['field' => 'slip_gaji', 'label' => 'Slip Gaji'],
            ['field' => 'sk_karyawan', 'label' => 'SK Karyawan'],
            ['field' => 'form_slik', 'label' => 'Permohonan SLIK'],
            ['field' => 'form_kredit', 'label' => 'Formulir Kredit'],
            ['field' => 'buku_tabungan', 'label' => 'Buku Tabungan'],
            ['field' => 'form_checklist', 'label' => 'Dokumen Checklist'],
            ['field' => 'form_bta', 'label' => 'Pembukaan Rekening BTA'],
            ['field' => 'mutasi_rekening', 'label' => 'Mutasi Rekening'],
        ] as $doc)
                                                    @php
                                                        $filePath = $nasabah->{$doc['field']};
                                                        $status = $nasabah->{'status_' . $doc['field']};
                                                        $note = $nasabah->{'note_' . $doc['field']};
                                                        $ext = $filePath
                                                            ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION))
                                                            : null;
                                                        $isPdf = $ext === 'pdf';
                                                        // Gunakan URL yang dihasilkan secara aman dari controller
                                                        $storagePath = isset($safeUrls[$doc['field']])
                                                            ? $safeUrls[$doc['field']]
                                                            : null;
                                                    @endphp
                                                    <div class="col-md-6 mb-3" id="doc-container-{{ $doc['field'] }}">
                                                        <label>{{ $doc['label'] }}</label>
                                                        <div class="border p-2 rounded">
                                                            @if ($storagePath)
                                                                <div class="thumbnail-viewer"
                                                                    onclick="openDocumentModal('{{ $doc['field'] }}', '{{ addslashes($doc['label']) }}', '{{ $storagePath }}', '{{ $isPdf ? 'pdf' : 'image' }}', '{{ $status }}', '{{ addslashes($note) }}')">
                                                                    @if ($isPdf)
                                                                        <iframe src="{{ $storagePath }}"
                                                                            frameborder="0"></iframe>
                                                                    @else
                                                                        <img src="{{ $storagePath }}"
                                                                            alt="{{ $doc['label'] }}">
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-muted">Tidak ada file</span>
                                                            @endif

                                                            <div class="document-actions">
                                                                <div class="document-status-display">
                                                                    Status:
                                                                    <span
                                                                        class="status-badge {{ $status === 'approve' ? 'approve' : ($status === 'reject' ? 'reject' : '') }}">
                                                                        {{ $status === 'approve' ? 'Sesuai' : ($status === 'reject' ? 'Tidak Sesuai' : 'Belum Dinilai') }}
                                                                    </span>
                                                                </div>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-primary"
                                                                    onclick="openDocumentModal('{{ $doc['field'] }}', '{{ addslashes($doc['label']) }}', '{{ $storagePath }}', '{{ $isPdf ? 'pdf' : 'image' }}', '{{ $status }}', '{{ addslashes($note) }}')">
                                                                    Detail
                                                                </button>
                                                            </div>

                                                            {{-- Tambahkan hidden input jika status sudah ada --}}
                                                            @if ($status)
                                                                <input type="hidden" name="status_{{ $doc['field'] }}"
                                                                    value="{{ $status }}">
                                                            @endif
                                                            @if ($note)
                                                                <input type="hidden" name="note_{{ $doc['field'] }}"
                                                                    value="{{ $note }}">
                                                            @endif


                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card shadow-none border">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Rekomendasi Analyst</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Rekomendasi <span class="text-danger">*</span></label>
                                                <select class="form-control" name="rekomendasi" id="rekomendasi"
                                                    required>
                                                    <option value="">Pilih Rekomendasi</option>
                                                    <option value="ditolak"
                                                        {{ old('rekomendasi', $nasabah->rekomendasi) == 'ditolak' ? 'selected' : '' }}>
                                                        Ditolak</option>
                                                    <option value="dikembalikan"
                                                        {{ old('rekomendasi', $nasabah->rekomendasi) == 'dikembalikan' ? 'selected' : '' }}>
                                                        Dikembalikan</option>
                                                    <option value="disetujui"
                                                        {{ old('rekomendasi', $nasabah->rekomendasi) == 'disetujui' ? 'selected' : '' }}>
                                                        Disetujui</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Catatan</label>
                                                <textarea class="form-control" name="catatan_analyst" id="notes" rows="4"
                                                    placeholder="Masukkan catatan analisis...">{{ old('catatan_analyst', $nasabah->catatan_analyst) }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary"
                                                id="btnSubmitRekomendasi">Simpan Rekomendasi</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dokumen -->
    <div class="modal fade" id="documentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalTitle">Detail Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-document-container" id="modalDocumentContent">
                        <!-- Konten dokumen akan diisi oleh JS -->
                    </div>
                    <div class="modal-document-info">
                        <div class="modal-document-status">
                            <span>Status:</span>
                            <button type="button" class="btn btn-sm" id="btnApproveModal">Sesuai</button>
                            <button type="button" class="btn btn-sm" id="btnRejectModal">Tidak Sesuai</button>
                            <input type="hidden" name="status_modal" id="status_modal" value="">
                        </div>
                        <div class="modal-document-notes" id="noteSectionModal" style="display: none;">
                            <label for="note_modal">Catatan:</label>
                            <textarea class="form-control" name="note_modal" id="note_modal" rows="3"
                                placeholder="Jelaskan alasan tidak sesuai..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endpush

@push('js')
    <script>
        let currentModalField = '';
        // --- TAMBAHKAN OBJEK INI UNTUK MENYIMPAN STATUS INTERNAL ---
        let currentModalState = {};
        // ---------------------------------------------------------

        function openDocumentModal(field, label, path, type, currentStatus, currentNote) {
            currentModalField = field;
            $('#documentModalTitle').text(label);
            const $contentContainer = $('#modalDocumentContent');

            if (type === 'pdf') {
                $contentContainer.html(
                    `<iframe class="modal-document-frame" src="${path}#toolbar=0&navpanes=0&scrollbar=0"></iframe>`);
            } else {
                $contentContainer.html(`<img class="modal-document-image" src="${path}" alt="${label}">`);
            }

            // --- PERUBAHAN DI SINI ---
            // Ambil status dan catatan dari state internal jika ada, jika tidak gunakan dari Blade
            const savedStatus = currentModalState[field]?.status || currentStatus;
            const savedNote = currentModalState[field]?.note || currentNote;

            // Set status aktif dan update hidden input berdasarkan state internal atau nilai awal
            $('#status_modal').val(savedStatus || '');
            $('#btnApproveModal').removeClass('btn-success btn-outline-success').addClass(savedStatus === 'approve' ?
                'btn-success' : 'btn-outline-success');
            $('#btnRejectModal').removeClass('btn-danger btn-outline-danger').addClass(savedStatus === 'reject' ?
                'btn-danger' : 'btn-outline-danger');

            // Tampilkan/catatan sesuai status dari state internal
            if (savedStatus === 'reject') {
                $('#noteSectionModal').show();
                $('#note_modal').val(savedNote || ''); // Gunakan catatan dari state internal
            } else {
                $('#noteSectionModal').hide();
                $('#note_modal').val('');
            }
            // ------------------------

            $('#documentModal').modal('show');
        }

        $(document).ready(function() {
            // Event listener untuk tombol status di modal
            $('#btnApproveModal').click(function() {
                $('#status_modal').val('approve');
                $(this).removeClass('btn-outline-success').addClass('btn-success');
                $('#btnRejectModal').removeClass('btn-danger').addClass('btn-outline-danger');
                $('#noteSectionModal').hide();
            });

            $('#btnRejectModal').click(function() {
                $('#status_modal').val('reject');
                $(this).removeClass('btn-outline-danger').addClass('btn-danger');
                $('#btnApproveModal').removeClass('btn-success').addClass('btn-outline-success');
                $('#noteSectionModal').show();
            });

            // Update form input dan simpan ke state internal saat modal ditutup
            $('#documentModal').on('hidden.bs.modal', function() {
                if (currentModalField) { // Pastikan field valid
                    const statusValue = $('#status_modal').val();
                    const noteValue = $('#note_modal').val();

                    // Simpan ke state internal
                    currentModalState[currentModalField] = {
                        status: statusValue,
                        note: noteValue
                    };

                    // Update badge status di thumbnail berdasarkan ID container
                    const containerId = `#doc-container-${currentModalField}`;
                    const $statusBadge = $(containerId).find('.document-status-display .status-badge');

                    if (statusValue === 'approve') {
                        $statusBadge.removeClass('reject').addClass('approve').text('Sesuai');
                    } else if (statusValue === 'reject') {
                        $statusBadge.removeClass('approve').addClass('reject').text('Tidak Sesuai');
                    } else {
                        $statusBadge.removeClass('approve reject').text('Belum Dinilai');
                    }

                    // Update hidden input di form utama
                    $(`input[name="status_${currentModalField}"]`).remove(); // Hapus dulu jika ada
                    $('#combinedForm').append(
                        `<input type="hidden" name="status_${currentModalField}" value="${statusValue}">`
                    );

                    $(`input[name="note_${currentModalField}"]`).remove(); // Hapus dulu jika ada
                    if (noteValue) {
                        $('#combinedForm').append(
                            `<input type="hidden" name="note_${currentModalField}" value="${noteValue}">`
                        );
                    }
                }
            });

            // --- Fungsi-fungsi lainnya tetap seperti sebelumnya ---
            function formatRupiah(angka) {
                let number_string = angka.replace(/[^,\d]/g, "").toString(),
                    split = number_string.split(","),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) {
                    let separator = sisa ? "." : "";
                    rupiah += separator + ribuan.join(".");
                }
                rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
                return rupiah ? "Rp " + rupiah : "";
            }

            function cleanNumber(value) {
                return parseFloat(value.replace(/[^0-9]/g, "")) || 0;
            }

            function calculatePMT() {
                let p = cleanNumber($('#plafond').val());
                let r = parseFloat($('#suku_bunga').val()) / 100 / 12;
                let n = parseInt($('#jangka_waktu').val());
                if (p > 0 && r > 0 && n > 0) {
                    let pmt = (p * r) / (1 - Math.pow(1 + r, -n));
                    $('#nilai_angsuran').val(formatRupiah(Math.round(pmt).toString()));
                } else {
                    $('#nilai_angsuran').val('');
                }
            }

            $('.rupiah-format').each(function() {
                var val = $(this).val();
                if (val) {
                    $(this).val(formatRupiah(val));
                }
            });


            $(document).on('keyup', '.rupiah-format, #plafond', function() {
                $(this).val(formatRupiah($(this).val()));
            });

            $(document).on('keyup change', '#plafond, #jangka_waktu, #suku_bunga', function() {
                calculatePMT();
            });

            function calculateEndDate() {
                let startDateVal = $('#mulai_pinjaman').val();
                let tenor = parseInt($('#jangka_waktu').val());
                if (startDateVal && tenor > 0) {
                    let startDate = new Date(startDateVal);
                    startDate.setMonth(startDate.getMonth() + tenor);
                    let year = startDate.getFullYear();
                    let month = ("0" + (startDate.getMonth() + 1)).slice(-2);
                    let day = ("0" + startDate.getDate()).slice(-2);
                    let endDate = year + "-" + month + "-" + day;
                    $('#akhir_pinjaman').val(endDate);
                } else {
                    $('#akhir_pinjaman').val('');
                }
            }

            $(document).on('change keyup', '#mulai_pinjaman, #jangka_waktu', function() {
                calculateEndDate();
            });

            if ($('#mulai_pinjaman').val() && $('#jangka_waktu').val()) {
                calculateEndDate();
            }

            // function hitungDBR() {
            //     const gajiBersih = parseFloat($('#gaji_bersih').val()) || 0;
            //     const nilaiAngsuranStr = $('#nilai_angsuran').val().replace(/[^\d,]/g, '');
            //     const nilaiAngsuran = parseFloat(nilaiAngsuranStr) || 0;
            //     if (gajiBersih > 0) {
            //         const dbr = (nilaiAngsuran / gajiBersih) * 100;
            //         $('#dbr').val(dbr.toFixed(2));
            //     } else {
            //         $('#dbr').val('');
            //     }
            // }

            // $('#gaji_bersih, #nilai_angsuran').on('input', function() {
            //     hitungDBR();
            // });

            // Validasi dan Submit Form
            $('#combinedForm').on('submit', function(e) {
                e.preventDefault();
                let hasRejectedDoc = false;
                let firstRejectedDocName = '';

                // Iterasi melalui semua input hidden status yang mungkin ditambahkan oleh modal
                // Kita cari input dengan nama yang dimulai dengan "status_" dan nilainya "reject"
                $('input[name^="status_"][value="reject"]').each(function() {
                    // Ambil nama field dokumen dari nama input (contoh: status_ktp -> ktp)
                    const fieldName = $(this).attr('name').replace('status_', '');
                    // Cari label dokumen yang sesuai untuk ditampilkan di pesan
                    // Kita cari berdasarkan ID container yang sesuai dengan field
                    const docLabel = $(`#doc-container-${fieldName} label`).first().text().trim();
                    hasRejectedDoc = true;
                    firstRejectedDocName = docLabel || fieldName ||
                        'Dokumen'; // Gunakan label, atau field, atau default
                    return false; // Hentikan iterasi setelah menemukan satu saja yang rejected
                });

                const selectedRecommendation = $('#rekomendasi').val();

                // Jika ada dokumen yang tidak sesuai
                if (hasRejectedDoc) {
                    // Dan rekomendasi bukan 'ditolak' atau 'dikembalikan'
                    if (selectedRecommendation !== 'ditolak' && selectedRecommendation !== 'dikembalikan') {
                        alert(
                            `Dokumen "${firstRejectedDocName}" tidak sesuai. Silakan pilih rekomendasi "Ditolak" atau "Dikembalikan".`
                        );
                        // Optional: Fokus ke dropdown rekomendasi untuk memudahkan user
                        $('#rekomendasi').focus();
                        return; // Batalkan submit
                    }
                }

                // Jika semua validasi lolos (termasuk pengecekan dokumen jika ada yang reject, maka rekomendasinya benar)
                const formData = new FormData(this);

                // Tambahkan logika untuk mengirim data ke server
                $.ajax({
                    url: '{{ route('bta.analyst.update', $nasabah->id) }}', // Ganti dengan route update Anda
                    method: 'POST',
                    data: formData, // Gunakan 'data' bukan 'formData' di sini
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.message);
                        console.log(response);
                        // Redirect setelah toastr
                        setTimeout(function() {
                            window.location.href =
                                '/bta/analyst';
                        }, 2000);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", xhr.responseText);
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                    }
                });
            });

            hitungDBR();
        });
    </script>
@endpush
