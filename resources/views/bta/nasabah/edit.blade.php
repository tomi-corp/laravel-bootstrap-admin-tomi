@extends('bta.layout.app')
@section('title', 'Edit Data Nasabah')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs nav-bordered mb-3" id="formTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-identitas" role="tab">
                                <i class="mdi mdi-account-circle mr-1"></i>Identitas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-pribadi" role="tab">
                                <i class="mdi mdi-information-outline mr-1"></i>Data Pribadi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-alamat" role="tab">
                                <i class="mdi mdi-map-marker mr-1"></i>Alamat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-pekerjaan" role="tab">
                                <i class="mdi mdi-briefcase mr-1"></i>Pekerjaan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-bank" role="tab">
                                <i class="mdi mdi-bank mr-1"></i>Bank
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-kta" role="tab">
                                <i class="mdi mdi-credit-card mr-1"></i>KTA Multiguna
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-dokumen" role="tab">
                                <i class="mdi mdi-file-document mr-1"></i>Dokumen
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <form id="formNasabahBertahap" enctype="multipart/form-data">
                        @csrf

                        {{-- BOX CATATAN PERBAIKAN DARI ANALYST --}}
                        @if ($nasabah->catatan_analyst)
                            <div class="alert alert-warning border-left border-warning mb-4">
                                <h6 class="font-weight-bold"><i class="mdi mdi-alert-circle mr-1"></i> Catatan Perbaikan
                                </h6>
                                <p class="mb-0">{{ nl2br(e($nasabah->catatan_analyst)) }}</p>
                            </div>
                        @endif

                        <div class="tab-content">
                            <!-- TAB 1: IDENTITAS UTAMA -->
                            <div class="tab-pane show active" id="tab-identitas" role="tabpanel">
                                <h5 class="mb-4"> <i class="mdi mdi-account-circle text-primary"></i> Identitas Utama
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                                            <input class="form-control" id="namaLengkap" name="nama_lengkap"
                                                placeholder="Nama sesuai KTP"
                                                value="{{ old('nama_lengkap', $nasabah->nama_lengkap) }}" />
                                            <small class="text-danger" id="errNamaLengkap"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Singkat</label>
                                            <input class="form-control" id="namaSingkat" name="nama_singkat"
                                                placeholder="Nama panggilan"
                                                value="{{ old('nama_singkat', $nasabah->nama_singkat) }}" />
                                            <small class="text-danger" id="errNamaSingkat"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>No. KTP <span class="text-danger">*</span></label>
                                            <input class="form-control" id="noKtp" name="no_ktp"
                                                placeholder="Contoh: 3275041234567890" maxlength="16"
                                                value="{{ old('no_ktp', $nasabah->no_ktp) }}" />
                                            <small class="text-danger" id="errNoKtp"></small>
                                        </div>
                                    </div>

                                    <!-- UPLOAD KTP -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload KTP (JPEG/PNG/PDF) <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['ktp']['status']) && $fileMeta['ktp']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['ktp']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['ktp']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['ktp']['status']) && $fileMeta['ktp']['status'] === 'reject' && $fileMeta['ktp']['note'])
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i> {{ $fileMeta['ktp']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <p>dasdasdas</p>
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadKtp" @if (empty($fileMeta['ktp']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih File KTP
                                                </button>
                                                <input type="file" class="d-none" id="uploadKtp" name="upload_ktp"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['ktp']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['ktp']['can_upload']) && $fileMeta['ktp']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['ktp']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadKtp"></small>
                                            @if ($safeUrls['ktp'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['ktp'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['ktp']['status']) && $fileMeta['ktp']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadKtp"></div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD Selfie -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Selfie dengan KTP <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['selfie']['status']) && $fileMeta['selfie']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['selfie']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['selfie']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['selfie']['status']) && $fileMeta['selfie']['status'] === 'reject' && $fileMeta['selfie']['note'])
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['selfie']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadSelfie" @if (empty($fileMeta['selfie']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih File Selfie
                                                </button>
                                                <input type="file" class="d-none" id="uploadSelfie"
                                                    name="upload_selfie" accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['selfie']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['selfie']['can_upload']) && $fileMeta['selfie']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['selfie']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadSelfie"></small>
                                            @if ($safeUrls['selfie'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['selfie'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['selfie']['status']) && $fileMeta['selfie']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadSelfie"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>No. NPWP <span class="text-danger">*</span></label>
                                            <input class="form-control" id="noNpwp" name="no_npwp"
                                                placeholder="Contoh: 012345678901234" maxlength="16"
                                                value="{{ old('no_npwp', $nasabah->no_npwp) }}" />
                                            <small class="text-danger" id="errNoNpwp"></small>
                                        </div>
                                    </div>

                                    <!-- UPLOAD NPWP -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload NPWP (JPEG/PNG/PDF) <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['npwp']['status']) && $fileMeta['npwp']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['npwp']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['npwp']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['npwp']['status']) && $fileMeta['npwp']['status'] === 'reject' && $fileMeta['npwp']['note'])
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['npwp']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadNpwp" @if (empty($fileMeta['npwp']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih File NPWP
                                                </button>
                                                <input type="file" class="d-none" id="uploadNpwp" name="upload_npwp"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['npwp']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['npwp']['can_upload']) && $fileMeta['npwp']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['npwp']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadNpwp"></small>
                                            @if ($safeUrls['npwp'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['npwp'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['npwp']['status']) && $fileMeta['npwp']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadNpwp"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                            <select class="form-control" id="jenisKelamin" name="jenis_kelamin">
                                                <option value="">Pilih</option>
                                                <option value="Laki-laki"
                                                    {{ $nasabah->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                                    Laki-laki</option>
                                                <option value="Perempuan"
                                                    {{ $nasabah->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                            <small class="text-danger" id="errJenisKelamin"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tempat Lahir <span class="text-danger">*</span></label>
                                            <input class="form-control" id="tempatLahir" name="tempat_lahir"
                                                placeholder="Contoh: Jakarta"
                                                value="{{ old('tempat_lahir', $nasabah->tempat_lahir) }}" />
                                            <small class="text-danger" id="errTempatLahir"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tanggal Lahir <span class="text-danger">*</span></label>
                                            <input class="form-control" id="tanggalLahir" name="tanggal_lahir"
                                                type="date"
                                                value="{{ old('tanggal_lahir', $nasabah->tanggal_lahir) }}" />
                                            <small class="text-danger" id="errTanggalLahir"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Gadis Ibu Kandung <span class="text-danger">*</span></label>
                                            <input class="form-control" id="namaIbu" name="nama_ibu"
                                                placeholder="Nama ibu kandung"
                                                value="{{ old('nama_ibu', $nasabah->nama_ibu) }}" />
                                            <small class="text-danger" id="errNamaIbu"></small>
                                        </div>
                                    </div>

                                    <!-- UPLOAD KK -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['kk']['status']) && $fileMeta['kk']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['kk']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['kk']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['kk']['status']) && $fileMeta['kk']['status'] === 'reject' && $fileMeta['kk']['note'])
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['kk']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadKk" @if (empty($fileMeta['kk']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih File KK
                                                </button>
                                                <input type="file" class="d-none" id="uploadKk" name="upload_kk"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['kk']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['kk']['can_upload']) && $fileMeta['kk']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['kk']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadKk"></small>
                                            @if ($safeUrls['kk'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['kk'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['kk']['status']) && $fileMeta['kk']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadKk"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>No. Telepon <span class="text-danger">*</span></label>
                                            <input class="form-control" id="noTelp" name="no_telp"
                                                placeholder="081234567890"
                                                value="{{ old('no_telp', $nasabah->no_telp) }}" />
                                            <small class="text-danger" id="errNoTelp"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: DATA PRIBADI -->
                            <div class="tab-pane" id="tab-pribadi" role="tabpanel">
                                <h5 class="mb-4"> <i class="mdi mdi-information-outline text-primary"></i> Data Pribadi
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Agama <span class="text-danger">*</span></label>
                                            <select class="form-control" id="agama" name="agama">
                                                <option value="">Pilih</option>
                                                <option value="Islam" {{ $nasabah->agama == 'Islam' ? 'selected' : '' }}>
                                                    Islam</option>
                                                <option value="Katolik"
                                                    {{ $nasabah->agama == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                                <option value="Kristen"
                                                    {{ $nasabah->agama == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                                <option value="Buddha"
                                                    {{ $nasabah->agama == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                                <option value="Konghucu"
                                                    {{ $nasabah->agama == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                            </select>
                                            <small class="text-danger" id="errAgama"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Pendidikan Terakhir <span class="text-danger">*</span></label>
                                            <select class="form-control" id="pendidikan" name="pendidikan_terakhir">
                                                <option value="">Pilih</option>
                                                <option value="SD"
                                                    {{ $nasabah->pendidikan_terakhir == 'SD' ? 'selected' : '' }}>SD
                                                </option>
                                                <option value="SMP"
                                                    {{ $nasabah->pendidikan_terakhir == 'SMP' ? 'selected' : '' }}>SMP
                                                </option>
                                                <option value="SMA"
                                                    {{ $nasabah->pendidikan_terakhir == 'SMA' ? 'selected' : '' }}>SMA
                                                </option>
                                                <option value="D1"
                                                    {{ $nasabah->pendidikan_terakhir == 'D1' ? 'selected' : '' }}>D1
                                                </option>
                                                <option value="D3"
                                                    {{ $nasabah->pendidikan_terakhir == 'D3' ? 'selected' : '' }}>D3
                                                </option>
                                                <option value="D4"
                                                    {{ $nasabah->pendidikan_terakhir == 'D4' ? 'selected' : '' }}>D4
                                                </option>
                                                <option value="S1"
                                                    {{ $nasabah->pendidikan_terakhir == 'S1' ? 'selected' : '' }}>S1
                                                </option>
                                                <option value="S2"
                                                    {{ $nasabah->pendidikan_terakhir == 'S2' ? 'selected' : '' }}>S2
                                                </option>
                                                <option value="S3"
                                                    {{ $nasabah->pendidikan_terakhir == 'S3' ? 'selected' : '' }}>S3
                                                </option>
                                            </select>
                                            <small class="text-danger" id="errPendidikan"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status Perkawinan <span class="text-danger">*</span></label>
                                            <select class="form-control" id="statusPerkawinan" name="status_perkawinan">
                                                <option value="">Pilih</option>
                                                <option value="Kawin"
                                                    {{ $nasabah->status_perkawinan == 'Kawin' ? 'selected' : '' }}>Kawin
                                                </option>
                                                <option value="Tidak Kawin"
                                                    {{ $nasabah->status_perkawinan == 'Tidak Kawin' ? 'selected' : '' }}>
                                                    Tidak Kawin</option>
                                                <option value="Cerai Hidup"
                                                    {{ $nasabah->status_perkawinan == 'Cerai Hidup' ? 'selected' : '' }}>
                                                    Cerai Hidup</option>
                                                <option value="Cerai Mati"
                                                    {{ $nasabah->status_perkawinan == 'Cerai Mati' ? 'selected' : '' }}>
                                                    Cerai Mati</option>
                                            </select>
                                            <small class="text-danger" id="errStatusPerkawinan"></small>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6 pasangan-field {{ $nasabah->status_perkawinan !== 'Kawin' ? 'd-none' : '' }}">
                                        <div class="form-group">
                                            <label>Nama Pasangan</label>
                                            <input class="form-control" id="namaPasangan" name="nama_pasangan"
                                                placeholder="Nama pasangan"
                                                value="{{ old('nama_pasangan', $nasabah->nama_pasangan) }}" />
                                            <small class="text-danger" id="errNamaPasangan"></small>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6 pasangan-field {{ $nasabah->status_perkawinan !== 'Kawin' ? 'd-none' : '' }}">
                                        <div class="form-group">
                                            <label>Tgl Lahir Pasangan</label>
                                            <input class="form-control" id="tglLahirPasangan" name="tgl_lahir_pasangan"
                                                type="date"
                                                value="{{ old('tgl_lahir_pasangan', $nasabah->tgl_lahir_pasangan) }}" />
                                            <small class="text-danger" id="errTglLahirPasangan"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: ALAMAT -->
                            <div class="tab-pane" id="tab-alamat" role="tabpanel">
                                <h5 class="mb-4"> <i class="mdi mdi-map-marker text-primary"></i> Alamat Rumah </h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Alamat Rumah <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="alamatRumah" name="alamat_rumah" rows="3" placeholder="Alamat lengkap">{{ old('alamat_rumah', $nasabah->alamat_rumah) }}</textarea>
                                            <small class="text-danger" id="errAlamatRumah"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Provinsi <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="provinsiSelect" name="provinsi_id">
                                                <option value="">Pilih Provinsi</option>
                                                @foreach ($provinsis as $provinsi)
                                                    <option value="{{ $provinsi->id }}"
                                                        {{ $nasabah->provinsi_id == $provinsi->id ? 'selected' : '' }}>
                                                        {{ $provinsi->provinsi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="provinsi_id"
                                                value="{{ old('provinsi_id', $nasabah->provinsi_id) }}">
                                            <small class="text-danger" id="errProvinsi"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kota/Kabupaten <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="kotaSelect" name="kota_id"
                                                {{ !$nasabah->provinsi_id ? 'disabled' : '' }}>
                                                <option value="">Pilih Kota/Kabupaten</option>
                                                @if ($nasabah->kabupaten)
                                                    <option value="{{ $nasabah->kota_id }}" selected>
                                                        {{ $nasabah->kabupaten->kabupaten }}</option>
                                                @endif
                                            </select>
                                            <input type="hidden" name="kota_id"
                                                value="{{ old('kota_id', $nasabah->kota_id) }}">
                                            <small class="text-danger" id="errKota"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kecamatan <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="kecamatanSelect" name="kecamatan_id"
                                                {{ !$nasabah->kota_id ? 'disabled' : '' }}>
                                                <option value="">Pilih Kecamatan</option>
                                                @if ($nasabah->kecamatan)
                                                    <option value="{{ $nasabah->kecamatan_id }}" selected>
                                                        {{ $nasabah->kecamatan->kecamatan }}</option>
                                                @endif
                                            </select>
                                            <input type="hidden" name="kecamatan_id"
                                                value="{{ old('kecamatan_id', $nasabah->kecamatan_id) }}">
                                            <small class="text-danger" id="errKecamatan"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kelurahan <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="kelurahanSelect" name="kelurahan_id"
                                                {{ !$nasabah->kecamatan_id ? 'disabled' : '' }}>
                                                <option value="">Pilih Kelurahan</option>
                                                @if ($nasabah->desa)
                                                    <option value="{{ $nasabah->kelurahan_id }}" selected>
                                                        {{ $nasabah->desa->desa }} - {{ $nasabah->desa->kodepos }}
                                                    </option>
                                                @endif
                                            </select>
                                            <input type="hidden" name="kelurahan_id"
                                                value="{{ old('kelurahan_id', $nasabah->kelurahan_id) }}">
                                            <small class="text-danger" id="errKelurahan"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kode Pos <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="kodePos" name="kode_pos"
                                                value="{{ old('kode_pos', $nasabah->kode_pos) }}" readonly
                                                placeholder="00000">
                                            <small class="text-danger" id="errKodePos"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kode DATI</label>
                                            <input type="text" class="form-control" id="kodeDati"
                                                name="kode_dati_id"
                                                value="{{ old('kode_dati_id', $nasabah->kode_dati_id) }}" readonly
                                                placeholder="00000">
                                            <small class="text-danger" id="errKodeDati"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: PEKERJAAN -->
                            <div class="tab-pane" id="tab-pekerjaan" role="tabpanel">
                                <h5 class="mb-4"> <i class="mdi mdi-briefcase text-primary"></i> Data Pekerjaan </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Perusahaan <span class="text-danger">*</span></label>
                                            <input class="form-control" id="namaPerusahaan" name="nama_perusahaan"
                                                placeholder="Nama perusahaan"
                                                value="{{ old('nama_perusahaan', $nasabah->nama_perusahaan) }}" />
                                            <small class="text-danger" id="errNamaPerusahaan"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bidang Usaha <span class="text-danger">*</span></label>
                                            <input class="form-control" id="bidangUsaha" name="bidang_usaha"
                                                placeholder="Contoh: Retail, Konstruksi"
                                                value="{{ old('bidang_usaha', $nasabah->bidang_usaha) }}" />
                                            <small class="text-danger" id="errBidangUsaha"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Alamat Tempat Kerja <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="alamatKerja" name="alamat_kerja" rows="3" placeholder="Alamat kantor">{{ old('alamat_kerja', $nasabah->alamat_kerja) }}</textarea>
                                            <small class="text-danger" id="errAlamatKerja"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NIP (No Induk Pegawai)</label>
                                            <input class="form-control" id="nip" name="nip"
                                                placeholder="Opsional" value="{{ old('nip', $nasabah->nip) }}" />
                                            <small class="text-danger" id="errNip"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status Pegawai <span class="text-danger">*</span></label>
                                            <select class="form-control" id="statusPegawai" name="status_pegawai">
                                                <option value="">Pilih</option>
                                                <option value="Karyawan Tetap"
                                                    {{ $nasabah->status_pegawai == 'Karyawan Tetap' ? 'selected' : '' }}>
                                                    Karyawan Tetap</option>
                                                <option value="Karyawan Tidak Tetap"
                                                    {{ $nasabah->status_pegawai == 'Karyawan Tidak Tetap' ? 'selected' : '' }}>
                                                    Karyawan Tidak Tetap</option>
                                            </select>
                                            <small class="text-danger" id="errStatusPegawai"></small>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6 kontrak-field {{ $nasabah->status_pegawai !== 'Karyawan Tidak Tetap' ? 'd-none' : '' }}">
                                        <div class="form-group">
                                            <label>Tgl Akhir Kontrak Kerja</label>
                                            <input class="form-control" id="tglAkhirKontrak" name="tgl_akhir_kontrak"
                                                type="date"
                                                value="{{ old('tgl_akhir_kontrak', $nasabah->tgl_akhir_kontrak) }}" />
                                            <small class="text-danger" id="errTglAkhirKontrak"></small>
                                        </div>
                                    </div>

                                    <!-- UPLOAD SK Karyawan -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload SK Karyawan <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['sk_karyawan']['status']) && $fileMeta['sk_karyawan']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['sk_karyawan']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['sk_karyawan']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['sk_karyawan']['status']) &&
                                                    $fileMeta['sk_karyawan']['status'] === 'reject' &&
                                                    $fileMeta['sk_karyawan']['note']
                                            )
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['sk_karyawan']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadSk" @if (empty($fileMeta['sk_karyawan']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih File SK
                                                </button>
                                                <input type="file" class="d-none" id="uploadSk" name="upload_sk"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['sk_karyawan']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['sk_karyawan']['can_upload']) && $fileMeta['sk_karyawan']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['sk_karyawan']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadSk"></small>
                                            @if ($safeUrls['sk_karyawan'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['sk_karyawan'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['sk_karyawan']['status']) && $fileMeta['sk_karyawan']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadSk"></div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD Slip Gaji -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Slip Gaji Terakhir <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['slip_gaji']['status']) && $fileMeta['slip_gaji']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['slip_gaji']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['slip_gaji']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['slip_gaji']['status']) &&
                                                    $fileMeta['slip_gaji']['status'] === 'reject' &&
                                                    $fileMeta['slip_gaji']['note']
                                            )
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['slip_gaji']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadSlip" @if (empty($fileMeta['slip_gaji']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih Slip Gaji
                                                </button>
                                                <input type="file" class="d-none" id="uploadSlip" name="upload_slip"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['slip_gaji']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['slip_gaji']['can_upload']) && $fileMeta['slip_gaji']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['slip_gaji']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadSlip"></small>
                                            @if ($safeUrls['slip_gaji'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['slip_gaji'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['slip_gaji']['status']) && $fileMeta['slip_gaji']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadSlip"></div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD ID Card -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload ID Card <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['id_card']['status']) && $fileMeta['id_card']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['id_card']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['id_card']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['id_card']['status']) &&
                                                    $fileMeta['id_card']['status'] === 'reject' &&
                                                    $fileMeta['id_card']['note']
                                            )
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['id_card']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadIdCard"
                                                    @if (empty($fileMeta['id_card']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih ID Card
                                                </button>
                                                <input type="file" class="d-none" id="uploadIdCard"
                                                    name="upload_id_card" accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['id_card']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['id_card']['can_upload']) && $fileMeta['id_card']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['id_card']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadIdCard"></small>
                                            @if ($safeUrls['id_card'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['id_card'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['id_card']['status']) && $fileMeta['id_card']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadIdCard"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 5: BANK -->
                            <div class="tab-pane" id="tab-bank" role="tabpanel">
                                <h5 class="mb-4"> <i class="mdi mdi-bank text-primary"></i> Data Bank </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Bank <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="bankSelect" name="bank_id">
                                                <option value="">Pilih</option>
                                                @foreach ($banks as $bank)
                                                    <option value="{{ $bank->id }}"
                                                        {{ $nasabah->bank_id == $bank->id ? 'selected' : '' }}>
                                                        {{ $bank->code_bank }} - {{ $bank->description }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger" id="errNamaBank"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Pemilik Rekening <span class="text-danger">*</span></label>
                                            <input class="form-control" id="namaPemilikRek" name="nama_pemilik_rek"
                                                placeholder="Harus sama dengan nama lengkap"
                                                value="{{ old('nama_pemilik_rek', $nasabah->nama_pemilik_rek) }}" />
                                            <small class="text-danger" id="errNamaPemilikRek"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>No Rekening Bank <span class="text-danger">*</span></label>
                                            <input class="form-control" id="noRek" name="no_rek"
                                                placeholder="1234567890"
                                                value="{{ old('no_rek', $nasabah->no_rek) }}" />
                                            <small class="text-danger" id="errNoRek"></small>
                                        </div>
                                    </div>

                                    <!-- UPLOAD Buku Tabungan -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Buku Tabungan <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['buku_tabungan']['status']) && $fileMeta['buku_tabungan']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['buku_tabungan']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['buku_tabungan']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['buku_tabungan']['status']) &&
                                                    $fileMeta['buku_tabungan']['status'] === 'reject' &&
                                                    $fileMeta['buku_tabungan']['note']
                                            )
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['buku_tabungan']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadBukuTabungan"
                                                    @if (empty($fileMeta['buku_tabungan']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih Buku Tabungan
                                                </button>
                                                <input type="file" class="d-none" id="uploadBukuTabungan"
                                                    name="upload_buku_tabungan" accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['buku_tabungan']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['buku_tabungan']['can_upload']) && $fileMeta['buku_tabungan']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['buku_tabungan']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadBukuTabungan"></small>
                                            @if ($safeUrls['buku_tabungan'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['buku_tabungan'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['buku_tabungan']['status']) && $fileMeta['buku_tabungan']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadBukuTabungan"></div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD Mutasi Rekening -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Mutasi Rekening (3 Bulan) <span
                                                    class="text-danger">*</span></label>
                                            @if (isset($fileMeta['mutasi_rekening']['status']) && $fileMeta['mutasi_rekening']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['mutasi_rekening']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['mutasi_rekening']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['mutasi_rekening']['status']) &&
                                                    $fileMeta['mutasi_rekening']['status'] === 'reject' &&
                                                    $fileMeta['mutasi_rekening']['note']
                                            )
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['mutasi_rekening']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadMutasi"
                                                    @if (empty($fileMeta['mutasi_rekening']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih Mutasi Rekening
                                                </button>
                                                <input type="file" class="d-none" id="uploadMutasi"
                                                    name="upload_mutasi" accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['mutasi_rekening']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['mutasi_rekening']['can_upload']) && $fileMeta['mutasi_rekening']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['mutasi_rekening']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadMutasi"></small>
                                            @if ($safeUrls['mutasi_rekening'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['mutasi_rekening'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['mutasi_rekening']['status']) && $fileMeta['mutasi_rekening']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadMutasi"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 6: KTA MULTIGUNA -->
                            <div class="tab-pane" id="tab-kta" role="tabpanel">
                                <h5 class="mb-4"> <i class="mdi mdi-credit-card text-primary"></i> Produk Kredit: KTA
                                    Multiguna </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="plafond">Plafond: <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control rupiah-format" name="plafond"
                                                id="plafond"
                                                value="{{ old('plafond', $nasabah->plafond ? number_format($nasabah->plafond, 0, ',', '.') : '') }}"
                                                placeholder="Jumlah Plafond">
                                            <small class="text-danger" id="errPlafond"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="jangka_waktu">Jangka Waktu (bulan): <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="jangka_waktu"
                                                id="jangka_waktu"
                                                value="{{ old('jangka_waktu', $nasabah->jangka_waktu) }}" min="1"
                                                max="60" placeholder="Jangka Waktu">
                                            <small class="text-danger" id="errJangkaWaktu"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="suku_bunga" class="form-label">Suku Bunga: <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control"
                                                    id="suku_bunga" placeholder="Masukkan angka" aria-label="suku_bunga"
                                                    aria-describedby="persen-addon" name="suku_bunga"
                                                    value="{{ old('suku_bunga', $nasabah->suku_bunga) }}">
                                                <span class="input-group-text" id="persen-addon">%</span>
                                            </div>
                                            <small class="text-danger" id="errSukuBunga"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="nilai_angsuran">Nilai Angsuran (PMT): <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" readonly class="form-control" name="nilai_angsuran"
                                                id="nilai_angsuran"
                                                value="{{ old('nilai_angsuran', $nasabah->nilai_angsuran ? number_format($nasabah->nilai_angsuran, 0, ',', '.') : '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="biaya_bank">Biaya Bank: <span class="text-danger">*</span></label>
                                            <input type="text" step="0.01" class="form-control rupiah-format"
                                                name="biaya_bank" id="biaya_bank"
                                                value="{{ old('biaya_bank', $nasabah->biaya_bank ? number_format($nasabah->biaya_bank, 0, ',', '.') : '') }}"
                                                placeholder="Masukan angka">
                                        </div>
                                        <div class="form-group">
                                            <label for="biaya_asuransi">Biaya Asuransi: <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" step="0.01" class="form-control rupiah-format"
                                                name="biaya_asuransi" id="biaya_asuransi"
                                                value="{{ old('biaya_asuransi', $nasabah->biaya_asuransi ? number_format($nasabah->biaya_asuransi, 0, ',', '.') : '') }}"
                                                placeholder="Masukan angka">
                                        </div>
                                        <div class="form-group">
                                            <label for="biaya_koperasi">Biaya Koperasi: <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" step="0.01" class="form-control rupiah-format"
                                                name="biaya_koperasi" id="biaya_koperasi"
                                                value="{{ old('biaya_koperasi', $nasabah->biaya_koperasi ? number_format($nasabah->biaya_koperasi, 0, ',', '.') : '') }}"
                                                placeholder="Masukan angka">
                                        </div>
                                        <div class="form-group">
                                            <label for="simpanan_koperasi">Simpanan Koperasi: <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" step="0.01" class="form-control rupiah-format"
                                                name="simpanan_koperasi" id="simpanan_koperasi"
                                                value="{{ old('simpanan_koperasi', $nasabah->simpanan_koperasi ? number_format($nasabah->simpanan_koperasi, 0, ',', '.') : '') }}"
                                                placeholder="Masukan angka">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tujuan_penggunaan">Tujuan Penggunaan: <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="tujuan_penggunaan" id="tujuan_penggunaan">
                                                <option value="" disabled selected>-- Pilih Tujuan --</option>
                                                @php $options = ['Konsumsi', 'Modal Kerja', 'Investasi']; @endphp
                                                @foreach ($options as $option)
                                                    <option value="{{ $option }}"
                                                        {{ $nasabah->tujuan_penggunaan == $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger" id="errTujuanPenggunaan"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="gaji_bersih">Gaji Bersih: <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" step="0.01" class="form-control rupiah-format"
                                                name="gaji_bersih" id="gaji_bersih"
                                                value="{{ old('gaji_bersih', $nasabah->gaji_bersih ? number_format($nasabah->gaji_bersih, 0, ',', '.') : '') }}"
                                                placeholder="Masukan angka">
                                            <small class="text-danger" id="errGajiBersih"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="mulai_pinjaman">Mulai Pinjaman: <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="mulai_pinjaman"
                                                id="mulai_pinjaman"
                                                value="{{ old('mulai_pinjaman', $nasabah->mulai_pinjaman) }}">
                                            <small class="text-danger" id="errMulaiPinjaman"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="akhir_pinjaman">Akhir Pinjaman: <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" readonly name="akhir_pinjaman"
                                                id="akhir_pinjaman"
                                                value="{{ old('akhir_pinjaman', $nasabah->akhir_pinjaman) }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="dbr">DBR (Debt Burden Ratio): <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control" name="dbr"
                                                id="dbr" value="{{ old('dbr', $nasabah->dbr) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 7: UPLOAD DOKUMEN -->
                            <div class="tab-pane" id="tab-dokumen" role="tabpanel">
                                <h5 class="mb-4"> <i class="mdi mdi-file-document text-primary"></i> Upload Dokumen
                                </h5>
                                <div class="row">
                                    <!-- UPLOAD Form SLIK -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Form SLIK <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['form_slik']['status']) && $fileMeta['form_slik']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['form_slik']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['form_slik']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['form_slik']['status']) &&
                                                    $fileMeta['form_slik']['status'] === 'reject' &&
                                                    $fileMeta['form_slik']['note']
                                            )
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['form_slik']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadSlik" @if (empty($fileMeta['form_slik']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih Form SLIK
                                                </button>
                                                <input type="file" class="d-none" id="uploadSlik" name="upload_slik"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['form_slik']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['form_slik']['can_upload']) && $fileMeta['form_slik']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['form_slik']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadSlik"></small>
                                            @if ($safeUrls['form_slik'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['form_slik'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['form_slik']['status']) && $fileMeta['form_slik']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadSlik"></div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD Form Kredit -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Form Kredit <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['form_kredit']['status']) && $fileMeta['form_kredit']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['form_kredit']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['form_kredit']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['form_kredit']['status']) &&
                                                    $fileMeta['form_kredit']['status'] === 'reject' &&
                                                    $fileMeta['form_kredit']['note']
                                            )
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['form_kredit']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadKredit"
                                                    @if (empty($fileMeta['form_kredit']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih Form Kredit
                                                </button>
                                                <input type="file" class="d-none" id="uploadKredit"
                                                    name="upload_kredit" accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['form_kredit']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['form_kredit']['can_upload']) && $fileMeta['form_kredit']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['form_kredit']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadKredit"></small>
                                            @if ($safeUrls['form_kredit'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['form_kredit'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['form_kredit']['status']) && $fileMeta['form_kredit']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadKredit"></div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD Form BTA -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Form BTA <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['form_bta']['status']) && $fileMeta['form_bta']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['form_bta']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['form_bta']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['form_bta']['status']) &&
                                                    $fileMeta['form_bta']['status'] === 'reject' &&
                                                    $fileMeta['form_bta']['note']
                                            )
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['form_bta']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadBta" @if (empty($fileMeta['form_bta']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih Form BTA
                                                </button>
                                                <input type="file" class="d-none" id="uploadBta" name="upload_bta"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['form_bta']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['form_bta']['can_upload']) && $fileMeta['form_bta']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['form_bta']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadBta"></small>
                                            @if ($safeUrls['form_bta'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['form_bta'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['form_bta']['status']) && $fileMeta['form_bta']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadBta"></div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD Checklist -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Checklist Verifikasi <span class="text-danger">*</span></label>
                                            @if (isset($fileMeta['form_checklist']['status']) && $fileMeta['form_checklist']['status'] !== 'pending')
                                                <span
                                                    class="badge badge-{{ $fileMeta['form_checklist']['status'] === 'approve' ? 'success' : 'danger' }} mb-2">
                                                    {{ ucfirst($fileMeta['form_checklist']['status']) }}
                                                </span>
                                            @endif
                                            @if (isset($fileMeta['form_checklist']['status']) &&
                                                    $fileMeta['form_checklist']['status'] === 'reject' &&
                                                    $fileMeta['form_checklist']['note']
                                            )
                                                <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                    <i class="mdi mdi-alert-circle mr-1"></i>
                                                    {{ $fileMeta['form_checklist']['note'] }}
                                                </div>
                                            @endif
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadChecklist"
                                                    @if (empty($fileMeta['form_checklist']['can_upload'])) disabled @endif>
                                                    <i class="mdi mdi-upload"></i> Pilih Checklist
                                                </button>
                                                <input type="file" class="d-none" id="uploadChecklist"
                                                    name="upload_checklist" accept=".jpg,.jpeg,.png,.pdf"
                                                    @if (empty($fileMeta['form_checklist']['can_upload'])) disabled @endif
                                                    data-can-upload="{{ isset($fileMeta['form_checklist']['can_upload']) && $fileMeta['form_checklist']['can_upload'] ? '1' : '0' }}"
                                                    data-status="{{ $fileMeta['form_checklist']['status'] ?? 'pending' }}" />
                                            </div>
                                            <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                                PDF</small>
                                            <small class="text-danger" id="errUploadChecklist"></small>
                                            @if ($safeUrls['form_checklist'])
                                                <div class="mt-2">
                                                    <a href="{{ $safeUrls['form_checklist'] }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-eye"></i> Lihat File Saat Ini
                                                    </a>
                                                    @if (isset($fileMeta['form_checklist']['status']) && $fileMeta['form_checklist']['status'] === 'approve')
                                                        <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                            Sudah disetujui</small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadChecklist"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation & Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary" id="btnPrevTab">
                                        <i class="mdi mdi-arrow-left mr-1"></i> Tab Sebelumnya
                                    </button>
                                    <div>
                                        <button type="button" class="btn btn-primary mr-2" id="btnNextTab">
                                            Tab Selanjutnya <i class="mdi mdi-arrow-right ml-1"></i>
                                        </button>
                                        <button type="submit" class="btn btn-success" id="submitBtn">
                                            <i class="mdi mdi-content-save mr-1"></i> Simpan Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Init Select2
            $('.select2').select2({
                placeholder: "Pilih...",
                allowClear: true,
                width: '100%'
            });

            // Helper: Cek apakah upload field wajib diisi
            function isUploadRequired(fieldId) {
                const $field = $(`#${fieldId}`);
                const canUpload = $field.data('can-upload');
                const status = $field.data('status');
                return canUpload === '1' || status === 'reject';
            }

            // Helper: Reset dropdowns anak
            function resetDropdowns(selectors) {
                selectors.forEach(selector => {
                    $(selector).empty().append('<option value="">Pilih...</option>').prop('disabled', true)
                        .trigger('change.select2');
                });
            }

            // CASCADING SELECT2 WILAYAH
            $('#provinsiSelect').on('change', function() {
                const provId = $(this).val();
                const $kota = $('#kotaSelect');
                $('input[name="provinsi_id"]').val(provId);
                resetDropdowns(['#kotaSelect', '#kecamatanSelect', '#kelurahanSelect']);
                $('input[name="kota_id"], input[name="kecamatan_id"], input[name="kelurahan_id"]').val('');
                if (!provId) return;
                $.ajax({
                    url: "{{ route('bta.wilayah.kota.new') }}",
                    method: "POST",
                    data: {
                        id: provId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $kota.empty().append('<option value="">Pilih Kota/Kabupaten</option>');
                        data.forEach(item => {
                            $kota.append(new Option(item.kabupaten, item.id));
                        });
                        $kota.prop('disabled', false).trigger('change.select2');
                    }
                });
            });

            $('#kotaSelect').on('change', function() {
                const kotaId = $(this).val();
                const $kec = $('#kecamatanSelect');
                $('input[name="kota_id"]').val(kotaId);
                resetDropdowns(['#kecamatanSelect', '#kelurahanSelect']);
                $('input[name="kecamatan_id"], input[name="kelurahan_id"]').val('');
                $('#kodeDati').val('');
                if (!kotaId) return;
                $.ajax({
                    url: "{{ route('bta.wilayah.kecamatan.new') }}",
                    method: "POST",
                    data: {
                        id: kotaId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $kec.empty().append('<option value="">Pilih Kecamatan</option>');
                        data.forEach(item => {
                            $kec.append(new Option(item.kecamatan, item.id));
                        });
                        $kec.prop('disabled', false).trigger('change.select2');
                    }
                });
                $.ajax({
                    url: "{{ route('bta.wilayah.dati.new') }}",
                    method: "POST",
                    data: {
                        id: kotaId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $('#kodeDati').val(data.sandi);
                        $('input[name="kode_dati_id"]').val(data.sandi);
                    }
                });
            });

            $('#kecamatanSelect').on('change', function() {
                const kecId = $(this).val();
                const $kel = $('#kelurahanSelect');
                $('input[name="kecamatan_id"]').val(kecId);
                resetDropdowns(['#kelurahanSelect']);
                $('input[name="kelurahan_id"]').val('');
                if (!kecId) return;
                $.ajax({
                    url: "{{ route('bta.wilayah.kelurahan.new') }}",
                    method: "POST",
                    data: {
                        id: kecId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $kel.empty().append('<option value="">Pilih Kelurahan</option>');
                        data.forEach(item => {
                            $kel.append(new Option(item.desa + ' - ' + item.kodepos,
                                item.id));
                        });
                        $kel.prop('disabled', false).trigger('change.select2');
                    }
                });
            });

            $('#kelurahanSelect').on('change', function() {
                const desaId = $(this).val();
                $('input[name="kelurahan_id"]').val(desaId);
                if (!desaId) return;
                $.ajax({
                    url: "{{ route('bta.wilayah.kodepos.new') }}",
                    method: "POST",
                    data: {
                        id: desaId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $('#kodePos').val(data.kodepos);
                        $('input[name="kode_pos"]').val(data.kodepos);
                    }
                });
            });

            // LOAD EXISTING WILAYAH ON PAGE LOAD
            function loadExistingWilayah() {
                const provId = $('#provinsiSelect').val();
                if (!provId) return;
                $.ajax({
                    url: "{{ route('bta.wilayah.kota.new') }}",
                    method: "POST",
                    data: {
                        id: provId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        const $kota = $('#kotaSelect');
                        $kota.empty().append('<option value="">Pilih Kota/Kabupaten</option>');
                        data.forEach(item => {
                            $kota.append(new Option(item.kabupaten, item.id));
                        });
                        $kota.prop('disabled', false);
                        const kotaId = $('input[name="kota_id"]').val();
                        if (kotaId) {
                            $kota.val(kotaId).trigger('change.select2');
                            $.ajax({
                                url: "{{ route('bta.wilayah.kecamatan.new') }}",
                                method: "POST",
                                data: {
                                    id: kotaId,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(data) {
                                    const $kec = $('#kecamatanSelect');
                                    $kec.empty().append(
                                        '<option value="">Pilih Kecamatan</option>');
                                    data.forEach(item => {
                                        $kec.append(new Option(item.kecamatan, item
                                            .id));
                                    });
                                    $kec.prop('disabled', false);
                                    $.ajax({
                                        url: "{{ route('bta.wilayah.dati.new') }}",
                                        method: "POST",
                                        data: {
                                            id: kotaId,
                                            _token: "{{ csrf_token() }}"
                                        },
                                        success: function(datiData) {
                                            $('#kodeDati').val(datiData.sandi);
                                            $('input[name="kode_dati_id"]').val(
                                                datiData.sandi);
                                        }
                                    });
                                    const kecId = $('input[name="kecamatan_id"]').val();
                                    if (kecId) {
                                        $kec.val(kecId).trigger('change.select2');
                                        $.ajax({
                                            url: "{{ route('bta.wilayah.kelurahan.new') }}",
                                            method: "POST",
                                            data: {
                                                id: kecId,
                                                _token: "{{ csrf_token() }}"
                                            },
                                            success: function(data) {
                                                const $kel = $(
                                                    '#kelurahanSelect');
                                                $kel.empty().append(
                                                    '<option value="">Pilih Kelurahan</option>'
                                                );
                                                data.forEach(item => {
                                                    $kel.append(
                                                        new Option(
                                                            item
                                                            .desa +
                                                            ' - ' +
                                                            item
                                                            .kodepos,
                                                            item.id)
                                                    );
                                                });
                                                $kel.prop('disabled', false);
                                                const desaId = $(
                                                    'input[name="kelurahan_id"]'
                                                ).val();
                                                if (desaId) {
                                                    $kel.val(desaId).trigger(
                                                        'change.select2');
                                                    $.ajax({
                                                        url: "{{ route('bta.wilayah.kodepos.new') }}",
                                                        method: "POST",
                                                        data: {
                                                            id: desaId,
                                                            _token: "{{ csrf_token() }}"
                                                        },
                                                        success: function(
                                                            data) {
                                                            $('#kodePos')
                                                                .val(
                                                                    data
                                                                    .kodepos
                                                                );
                                                            $('input[name="kode_pos"]')
                                                                .val(
                                                                    data
                                                                    .kodepos
                                                                );
                                                        }
                                                    });
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    }
                });
            }
            setTimeout(loadExistingWilayah, 300);

            // TAB NAVIGATION
            const totalTabs = 7;
            let currentTabIndex = 0;
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                currentTabIndex = getTabIndexFromId($(e.target).attr('href'));
                updateTabNavigation();
            });

            function getTabIndexFromId(tabId) {
                const tabs = ['#tab-identitas', '#tab-pribadi', '#tab-alamat', '#tab-pekerjaan', '#tab-bank',
                    '#tab-kta', '#tab-dokumen'
                ];
                return tabs.indexOf(tabId);
            }

            function updateTabNavigation() {
                $('#btnPrevTab').toggle(currentTabIndex > 0);
                $('#btnNextTab').toggle(currentTabIndex < totalTabs - 1);
            }
            $('#btnNextTab').click(function() {
                if (validateCurrentTab()) {
                    if (currentTabIndex < totalTabs - 1) {
                        const nextTab = $('.nav-tabs .nav-item').eq(currentTabIndex + 1).find(
                            'a[data-toggle="tab"]');
                        $('html, body').animate({
                            scrollTop: 0
                        }, 300, function() {
                            nextTab.tab('show');
                        });
                    }
                }
            });
            $('#btnPrevTab').click(function() {
                if (currentTabIndex > 0) {
                    const prevTab = $('.nav-tabs .nav-item').eq(currentTabIndex - 1).find(
                        'a[data-toggle="tab"]');
                    $('html, body').animate({
                        scrollTop: 0
                    }, 300, function() {
                        prevTab.tab('show');
                    });
                }
            });

            // FORM SUBMISSION
            $('#formNasabahBertahap').submit(function(e) {
                e.preventDefault();
                if (!validateAllTabs()) return false;
                let formData = new FormData(this);
                formData.append('_token', '{{ csrf_token() }}');
                $.post({
                    url: "{{ route('bta.nasabah.update') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.message || 'Data nasabah berhasil disimpan',
                            'Berhasil!');
                        setTimeout(() => {
                            window.location.href = '{{ route('bta.nasabah.index') }}';
                        }, 2000);
                    },
                    error: function(xhr) {
                        $('.text-danger').text('');
                        $('.form-control').removeClass('is-invalid');
                        let responseJson = xhr.responseJSON;
                        let errors = responseJson?.errors || {};
                        let message = responseJson?.message ||
                            'Terjadi kesalahan saat menyimpan data';
                        if (Object.keys(errors).length > 0) displayErrors(errors);
                        let errorMessageToShow = message;
                        if (!message || message.includes('more errors')) {
                            const firstErrorKey = Object.keys(errors)[0];
                            if (firstErrorKey && errors[firstErrorKey][0]) errorMessageToShow =
                                errors[firstErrorKey][0];
                        }
                        toastr.error(errorMessageToShow, 'Gagal!');
                        $('#submitBtn').prop('disabled', false).html(
                            '<i class="mdi mdi-content-save mr-1"></i> Simpan Data');
                    }
                });
            });

            // VALIDATION
            function validateAllTabs() {
                let allValid = true,
                    firstErrorTab = null;
                for (let i = 0; i < totalTabs; i++) {
                    currentTabIndex = i;
                    if (!validateCurrentTabSilent() && firstErrorTab === null) {
                        firstErrorTab = i;
                        allValid = false;
                    }
                }
                if (firstErrorTab !== null) {
                    $('.nav-tabs .nav-item').eq(firstErrorTab).find('a[data-toggle="tab"]').tab('show');
                    setTimeout(() => {
                        currentTabIndex = firstErrorTab;
                        validateCurrentTab();
                    }, 350);
                }
                currentTabIndex = getTabIndexFromId($('.tab-pane.active').attr('id'));
                updateTabNavigation();
                return allValid;
            }

            function validateCurrentTabSilent() {
                let isValid = true;
                $('.text-danger').text('');
                $('.form-control').removeClass('is-invalid');
                switch (currentTabIndex) {
                    case 0:
                        if (!$('#namaLengkap').val().trim()) {
                            markError('namaLengkap', 'Nama lengkap wajib diisi');
                            isValid = false;
                        }
                        if (!$('#noKtp').val().trim() || $('#noKtp').val().length !== 16) {
                            markError('noKtp', 'NIK harus 16 digit');
                            isValid = false;
                        }
                        if (!$('#noNpwp').val().trim() || $('#noNpwp').val().length !== 16) {
                            markError('noNpwp', 'NPWP harus 16 digit');
                            isValid = false;
                        }
                        if (isUploadRequired('uploadKtp') && !$('#uploadKtp')[0].files.length) {
                            markError('uploadKtp', 'Upload KTP wajib diisi');
                            isValid = false;
                        }
                        if (isUploadRequired('uploadSelfie') && !$('#uploadSelfie')[0].files.length) {
                            markError('uploadSelfie', 'Upload Selfie wajib diisi');
                            isValid = false;
                        }
                        if (isUploadRequired('uploadNpwp') && !$('#uploadNpwp')[0].files.length) {
                            markError('uploadNpwp', 'Upload NPWP wajib diisi');
                            isValid = false;
                        }
                        if (!$('#jenisKelamin').val()) {
                            markError('jenisKelamin', 'Jenis kelamin wajib dipilih');
                            isValid = false;
                        }
                        if (!$('#tempatLahir').val().trim()) {
                            markError('tempatLahir', 'Tempat lahir wajib diisi');
                            isValid = false;
                        }
                        if (!$('#tanggalLahir').val()) {
                            markError('tanggalLahir', 'Tanggal lahir wajib diisi');
                            isValid = false;
                        }
                        if (!$('#namaIbu').val().trim()) {
                            markError('namaIbu', 'Nama ibu kandung wajib diisi');
                            isValid = false;
                        }
                        if (isUploadRequired('uploadKk') && !$('#uploadKk')[0].files.length) {
                            markError('uploadKk', 'Upload KK wajib diisi');
                            isValid = false;
                        }
                        if (!$('#noTelp').val().trim()) {
                            markError('noTelp', 'Nomor telepon wajib diisi');
                            isValid = false;
                        }
                        break;
                    case 1:
                        if (!$('#agama').val()) {
                            markError('agama', 'Agama wajib dipilih');
                            isValid = false;
                        }
                        if (!$('#pendidikan').val()) {
                            markError('pendidikan', 'Pendidikan wajib dipilih');
                            isValid = false;
                        }
                        if (!$('#statusPerkawinan').val()) {
                            markError('statusPerkawinan', 'Status perkawinan wajib dipilih');
                            isValid = false;
                        }
                        break;
                    case 2:
                        if (!$('#alamatRumah').val().trim()) {
                            markError('alamatRumah', 'Alamat rumah wajib diisi');
                            isValid = false;
                        }
                        if (!$('#provinsiSelect').val()) {
                            markError('provinsi', 'Provinsi wajib dipilih');
                            isValid = false;
                        }
                        if (!$('#kotaSelect').val()) {
                            markError('kota', 'Kota/Kabupaten wajib dipilih');
                            isValid = false;
                        }
                        if (!$('#kecamatanSelect').val()) {
                            markError('kecamatan', 'Kecamatan wajib dipilih');
                            isValid = false;
                        }
                        if (!$('#kelurahanSelect').val()) {
                            markError('kelurahan', 'Kelurahan wajib dipilih');
                            isValid = false;
                        }
                        if (!$('#kodePos').val()) {
                            markError('kodePos', 'Kode pos wajib diisi');
                            isValid = false;
                        }
                        if (!$('#kodeDati').val()) {
                            markError('kodeDati', 'Kode DATI wajib diisi');
                            isValid = false;
                        }
                        break;
                    case 3:
                        if (!$('#namaPerusahaan').val().trim()) {
                            markError('namaPerusahaan', 'Nama perusahaan wajib diisi');
                            isValid = false;
                        }
                        if (!$('#bidangUsaha').val().trim()) {
                            markError('bidangUsaha', 'Bidang usaha wajib diisi');
                            isValid = false;
                        }
                        if (!$('#alamatKerja').val().trim()) {
                            markError('alamatKerja', 'Alamat kerja wajib diisi');
                            isValid = false;
                        }
                        if (!$('#statusPegawai').val()) {
                            markError('statusPegawai', 'Status pegawai wajib dipilih');
                            isValid = false;
                        }
                        if (isUploadRequired('uploadSk') && !$('#uploadSk')[0].files.length) {
                            markError('uploadSk', 'Upload SK wajib diisi');
                            isValid = false;
                        }
                        if (isUploadRequired('uploadSlip') && !$('#uploadSlip')[0].files.length) {
                            markError('uploadSlip', 'Upload Slip Gaji wajib diisi');
                            isValid = false;
                        }
                        if (isUploadRequired('uploadIdCard') && !$('#uploadIdCard')[0].files.length) {
                            markError('uploadIdCard', 'Upload ID Card wajib diisi');
                            isValid = false;
                        }
                        break;
                    case 4:
                        if (!$('#bankSelect').val()) {
                            markError('bank', 'Nama bank wajib dipilih');
                            isValid = false;
                        }
                        if (!$('#namaPemilikRek').val().trim()) {
                            markError('namaPemilikRek', 'Nama pemilik rekening wajib diisi');
                            isValid = false;
                        }
                        if (!$('#noRek').val().trim()) {
                            markError('noRek', 'Nomor rekening wajib diisi');
                            isValid = false;
                        }
                        if (isUploadRequired('uploadBukuTabungan') && !$('#uploadBukuTabungan')[0].files.length) {
                            markError('uploadBukuTabungan', 'Upload Buku Tabungan wajib diisi');
                            isValid = false;
                        }
                        if (isUploadRequired('uploadMutasi') && !$('#uploadMutasi')[0].files.length) {
                            markError('uploadMutasi', 'Upload Mutasi Rekening wajib diisi');
                            isValid = false;
                        }
                        break;
                    case 5:
                        if (!$('#plafond').val().trim()) {
                            markError('plafond', 'Plafond wajib diisi');
                            isValid = false;
                        }
                        if (!$('#jangka_waktu').val() || $('#jangka_waktu').val() <= 0) {
                            markError('jangka_waktu', 'Jangka waktu minimal 1 bulan');
                            isValid = false;
                        }
                        if (!$('#suku_bunga').val() || $('#suku_bunga').val() <= 0) {
                            markError('suku_bunga', 'Suku bunga wajib diisi');
                            isValid = false;
                        }
                        if (!$('#tujuan_penggunaan').val()) {
                            markError('tujuan_penggunaan', 'Tujuan penggunaan wajib dipilih');
                            isValid = false;
                        }
                        if (!$('#gaji_bersih').val().trim()) {
                            markError('gaji_bersih', 'Gaji bersih wajib diisi');
                            isValid = false;
                        }
                        if (!$('#mulai_pinjaman').val()) {
                            markError('mulai_pinjaman', 'Tanggal mulai wajib diisi');
                            isValid = false;
                        }
                        break;
                    case 6:
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                        const maxSize = 5120 * 1024;
                        ['uploadSlik', 'uploadKredit', 'uploadBta', 'uploadChecklist'].forEach(fieldId => {
                            if (isUploadRequired(fieldId) && !$(`#${fieldId}`)[0].files.length) {
                                const labels = {
                                    uploadSlik: 'Form SLIK',
                                    uploadKredit: 'Form Kredit',
                                    uploadBta: 'Form BTA',
                                    uploadChecklist: 'Checklist'
                                };
                                markError(fieldId, `Upload ${labels[fieldId]} wajib diisi`);
                                isValid = false;
                            }
                            const file = $(`#${fieldId}`)[0]?.files[0];
                            if (file) {
                                if (!allowedTypes.includes(file.type)) {
                                    markError(fieldId, 'Format file hanya JPG, PNG, PDF');
                                    isValid = false;
                                }
                                if (file.size > maxSize) {
                                    markError(fieldId, 'Ukuran file maksimal 5 MB');
                                    isValid = false;
                                }
                            }
                        });
                        break;
                }
                return isValid;
            }

            function validateCurrentTab() {
                let isValid = true;
                $('.text-danger').text('');
                $('.form-control').removeClass('is-invalid');
                // Sama seperti validateCurrentTabSilent tapi pakai showError
                // (Copy logic dari validateCurrentTabSilent, ganti markError jadi showError)
                // Untuk ringkas, saya pakai markError saja di sini
                return validateCurrentTabSilent();
            }

            function markError(fieldId, message) {
                $(`#${fieldId}`).addClass('is-invalid');
                const fieldName = fieldId.charAt(0).toUpperCase() + fieldId.slice(1);
                $(`#err${fieldName}`).text(message);
            }

            function showError(fieldId, message) {
                const $field = $(`#${fieldId}`);
                if (!$field.length) return;
                const cleanFieldId = fieldId.replace(/[^a-zA-Z0-9]/g, '');
                const errorId = `err${cleanFieldId.charAt(0).toUpperCase() + cleanFieldId.slice(1)}`;
                $field.addClass('is-invalid');
                $(`#${errorId}`).text(message);
                $('html, body').animate({
                    scrollTop: $field.offset().top - 100
                }, 500, () => {
                    $field.focus();
                });
            }

            function displayErrors(errors) {
                $.each(errors, function(key, value) {
                    $(`[name="${key}"]`).addClass('is-invalid');
                    const fieldName = key.charAt(0).toUpperCase() + key.slice(1);
                    if ($(`#err${fieldName}`).length) $(`#err${fieldName}`).text(value[0]);
                });
            }

            $('.form-control').on('input', function() {
                if ($(this).hasClass('is-invalid')) {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.text-danger').text('');
                }
            });

            // ============================================
            // FILE UPLOAD HANDLERS (Manual - Satu per Satu)
            // ============================================

            // KTP
            $('#btnUploadKtp').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File KTP sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadKtp').click();
            });

            // Selfie
            $('#btnUploadSelfie').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File Selfie sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadSelfie').click();
            });

            // NPWP
            $('#btnUploadNpwp').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File NPWP sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadNpwp').click();
            });

            // KK
            $('#btnUploadKk').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File KK sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadKk').click();
            });

            // SK Karyawan
            $('#btnUploadSk').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File SK Karyawan sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadSk').click();
            });

            // Slip Gaji
            $('#btnUploadSlip').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File Slip Gaji sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadSlip').click();
            });

            // ID Card
            $('#btnUploadIdCard').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File ID Card sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadIdCard').click();
            });

            // Buku Tabungan
            $('#btnUploadBukuTabungan').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File Buku Tabungan sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadBukuTabungan').click();
            });

            // Mutasi Rekening
            $('#btnUploadMutasi').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File Mutasi Rekening sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadMutasi').click();
            });

            // Form SLIK
            $('#btnUploadSlik').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File Form SLIK sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadSlik').click();
            });

            // Form Kredit
            $('#btnUploadKredit').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File Form Kredit sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadKredit').click();
            });

            // Form BTA
            $('#btnUploadBta').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File Form BTA sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadBta').click();
            });

            // Checklist
            $('#btnUploadChecklist').click(function() {
                if ($(this).prop('disabled')) {
                    toastr.info('File Checklist sudah disetujui dan tidak dapat diubah', 'Info');
                    return false;
                }
                $('#uploadChecklist').click();
            });

            // ============================================
            // FILE PREVIEW (Hanya jika file dipilih & input tidak disabled)
            // ============================================
            $('.d-none[type="file"]').on('change', function() {
                // Skip jika input disabled
                if ($(this).prop('disabled')) {
                    $(this).val(''); // Reset value
                    return;
                }

                const id = $(this).attr('id');
                const file = this.files[0];

                if (file) {
                    // Validasi ukuran (5 MB)
                    const maxSize = 5 * 1024 * 1024;
                    if (file.size > maxSize) {
                        toastr.error('Ukuran file maksimal 5 MB', 'Gagal!');
                        $(this).val('');
                        return;
                    }

                    // Validasi tipe file
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                    if (!allowedTypes.includes(file.type)) {
                        toastr.error('Format file hanya JPG, PNG, PDF', 'Gagal!');
                        $(this).val('');
                        return;
                    }

                    // Preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(`#preview${id.charAt(0).toUpperCase() + id.slice(1)}`).html(`
                <div class="alert alert-info">
                    <i class="mdi mdi-file mr-2"></i> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                </div>
            `);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // CONDITIONAL FIELDS
            $('#statusPerkawinan').change(function() {
                if ($(this).val() === 'Kawin') {
                    $('.pasangan-field').removeClass('d-none');
                } else {
                    $('.pasangan-field').addClass('d-none');
                    $('#namaPasangan, #tglLahirPasangan').val('');
                }
            });

            $('#statusPegawai').change(function() {
                if ($(this).val() === 'Karyawan Tidak Tetap') {
                    $('.kontrak-field').removeClass('d-none');
                } else {
                    $('.kontrak-field').addClass('d-none');
                    $('#tglAkhirKontrak').val('');
                }
            });

            // FORMAT RUPIAH & CALCULATIONS
            $('.rupiah-format').on('input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                if (value) $(this).val(formatRupiah(value));
            });

            function formatRupiah(angka) {
                let number_string = angka.toString().replace(/[^,\d]/g, ''),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) rupiah += (sisa ? '.' : '') + ribuan.join('.');
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return 'Rp ' + rupiah;
            }

            function parseRupiah(rupiah) {
                return parseInt(rupiah.replace(/[^0-9]/g, ''));
            }

            function calculateAngsuran() {
                const plafond = parseRupiah($('#plafond').val() || '0');
                const jangkaWaktu = parseInt($('#jangka_waktu').val()) || 0;
                const sukuBunga = parseFloat($('#suku_bunga').val()) || 0;
                if (plafond > 0 && jangkaWaktu > 0 && sukuBunga > 0) {
                    const bungaPerBulan = (plafond * sukuBunga) / 100;
                    const angsuranPokok = plafond / jangkaWaktu;
                    $('#nilai_angsuran').val(formatRupiah(Math.round(angsuranPokok + bungaPerBulan).toString()));
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
                    $('#akhir_pinjaman').val(
                        `${startDate.getFullYear()}-${String(startDate.getMonth() + 1).padStart(2, '0')}-${String(startDate.getDate()).padStart(2, '0')}`
                    );
                } else {
                    $('#akhir_pinjaman').val('');
                }
            }

            $('#plafond, #jangka_waktu, #suku_bunga').on('input', calculateAngsuran);
            $('#mulai_pinjaman, #jangka_waktu').on('change input', calculateAkhirPinjaman);

            updateTabNavigation();
        });
    </script>
@endpush
