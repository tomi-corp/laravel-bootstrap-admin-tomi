@extends('bta.layout.app')
@section('title', 'Edit Data Nasabah')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Data Nasabah</h4>


                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-identitas" role="tab">
                                <i class="mdi mdi-account-circle"></i> Identitas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-pribadi" role="tab">
                                <i class="mdi mdi-information-outline"></i> Data Pribadi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-pekerjaan" role="tab">
                                <i class="mdi mdi-briefcase"></i> Pekerjaan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-bank" role="tab">
                                <i class="mdi mdi-bank"></i> Bank
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-kta" role="tab">
                                <i class="mdi mdi-credit-card"></i> KTA Multiguna
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-dokumen" role="tab">
                                <i class="mdi mdi-file-document"></i> Dokumen
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <form id="formNasabahBertahap" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $nasabah->id }}">

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
                                <h5 class="mb-4"><i class="mdi mdi-account-circle text-primary"></i> Identitas Utama
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
                                            <label>Upload KTP <span class="text-danger">*</span></label>

                                            {{-- BADGE STATUS (Pending, Approve, Reject) --}}
                                            @if (isset($fileMeta['ktp']['status']))
                                            @if ($fileMeta['ktp']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['ktp']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['ktp']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
                                            @endif

                                            @if (isset($fileMeta['ktp']['status']) && $fileMeta['ktp']['status'] ===
                                            'reject' && $fileMeta['ktp']['note'])
                                            <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                <i class="mdi mdi-alert-circle mr-1"></i>
                                                {{ $fileMeta['ktp']['note'] }}
                                            </div>
                                            @endif

                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadKtp" @if (isset($fileMeta['ktp']['status']) &&
                                                    $fileMeta['ktp']['status']==='approve' ) disabled @endif>
                                                    <i class="mdi mdi-upload"></i>
                                                    @if (isset($fileMeta['ktp']['status']) && $fileMeta['ktp']['status']
                                                    === 'approve')
                                                    File Disetujui
                                                    @else
                                                    Pilih File KTP
                                                    @endif
                                                </button>
                                                <input type="file" class="d-none" id="uploadKtp" name="upload_ktp"
                                                    accept=".jpg,.jpeg,.png,.pdf" @if (isset($fileMeta['ktp']['status'])
                                                    && $fileMeta['ktp']['status']==='approve' ) disabled @endif
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
                                                @if (isset($fileMeta['ktp']['status']) && $fileMeta['ktp']['status'] ===
                                                'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['ktp']['status']) && $fileMeta['ktp']['status']
                                                === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
                                                @endif
                                            </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadKtp"></div>
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
                                            <label>Upload NPWP <span class="text-danger">*</span></label>

                                            @if (isset($fileMeta['npwp']['status']))
                                            @if ($fileMeta['npwp']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['npwp']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['npwp']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
                                            @endif

                                            @if (isset($fileMeta['npwp']['status']) && $fileMeta['npwp']['status'] ===
                                            'reject' && $fileMeta['npwp']['note'])
                                            <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                <i class="mdi mdi-alert-circle mr-1"></i>
                                                {{ $fileMeta['npwp']['note'] }}
                                            </div>
                                            @endif

                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadNpwp" @if (isset($fileMeta['npwp']['status']) &&
                                                    $fileMeta['npwp']['status']==='approve' ) disabled @endif>
                                                    <i class="mdi mdi-upload"></i>
                                                    @if (isset($fileMeta['npwp']['status']) &&
                                                    $fileMeta['npwp']['status'] === 'approve')
                                                    File Disetujui
                                                    @else
                                                    Pilih File NPWP
                                                    @endif
                                                </button>
                                                <input type="file" class="d-none" id="uploadNpwp" name="upload_npwp"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @disabled(isset($fileMeta['npwp']['status']) &&
                                                    $fileMeta['npwp']['status']==='approve' )
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
                                                @if (isset($fileMeta['npwp']['status']) && $fileMeta['npwp']['status']
                                                === 'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['npwp']['status']) &&
                                                $fileMeta['npwp']['status'] === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
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
                                                <option value="Laki-laki" {{ $nasabah->jenis_kelamin == 'Laki-laki' ?
                                                    'selected' : '' }}>
                                                    Laki-laki</option>
                                                <option value="Perempuan" {{ $nasabah->jenis_kelamin == 'Perempuan' ?
                                                    'selected' : '' }}>
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

                                            @if (isset($fileMeta['kk']['status']))
                                            @if ($fileMeta['kk']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['kk']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['kk']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
                                            @endif

                                            @if (isset($fileMeta['kk']['status']) && $fileMeta['kk']['status'] ===
                                            'reject' && $fileMeta['kk']['note'])
                                            <div class="alert alert-danger py-1 px-2 mb-2 small">
                                                <i class="mdi mdi-alert-circle mr-1"></i>
                                                {{ $fileMeta['kk']['note'] }}
                                            </div>
                                            @endif

                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadKk" @if (isset($fileMeta['kk']['status']) &&
                                                    $fileMeta['kk']['status']==='approve' ) disabled @endif>
                                                    <i class="mdi mdi-upload"></i>
                                                    @if (isset($fileMeta['kk']['status']) && $fileMeta['kk']['status']
                                                    === 'approve')
                                                    File Disetujui
                                                    @else
                                                    Pilih File KK
                                                    @endif
                                                </button>
                                                <input type="file" class="d-none" id="uploadKk" name="upload_kk"
                                                    accept=".jpg,.jpeg,.png,.pdf" @if (isset($fileMeta['kk']['status'])
                                                    && $fileMeta['kk']['status']==='approve' ) disabled @endif
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
                                                @if (isset($fileMeta['kk']['status']) && $fileMeta['kk']['status'] ===
                                                'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['kk']['status']) && $fileMeta['kk']['status']
                                                === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
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

                            <!-- TAB 2: DATA PRIBADI & ALAMAT (MERGED) -->
                            <div class="tab-pane" id="tab-pribadi" role="tabpanel">
                                <h5 class="mb-4"><i class="mdi mdi-information-outline text-primary"></i> Data
                                    Pribadi & Alamat</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Agama <span class="text-danger">*</span></label>
                                            <select class="form-control" id="agama" name="agama">
                                                <option value="">Pilih</option>
                                                <option value="Islam" {{ $nasabah->agama == 'Islam' ? 'selected' : ''
                                                    }}>Islam</option>
                                                <option value="Katolik" {{ $nasabah->agama == 'Katolik' ? 'selected' :
                                                    '' }}>Katolik
                                                </option>
                                                <option value="Kristen" {{ $nasabah->agama == 'Kristen' ? 'selected' :
                                                    '' }}>Kristen
                                                </option>
                                                <option value="Buddha" {{ $nasabah->agama == 'Buddha' ? 'selected' : ''
                                                    }}>Buddha</option>
                                                <option value="Konghucu" {{ $nasabah->agama == 'Konghucu' ? 'selected' :
                                                    '' }}>Konghucu
                                                </option>
                                            </select>
                                            <small class="text-danger" id="errAgama"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Pendidikan Terakhir <span class="text-danger">*</span></label>
                                            <select class="form-control" id="pendidikan" name="pendidikan_terakhir">
                                                <option value="">Pilih</option>
                                                <option value="SD" {{ $nasabah->pendidikan_terakhir == 'SD' ? 'selected'
                                                    : '' }}>SD
                                                </option>
                                                <option value="SMP" {{ $nasabah->pendidikan_terakhir == 'SMP' ?
                                                    'selected' : '' }}>SMP
                                                </option>
                                                <option value="SMA" {{ $nasabah->pendidikan_terakhir == 'SMA' ?
                                                    'selected' : '' }}>SMA
                                                </option>
                                                <option value="D1" {{ $nasabah->pendidikan_terakhir == 'D1' ? 'selected'
                                                    : '' }}>D1
                                                </option>
                                                <option value="D3" {{ $nasabah->pendidikan_terakhir == 'D3' ? 'selected'
                                                    : '' }}>D3
                                                </option>
                                                <option value="D4" {{ $nasabah->pendidikan_terakhir == 'D4' ? 'selected'
                                                    : '' }}>D4
                                                </option>
                                                <option value="S1" {{ $nasabah->pendidikan_terakhir == 'S1' ? 'selected'
                                                    : '' }}>S1
                                                </option>
                                                <option value="S2" {{ $nasabah->pendidikan_terakhir == 'S2' ? 'selected'
                                                    : '' }}>S2
                                                </option>
                                                <option value="S3" {{ $nasabah->pendidikan_terakhir == 'S3' ? 'selected'
                                                    : '' }}>S3
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
                                                <option value="Kawin" {{ $nasabah->status_perkawinan == 'Kawin' ?
                                                    'selected' : '' }}>
                                                    Kawin</option>
                                                <option value="Tidak Kawin" {{ $nasabah->status_perkawinan == 'Tidak
                                                    Kawin' ? 'selected' : '' }}>
                                                    Tidak Kawin</option>
                                                <option value="Cerai Hidup" {{ $nasabah->status_perkawinan == 'Cerai
                                                    Hidup' ? 'selected' : '' }}>
                                                    Cerai Hidup</option>
                                                <option value="Cerai Mati" {{ $nasabah->status_perkawinan == 'Cerai
                                                    Mati' ? 'selected' : '' }}>
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

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Alamat Rumah <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="alamatRumah" name="alamat_rumah" rows="3"
                                                placeholder="Alamat lengkap">{{ old('alamat_rumah', $nasabah->alamat_rumah) }}</textarea>
                                            <small class="text-danger" id="errAlamatRumah"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Provinsi <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="provinsiSelect" name="provinsi_id">
                                                <option value="">Pilih Provinsi</option>
                                                @foreach ($provinsis as $provinsi)
                                                <option value="{{ $provinsi->id }}" {{ $nasabah->provinsi_id ==
                                                    $provinsi->id ? 'selected' : '' }}>
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
                                            <select class="form-control select2" id="kotaSelect" name="kota_id" {{
                                                !$nasabah->provinsi_id ? 'disabled' : '' }}>
                                                <option value="">Pilih Kota/Kabupaten</option>
                                                @if ($nasabah->kabupaten)
                                                <option value="{{ $nasabah->kota_id }}" selected>
                                                    {{ $nasabah->kabupaten->kabupaten }}
                                                </option>
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
                                            <select class="form-control select2" id="kecamatanSelect"
                                                name="kecamatan_id" {{ !$nasabah->kota_id ? 'disabled' : '' }}>
                                                <option value="">Pilih Kecamatan</option>
                                                @if ($nasabah->kecamatan)
                                                <option value="{{ $nasabah->kecamatan_id }}" selected>
                                                    {{ $nasabah->kecamatan->kecamatan }}
                                                </option>
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
                                            <select class="form-control select2" id="kelurahanSelect"
                                                name="kelurahan_id" {{ !$nasabah->kecamatan_id ? 'disabled' : '' }}>
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
                                            <input type="text" class="form-control" id="kodeDati" name="kode_dati_id"
                                                value="{{ old('kode_dati_id', $nasabah->kode_dati_id) }}" readonly
                                                placeholder="00000">
                                            <small class="text-danger" id="errKodeDati"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: PEKERJAAN -->
                            <div class="tab-pane" id="tab-pekerjaan" role="tabpanel">
                                <h5 class="mb-4"><i class="mdi mdi-briefcase text-primary"></i> Data Pekerjaan</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status Pegawai <span class="text-danger">*</span></label>
                                            <select class="form-control" id="statusPegawai" name="status_pegawai">
                                                <option value="">Pilih</option>
                                                <option value="Karyawan Tetap" {{ ($nasabah->status_pegawai ?? '') ==
                                                    'Karyawan Tetap' ? 'selected' : '' }}>
                                                    Karyawan Tetap
                                                </option>
                                                <option value="Karyawan Tidak Tetap" {{ ($nasabah->status_pegawai ?? '')
                                                    == 'Karyawan Tidak Tetap' ? 'selected' : '' }}>
                                                    Karyawan Tidak Tetap
                                                </option>
                                                <option value="Pensiun" {{ ($nasabah->status_pegawai ?? '') == 'Pensiun'
                                                    ? 'selected' : '' }}>
                                                    Pensiun
                                                </option>
                                            </select>
                                            <small class="text-danger" id="errStatusPegawai"></small>
                                        </div>
                                    </div>

                                    <div
                                        class="col-md-6 pensiun-field {{ !in_array($nasabah->status_pegawai, ['Karyawan Tidak Tetap', 'Karyawan Tetap', 'Pensiun']) ? 'd-none' : '' }}">
                                        <div class="form-group">
                                            <label>Nama Perusahaan <span class="text-danger">*</span></label>
                                            <input class="form-control" id="namaPerusahaan" name="nama_perusahaan"
                                                placeholder="Nama perusahaan"
                                                value="{{ old('nama_perusahaan', $nasabah->nama_perusahaan) }}" />
                                            <small class="text-danger" id="errNamaPerusahaan"></small>
                                        </div>
                                    </div>

                                    <div
                                        class="col-md-6 pensiun-field {{ !in_array($nasabah->status_pegawai, ['Karyawan Tidak Tetap', 'Karyawan Tetap', 'Pensiun']) ? 'd-none' : '' }}">
                                        <div class="form-group">
                                            <label>Bidang Usaha <span class="text-danger">*</span></label>
                                            <input class="form-control" id="bidangUsaha" name="bidang_usaha"
                                                placeholder="Contoh: Retail, Konstruksi"
                                                value="{{ old('bidang_usaha', $nasabah->bidang_usaha) }}" />
                                            <small class="text-danger" id="errBidangUsaha"></small>
                                        </div>
                                    </div>

                                    <div
                                        class="col-md-12 pensiun-field {{ !in_array($nasabah->status_pegawai, ['Karyawan Tidak Tetap', 'Karyawan Tetap', 'Pensiun']) ? 'd-none' : '' }}">
                                        <div class="form-group">
                                            <label>Alamat Tempat Kerja <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="alamatKerja" name="alamat_kerja" rows="3"
                                                placeholder="Alamat kantor">{{ old('alamat_kerja', $nasabah->alamat_kerja) }}</textarea>
                                            <small class="text-danger" id="errAlamatKerja"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NIP (No Induk Pegawai) / KARIP (Kartu Identitas Pensiun)</label>
                                            <input class="form-control" id="nip" name="nip" placeholder="Opsional"
                                                value="{{ old('nip', $nasabah->nip) }}" />
                                            <small class="text-danger" id="errNip"></small>
                                        </div>
                                    </div>

                                    <div
                                        class="col-md-6 kontrak-field {{ !in_array($nasabah->status_pegawai, ['Karyawan Tidak Tetap', 'Pensiun']) ? 'd-none' : '' }}">
                                        <div class="form-group">
                                            <label>Tgl Akhir Kontrak Kerja / Tanggal Pensiun</label>
                                            <input class="form-control" id="tglAkhirKontrak" name="tgl_akhir_kontrak"
                                                type="date"
                                                value="{{ old('tgl_akhir_kontrak', $nasabah->tgl_akhir_kontrak) }}" />
                                            <small class="text-danger" id="errTglAkhirKontrak"></small>
                                        </div>
                                    </div>

                                    <!-- UPLOAD SK Karyawan -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload SK Karyawan / SK Pensiun <span
                                                    class="text-danger">*</span></label>

                                            @if (isset($fileMeta['sk_karyawan']['status']))
                                            @if ($fileMeta['sk_karyawan']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['sk_karyawan']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['sk_karyawan']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
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
                                                    id="btnUploadSk" @if (isset($fileMeta['sk_karyawan']['status']) &&
                                                    $fileMeta['sk_karyawan']['status']==='approve' ) disabled @endif>
                                                    <i class="mdi mdi-upload"></i>
                                                    @if (isset($fileMeta['sk_karyawan']['status']) &&
                                                    $fileMeta['sk_karyawan']['status'] === 'approve')
                                                    File Disetujui
                                                    @else
                                                    Pilih File SK
                                                    @endif
                                                </button>
                                                <input type="file" class="d-none" id="uploadSk" name="upload_sk"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @disabled(isset($fileMeta['sk_karyawan']['status']) &&
                                                    $fileMeta['sk_karyawan']['status']==='approve' )
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
                                                @if (isset($fileMeta['sk_karyawan']['status']) &&
                                                $fileMeta['sk_karyawan']['status'] === 'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['sk_karyawan']['status']) &&
                                                $fileMeta['sk_karyawan']['status'] === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
                                                @endif
                                            </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadSk"></div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD Slip Gaji -->
                                    <div
                                        class="col-md-6 pensiun-field {{ !in_array($nasabah->status_pegawai, ['Karyawan Tidak Tetap', 'Karyawan Tetap']) ? 'd-none' : '' }}">
                                        <div class="form-group">
                                            <label>Upload Slip Gaji Terakhir <span class="text-danger">*</span></label>

                                            @if (isset($fileMeta['slip_gaji']['status']))
                                            @if ($fileMeta['slip_gaji']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['slip_gaji']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['slip_gaji']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
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
                                                    id="btnUploadSlip" @if (isset($fileMeta['slip_gaji']['status']) &&
                                                    $fileMeta['slip_gaji']['status']==='approve' ) disabled @endif>
                                                    <i class="mdi mdi-upload"></i>
                                                    @if (isset($fileMeta['slip_gaji']['status']) &&
                                                    $fileMeta['slip_gaji']['status'] === 'approve')
                                                    File Disetujui
                                                    @else
                                                    Pilih Slip Gaji
                                                    @endif
                                                </button>
                                                <input type="file" class="d-none" id="uploadSlip" name="upload_slip"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @disabled(isset($fileMeta['slip_gaji']['status']) &&
                                                    $fileMeta['slip_gaji']['status']==='approve' )
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
                                                @if (isset($fileMeta['slip_gaji']['status']) &&
                                                $fileMeta['slip_gaji']['status'] === 'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['slip_gaji']['status']) &&
                                                $fileMeta['slip_gaji']['status'] === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
                                                @endif
                                            </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadSlip"></div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD ID Card -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload ID Card / ID Pensiun (Karip) <span
                                                    class="text-danger">*</span></label>

                                            @if (isset($fileMeta['id_card']['status']))
                                            @if ($fileMeta['id_card']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['id_card']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['id_card']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
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
                                                    id="btnUploadIdCard" @if (isset($fileMeta['id_card']['status']) &&
                                                    $fileMeta['id_card']['status']==='approve' ) disabled @endif>
                                                    <i class="mdi mdi-upload"></i>
                                                    @if (isset($fileMeta['id_card']['status']) &&
                                                    $fileMeta['id_card']['status'] === 'approve')
                                                    File Disetujui
                                                    @else
                                                    Pilih ID Card
                                                    @endif
                                                </button>
                                                <input type="file" class="d-none" id="uploadIdCard"
                                                    name="upload_id_card" accept=".jpg,.jpeg,.png,.pdf"
                                                    @disabled(isset($fileMeta['id_card']['status']) &&
                                                    $fileMeta['id_card']['status']==='approve' )
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
                                                @if (isset($fileMeta['id_card']['status']) &&
                                                $fileMeta['id_card']['status'] === 'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['id_card']['status']) &&
                                                $fileMeta['id_card']['status'] === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
                                                @endif
                                            </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadIdCard"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: BANK -->
                            <div class="tab-pane" id="tab-bank" role="tabpanel">
                                <h5 class="mb-4"><i class="mdi mdi-bank text-primary"></i> Data Bank</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Bank <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="bankSelect" name="bank_id">
                                                <option value="">Pilih</option>
                                                @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}" {{ $nasabah->bank_id == $bank->id ?
                                                    'selected' : '' }}>
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

                                            @if (isset($fileMeta['buku_tabungan']['status']))
                                            @if ($fileMeta['buku_tabungan']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['buku_tabungan']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['buku_tabungan']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
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
                                                    @disabled(isset($fileMeta['buku_tabungan']['status']) &&
                                                    $fileMeta['buku_tabungan']['status']==='approve' )>
                                                    <i class="mdi mdi-upload"></i>
                                                    @if (isset($fileMeta['buku_tabungan']['status']) &&
                                                    $fileMeta['buku_tabungan']['status'] === 'approve')
                                                    File Disetujui
                                                    @else
                                                    Pilih Buku Tabungan
                                                    @endif
                                                </button>
                                                <input type="file" class="d-none" id="uploadBukuTabungan"
                                                    name="upload_buku_tabungan" accept=".jpg,.jpeg,.png,.pdf"
                                                    @disabled(isset($fileMeta['buku_tabungan']['status']) &&
                                                    $fileMeta['buku_tabungan']['status']==='approve' )
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
                                                @if (isset($fileMeta['buku_tabungan']['status']) &&
                                                $fileMeta['buku_tabungan']['status'] === 'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['buku_tabungan']['status']) &&
                                                $fileMeta['buku_tabungan']['status'] === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
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

                                            @if (isset($fileMeta['mutasi_rekening']['status']))
                                            @if ($fileMeta['mutasi_rekening']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['mutasi_rekening']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['mutasi_rekening']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
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
                                                    @disabled(isset($fileMeta['mutasi_rekening']['status']) &&
                                                    $fileMeta['mutasi_rekening']['status']==='approve' )>
                                                    <i class="mdi mdi-upload"></i>
                                                    {{ (isset($fileMeta['mutasi_rekening']['status']) &&
                                                    $fileMeta['mutasi_rekening']['status'] === 'approve') ? 'File
                                                    Disetujui' : 'Pilih Mutasi Rekening' }}
                                                </button>
                                                <input type="file" class="d-none" id="uploadMutasi" name="upload_mutasi"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @disabled(isset($fileMeta['mutasi_rekening']['status']) &&
                                                    $fileMeta['mutasi_rekening']['status']==='approve' )
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
                                                @if (isset($fileMeta['mutasi_rekening']['status']) &&
                                                $fileMeta['mutasi_rekening']['status'] === 'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['mutasi_rekening']['status']) &&
                                                $fileMeta['mutasi_rekening']['status'] === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
                                                @endif
                                            </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadMutasi"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 5: KTA MULTIGUNA -->
                            <div class="tab-pane" id="tab-kta" role="tabpanel">
                                <h5 class="mb-4"><i class="mdi mdi-credit-card text-primary"></i> Produk Kredit: KTA
                                    Multiguna</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="plafond">Jumlah Pengajuan: <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control rupiah-format" name="plafond"
                                                id="plafond"
                                                value="{{ old('plafond', $nasabah->plafond ? number_format($nasabah->plafond, 0, ',', '.') : '') }}"
                                                placeholder="Jumlah Pengajuan">
                                            <small class="text-danger" id="errPlafond"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="persetujuan_analyst">Persetujuan Analyst: <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" readonly class="form-control rupiah-format"
                                                name="persetujuan_analyst" id="persetujuan_analyst"
                                                value="{{ old('persetujuan_analyst', $nasabah->persetujuan_analyst ? number_format($nasabah->persetujuan_analyst, 0, ',', '.') : '') }}"
                                                placeholder="Persetujuan Analyst">
                                            <small class="text-danger" id="errpersetujuan_analyst"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="tipe_bunga">Tipe Bunga: <span
                                                    class="text-danger">*</span></label>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio1" name="tipe_bunga"
                                                    class="custom-control-input" value="flat" {{ $nasabah->tipe_bunga ==
                                                'flat' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="customRadio1">Flat</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio2" name="tipe_bunga"
                                                    class="custom-control-input" value="efektif" {{ $nasabah->tipe_bunga
                                                == 'efektif' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="customRadio2">Efektif
                                                    (Anuitas)</label>
                                            </div>
                                            <small class="text-danger" id="errTipeBunga"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="jangka_waktu">Jangka Waktu (bulan): <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="jangka_waktu"
                                                id="jangka_waktu"
                                                value="{{ old('jangka_waktu', $nasabah->jangka_waktu) }}" min="1"
                                                max="180" placeholder="Jangka Waktu">
                                            <small class="text-danger" id="errJangkaWaktu"></small>
                                        </div>

                                        <div class="form-group">
                                            <label for="suku_bunga" class="form-label">Suku Bunga: <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="suku_bunga"
                                                    placeholder="Masukkan angka" aria-label="suku_bunga"
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
                                            <label for="biaya_bank">Biaya Bank: <span
                                                    class="text-danger">*</span></label>
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
                                            <select class="form-control" name="tujuan_penggunaan"
                                                id="tujuan_penggunaan">
                                                <option value="" disabled selected>-- Pilih Tujuan --</option>
                                                @php $options = ['Konsumsi', 'Modal Kerja', 'Investasi']; @endphp
                                                @foreach ($options as $option)
                                                <option value="{{ $option }}" {{ $nasabah->tujuan_penggunaan == $option
                                                    ? 'selected' : '' }}>
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
                                            <label for="tanggal_pengajuan">Tanggal Pengajuan: <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tanggal_pengajuan"
                                                id="tanggal_pengajuan"
                                                value="{{ old('tanggal_pengajuan', $nasabah->tanggal_pengajuan) }}"
                                                min="{{ now()->subWeek()->toDateString() }}"
                                                max="{{ now()->addWeek()->toDateString() }}">
                                            <small class="text-danger" id="errTanggalPengajuan"></small>
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
                                            <input type="number" step="0.01" class="form-control" name="dbr" id="dbr"
                                                value="{{ old('dbr', $nasabah->dbr) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 6: UPLOAD DOKUMEN -->
                            <div class="tab-pane" id="tab-dokumen" role="tabpanel">
                                <h5 class="mb-4"><i class="mdi mdi-file-document text-primary"></i> Upload Dokumen
                                </h5>
                                <div class="row">
                                    <!-- UPLOAD Form SLIK -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Form SLIK <span class="text-danger">*</span></label>

                                            @if (isset($fileMeta['form_slik']['status']))
                                            @if ($fileMeta['form_slik']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['form_slik']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['form_slik']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
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
                                                    id="btnUploadSlik" @if (isset($fileMeta['form_slik']['status']) &&
                                                    $fileMeta['form_slik']['status']==='approve' ) disabled @endif>
                                                    <i class="mdi mdi-upload"></i>
                                                    @if (isset($fileMeta['form_slik']['status']) &&
                                                    $fileMeta['form_slik']['status'] === 'approve')
                                                    File Disetujui
                                                    @else
                                                    Pilih Form SLIK
                                                    @endif
                                                </button>
                                                <input type="file" class="d-none" id="uploadSlik" name="upload_slik"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @disabled(isset($fileMeta['form_slik']['status']) &&
                                                    $fileMeta['form_slik']['status']==='approve' )
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
                                                @if (isset($fileMeta['form_slik']['status']) &&
                                                $fileMeta['form_slik']['status'] === 'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['form_slik']['status']) &&
                                                $fileMeta['form_slik']['status'] === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
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

                                            @if (isset($fileMeta['form_kredit']['status']))
                                            @if ($fileMeta['form_kredit']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['form_kredit']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['form_kredit']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
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
                                                    id="btnUploadKredit" @if (isset($fileMeta['form_kredit']['status'])
                                                    && $fileMeta['form_kredit']['status']==='approve' ) disabled @endif>
                                                    <i class="mdi mdi-upload"></i>
                                                    @if (isset($fileMeta['form_kredit']['status']) &&
                                                    $fileMeta['form_kredit']['status'] === 'approve')
                                                    File Disetujui
                                                    @else
                                                    Pilih Form Kredit
                                                    @endif
                                                </button>
                                                <input type="file" class="d-none" id="uploadKredit" name="upload_kredit"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @disabled(isset($fileMeta['form_kredit']['status']) &&
                                                    $fileMeta['form_kredit']['status']==='approve' )
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
                                                @if (isset($fileMeta['form_kredit']['status']) &&
                                                $fileMeta['form_kredit']['status'] === 'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['form_kredit']['status']) &&
                                                $fileMeta['form_kredit']['status'] === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
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

                                            @if (isset($fileMeta['form_bta']['status']))
                                            @if ($fileMeta['form_bta']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['form_bta']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['form_bta']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
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
                                                    id="btnUploadBta" @if (isset($fileMeta['form_bta']['status']) &&
                                                    $fileMeta['form_bta']['status']==='approve' ) disabled @endif>
                                                    <i class="mdi mdi-upload"></i>
                                                    @if (isset($fileMeta['form_bta']['status']) &&
                                                    $fileMeta['form_bta']['status'] === 'approve')
                                                    File Disetujui
                                                    @else
                                                    Pilih Form BTA
                                                    @endif
                                                </button>
                                                <input type="file" class="d-none" id="uploadBta" name="upload_bta"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    @disabled(isset($fileMeta['form_bta']['status']) &&
                                                    $fileMeta['form_bta']['status']==='approve' )
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
                                                @if (isset($fileMeta['form_bta']['status']) &&
                                                $fileMeta['form_bta']['status'] === 'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i>
                                                    Sudah disetujui</small>
                                                @elseif (isset($fileMeta['form_bta']['status']) &&
                                                $fileMeta['form_bta']['status'] === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i>
                                                    Menunggu review</small>
                                                @endif
                                            </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadBta"></div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD Checklist -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Checklist Verifikasi <span
                                                    class="text-danger">*</span></label>

                                            @if (isset($fileMeta['form_checklist']['status']))
                                            @if ($fileMeta['form_checklist']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['form_checklist']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['form_checklist']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
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
                                                    @disabled(isset($fileMeta['form_checklist']['status']) &&
                                                    $fileMeta['form_checklist']['status']==='approve' )>
                                                    <i class="mdi mdi-upload"></i>
                                                    {{ (isset($fileMeta['form_checklist']['status']) &&
                                                    $fileMeta['form_checklist']['status'] === 'approve') ? 'File
                                                    Disetujui' : 'Pilih Checklist' }}
                                                </button>
                                                <input type="file" class="d-none" id="uploadChecklist"
                                                    name="upload_checklist" accept=".jpg,.jpeg,.png,.pdf"
                                                    @disabled(isset($fileMeta['form_checklist']['status']) &&
                                                    $fileMeta['form_checklist']['status']==='approve' )
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
                                                @if (isset($fileMeta['form_checklist']['status']) &&
                                                $fileMeta['form_checklist']['status'] === 'approve')
                                                <small class="text-success ml-2"><i class="mdi mdi-check"></i> Sudah
                                                    disetujui</small>
                                                @elseif (isset($fileMeta['form_checklist']['status']) &&
                                                $fileMeta['form_checklist']['status'] === 'pending')
                                                <small class="text-warning ml-2"><i class="mdi mdi-clock"></i> Menunggu
                                                    review</small>
                                                @endif
                                            </div>
                                            @endif
                                            <div class="mt-2 preview-container" id="previewUploadChecklist"></div>
                                        </div>
                                    </div>

                                    @php
                                    $statusPegawai = strtolower(trim($dataPegawai->status_pegawai ?? ''));
                                    @endphp

                                    @if ($statusPegawai === 'pensiun')
                                    <!-- ================= VIDEO UPLOAD ================= -->

                                    <!-- UPLOAD Video Interview -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Video Interview <span class="text-danger">*</span></label>

                                            @if (isset($fileMeta['video_interview']['status']))
                                            @if ($fileMeta['video_interview']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['video_interview']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['video_interview']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
                                            @endif

                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadVideoInterview"
                                                    @disabled(isset($fileMeta['video_interview']['status']) &&
                                                    $fileMeta['video_interview']['status']==='approve' )>
                                                    <i class="mdi mdi-video"></i>
                                                    {{ (isset($fileMeta['video_interview']['status']) &&
                                                    $fileMeta['video_interview']['status'] === 'approve') ? 'File
                                                    Disetujui' : 'Pilih Video Interview' }}
                                                </button>

                                                <input type="file" class="d-none" id="uploadVideoInterview"
                                                    name="upload_video_interview" accept=".mp4,.mov,.avi,.webm"
                                                    @disabled(isset($fileMeta['video_interview']['status']) &&
                                                    $fileMeta['video_interview']['status']==='approve' )
                                                    data-status="{{ $fileMeta['video_interview']['status'] ?? 'pending' }}" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- UPLOAD Video Asuransi -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upload Video Asuransi <span class="text-danger">*</span></label>

                                            @if (isset($fileMeta['video_asuransi']['status']))
                                            @if ($fileMeta['video_asuransi']['status'] === 'pending')
                                            <span class="badge badge-warning mb-2">
                                                <i class="mdi mdi-clock-outline mr-1"></i> Pending
                                            </span>
                                            @elseif ($fileMeta['video_asuransi']['status'] === 'approve')
                                            <span class="badge badge-success mb-2">
                                                <i class="mdi mdi-check-circle mr-1"></i> Approved
                                            </span>
                                            @elseif ($fileMeta['video_asuransi']['status'] === 'reject')
                                            <span class="badge badge-danger mb-2">
                                                <i class="mdi mdi-close-circle mr-1"></i> Rejected
                                            </span>
                                            @endif
                                            @endif

                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-primary btn-block"
                                                    id="btnUploadVideoAsuransi"
                                                    @disabled(isset($fileMeta['video_asuransi']['status']) &&
                                                    $fileMeta['video_asuransi']['status']==='approve' )>
                                                    <i class="mdi mdi-video"></i>
                                                    {{ (isset($fileMeta['video_asuransi']['status']) &&
                                                    $fileMeta['video_asuransi']['status'] === 'approve') ? 'File
                                                    Disetujui' : 'Pilih Video Asuransi' }}
                                                </button>

                                                <input type="file" class="d-none" id="uploadVideoAsuransi"
                                                    name="upload_video_asuransi" accept=".mp4,.mov,.avi,.webm"
                                                    @disabled(isset($fileMeta['video_asuransi']['status']) &&
                                                    $fileMeta['video_asuransi']['status']==='approve' )
                                                    data-status="{{ $fileMeta['video_asuransi']['status'] ?? 'pending' }}" />
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <!-- ================= END VIDEO UPLOAD ================= -->
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
</div>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/@ffmpeg/ffmpeg@0.10.0/dist/ffmpeg.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@ffmpeg/core@0.10.0/dist/ffmpeg-core.js" crossorigin="anonymous"></script>

<script>
    $(document).ready(function () {

        $('input[type="file"]').on('change', function () {
            validateFile($(this));
        });

        function validateFile($input) {
            const file = $input[0].files[0];
            if (!file) return;

            const acceptAttr = $input.attr('accept') || '';
            const fileType = file.type;

            let maxSizeMB = 0;
            let category = '';

            // Cek apakah Video
            const isVideo = fileType.startsWith('video/') ||
                acceptAttr.includes('.mp4') ||
                acceptAttr.includes('.mov') ||
                acceptAttr.includes('.avi') ||
                acceptAttr.includes('.webm');

            // Cek apakah Image atau PDF
            const isImageOrPdf = fileType.startsWith('image/') ||
                fileType === 'application/pdf' ||
                acceptAttr.includes('.jpg') ||
                acceptAttr.includes('.jpeg') ||
                acceptAttr.includes('.png') ||
                acceptAttr.includes('.pdf');

            // Tentukan batas ukuran berdasarkan kategori
            if (isVideo) {
                maxSizeMB = 50;
                category = 'Video';
            } else if (isImageOrPdf) {
                maxSizeMB = 5;
                category = 'Image/PDF';
            } else {
                maxSizeMB = 5; // Default
                category = 'File';
            }

            // Validasi ukuran
            const maxSizeBytes = maxSizeMB * 1024 * 1024;

            if (file.size > maxSizeBytes) {
                const currentSizeMB = (file.size / 1024 / 1024).toFixed(2);
                toastr.warning(
                    `⚠️ Ukuran file melebihi batas!\n\nBatas Maksimal ${category}: ${maxSizeMB} MB`,
                    'Ukuran File terlalu besar');

                // Reset input
                $input.val('');
            } else {
                console.log(`✅ ${category} valid: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`);
            }
        }

        // Init Select2
        $('.select2').select2({
            placeholder: "Pilih...",
            allowClear: true,
            width: '100%'
        });

        // ================= VIDEO COMPRESSION (Versi 0.10.0) =================
        const {
            createFFmpeg,
            fetchFile
        } = FFmpeg;
        let ffmpegInterview = null;
        let ffmpegAsuransi = null;
        let compressedVideoInterview = null;
        let compressedVideoAsuransi = null;

        // Initialize FFmpeg
        async function initFFmpeg(type) {
            if (type === 'interview' && !ffmpegInterview) {
                ffmpegInterview = createFFmpeg({
                    log: true,
                    progress: updateProgress('progressVideoInterview') // 👈 Progress callback
                });
                await ffmpegInterview.load();
            }
            if (type === 'asuransi' && !ffmpegAsuransi) {
                ffmpegAsuransi = createFFmpeg({
                    log: true,
                    progress: updateProgress('progressVideoAsuransi') // 👈 Progress callback
                });
                await ffmpegAsuransi.load();
            }
        }

        // Helper: Update progress bar
        function updateProgress(progressId) {
            return ({
                ratio
            }) => {
                const progressElement = document.getElementById(progressId);
                const progressBar = progressElement.querySelector('.progress-bar');

                progressElement.classList.remove('d-none');
                progressBar.classList.add('progress-bar-animated');

                const percentage = Math.round(ratio * 100);
                progressBar.style.width = `${percentage}%`;
                progressBar.textContent = `${percentage}%`;

                if (percentage >= 100) {
                    setTimeout(() => {
                        progressElement.classList.add('d-none');
                        progressBar.classList.remove('progress-bar-animated');
                    }, 2000);
                }
            };
        }

        // Compress Video Function
        async function compressVideo(file, type, progressId) {
            const progressElement = document.getElementById(progressId);
            const progressBar = progressElement.querySelector('.progress-bar');

            try {
                // Tampilkan progress bar
                progressElement.classList.remove('d-none');
                progressBar.classList.add('progress-bar-animated');
                progressBar.style.width = '0%';
                progressBar.textContent = '0%';

                // Inisialisasi FFmpeg jika belum
                await initFFmpeg(type);
                const ffmpeg = type === 'interview' ? ffmpegInterview : ffmpegAsuransi;

                // Tampilkan status kompresi
                $(`#preview${type === 'interview' ? 'UploadVideoInterview' : 'UploadVideoAsuransi'}`).html(`
                        <div class="alert alert-info">
                            <i class="mdi mdi-video mr-2"></i> Sedang mengkompres video... (${(file.size / 1024 / 1024).toFixed(2)} MB)
                        </div>
                    `);

                // Persiapkan file untuk FFmpeg
                const fileName = `input_${Date.now()}.${file.name.split('.').pop()}`;
                const outputName = `output_${Date.now()}.mp4`;

                ffmpeg.FS('writeFile', fileName, await fetchFile(file));

                // Proses kompresi
                await ffmpeg.run(
                    '-i', fileName,
                    '-vcodec', 'libx264',
                    '-crf', '28',
                    '-preset', 'fast',
                    '-movflags', '+faststart',
                    '-vf', 'scale=1280:-2',
                    '-maxrate', '2M',
                    '-bufsize', '4M',
                    '-threads', '0',
                    outputName
                );

                // Baca hasil kompresi
                const data = ffmpeg.FS('readFile', outputName);
                const videoBlob = new Blob([data.buffer], {
                    type: 'video/mp4'
                });

                // Hitung pengurangan ukuran
                const originalSize = (file.size / 1024 / 1024).toFixed(2);
                const compressedSize = (videoBlob.size / 1024 / 1024).toFixed(2);
                const reduction = (((file.size - videoBlob.size) / file.size) * 100).toFixed(1);

                // Set progress 100% saat selesai
                progressBar.style.width = '100%';
                progressBar.classList.remove('progress-bar-animated');
                progressBar.classList.add('bg-success');
                progressBar.textContent = '100%';

                setTimeout(() => {
                    progressElement.classList.add('d-none');
                }, 2000);

                // Cleanup memory
                ffmpeg.FS('unlink', fileName);
                ffmpeg.FS('unlink', outputName);

                console.log(
                    `Video ${type}: ${originalSize}MB → ${compressedSize}MB (${reduction}% reduction)`);

                return {
                    blob: videoBlob,
                    originalSize: originalSize,
                    compressedSize: compressedSize,
                    reduction: reduction
                };
            } catch (error) {
                console.error('Compression error:', error);
                progressElement.classList.add('d-none');
                throw error;
            }
        }

        // Handle Video Upload dengan Kompresi
        async function handleVideoUpload(inputId, previewId, progressId, errorId, type) {
            const fileInput = document.getElementById(inputId);
            const file = fileInput.files[0];
            if (!file) return;

            // Validasi format
            const allowedTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm'];
            if (!allowedTypes.includes(file.type)) {
                $(`#${errorId}`).text('Format video harus MP4, MOV, AVI, atau WEBM');
                fileInput.value = '';
                return;
            }

            // Validasi ukuran (max 50MB sebelum kompresi)
            const maxSize = 50 * 1024 * 1024;
            if (file.size > maxSize) {
                $(`#${errorId}`).text('Ukuran video maksimal 50 MB');
                fileInput.value = '';
                return;
            }

            try {
                // Tampilkan status kompresi
                $(`#${previewId}`).html(`
                        <div class="alert alert-info">
                            <i class="mdi mdi-video mr-2"></i> Sedang mengkompres video... (${(file.size / 1024 / 1024).toFixed(2)} MB)
                        </div>
                    `);

                // Kompres video
                const result = await compressVideo(file, type, progressId);

                // Simpan compressed video untuk upload nanti
                if (type === 'interview') {
                    compressedVideoInterview = new File([result.blob], `interview_${Date.now()}.mp4`, {
                        type: 'video/mp4'
                    });
                } else {
                    compressedVideoAsuransi = new File([result.blob], `asuransi_${Date.now()}.mp4`, {
                        type: 'video/mp4'
                    });
                }

                // Tampilkan preview hasil kompresi
                $(`#${previewId}`).html(`
                        <div class="alert alert-success">
                            <i class="mdi mdi-video mr-2"></i> Video berhasil dikompres: 
                            ${result.originalSize}MB → ${result.compressedSize}MB (${result.reduction}%)
                        </div>
                    `);

            } catch (error) {
                $(`#${previewId}`).html(`
                        <div class="alert alert-danger">
                            <i class="mdi mdi-alert-circle mr-2"></i> Gagal mengkompres video. Silakan coba lagi atau upload video yang lebih kecil.
                        </div>
                    `);
                $(`#${errorId}`).text('Kompresi video gagal: ' + error.message);
                fileInput.value = '';
            }
        }

        // Event Listeners untuk Video Upload
        $('#btnUploadVideoInterview').click(function () {
            $('#uploadVideoInterview').click();
        });
        $('#btnUploadVideoAsuransi').click(function () {
            $('#uploadVideoAsuransi').click();
        });
        $('#uploadVideoInterview').on('change', function () {
            handleVideoUpload('uploadVideoInterview', 'previewUploadVideoInterview',
                'progressVideoInterview', 'errUploadVideoInterview', 'interview');
        });
        $('#uploadVideoAsuransi').on('change', function () {
            handleVideoUpload('uploadVideoAsuransi', 'previewUploadVideoAsuransi',
                'progressVideoAsuransi', 'errUploadVideoAsuransi', 'asuransi');
        });
        // ================= END VIDEO COMPRESSION =================

        // Helper: Cek apakah upload field wajib diisi
        function isUploadRequired(fieldId) {
            const $field = $(`#${fieldId}`);
            const status = $field.data('status');
            return status === 'pending' || status === 'reject';
        }

        // Helper: Reset dropdowns anak
        function resetDropdowns(selectors) {
            selectors.forEach(selector => {
                $(selector).empty().append('<option value="">Pilih...</option>').prop('disabled', true)
                    .trigger('change.select2');
            });
        }

        // CASCADING SELECT2 WILAYAH
        $('#provinsiSelect').on('change', function () {
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
                success: function (data) {
                    $kota.empty().append('<option value="">Pilih Kota/Kabupaten</option>');
                    data.forEach(item => {
                        $kota.append(new Option(item.kabupaten, item.id));
                    });
                    $kota.prop('disabled', false).trigger('change.select2');
                }
            });
        });

        $('#kotaSelect').on('change', function () {
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
                success: function (data) {
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
                success: function (data) {
                    $('#kodeDati').val(data.sandi);
                    $('input[name="kode_dati_id"]').val(data.sandi);
                }
            });
        });

        $('#kecamatanSelect').on('change', function () {
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
                success: function (data) {
                    $kel.empty().append('<option value="">Pilih Kelurahan</option>');
                    data.forEach(item => {
                        $kel.append(new Option(item.desa + ' - ' + item.kodepos,
                            item.id));
                    });
                    $kel.prop('disabled', false).trigger('change.select2');
                }
            });
        });

        $('#kelurahanSelect').on('change', function () {
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
                success: function (data) {
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
                success: function (data) {
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
                            success: function (data) {
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
                                    success: function (datiData) {
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
                                        success: function (data) {
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
                                                    success: function (
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
        const totalTabs = 6;
        let currentTabIndex = 0;
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            currentTabIndex = getTabIndexFromId($(e.target).attr('href'));
            updateTabNavigation();
        });

        function getTabIndexFromId(tabId) {
            const tabs = ['#tab-identitas', '#tab-pribadi', '#tab-pekerjaan', '#tab-bank', '#tab-kta',
                '#tab-dokumen'
            ];
            return tabs.indexOf(tabId);
        }

        function updateTabNavigation() {
            $('#btnPrevTab').toggle(currentTabIndex > 0);
            $('#btnNextTab').toggle(currentTabIndex < totalTabs - 1);
        }
        $('#btnNextTab').click(function () {
            if (validateCurrentTab()) {
                if (currentTabIndex < totalTabs - 1) {
                    const nextTab = $('.nav-tabs .nav-item').eq(currentTabIndex + 1).find(
                        'a[data-toggle="tab"]');
                    $('html, body').animate({
                        scrollTop: 0
                    }, 300, function () {
                        nextTab.tab('show');
                    });
                }
            }
        });
        $('#btnPrevTab').click(function () {
            if (currentTabIndex > 0) {
                const prevTab = $('.nav-tabs .nav-item').eq(currentTabIndex - 1).find(
                    'a[data-toggle="tab"]');
                $('html, body').animate({
                    scrollTop: 0
                }, 300, function () {
                    prevTab.tab('show');
                });
            }
        });

        // FORM SUBMISSION
        $('#formNasabahBertahap').submit(function (e) {
            e.preventDefault();
            if (!validateAllTabs()) return false;

            let formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');

            // Tambahkan video yang sudah dikompres ke FormData
            if (compressedVideoInterview) {
                formData.append('upload_video_interview', compressedVideoInterview);
            }
            if (compressedVideoAsuransi) {
                formData.append('upload_video_asuransi', compressedVideoAsuransi);
            }

            $.post({
                url: "{{ route('bta.nasabah.update') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    toastr.success(response.message || 'Data nasabah berhasil disimpan',
                        'Berhasil!');
                    setTimeout(() => {
                        window.location.href = '{{ route('bta.nasabah.index') }}';
                    }, 2000);
                },
                error: function (xhr) {
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
                case 0: // Identitas
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
                case 1: // Data Pribadi & Alamat
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
                case 2: // Pekerjaan
                    const statusPegawai = $('#statusPegawai').val();


                    if (!$('#statusPegawai').val()) {
                        showError('statusPegawai', 'Status pegawai wajib dipilih');
                        isValid = false;
                    }

                    // Validasi conditional berdasarkan status pegawai
                    if (statusPegawai === 'Karyawan Tidak Tetap') {

                        if (!$('#nip').val().trim()) {
                            showError('nip', 'NIP / No. KARIP wajib diisi untuk pensiunan');
                            isValid = false;
                        }

                        if (!$('#namaPerusahaan').val().trim()) {
                            showError('namaPerusahaan', 'Nama perusahaan wajib diisi');
                            isValid = false;
                        }
                        if (!$('#bidangUsaha').val().trim()) {
                            showError('bidangUsaha', 'Bidang usaha wajib diisi');
                            isValid = false;
                        }
                        if (!$('#alamatKerja').val().trim()) {
                            showError('alamatKerja', 'Alamat tempat kerja wajib diisi');
                            isValid = false;
                        }

                        // Kontrak: wajib tgl akhir kontrak
                        if (!$('#tglAkhirKontrak').val()) {
                            showError('tglAkhirKontrak', 'Tanggal akhir kontrak wajib diisi');
                            isValid = false;
                        }
                        // Slip gaji wajib untuk karyawan tidak tetap
                        if (!$('#uploadSlip')[0].files.length) {
                            showError('uploadSlip', 'Upload Slip Gaji wajib diisi');
                            isValid = false;
                        }
                    } else if (statusPegawai === 'Pensiun') {
                        // Pensiun: wajib NIP/KARIP
                        if (!$('#nip').val().trim()) {
                            showError('nip', 'NIP / No. KARIP wajib diisi untuk pensiunan');
                            isValid = false;
                        }

                        if (!$('#tglAkhirKontrak').val()) {
                            showError('tglAkhirKontrak', 'Tanggal akhir kontrak wajib diisi');
                            isValid = false;
                        }

                        // Slip gaji opsional untuk pensiun (tidak divalidasi)
                    } else if (statusPegawai === "Tetap") {
                        if (!$('#nip').val().trim()) {
                            showError('nip', 'NIP / No. KARIP wajib diisi untuk pensiunan');
                            isValid = false;
                        }

                        // Validasi field wajib untuk SEMUA status pegawai
                        if (!$('#namaPerusahaan').val().trim()) {
                            showError('namaPerusahaan', 'Nama perusahaan wajib diisi');
                            isValid = false;
                        }
                        if (!$('#bidangUsaha').val().trim()) {
                            showError('bidangUsaha', 'Bidang usaha wajib diisi');
                            isValid = false;
                        }
                        if (!$('#alamatKerja').val().trim()) {
                            showError('alamatKerja', 'Alamat tempat kerja wajib diisi');
                            isValid = false;
                        }
                    }
                    // Karyawan Tetap: tidak ada validasi tambahan khusus
                    break;
                case 3: // Bank
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
                case 4: // KTA Multiguna
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
                    if (!isTanggalPengajuanValid()) {
                        isValid = false;
                    }
                    if (!$('#mulai_pinjaman').val()) {
                        markError('mulai_pinjaman', 'Tanggal mulai wajib diisi');
                        isValid = false;
                    }
                    break;
                case 5: // Dokumen
                    const allowedDocTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                    const docMaxSize = 5 * 1024 * 1024; // 5 MB

                    const docFields = ['uploadSlik', 'uploadKredit', 'uploadBta', 'uploadChecklist'];
                    docFields.forEach(fieldId => {
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
                            if (!allowedDocTypes.includes(file.type)) {
                                markError(fieldId, 'Format file hanya JPG, PNG, PDF');
                                isValid = false;
                            }
                            if (file.size > docMaxSize) {
                                markError(fieldId, 'Ukuran file maksimal 5 MB');
                                isValid = false;
                            }
                        }
                    });

                    // Validasi khusus untuk video
                    const videoMaxSize = 50 * 1024 * 1024; // 50 MB

                    // Video Interview
                    if (isUploadRequired('uploadVideoInterview')) {
                        const videoInterview = $('#uploadVideoInterview')[0]?.files[0];
                        if (!videoInterview && !compressedVideoInterview) {
                            markError('uploadVideoInterview', 'Upload Video Interview wajib diisi');
                            isValid = false;
                        } else if (videoInterview) {
                            const allowedVideoTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo',
                                'video/webm'
                            ];
                            if (!allowedVideoTypes.includes(videoInterview.type)) {
                                markError('uploadVideoInterview',
                                    'Format video harus MP4, MOV, AVI, atau WEBM');
                                isValid = false;
                            }
                            if (videoInterview.size > videoMaxSize) {
                                markError('uploadVideoInterview', 'Ukuran video maksimal 50 MB');
                                isValid = false;
                            }
                        }
                    }

                    // Video Asuransi
                    if (isUploadRequired('uploadVideoAsuransi')) {
                        const videoAsuransi = $('#uploadVideoAsuransi')[0]?.files[0];
                        if (!videoAsuransi && !compressedVideoAsuransi) {
                            markError('uploadVideoAsuransi', 'Upload Video Asuransi wajib diisi');
                            isValid = false;
                        } else if (videoAsuransi) {
                            const allowedVideoTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo',
                                'video/webm'
                            ];
                            if (!allowedVideoTypes.includes(videoAsuransi.type)) {
                                markError('uploadVideoAsuransi', 'Format video harus MP4, MOV, AVI, atau WEBM');
                                isValid = false;
                            }
                            if (videoAsuransi.size > videoMaxSize) {
                                markError('uploadVideoAsuransi', 'Ukuran video maksimal 50 MB');
                                isValid = false;
                            }
                        }
                    }
                    break;
            }
            return isValid;
        }

        function validateCurrentTab() {
            let isValid = true;
            $('.text-danger').text('');
            $('.form-control').removeClass('is-invalid');
            return validateCurrentTabSilent();
        }

        function markError(fieldId, message) {
            $(`#${fieldId}`).addClass('is-invalid');
            const fieldName = fieldId.charAt(0).toUpperCase() + fieldId.slice(1);
            $(`#err${fieldName}`).text(message);
        }

        function isTanggalPengajuanValid() {
            const value = $('#tanggal_pengajuan').val();
            const minDate = $('#tanggal_pengajuan').attr('min');
            const maxDate = $('#tanggal_pengajuan').attr('max');

            if (!value) {
                showError('tanggal_pengajuan', 'Tanggal pengajuan wajib diisi');
                $('#errTanggalPengajuan').text('Tanggal pengajuan wajib diisi');
                return false;
            }

            if (value < minDate || value > maxDate) {
                const message = 'Tanggal pengajuan hanya boleh dalam rentang 1 minggu dari tanggal hari ini';
                showError('tanggal_pengajuan', message);
                $('#errTanggalPengajuan').text(message);
                return false;
            }

            return true;
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
            $.each(errors, function (key, value) {
                $(`[name="${key}"]`).addClass('is-invalid');
                const fieldName = key.charAt(0).toUpperCase() + key.slice(1);
                if ($(`#err${fieldName}`).length) $(`#err${fieldName}`).text(value[0]);
            });
        }


        $('#btnUploadKtp').click(function () {
            $('#uploadKtp').click();
        });
        $('#btnUploadNpwp').click(function () {
            $('#uploadNpwp').click();
        });
        $('#btnUploadKk').click(function () {
            $('#uploadKk').click();
        });
        $('#btnUploadSk').click(function () {
            $('#uploadSk').click();
        });
        $('#btnUploadSlip').click(function () {
            $('#uploadSlip').click();
        });
        $('#btnUploadIdCard').click(function () {
            $('#uploadIdCard').click();
        });
        $('#btnUploadBukuTabungan').click(function () {
            $('#uploadBukuTabungan').click();
        });
        $('#btnUploadMutasi').click(function () {
            $('#uploadMutasi').click();
        });
        $('#btnUploadSlik').click(function () {
            $('#uploadSlik').click();
        });
        $('#btnUploadKredit').click(function () {
            $('#uploadKredit').click();
        });
        $('#btnUploadBta').click(function () {
            $('#uploadBta').click();
        });
        $('#btnUploadChecklist').click(function () {
            $('#uploadChecklist').click();
        });

        // HAPUS FILE PREVIEW YANG LAMA UNTUK VIDEO
        // FILE PREVIEW (Hanya untuk dokumen, bukan video)
        $('.d-none[type="file"]').not('#uploadVideoInterview, #uploadVideoAsuransi').on('change', function () {
            if ($(this).prop('disabled')) {
                $(this).val('');
                return;
            }
            const id = $(this).attr('id');
            const file = this.files[0];
            if (file) {
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    toastr.error('Ukuran file maksimal 5 MB', 'Gagal!');
                    $(this).val('');
                    return;
                }
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    toastr.error('Format file hanya JPG, PNG, PDF', 'Gagal!');
                    $(this).val('');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    $(`#preview${id.charAt(0).toUpperCase() + id.slice(1)}`).html(
                        `<div class="alert alert-info"><i class="mdi mdi-file mr-2"></i> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</div>`
                    );
                };
                reader.readAsDataURL(file);
            }
        });

        // CONDITIONAL FIELDS
        $('#statusPerkawinan').change(function () {
            if ($(this).val() === 'Kawin') {
                $('.pasangan-field').removeClass('d-none');
            } else {
                $('.pasangan-field').addClass('d-none');
                $('#namaPasangan, #tglLahirPasangan').val('');
            }
        }).trigger('change');

        $('#statusPegawai').change(function () {
            const status = $(this).val();
            if (status === 'Karyawan Tidak Tetap') {
                $('.kontrak-field').removeClass('d-none');
                $('.pensiun-field').removeClass('d-none');
            } else if (status === 'Pensiun') {
                $('.kontrak-field').removeClass('d-none');
                $('.pensiun-field').addClass('d-none');
            } else {
                $('.kontrak-field').addClass('d-none');
                $('.pensiun-field').removeClass('d-none');
                $('#tglAkhirKontrak').val('');
            }
        }).trigger('change');

        // FORMAT RUPIAH
        $('.rupiah-format').each(function () {
            let val = $(this).val();
            if (val && !val.startsWith('Rp')) {
                $(this).val(formatRupiah(val.replace(/[^0-9]/g, '')));
            }
        });

        $('.rupiah-format').on('input', function () {
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
            const tipeBunga = $('input[name="tipe_bunga"]:checked').val();

            if (plafond > 0 && jangkaWaktu > 0 && sukuBunga > 0) {
                let totalAngsuran = 0;
                if (tipeBunga === 'flat') {
                    const bungaPerBulan = (plafond * (sukuBunga / 100)) / 12;
                    const pokokPerBulan = plafond / jangkaWaktu;
                    totalAngsuran = pokokPerBulan + bungaPerBulan;
                } else {
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
                $('#akhir_pinjaman').val(
                    `${startDate.getFullYear()}-${String(startDate.getMonth() + 1).padStart(2, '0')}-${String(startDate.getDate()).padStart(2, '0')}`
                );
            } else {
                $('#akhir_pinjaman').val('');
            }
        }

        $('#plafond, #jangka_waktu, #suku_bunga').on('input', calculateAngsuran);
        $('#mulai_pinjaman, #jangka_waktu').on('change input', calculateAkhirPinjaman);
        $('input[name="tipe_bunga"]').on('change', calculateAngsuran);

        updateTabNavigation();
    });
</script>
@endpush
