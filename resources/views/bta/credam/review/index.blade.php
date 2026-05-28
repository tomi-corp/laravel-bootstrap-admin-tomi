@extends('bta.layout.app')
@section('title', 'Review Nasabah - ' . $nasabah->nama_lengkap)
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('bta.dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('bta.analyst.index') }}">Credit Administrator</a></li>
                    <li class="breadcrumb-item active">Review</li>
                </ol>
            </div>
            <h4 class="page-title">Review Nasabah: {{ $nasabah->nama_lengkap }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-bordered mb-3" id="reviewTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-identitas-review" role="tab">
                            <i class="mdi mdi-account-circle mr-1"></i>Identitas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-pribadi-review" role="tab">
                            <i class="mdi mdi-information-outline mr-1"></i>Data Pribadi
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-pekerjaan-review" role="tab">
                            <i class="mdi mdi-briefcase mr-1"></i>Pekerjaan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-bank-review" role="tab">
                            <i class="mdi mdi-bank mr-1"></i>Bank
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-kta-review" role="tab">
                            <i class="mdi mdi-credit-card mr-1"></i>KTA Multiguna
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-dokumen-review" role="tab">
                            <i class="mdi mdi-file-document mr-1"></i>Dokumen (List)
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- TAB 1: IDENTITAS REVIEW -->
                    <div class="tab-pane show active" id="tab-identitas-review" role="tabpanel">
                        <h5 class="mb-4"><i class="mdi mdi-account-circle text-primary"></i> Identitas Utama</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input class="form-control" value="{{ $nasabah->nama_lengkap }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Singkat</label>
                                    <input class="form-control" value="{{ $nasabah->nama_singkat ?? '-' }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>No. KTP</label>
                                    <input class="form-control" value="{{ $nasabah->no_ktp }}" readonly />
                                </div>
                            </div>

                            <!-- PREVIEW KTP -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload KTP</label>
                                    @if ($nasabah->ktp)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="ktp"
                                            onclick="previewFile('{{ $safeUrls['ktp'] }}', 'KTP - {{ $nasabah->nama_lengkap }}', 'ktp')">
                                            <i class="mdi mdi-eye"></i> Lihat File KTP
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_ktp == 'approve' ? 'badge-success' : ($nasabah->status_ktp == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_ktp) }}</span></small>
                                    @if ($nasabah->note_ktp)
                                    <small class="text-info d-block">Catatan: {{ $nasabah->note_ktp }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>

                            <!-- PREVIEW Selfie -->
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Selfie dengan KTP</label>
                                    @if ($nasabah->selfie)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="selfie"
                                            onclick="previewFile('{{ $safeUrls['selfie'] }}', 'Selfie - {{ $nasabah->nama_lengkap }}', 'selfie')">
                                            <i class="mdi mdi-eye"></i> Lihat File Selfie
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_selfie == 'approve' ? 'badge-success' : ($nasabah->status_selfie == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_selfie) }}</span></small>
                                    @if ($nasabah->note_selfie)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_selfie }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div> --}}

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. NPWP</label>
                                    <input class="form-control" value="{{ $nasabah->no_npwp }}" readonly />
                                </div>
                            </div>

                            <!-- PREVIEW NPWP -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload NPWP</label>
                                    @if ($nasabah->npwp)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="npwp"
                                            onclick="previewFile('{{ $safeUrls['npwp'] }}', 'NPWP - {{ $nasabah->nama_lengkap }}', 'npwp')">
                                            <i class="mdi mdi-eye"></i> Lihat File NPWP
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_npwp == 'approve' ? 'badge-success' : ($nasabah->status_npwp == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_npwp) }}</span></small>
                                    @if ($nasabah->note_npwp)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_npwp }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <input class="form-control" value="{{ $nasabah->jenis_kelamin }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tempat Lahir</label>
                                    <input class="form-control" value="{{ $nasabah->tempat_lahir }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <input class="form-control"
                                        value="{{ \Carbon\Carbon::parse($nasabah->tanggal_lahir)->format('d-m-Y') }}"
                                        readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Gadis Ibu Kandung</label>
                                    <input class="form-control" value="{{ $nasabah->nama_ibu }}" readonly />
                                </div>
                            </div>

                            <!-- PREVIEW KK -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Kartu Keluarga (KK)</label>
                                    @if ($nasabah->kk)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="kk"
                                            onclick="previewFile('{{ $safeUrls['kk'] }}', 'KK - {{ $nasabah->nama_lengkap }}', 'kk')">
                                            <i class="mdi mdi-eye"></i> Lihat File KK
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_kk == 'approve' ? 'badge-success' : ($nasabah->status_kk == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_kk) }}</span></small>
                                    @if ($nasabah->note_kk)
                                    <small class="text-info d-block">Catatan: {{ $nasabah->note_kk }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input class="form-control" value="{{ $nasabah->no_telp }}" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: DATA PRIBADI REVIEW -->
                    <div class="tab-pane" id="tab-pribadi-review" role="tabpanel">
                        <h5 class="mb-4"><i class="mdi mdi-information-outline text-primary"></i> Data Pribadi</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Agama</label>
                                    <input class="form-control" value="{{ $nasabah->agama }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pendidikan Terakhir</label>
                                    <input class="form-control" value="{{ $nasabah->pendidikan_terakhir }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status Perkawinan</label>
                                    <input class="form-control" value="{{ $nasabah->status_perkawinan }}" readonly />
                                </div>
                            </div>

                            <!-- DATA PASANGAN (conditional) -->
                            @if ($nasabah->status_perkawinan === 'Kawin')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Pasangan</label>
                                    <input class="form-control" value="{{ $nasabah->nama_pasangan ?? '-' }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tgl Lahir Pasangan</label>
                                    <input class="form-control"
                                        value="{{ $nasabah->tgl_lahir_pasangan ? \Carbon\Carbon::parse($nasabah->tgl_lahir_pasangan)->format('d-m-Y') : '-' }}"
                                        readonly />
                                </div>
                            </div>
                            @endif


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat Rumah</label>
                                    <textarea class="form-control" rows="3"
                                        readonly>{{ $nasabah->alamat_rumah }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Provinsi</label>
                                    <input class="form-control"
                                        value="{{ optional($nasabah->provinsi)->provinsi ?? 'Data Tidak Lengkap' }}"
                                        readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kota/Kabupaten</label>
                                    <input class="form-control"
                                        value="{{ optional($nasabah->kabupaten)->kabupaten ?? 'Data Tidak Lengkap' }}"
                                        readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <input class="form-control"
                                        value="{{ optional($nasabah->kecamatan)->kecamatan ?? 'Data Tidak Lengkap' }}"
                                        readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelurahan</label>
                                    <input class="form-control"
                                        value="{{ optional($nasabah->desa)->desa ?? 'Data Tidak Lengkap' }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Pos</label>
                                    <input type="text" class="form-control" value="{{ $nasabah->kode_pos }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode DATI</label>
                                    <input type="text" class="form-control"
                                        value="{{ optional($nasabah->dati)->sandi ?? ($nasabah->dati->sandi ?? 'Data Tidak Lengkap') }}"
                                        readonly />
                                </div>
                            </div>

                        </div>
                    </div>



                    <!-- TAB 4: PEKERJAAN REVIEW -->
                    <div class="tab-pane" id="tab-pekerjaan-review" role="tabpanel">
                        <h5 class="mb-4"><i class="mdi mdi-briefcase text-primary"></i> Data Pekerjaan</h5>

                        <div class="row">
                            {{-- 1. Field yang SELALU muncul untuk semua status --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status Pegawai</label>
                                    <input class="form-control" value="{{ $nasabah->status_pegawai }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label> NIP (No Induk Pegawai) / KARIP (Kartu Identitas Pensiun) </label>
                                    <input class="form-control" value="{{ $nasabah->nip ?? '-' }}" readonly />
                                </div>
                            </div>

                            {{-- 2. Field Khusus: Karyawan Tidak Tetap & Tetap (Perusahaan & Alamat) --}}
                            @if (in_array($nasabah->status_pegawai, ['Karyawan Tidak Tetap', 'Tetap']))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Perusahaan</label>
                                    <input class="form-control" value="{{ $nasabah->nama_perusahaan }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bidang Usaha</label>
                                    <input class="form-control" value="{{ $nasabah->bidang_usaha }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat Tempat Kerja</label>
                                    <textarea class="form-control" rows="3"
                                        readonly>{{ $nasabah->alamat_kerja }}</textarea>
                                </div>
                            </div>
                            @endif

                            {{-- 3. Field Khusus: Karyawan Tidak Tetap & Pensiun (Tanggal Akhir Kontrak) --}}
                            @if (in_array($nasabah->status_pegawai, ['Karyawan Tidak Tetap', 'Pensiun']))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tgl Akhir Kontrak Kerja</label>
                                    <input class="form-control"
                                        value="{{ $nasabah->tgl_akhir_kontrak ? \Carbon\Carbon::parse($nasabah->tgl_akhir_kontrak)->format('d-m-Y') : '-' }}"
                                        readonly />
                                </div>
                            </div>
                            @endif

                            {{-- 4. Logic Upload Slip Gaji (Conditional Requirement) --}}
                            @if ($nasabah->status_pegawai === 'Karyawan Tidak Tetap')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Slip Gaji Terakhir *</label>
                                    @if ($nasabah->slip_gaji)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="slip_gaji"
                                            onclick="previewFile('{{ $safeUrls['slip_gaji'] }}', 'Slip Gaji - {{ $nasabah->nama_lengkap }}', 'slip_gaji')">
                                            <i class="mdi mdi-eye"></i> Lihat Slip Gaji
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_slip_gaji == 'approve' ? 'badge-success' : ($nasabah->status_slip_gaji == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_slip_gaji) }}</span></small>
                                    @if ($nasabah->note_slip_gaji)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_slip_gaji }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>
                            @endif
                            {{-- Jika 'Tetap', section Slip Gaji tidak dirender sama sekali --}}


                            {{-- 5. Section Dokumen Pendukung Lainnya (SK & ID Card) --}}
                            {{-- Disesuaikan agar tetap muncul sesuai kebutuhan review umum --}}

                            <!-- PREVIEW SK Karyawan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload SK Karyawan</label>
                                    @if ($nasabah->sk_karyawan)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="sk_karyawan"
                                            onclick="previewFile('{{ $safeUrls['sk_karyawan'] }}', 'SK Karyawan - {{ $nasabah->nama_lengkap }}', 'sk_karyawan')">
                                            <i class="mdi mdi-eye"></i> Lihat File SK
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_sk_karyawan == 'approve' ? 'badge-success' : ($nasabah->status_sk_karyawan == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_sk_karyawan) }}</span></small>
                                    @if ($nasabah->note_sk_karyawan)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_sk_karyawan }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>

                            <!-- PREVIEW ID Card -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload ID Card / ID Pensiun (Karip)</label>
                                    @if ($nasabah->id_card)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="id_card"
                                            onclick="previewFile('{{ $safeUrls['id_card'] }}', 'ID Card - {{ $nasabah->nama_lengkap }}', 'id_card')">
                                            <i class="mdi mdi-eye"></i> Lihat ID Card
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_id_card == 'approve' ? 'badge-success' : ($nasabah->status_id_card == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_id_card) }}</span></small>
                                    @if ($nasabah->note_id_card)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_id_card }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: BANK REVIEW -->
                    <div class="tab-pane" id="tab-bank-review" role="tabpanel">
                        <h5 class="mb-4"><i class="mdi mdi-bank text-primary"></i> Data Bank</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Bank</label>
                                    <input class="form-control"
                                        value="{{ optional($nasabah->bank)->code_bank ?? 'Data Tidak Lengkap' }}"
                                        readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Pemilik Rekening</label>
                                    <input class="form-control" value="{{ $nasabah->nama_pemilik_rek }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No Rekening Bank</label>
                                    <input class="form-control" value="{{ $nasabah->no_rek }}" readonly />
                                </div>
                            </div>

                            <!-- PREVIEW Buku Tabungan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Buku Tabungan</label>
                                    @if ($nasabah->buku_tabungan)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="buku_tabungan"
                                            onclick="previewFile('{{ $safeUrls['buku_tabungan'] }}', 'Buku Tabungan - {{ $nasabah->nama_lengkap }}', 'buku_tabungan')">
                                            <i class="mdi mdi-eye"></i> Lihat Buku Tabungan
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_buku_tabungan == 'approve' ? 'badge-success' : ($nasabah->status_buku_tabungan == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_buku_tabungan) }}</span></small>
                                    @if ($nasabah->note_buku_tabungan)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_buku_tabungan }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>

                            <!-- PREVIEW Mutasi Rekening -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Mutasi Rekening (3 Bulan)</label>
                                    @if ($nasabah->mutasi_rekening)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="mutasi_rekening"
                                            onclick="previewFile('{{ $safeUrls['mutasi_rekening'] }}', 'Mutasi Rekening - {{ $nasabah->nama_lengkap }}', 'mutasi_rekening')">
                                            <i class="mdi mdi-eye"></i> Lihat Mutasi Rekening
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_mutasi_rekening == 'approve' ? 'badge-success' : ($nasabah->status_mutasi_rekening == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_mutasi_rekening) }}</span></small>
                                    @if ($nasabah->note_mutasi_rekening)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_mutasi_rekening }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 6: KTA MULTIGUNA REVIEW (EDITABLE) -->
                    <div class="tab-pane" id="tab-kta-review" role="tabpanel">
                        <h5 class="mb-4"><i class="mdi mdi-credit-card text-primary"></i> Produk Kredit: KTA
                            Multiguna</h5>

                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="plafond">Pengajuan Nasabah: <span class="text-danger">*</span></label>
                                    <input type="text" readonly class="form-control rupiah-format" id="pengajuan"
                                        value="Rp {{ number_format($nasabah->plafond, 0, ',', '.') }}"
                                        placeholder="Jumlah Pengajuan Nasabah">
                                    <small class="text-danger" id="errPlafond"></small>
                                </div>

                                <div class="form-group">
                                    <label for="plafond">Persetujuan Analyst: <span class="text-danger">*</span></label>
                                    <input type="text" readonly class="form-control rupiah-format" name="plafond"
                                        id="plafond"
                                        value="Rp {{ number_format($nasabah->persetujuan_analyst, 0, ',', '.') }}"
                                        placeholder="Jumlah Plafond">
                                    <small class="text-danger" id="errPlafond"></small>
                                </div>

                                <div class="form-group">
                                    <label for="tipe_bunga">Tipe Bunga: <span class="text-danger">*</span></label>

                                    <input type="hidden" name="tipe_bunga" value="{{ $nasabah->tipe_bunga ?? 'flat' }}">

                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="customRadio1" class="custom-control-input" value="flat"
                                            {{ $nasabah->tipe_bunga == 'flat' ? 'checked' : '' }}
                                        disabled>
                                        <label class="custom-control-label" for="customRadio1">Flat</label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="customRadio2" class="custom-control-input"
                                            value="efektif" {{ $nasabah->tipe_bunga == 'efektif' ? 'checked' : '' }}
                                        disabled>
                                        <label class="custom-control-label" for="customRadio2">Efektif
                                            (Anuitas)</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="jangka_waktu">Jangka Waktu (bulan): <span
                                            class="text-danger">*</span></label>
                                    <input type="number" readonly class="form-control" name="jangka_waktu"
                                        id="jangka_waktu" value="{{ $nasabah->jangka_waktu }}" min="1" max="60"
                                        placeholder="Jangka Waktu">
                                    <small class="text-danger" id="errJangkaWaktu"></small>
                                </div>

                                <div class="form-group">
                                    <label for="suku_bunga" class="form-label">Suku Bunga: <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" readonly step="0.01" class="form-control" id="suku_bunga"
                                            placeholder="Masukkan angka" aria-label="suku_bunga"
                                            aria-describedby="persen-addon" name="suku_bunga"
                                            value="{{ $nasabah->suku_bunga }}">
                                        <span class="input-group-text" id="persen-addon">%</span>
                                    </div>
                                    <small class="text-danger" id="errSukuBunga"></small>
                                </div>

                                <div class="form-group">
                                    <label for="nilai_angsuran">Nilai Angsuran (PMT): <span
                                            class="text-danger">*</span></label>
                                    <input type="text" readonly class="form-control" name="nilai_angsuran"
                                        id="nilai_angsuran"
                                        value="Rp {{ number_format($nasabah->nilai_angsuran, 0, ',', '.') }}">
                                </div>
                                <div class="form-group">
                                    <label for="biaya_bank">Biaya Bank: <span class="text-danger">*</span></label>
                                    <input type="text" readonly step="0.01" class="form-control rupiah-format"
                                        name="biaya_bank" id="biaya_bank"
                                        value="Rp {{ number_format($nasabah->biaya_bank, 0, ',', '.') }}"
                                        placeholder="Masukan angka">
                                </div>
                                <div class="form-group">
                                    <label for="biaya_asuransi">Biaya Asuransi:<span
                                            class="text-danger">*</span></label>
                                    <input type="text" readonly step="0.01" class="form-control rupiah-format"
                                        name="biaya_asuransi" id="biaya_asuransi"
                                        value="Rp {{ number_format($nasabah->biaya_asuransi, 0, ',', '.') }}"
                                        placeholder="Masukan angka">
                                </div>
                                <div class="form-group">
                                    <label for="biaya_koperasi">Biaya Koperasi: <span
                                            class="text-danger">*</span></label>
                                    <input type="text" readonly step="0.01" class="form-control rupiah-format"
                                        name="biaya_koperasi" id="biaya_koperasi"
                                        value="Rp {{ number_format($nasabah->biaya_koperasi, 0, ',', '.') }}"
                                        placeholder="Masukan angka">
                                </div>
                                <div class="form-group">
                                    <label for="simpanan_koperasi">Simpanan Koperasi: <span
                                            class="text-danger">*</span></label>
                                    <input type="text" readonly step="0.01" class="form-control rupiah-format"
                                        name="simpanan_koperasi" id="simpanan_koperasi"
                                        value="Rp {{ number_format($nasabah->simpanan_koperasi, 0, ',', '.') }}"
                                        placeholder="Masukan angka">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tujuan_penggunaan">Tujuan Penggunaan: <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" readonly name="tujuan_penggunaan"
                                        id="tujuan_penggunaan">
                                        <option value="" disabled>-- Pilih Tujuan --</option>
                                        @php
                                        $options = ['Konsumsi', 'Modal Kerja', 'Investasi'];
                                        @endphp
                                        @foreach ($options as $option)
                                        <option value="{{ $option }}" {{ $nasabah->tujuan_penggunaan == $option ?
                                            'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger" id="errTujuanPenggunaan"></small>
                                </div>
                                <div class="form-group">
                                    <label for="gaji_bersih">Gaji Bersih: <span class="text-danger">*</span></label>
                                    <input type="text" readonly step="0.01" class="form-control rupiah-format"
                                        name="gaji_bersih" id="gaji_bersih"
                                        value="Rp {{ number_format($nasabah->gaji_bersih, 0, ',', '.') }}"
                                        placeholder="Masukan angka">
                                    <small class="text-danger" id="errGajiBersih"></small>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_pengajuan">Tanggal Pengajuan: <span
                                            class="text-danger">*</span></label>
                                    <input type="date" readonly class="form-control" name="tanggal_pengajuan"
                                        id="tanggal_pengajuan"
                                        value="{{ $nasabah->tanggal_pengajuan ? \Carbon\Carbon::parse($nasabah->tanggal_pengajuan)->format('Y-m-d') : '' }}"
                                        min="{{ now()->subWeek()->toDateString() }}"
                                        max="{{ now()->addWeek()->toDateString() }}">
                                    <small class="text-danger" id="errTanggalPengajuan"></small>
                                </div>
                                <div class="form-group">
                                    <label for="mulai_pinjaman">Mulai Pinjaman: <span
                                            class="text-danger">*</span></label>
                                    <input type="date" readonly class="form-control" name="mulai_pinjaman"
                                        id="mulai_pinjaman"
                                        value="{{ \Carbon\Carbon::parse($nasabah->mulai_pinjaman)->format('Y-m-d') }}">
                                    <small class="text-danger" id="errMulaiPinjaman"></small>
                                </div>
                                <div class="form-group">
                                    <label for="akhir_pinjaman">Akhir Pinjaman: <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" readonly name="akhir_pinjaman"
                                        id="akhir_pinjaman"
                                        value="{{ \Carbon\Carbon::parse($nasabah->akhir_pinjaman)->format('Y-m-d') }}">
                                </div>
                                <div class="form-group">
                                    <label for="dbr">DBR (Debt Burden Ratio): <span class="text-danger">*</span></label>
                                    <input type="number" readonly step="0.01" class="form-control" name="dbr" id="dbr"
                                        value="{{ $nasabah->dbr }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 7: DOKUMEN AKHIR REVIEW -->
                    <div class="tab-pane" id="tab-dokumen-review" role="tabpanel">
                        <h5 class="mb-4"><i class="mdi mdi-file-document text-primary"></i> Dokumen Pendukung Akhir
                        </h5>

                        <div class="row">
                            <!-- DOKUMEN Form SLIK -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Form SLIK</label>
                                    @if ($nasabah->form_slik)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="form_slik"
                                            onclick="previewFile('{{ $safeUrls['form_slik'] }}', 'Form SLIK - {{ $nasabah->nama_lengkap }}', 'form_slik')">
                                            <i class="mdi mdi-eye"></i> Lihat File Form SLIK
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_form_slik == 'approve' ? 'badge-success' : ($nasabah->status_form_slik == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_form_slik) }}</span></small>
                                    @if ($nasabah->note_form_slik)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_form_slik }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>

                            <!-- DOKUMEN Form Kredit -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Form Kredit</label>
                                    @if ($nasabah->form_kredit)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="form_kredit"
                                            onclick="previewFile('{{ $safeUrls['form_kredit'] }}', 'Form Kredit - {{ $nasabah->nama_lengkap }}', 'form_kredit')">
                                            <i class="mdi mdi-eye"></i> Lihat File Form Kredit
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_form_kredit == 'approve' ? 'badge-success' : ($nasabah->status_form_kredit == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_form_kredit) }}</span></small>
                                    @if ($nasabah->note_form_kredit)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_form_kredit }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>

                            <!-- DOKUMEN Form BTA -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Form BTA</label>
                                    @if ($nasabah->form_bta)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="form_bta"
                                            onclick="previewFile('{{ $safeUrls['form_bta'] }}', 'Form BTA - {{ $nasabah->nama_lengkap }}', 'form_bta')">
                                            <i class="mdi mdi-eye"></i> Lihat File Form BTA
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_form_bta == 'approve' ? 'badge-success' : ($nasabah->status_form_bta == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_form_bta) }}</span></small>
                                    @if ($nasabah->note_form_bta)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_form_bta }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>

                            <!-- DOKUMEN Checklist Verifikasi -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Checklist Verifikasi</label>
                                    @if ($nasabah->form_checklist)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="form_checklist"
                                            onclick="previewFile('{{ $safeUrls['form_checklist'] }}', 'Checklist Verifikasi - {{ $nasabah->nama_lengkap }}', 'form_checklist')">
                                            <i class="mdi mdi-eye"></i> Lihat File Checklist
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_form_checklist == 'approve' ? 'badge-success' : ($nasabah->status_form_checklist == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_form_checklist) }}</span></small>
                                    @if ($nasabah->note_form_checklist)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_form_checklist }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Foto Akad Kredit</label>
                                    @if ($nasabah->form_checklist)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="form_checklist"
                                            onclick="previewFile('{{ $safeUrls['foto_akad_kredit'] }}', 'Akad Kredit - {{ $nasabah->nama_lengkap }}', 'foto_akad_kredit')">
                                            <i class="mdi mdi-eye"></i> Lihat Foto Akad Kredit
                                        </button>
                                    </div>
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>PK Tanda Tangan</label>
                                    @if ($nasabah->form_checklist)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="pk_tanda_tangan"
                                            onclick="previewFile('{{ $safeUrls['pk_tanda_tangan'] }}', 'Checklist Verifikasi - {{ $nasabah->nama_lengkap }}', 'pk_tanda_tangan')">
                                            <i class="mdi mdi-eye"></i> Lihat File Checklist
                                        </button>
                                    </div>
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>



                            @if ($nasabah->status_pegawai === 'Pensiun')

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Video Interview</label>
                                    @if ($nasabah->video_interview)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="video_interview"
                                            onclick="previewFile('{{ $safeUrls['video_interview'] }}', 'Video Interview - {{ $nasabah->nama_lengkap }}', 'video_interview')">
                                            <i class="mdi mdi-video"></i> Lihat Video Interview
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_video_interview == 'approve' ? 'badge-success' : ($nasabah->status_video_interview == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_video_interview) }}</span></small>
                                    @if ($nasabah->note_video_interview)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_video_interview }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>

                            <!-- DOKUMEN Video Asuransi -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Video Asuransi</label>
                                    @if ($nasabah->video_asuransi)
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-info btn-block btn-preview-doc"
                                            data-doc-key="video_asuransi"
                                            onclick="previewFile('{{ $safeUrls['video_asuransi'] }}', 'Video Asuransi - {{ $nasabah->nama_lengkap }}', 'video_asuransi')">
                                            <i class="mdi mdi-video"></i> Lihat Video Asuransi
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Status: <span
                                            class="badge {{ $nasabah->status_video_asuransi == 'approve' ? 'badge-success' : ($nasabah->status_video_asuransi == 'reject' ? 'badge-danger' : 'badge-warning') }}">{{
                                            ucfirst($nasabah->status_video_asuransi) }}</span></small>
                                    @if ($nasabah->note_video_asuransi)
                                    <small class="text-info d-block">Catatan:
                                        {{ $nasabah->note_video_asuransi }}</small>
                                    @endif
                                    @else
                                    <input class="form-control" value="Belum Diupload" readonly />
                                    @endif
                                </div>
                            </div>
                            @endif



                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex float-right">
                                    <a href="{{ route('bta.credam.index') }}" class="btn btn-secondary mr-2">Kembali</a>
                                    <button class="btn btn-primary" id="lanjutkanKredit">Selesai</button>
                                </div>

                            </div>
                        </div>

                        <!-- Hidden Form untuk menyimpan status dan catatan dokumen -->
                        <form id="formReviewDokumen" style="display: none;">
                            @csrf
                            <input type="hidden" name="no_ktp" value="{{ $nasabah->no_ktp }}">
                            <input type="hidden" name="status_pegawai" value="{{ $nasabah->status_pegawai }}">

                            @php
                            $docItems = [
                            ['key' => 'ktp', 'label' => 'KTP'],
                            ['key' => 'npwp', 'label' => 'NPWP'],
                            // ['key' => 'selfie', 'label' => 'Selfie'],
                            ['key' => 'kk', 'label' => 'Kartu Keluarga'],
                            ['key' => 'id_card', 'label' => 'ID Card'],
                            ['key' => 'sk_karyawan', 'label' => 'SK Karyawan'],
                            // slip_gaji akan di-handle terpisah karena conditional
                            ['key' => 'buku_tabungan', 'label' => 'Buku Tabungan'],
                            ['key' => 'mutasi_rekening', 'label' => 'Mutasi Rekening'],
                            ['key' => 'form_slik', 'label' => 'Form SLIK'],
                            ['key' => 'form_kredit', 'label' => 'Form Kredit'],
                            ['key' => 'form_bta', 'label' => 'Form BTA'],
                            ['key' => 'form_checklist', 'label' => 'Form Checklist'],
                            ['key' => 'foto_akad_kredit', 'label' => 'Foto Akad Kredit'],
                            ['key' => 'pk_tanda_tangan', 'label' => 'PK Tanda Tangan'],
                            ['key' => 'video_interview', 'label' => 'Video Interview'],
                            ['key' => 'video_asuransi', 'label' => 'Video Asuransi'],
                            ];
                            @endphp

                            {{-- Loop untuk dokumen yang SELALU wajib divalidasi --}}
                            @foreach ($docItems as $item)
                            @php
                            $currentStatus = $nasabah->{'status_' . $item['key']} ?? 'pending';
                            $currentNote = $nasabah->{'note_' . $item['key']} ?? '';
                            @endphp
                            <select name="verifikasi[{{ $item['key'] }}][status]"
                                class="form-control form-control-sm doc-status-select"
                                data-doc-key="{{ $item['key'] }}">
                                <option value="pending" {{ $currentStatus=='pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="approve" {{ $currentStatus=='approve' ? 'selected' : '' }}>
                                    Setuju</option>
                                <option value="reject" {{ $currentStatus=='reject' ? 'selected' : '' }}>Tolak
                                </option>
                                <option value="revisi" {{ $currentStatus=='revisi' ? 'selected' : '' }}>
                                    Kembalikan</option>
                            </select>
                            <textarea name="verifikasi[{{ $item['key'] }}][note]"
                                class="form-control form-control-sm doc-note-textarea" rows="1" placeholder="Catatan..."
                                data-doc-key="{{ $item['key'] }}">{{ old("verifikasi.{$item['key']}.note", $currentNote) }}</textarea>
                            @endforeach

                            {{-- ✅ CONDITIONAL: Slip Gaji hanya untuk NON-Pensiun --}}
                            @if ($nasabah->status_pegawai !== 'Pensiun')
                            @php
                            $currentStatus = $nasabah->status_slip_gaji ?? 'pending';
                            $currentNote = $nasabah->note_slip_gaji ?? '';
                            @endphp
                            <select name="verifikasi[slip_gaji][status]"
                                class="form-control form-control-sm doc-status-select" data-doc-key="slip_gaji">
                                <option value="pending" {{ $currentStatus=='pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="approve" {{ $currentStatus=='approve' ? 'selected' : '' }}>
                                    Setuju</option>
                                <option value="reject" {{ $currentStatus=='reject' ? 'selected' : '' }}>Tolak
                                </option>
                                <option value="revisi" {{ $currentStatus=='revisi' ? 'selected' : '' }}>
                                    Kembalikan</option>
                            </select>
                            <textarea name="verifikasi[slip_gaji][note]"
                                class="form-control form-control-sm doc-note-textarea" rows="1" placeholder="Catatan..."
                                data-doc-key="slip_gaji">{{ old('verifikasi.slip_gaji.note', $currentNote) }}</textarea>
                            @else
                            {{-- Untuk Pensiun: kirim nilai null/empty agar tidak divalidasi backend --}}
                            <input type="hidden" name="verifikasi[slip_gaji][status]" value="">
                            <input type="hidden" name="verifikasi[slip_gaji][note]" value="">
                            @endif
                        </form>
                    </div>
                </div> <!-- end tab-content -->




            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div> <!-- end row -->

<!-- Modal Preview -->
<div id="modalPreview" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="labelModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelModal">Preview Dokumen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body text-center">
                <div id="previewContent">
                </div>
                <!-- NEW: Status dan Note di dalam Modal -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a id="btnDownload" href="#" class="btn btn-info" download>Download</a>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    function previewFile(url, title, docKey) {
        const previewContent = document.getElementById('previewContent');
        const labelModal = document.getElementById('labelModal');
        const btnDownload = document.getElementById('btnDownload');

        labelModal.innerText = 'Preview: ' + title;
        btnDownload.href = url;
        previewContent.innerHTML =
            `<div class="p-3"><i class="mdi mdi-spin mdi-loading"></i> Loading Dokumen...</div>`;
        currentDocKey = docKey; // Simpan key dokumen

        // Ambil status dan catatan saat ini dari form utama dan set ke modal
        if (currentDocKey) {
            // Ambil nilai dari form utama (dalam kasus ini, formReviewDokumen - yang tersembunyi)
            const $statusSelectInForm = $(
                `#formReviewDokumen select[name="verifikasi[${currentDocKey}][status]"]`);
            const $noteTextareaInForm = $(
                `#formReviewDokumen textarea[name="verifikasi[${currentDocKey}][note]"]`);

            // Set ke elemen di modal
            $('#modalStatusSelect').val($statusSelectInForm.val() || 'approve');
            $('#modalNoteTextarea').val($noteTextareaInForm.val() || '');
        }

        // Gunakan Iframe untuk preview
        previewContent.innerHTML = `
                    <iframe src="${url}"
                            style="width: 100%; height: 500px; border: none;"
                            onload="this.style.background='none'">
                    </iframe>`;

        $('#modalPreview').modal('show');
    };
</script>
<script>
    $(document).ready(function () {



        $('#lanjutkanKredit').on('click', function (e) {
            e.preventDefault(); // Mencegah reload halaman

            let $btn = $(this); // Menyimpan context tombol

            // Cegah double click (spam klik)
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '{!! route('bta.credam.approve') !!}',
                type: 'POST',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'no_ktp': '{{ $nasabah->no_ktp }}',
                },
                dataType: 'json',
                success: function (response) {
                    toastr.success(response.message || 'Dokumen berhasil diupload');

                    // Redirect ke route index setelah jeda singkat (agar user sempat baca toastr)
                    setTimeout(function () {
                        window.location.href = "{{ route('bta.credam.index') }}";
                    }, 1500);
                },
                error: function (xhr) {
                    // Mengambil pesan error dari server jika ada, jika tidak pakai pesan default
                    let errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                        'Ada kesalahan pada server';
                    toastr.error(errorMsg);

                    // Kembalikan tombol jika error agar user bisa coba lagi
                    $btn.prop('disabled', false).html('Lanjutkan');
                },
                complete: function () {
                    console.log('Upload process completed');
                }
            });
        });

        // Hanya menyimpan data form ke hidden field saat submit
        $('#formReview').submit(function (e) {
            // Tidak ada validasi karena hanya read-only
            // Hanya menampilkan spinner saat proses submit
            $('#submitBtn').prop('disabled', true)
                .html('<i class="mdi mdi-loading mdi-spin mr-1"></i> Memproses...');
        });

        // Inisialisasi Select2 untuk dropdown yang read-only
        $('.select2').select2({
            placeholder: "Pilih...",
            allowClear: true,
            width: '100%',
            disabled: true // Hanya read-only
        });

        // Format Rupiah untuk tampilan (hanya format, tidak bisa diubah)
        $('.rupiah-format').each(function () {
            let val = $(this).val();
            if (val && !val.startsWith('Rp')) {
                $(this).val(formatRupiah(val.replace(/[^0-9]/g, '')));
            }
        });

        // Fungsi format rupiah sederhana
        function formatRupiah(angka) {
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                rupiah += (sisa ? '.' : '') + ribuan.join('.');
            }

            return 'Rp ' + rupiah;
        }

        // Setup preview modal untuk dokumen
        $('.btn-preview').click(function () {
            const url = $(this).data('url');
            const title = $(this).data('title');

            $('#modalPreview .modal-title').text(title);
            $('#modalPreview iframe').attr('src', url);

            // Untuk file gambar, gunakan tag img
            if (url.match(/\.(jpeg|jpg|png|gif)$/i)) {
                $('#modalPreview iframe').addClass('d-none');
                $('#modalPreview .modal-body').append(
                    `<img src="${url}" class="img-fluid" style="max-height: 60vh;">`
                );
            } else {
                $('#modalPreview iframe').removeClass('d-none').attr('src', url);
            }

            $('#modalPreview').modal('show');
        });

        // Reset modal preview saat ditutup
        $('#modalPreview').on('hidden.bs.modal', function () {
            $('#modalPreview iframe').attr('src', '');
            $('#modalPreview .modal-body img').remove();
        });



    });
</script>
@endpush
{{-- @push('js')
<script>
    $('#lanjutkanKredit').on('click', function (e) {
        e.preventDefault(); // Mencegah reload halaman

        let $btn = $(this); // Menyimpan context tombol

        // Cegah double click (spam klik)
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: '{!! route('bta.credam.approve') !!}',
            type: 'POST',
            data: {
                '_token': "{{ csrf_token() }}",
                'no_ktp': '{{ $nasabah->no_ktp }}',
            },
            dataType: 'json',
            success: function (response) {
                toastr.success(response.message || 'Dokumen berhasil diupload');

                // Redirect ke route index setelah jeda singkat (agar user sempat baca toastr)
                setTimeout(function () {
                    window.location.href = "{{ route('bta.credam.index') }}";
                }, 1500);
            },
            error: function (xhr) {
                // Mengambil pesan error dari server jika ada, jika tidak pakai pesan default
                let errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                    'Ada kesalahan pada server';
                toastr.error(errorMsg);

                // Kembalikan tombol jika error agar user bisa coba lagi
                $btn.prop('disabled', false).html('Lanjutkan');
            },
            complete: function () {
                console.log('Upload process completed');
            }
        });
    });

    // Simpan key dokumen yang sedang aktif di modal
    let currentDocKey = null;
    $('#plafond, #jangka_waktu, #suku_bunga').on('input', function () {
        calculateAngsuran();
        calculateAkhirPinjaman();
    });

    // Tambahkan listener untuk perubahan Tipe Bunga (Radio Button)
    $('input[name="tipe_bunga"]').on('change', function () {
        calculateAngsuran();
    });

    $('#mulai_pinjaman, #jangka_waktu').on('change input', function () {
        calculateAkhirPinjaman();
    });

    $('#mulai_pinjaman, #jangka_waktu').on('change input', function () {
        calculateAkhirPinjaman();
    });

    function calculateAngsuran() {
        const plafond = parseRupiah($('#plafond').val() || '0');
        const jangkaWaktu = parseInt($('#jangka_waktu').val()) || 0;
        const sukuBunga = parseFloat($('#suku_bunga').val()) || 0;

        // Cek tipe bunga yang dipilih
        const tipeBunga = $('input[name="tipe_bunga"]:checked').val();

        if (plafond > 0 && jangkaWaktu > 0 && sukuBunga > 0) {
            let totalAngsuran = 0;

            if (tipeBunga === 'flat') {
                // RUMUS FLAT (Sesuai Excel Simulasi Flat)
                const bungaPerBulan = (plafond * (sukuBunga / 100)) / 12;
                const pokokPerBulan = plafond / jangkaWaktu;
                totalAngsuran = pokokPerBulan + bungaPerBulan;
            } else {
                // RUMUS EFEKTIF / ANUITAS (Sesuai Excel Simulasi In Arrears)
                const r = (sukuBunga / 100) / 12;
                const n = jangkaWaktu;
                const powTerm = Math.pow(1 + r, n);
                totalAngsuran = plafond * (r * powTerm) / (powTerm - 1);
            }

            $('#nilai_angsuran').val(formatRupiah(Math.round(totalAngsuran).toString()));
        } else {
            $('#nilai_angsuran').val('');
        }
    }


    function calculateAkhirPinjaman() {
        const mulaiPinjaman = $('#mulai_pinjaman').val();
        const jangkaWaktu = parseInt($('#jangka_waktu').val()) || 0;

        if (mulaiPinjaman && jangkaWaktu > 0) {
            const startDate = new Date(mulaiPinjaman);
            startDate.setMonth(startDate.getMonth() + jangkaWaktu);

            const year = startDate.getFullYear();
            const month = String(startDate.getMonth() + 1).padStart(2, '0');
            const day = String(startDate.getDate()).padStart(2, '0');

            $('#akhir_pinjaman').val(`${year}-${month}-${day}`);
        } else {
            $('#akhir_pinjaman').val('');
        }
    }


    // Fungsi previewFile: Menyimpan key, mengisi modal dari form utama, menampilkan
    // GUNAKAN route yang SESUAI dengan HTML Anda (bukan 'bta.file.show')
    function previewFile(url, title, docKey) {
        const previewContent = document.getElementById('previewContent');
        const labelModal = document.getElementById('labelModal');
        const btnDownload = document.getElementById('btnDownload');

        labelModal.innerText = 'Preview: ' + title;
        btnDownload.href = url;
        previewContent.innerHTML = `<div class="p-3"><i class="mdi mdi-spin mdi-loading"></i> Loading Dokumen...</div>`;
        currentDocKey = docKey; // Simpan key dokumen

        // Ambil status dan catatan saat ini dari form utama dan set ke modal
        if (currentDocKey) {
            // Ambil nilai dari form utama (dalam kasus ini, formReviewDokumen - yang tersembunyi)
            const $statusSelectInForm = $(`#formReviewDokumen select[name="verifikasi[${currentDocKey}][status]"]`);
            const $noteTextareaInForm = $(`#formReviewDokumen textarea[name="verifikasi[${currentDocKey}][note]"]`);

            // Set ke elemen di modal
            $('#modalStatusSelect').val($statusSelectInForm.val() || 'approve');
            $('#modalNoteTextarea').val($noteTextareaInForm.val() || '');
        }

        // Gunakan Iframe untuk preview
        previewContent.innerHTML = `
            <iframe src="${url}"
                    style="width: 100%; height: 500px; border: none;"
                    onload="this.style.background='none'">
            </iframe>`;

        $('#modalPreview').modal('show');
    }

    // Handler untuk tombol "Simpan Status" di modal
    $('#btnSaveModalStatus').on('click', function (e) {
        e.preventDefault();
        if (!currentDocKey) {
            toastr.warning('Tidak ada dokumen yang sedang aktif di modal.');
            return;
        }

        // Ambil nilai dari modal
        const selectedStatus = $('#modalStatusSelect').val();
        const enteredNote = $('#modalNoteTextarea').val();

        // Update form utama (tersembunyi) dengan nilai dari modal
        $(`#formReviewDokumen select[name="verifikasi[${currentDocKey}][status]"]`).val(selectedStatus);
        $(`#formReviewDokumen textarea[name="verifikasi[${currentDocKey}][note]"]`).val(enteredNote);

        // *** UPDATE: Perbarui badge status dan catatan di tempat pertama dokumen ditampilkan (form group) ***
        // Kita harus mencari elemen yang tepat di *semua* tab berdasarkan currentDocKey.
        // Kita menggunakan class="btn-preview-doc" data-doc-key="..."

        // MENCARI ELEMEN DI SEMUA TAB BERDASARKAN CLASS DAN DATA ATTRIBUTE
        const $triggerButton = $(`.btn-preview-doc[data-doc-key="${currentDocKey}"]`);
        if ($triggerButton.length) {
            // Temukan container utama dari tombol ini (biasanya div.form-group)
            const $container = $triggerButton.closest('.form-group');

            // Perbarui badge status
            // Asumsikan struktur: ... <button> ... </button> <small>Status: <span class="badge...">...</span></small> <small>Catatan: ...</small> (jika ada)
            const $statusSmall = $container.find('small:contains("Status:")')
                .first(); // Ambil elemen small pertama yang mengandung "Status:"
            if ($statusSmall.length) {
                let badgeClass = 'badge-warning'; // Default
                if (selectedStatus === 'approve') badgeClass = 'badge-success';
                if (selectedStatus === 'reject') badgeClass = 'badge-danger';
                if (selectedStatus === 'revisi') badgeClass = 'badge-warning'; // atau sesuaikan

                // Ganti seluruh konten span badge di dalam small ini
                $statusSmall.html(
                    `Status: <span class="badge ${badgeClass}">${selectedStatus.charAt(0).toUpperCase() + selectedStatus.slice(1)}</span>`
                );
            }

            // Perbarui catatan
            // Asumsikan struktur: ... <small>Status: ...</small> <small class="text-info d-block">Catatan: ...</small> (jika ada catatan)
            // Kita cari small yang mengandung "Catatan:" dan berada tepat setelah elemen status atau tombol.
            const $existingNoteSmall = $container.find('small.text-info.d-block')
                .first(); // Ambil small catatan pertama
            if (enteredNote) {
                if ($existingNoteSmall.length) {
                    $existingNoteSmall.text(`Catatan: ${enteredNote}`);
                } else {
                    // Jika sebelumnya tidak ada catatan, buat elemen baru
                    $statusSmall.after(`<small class="text-info d-block">Catatan: ${enteredNote}</small>`);
                }
            } else {
                // Jika catatan kosong, hapus elemen catatan jika ada
                if ($existingNoteSmall.length && $existingNoteSmall.text().startsWith('Catatan:')) {
                    $existingNoteSmall.remove();
                }
            }

        } else {
            console.warn(
                `Tidak menemukan tombol trigger untuk dokumen: ${currentDocKey}. Pastikan tombol memiliki class="btn-preview-doc" dan data-doc-key yang benar.`
            );
        }
        // *** END OF UPDATE ***


        // Beri feedback visual (opsional)
        toastr.info(`Status untuk ${currentDocKey} diperbarui.`);

        // Tutup modal
        $('#modalPreview').modal('hide');

        // Reset key
        currentDocKey = null;
    });

    // Fungsi validasi sebelum submit final (Approve)
    function validateFinalSubmission() {
        const $form = $('#formReviewDokumen'); // Pastikan ini adalah ID yang benar dari form Anda
        let hasRejectedOrRevised = false;

        // Debug: Log jumlah select yang ditemukan
        const selectsFound = $form.find('select[name^="verifikasi["]').length;
        console.log(`Found ${selectsFound} status select elements in #formReviewDokumen`);

        $form.find('select[name^="verifikasi["]').each(function () {
            const val = $(this).val();
            console.log("vsavasv");
            console.log($(this).val());
            console.log(`Status field value: ${val}`); // Debug log nilai status
            console.log(val);
            if (val === 'reject' || val === 'revisi') {
                hasRejectedOrRevised = true;
                return false; // Keluar dari loop
            }
        });

        if (hasRejectedOrRevised) {
            return {
                valid: false,
                message: 'Terdapat dokumen yang ditolak atau dikembalikan. Tidak dapat menyetujui secara keseluruhan.'
            };
        }

        return {
            valid: true,
            message: ''
        };
    }

    // Tambahkan fungsi untuk menampilkan error individual di form
    function displayIndividualErrors(errors) {
        // Reset semua error sebelumnya
        $('.text-danger[id^="err"]').text(''); // Hanya reset error yang id-nya dimulai dengan 'err'

        // Loop melalui error dari server
        $.each(errors, function (fieldName, messages) {
            // Cek apakah field ada di halaman (misalnya field KTA)
            // Format field di form: name="plafond" -> id error: errPlafond
            // Format field di form: name="dbr" -> id error: errDbr
            // Format field di form: name="gaji_bersih" -> id error: errGajiBersih
            // Format field di form: name="status_npwp" -> id error: errStatusNpwp
            // Format field di form: name="note_npwp" -> id error: errNoteNpwp
            let capitalizedFieldName = fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(
                /_([a-z])/g,
                function (match, letter) {
                    return letter.toUpperCase();
                });

            let errorId = `err${capitalizedFieldName}`;

            // Jika elemen error ditemukan di DOM, set pesan kesalahan
            if ($(`#${errorId}`).length) {
                $(`#${errorId}`).text(messages[0]); // Tampilkan pesan error pertama
            } else {
                // Jika tidak ditemukan, tampilkan di toastr
                toastr.error(messages[0], fieldName);
            }
        });
    }


    // Fungsi untuk handle tombol overall action (Approve, Reject, Revisi) dari select
    function handleOverallActionFromSelect(actionType, reasonFromUI) {
        let confirmationTitle = '';
        let confirmationMessage = '';

        if (actionType === 'disetujui') {
            // Validasi sebelum approve
            const validationResult = validateFinalSubmission();
            console.log(validationResult);

            if (!validationResult.valid) {
                toastr.error(validationResult.message);
                return;
            }
            confirmationTitle = 'Konfirmasi Approve';
            confirmationMessage = 'Apakah Anda yakin ingin menyetujui pengajuan ini?';
        } else if (actionType === 'ditolak') {
            confirmationTitle = 'Konfirmasi Reject';
            confirmationMessage = 'Apakah Anda yakin ingin menolak pengajuan ini?';
        } else if (actionType === 'dikembalikan') {
            confirmationTitle = 'Konfirmasi Revisi';
            confirmationMessage = 'Apakah Anda yakin ingin mengembalikan pengajuan ini untuk revisi?';
        }

        // Gunakan reason dari UI, bukan prompt
        let reason = reasonFromUI;

        if (reason === '') { // Cek ulang, walaupun UI seharusnya mencegah ini
            if (actionType === 'ditolak' || actionType === 'dikembalikan') {
                toastr.warning('Alasan wajib diisi untuk Reject atau Revisi.');
                return;
            }
            // Jika approve, reason bisa kosong
            reason = ''; // atau beri nilai default jika perlu
        }


        // Cegah double click (spam klik) - gunakan tombol submit
        const $btn = $('#submitOverallAction');
        $btn.prop('disabled', true).html(`<i class="fa fa-spinner fa-spin"></i> Processing...`);

        // Kirim semua data
        let formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('id',
            '{{ $nasabah->id }}'); // Ganti 'id' dengan nama field yang benar jika berbeda (misalnya 'no_ktp')
        formData.append('rekomendasi', actionType);
        formData.append('catatan_analyst', reason);

        // Ambil data dari form verifikasi dokumen (status dan catatan)
        // Pastikan #formReviewDokumen adalah ID yang benar dari form yang berisi select/textarea verifikasi
        const $formDokumen = $('#formReviewDokumen');
        if ($formDokumen.length) {
            console.log('Looking for verification fields inside #formReviewDokumen');
            $formDokumen.find('select[name^="verifikasi["], textarea[name^="verifikasi["]').each(function () {
                let name = $(this).attr('name'); // Contoh: verifikasi[npwp][status]
                let value = $(this).val();
                console.log(`Processing field: ${name} = ${value}`); // Debug log

                // Ubah nama dari format verifikasi[key][subkey] ke status_key atau note_key
                let match = name.match(/verifikasi\[([^\]]+)\]\[([^\]]+)\]/);
                if (match) {
                    let key = match[1]; // Contoh: npwp
                    let subKey = match[2]; // Contoh: status

                    if (subKey === 'status') {
                        // Kirim status dokumen sebagai status_{key} (misalnya status_ktp, status_npwp)
                        const finalName = `status_${key}`;
                        formData.append(finalName, value);
                        console.log(`Appended to FormData: ${finalName} = ${value}`); // Debug log
                    } else if (subKey === 'note') {
                        // Kirim catatan dokumen sebagai note_{key} (misalnya note_ktp, note_npwp)
                        // Jika actionType adalah approve, mungkin tidak perlu kirim catatan?
                        // Untuk reject/revisi, catatan penting.
                        if (actionType === 'ditolak' || actionType === 'dikembalikan' || value.trim() !== '') {
                            const finalName = `note_${key}`;
                            formData.append(finalName, value);
                            console.log(`Appended to FormData: ${finalName} = ${value}`); // Debug log
                        }
                    }
                } else {
                    // Jika nama field tidak cocok dengan format verifikasi, tambahkan seperti biasa
                    // Ini mungkin terjadi jika ada field lain di dalam #formReviewDokumen
                    console.warn(`Field name does not match verifikasi pattern: ${name}`);
                    formData.append(name, value);
                }
            });
        } else {
            console.error('Form #formReviewDokumen not found!');
        }


        // Ambil data dari form KTA Multiguna (hanya field yang bisa diedit)
        $('#tab-kta-review').find('input[name], select[name]').each(function () {
            let name = $(this).attr('name');
            let value = $(this).val();
            // Abaikan field produk_kredit jika readonly
            if (name !== 'produk_kredit') {
                formData.append(name, value);
            }
        });

        // DEBUG: Log isi FormData sebelum kirim
        console.group("FormData Contents Before Send:");
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        console.groupEnd();

        $.ajax({
            url: '{{ route('bta.analyst.update') }}', // Pastikan route ini benar
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                toastr.success(response.message ||
                    `Pengajuan berhasil ${actionType === 'disetujui' ? 'disetujui' : (actionType === 'ditolak' ? 'ditolak' : 'dikembalikan')}.`
                );
                // Redirect ke route index setelah jeda singkat
                setTimeout(function () {
                    window.location.href = "{{ route('bta.analyst.index') }}";
                }, 1500);
            },
            error: function (xhr) {
                // Handle error dari server
                if (xhr.status === 422) { // Validasi gagal
                    let responseJson = xhr.responseJSON;
                    let serverErrors = responseJson.errors || {};
                    let mainMessage = responseJson.message || 'Validasi gagal.';

                    // Tampilkan error individual
                    displayIndividualErrors(serverErrors);

                    // Tampilkan pesan utama atau pesan pertama dari errors jika tidak ada yang ditampilkan individual
                    if (Object.keys(serverErrors).length === 0) {
                        toastr.error(mainMessage);
                    } else {
                        // Jika ada error individual, mungkin cukup tampilkan yang umum
                        toastr.error(mainMessage);
                    }
                } else {
                    // Error lainnya
                    let errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                        'Ada kesalahan pada server';
                    toastr.error(errorMsg);
                }

                // Kembalikan tombol jika error
                $btn.prop('disabled', false).html(`Proses`); // Sesuaikan teks tombol kembali
            },
            complete: function () {
                console.log(`Overall ${actionType} process completed`);
            }
        });
    }

    // Tambahkan script baru untuk mengatur tombol aksi berbasis select
    $(document).ready(function () {
        const $select = $('#overallActionSelect');
        const $textarea = $('#overallActionReason');
        const $submitButton = $('#submitOverallAction');

        // Fungsi untuk mengatur status textarea dan tombol submit
        function updateUIState() {
            const selectedAction = $select.val();
            const isRejectOrRevisi = selectedAction === 'ditolak' || selectedAction === 'dikembalikan';

            // Aktifkan/inaktifkan textarea
            $textarea.prop('disabled', !isRejectOrRevisi);
            $textarea.attr('required', isRejectOrRevisi);

            // Validasi textarea sebelum mengaktifkan tombol submit
            const isTextareaFilled = isRejectOrRevisi ? $textarea.val().trim() !== '' : true;
            $submitButton.prop('disabled', !(selectedAction && isTextareaFilled));
        }

        // Event listener untuk perubahan select
        $select.on('change', updateUIState);

        // Event listener untuk perubahan textarea (ketika aktif)
        $textarea.on('input', updateUIState);

        // Event listener untuk tombol submit
        $submitButton.on('click', function (e) {
            e.preventDefault();
            const actionType = $select.val();
            const reason = $textarea.val().trim();

            if (!actionType) {
                toastr.warning('Silakan pilih tindakan terlebih dahulu.');
                return;
            }

            if ((actionType === 'ditolak' || actionType === 'dikembalikan') && !reason) {
                toastr.warning('Alasan wajib diisi untuk Reject atau Revisi.');
                return;
            }

            // Panggil fungsi handleOverallActionFromSelect dengan nilai yang diambil dari select dan textarea
            handleOverallActionFromSelect(actionType, reason);
        });
    });


    // Format Rupiah (dari tambah.blade.php)
    $('.rupiah-format').on('input', function () {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            $(this).val(formatRupiah(value));
        }
    });

    function formatRupiah(angka) {
        let number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return 'Rp ' + rupiah;
    }

    function parseRupiah(rupiah) {
        return parseInt(rupiah.replace(/[^0-9]/g, ''));
    }
</script>
@endpush --}}
