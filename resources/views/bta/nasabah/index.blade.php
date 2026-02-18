@extends('bta.layout.app')
@section('title', 'Nasabah')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">BTA</a></li>
                        <li class="breadcrumb-item active">Nasabah</li>
                    </ol>
                </div>
                <h4 class="page-title">Nasabah</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="text-header">List Nasabah</h4>


                        {{-- <button type="button" class="btn btn-sm btn-primary float-right waves-effect waves-light"
                            data-toggle="modal" data-target="#modalAdd">
                            <i class="mdi mdi-plus-circle"></i> Tambah Nasabah
                        </button> --}}

                        <a href="{{ route('bta.nasabah.tambah') }}" type="button"
                            class="btn btn-sm btn-primary float-right waves-effect waves-light"> <i
                                class="mdi mdi-plus-circle"></i>
                            Tambah Nasabah
                        </a>
                    </div>



                    <div class="card-box">
                        <ul class="nav nav-pills navtab-bg nav-justified">
                            <li class="nav-item">
                                <a href="#revisi" data-toggle="tab" class="nav-link active">Revisi</a>
                            </li>
                            <li class="nav-item">
                                <a href="#analyst" data-toggle="tab" class="nav-link">Analyst</a>
                            </li>
                            <li class="nav-item">
                                <a href="#disetujui-analyst" data-toggle="tab" class="nav-link">Disetujui
                                    Analyst</a>
                            </li>
                            <li class="nav-item">
                                <a href="#credit-admin" data-toggle="tab" class="nav-link">Credit Administrator</a>
                            </li>
                            <li class="nav-item">
                                <a href="#ditolak" data-toggle="tab" class="nav-link">Ditolak</a>
                            </li>
                            <li class="nav-item">
                                <a href="#selesai" data-toggle="tab" class="nav-link">Selesai</a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">
                            <!-- Revisi -->
                            <div class="tab-pane fade show active" id="revisi">

                                <table id="tableRevisi"
                                    class="table dt-responsive table-hover mt-4 text-center justify-center"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th data-priority="1">No. KTP</th>
                                            <th data-priority="1">Nama Lengkap</th>
                                            <th data-priority="2">Tanggal Lahir</th>
                                            <th data-priority="3">Jenis Kelamin</th>
                                            <th data-priority="1">No. Telepon</th>
                                            <th data-priority="4">Status Perkawinan</th>
                                            <th data-priority="2">Perusahaan</th>
                                            <th data-priority="3">Status Pegawai</th>
                                            <th data-priority="3">Status Analyst</th>
                                            <th data-priority="3">Note Analyst</th>
                                            <th data-priority="3">Action</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>

                            <!-- Analyst -->
                            <div class="tab-pane fade" id="analyst">
                                <table id="tableAnalyst"
                                    class="table dt-responsive table-hover mt-4 text-center justify-center"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th data-priority="1">No. KTP</th>
                                            <th data-priority="1">Nama Lengkap</th>
                                            <th data-priority="2">Tanggal Lahir</th>
                                            <th data-priority="3">Jenis Kelamin</th>
                                            <th data-priority="1">No. Telepon</th>
                                            <th data-priority="4">Status Perkawinan</th>
                                            <th data-priority="2">Perusahaan</th>
                                            <th data-priority="3">Status Pegawai</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <!-- Analyst -->
                            <div class="tab-pane fade" id="disetujui-analyst">
                                <table id="tableAnalystSetuju"
                                    class="table dt-responsive table-hover mt-4 text-center justify-center"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th data-priority="1">No. KTP</th>
                                            <th data-priority="1">Nama Lengkap</th>
                                            <th data-priority="2">Tanggal Lahir</th>
                                            <th data-priority="3">Jenis Kelamin</th>
                                            <th data-priority="1">No. Telepon</th>
                                            <th data-priority="4">Status Perkawinan</th>
                                            <th data-priority="2">Perusahaan</th>
                                            <th data-priority="3">Status Pegawai</th>
                                            <th data-priority="3">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>


                            <!-- Credit Administrator -->
                            <div class="tab-pane fade" id="credit-admin">
                                <p>Konten untuk tahap <strong>Credit Administrator</strong>.</p>

                                <table id="tableCredam"
                                    class="table dt-responsive table-hover mt-4 text-center justify-center"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th data-priority="1">No. KTP</th>
                                            <th data-priority="1">Nama Lengkap</th>
                                            <th data-priority="2">Tanggal Lahir</th>
                                            <th data-priority="3">Jenis Kelamin</th>
                                            <th data-priority="1">No. Telepon</th>
                                            <th data-priority="4">Status Perkawinan</th>
                                            <th data-priority="2">Perusahaan</th>
                                            <th data-priority="3">Status Pegawai</th>
                                            <th data-priority="3">Status Credam</th>
                                        </tr>
                                    </thead>
                                </table>


                            </div>

                            <!-- Ditolak -->
                            <div class="tab-pane fade" id="ditolak">

                                <table id="tableDitolak"
                                    class="table dt-responsive table-hover mt-4 text-center justify-center"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th data-priority="1">No. KTP</th>
                                            <th data-priority="1">Nama Lengkap</th>
                                            <th data-priority="2">Tanggal Lahir</th>
                                            <th data-priority="3">Jenis Kelamin</th>
                                            <th data-priority="1">No. Telepon</th>
                                            <th data-priority="4">Status Perkawinan</th>
                                            <th data-priority="2">Perusahaan</th>
                                            <th data-priority="3">Status Pegawai</th>
                                            <th data-priority="3">Status Analyst</th>
                                            <th data-priority="3">Note Analyst</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>



                            <!-- Selesai -->
                            <div class="tab-pane fade" id="selesai">
                                <table id="tableSelesai"
                                    class="table dt-responsive table-hover mt-4 text-center justify-center"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th data-priority="1">No. KTP</th>
                                            <th data-priority="1">Nama Lengkap</th>
                                            <th data-priority="2">Tanggal Lahir</th>
                                            <th data-priority="3">Jenis Kelamin</th>
                                            <th data-priority="1">No. Telepon</th>
                                            <th data-priority="4">Status Perkawinan</th>
                                            <th data-priority="2">Perusahaan</th>
                                            <th data-priority="3">Status Pegawai</th>
                                            <th data-priority="3">Status Analyst</th>
                                            <th data-priority="3">Note Analyst</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div> <!-- end card-box -->

                </div>
            </div>



        </div>
    </div>


    {{-- modal tambah --}}

    <!-- Modal Edit (Terpisah dari Modal Add) -->
    <div class="modal fade" id="modalEdit" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Data Nasabah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEditNasabah" enctype="multipart/form-data">
                        <input type="hidden" id="editNasabahId">
                        <div class="row">

                            <!-- IDENTITAS UTAMA -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editNamaLengkap" placeholder="Nama sesuai KTP" />
                                    <small class="text-danger" id="errEditNamaLengkap"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Singkat</label>
                                    <input class="form-control" id="editNamaSingkat" placeholder="Nama panggilan" />
                                    <small class="text-danger" id="errEditNamaSingkat"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. KTP <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editNoKtp" placeholder="Contoh: 3275041234567890"
                                        maxlength="16" readonly />
                                    <small class="text-muted mt-1 d-block"><i class="mdi mdi-lock"></i> No. KTP tidak
                                        dapat diubah</small>
                                    <small class="text-danger" id="errEditNoKtp"></small>
                                </div>
                            </div>

                            <!-- NO. NPWP -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. NPWP</label>
                                    <input class="form-control" id="editNoNpwp" placeholder="Contoh: 012345678901234"
                                        maxlength="16" />
                                    <small class="text-danger" id="errEditNoNpwp"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control" id="editJenisKelamin">
                                        <option value="">Pilih</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                    <small class="text-danger" id="errEditJenisKelamin"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tempat Lahir <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editTempatLahir" placeholder="Contoh: Jakarta" />
                                    <small class="text-danger" id="errEditTempatLahir"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editTanggalLahir" type="date" />
                                    <small class="text-danger" id="errEditTanggalLahir"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Gadis Ibu Kandung <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editNamaIbu" placeholder="Nama ibu kandung" />
                                    <small class="text-danger" id="errEditNamaIbu"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. Telepon <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editNoTelp" placeholder="081234567890" />
                                    <small class="text-danger" id="errEditNoTelp"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Agama <span class="text-danger">*</span></label>
                                    <select class="form-control" id="editAgama">
                                        <option value="">Pilih</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                    <small class="text-danger" id="errEditAgama"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pendidikan Terakhir <span class="text-danger">*</span></label>
                                    <select class="form-control" id="editPendidikan">
                                        <option value="">Pilih</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA">SMA</option>
                                        <option value="D1">D1</option>
                                        <option value="D3">D3</option>
                                        <option value="D4">D4</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                    <small class="text-danger" id="errEditPendidikan"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status Perkawinan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="editStatusPerkawinan">
                                        <option value="">Pilih</option>
                                        <option value="Kawin">Kawin</option>
                                        <option value="Tidak Kawin">Tidak Kawin</option>
                                    </select>
                                    <small class="text-danger" id="errEditStatusPerkawinan"></small>
                                </div>
                            </div>

                            <!-- DATA PASANGAN (conditional) -->
                            <div class="col-md-6 pasangan-field-edit d-none">
                                <div class="form-group">
                                    <label>Nama Pasangan</label>
                                    <input class="form-control" id="editNamaPasangan" placeholder="Nama pasangan" />
                                    <small class="text-danger" id="errEditNamaPasangan"></small>
                                </div>
                            </div>

                            <div class="col-md-6 pasangan-field-edit d-none">
                                <div class="form-group">
                                    <label>Tgl Lahir Pasangan</label>
                                    <input class="form-control" id="editTglLahirPasangan" type="date" />
                                    <small class="text-danger" id="errEditTglLahirPasangan"></small>
                                </div>
                            </div>

                            <!-- Alamat Rumah -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat Rumah <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="editAlamatRumah" rows="2" placeholder="Alamat lengkap"></textarea>
                                    <small class="text-danger" id="errEditAlamatRumah"></small>
                                </div>
                            </div>

                            <!-- Provinsi, Kota, Kecamatan, Kelurahan, Kode Pos -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Provinsi <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="editProvinsiSelect">
                                        <option value="">Pilih</option>
                                        <!-- Isi via AJAX -->
                                    </select>
                                    <small class="text-danger" id="errEditProvinsi"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kota/Kabupaten <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="editKotaSelect">
                                        <option value="">Pilih</option>
                                    </select>
                                    <small class="text-danger" id="errEditKota"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kecamatan <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="editKecamatanSelect">
                                        <option value="">Pilih</option>
                                    </select>
                                    <small class="text-danger" id="errEditKecamatan"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelurahan <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="editKelurahanSelect">
                                        <option value="">Pilih</option>
                                    </select>
                                    <small class="text-danger" id="errEditKelurahan"></small>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Dati <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editKodeDati" readonly></input>
                                    <small class="text-danger" id="errEditKodeDati"></small>
                                </div>
                            </div>

                            <!-- BANK -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Bank <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="editBankSelect">
                                        <option value="">Pilih</option>
                                    </select>
                                    <small class="text-danger" id="errEditNamaBank"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Pemilik Rekening <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editNamaPemilikRek"
                                        placeholder="Harus sama dengan nama lengkap" />
                                    <small class="text-danger" id="errEditNamaPemilikRek"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No Rekening Bank <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editNoRek" placeholder="1234567890" />
                                    <small class="text-danger" id="errEditNoRek"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Perusahaan <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editNamaPerusahaan" placeholder="Nama perusahaan" />
                                    <small class="text-danger" id="errEditNamaPerusahaan"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bidang Usaha <span class="text-danger">*</span></label>
                                    <input class="form-control" id="editBidangUsaha"
                                        placeholder="Contoh: Retail, Konstruksi" />
                                    <small class="text-danger" id="errEditBidangUsaha"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat Tempat Kerja <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="editAlamatKerja" rows="2" placeholder="Alamat kantor"></textarea>
                                    <small class="text-danger" id="errEditAlamatKerja"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIP (No Induk Pegawai)</label>
                                    <input class="form-control" id="editNip" placeholder="Opsional" />
                                    <small class="text-danger" id="errEditNip"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status Pegawai <span class="text-danger">*</span></label>
                                    <select class="form-control" id="editStatusPegawai">
                                        <option value="">Pilih</option>
                                        <option value="Karyawan Tetap">Karyawan Tetap</option>
                                        <option value="Karyawan Tidak Tetap">Karyawan Tidak Tetap</option>
                                    </select>
                                    <small class="text-danger" id="errEditStatusPegawai"></small>
                                </div>
                            </div>

                            <div class="col-md-6 kontrak-field-edit d-none">
                                <div class="form-group">
                                    <label>Tgl Akhir Kontrak Kerja</label>
                                    <input class="form-control" id="editTglAkhirKontrak" type="date" />
                                    <small class="text-danger" id="errEditTglAkhirKontrak"></small>
                                </div>
                            </div>

                            <!-- Upload Section -->
                            <div class="col-12 mt-4">
                                <h6><i class="mdi mdi-file-document"></i> Dokumen Terupload</h6>
                                <hr>
                            </div>

                            <!-- KTP -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>KTP Terupload</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditKtp">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="ktp">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceKtp"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadKtp"></small>
                                </div>
                            </div>

                            <!-- NPWP -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NPWP Terupload</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditNpwp">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="npwp">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceNpwp"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadNpwp"></small>
                                </div>
                            </div>

                            <!-- Selfie -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Selfie KTP</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditSelfie">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="selfie">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceSelfie"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadSelfie"></small>
                                </div>
                            </div>

                            <!-- KK -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kartu Keluarga (KK)</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditKk">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="kk">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceKk"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadKk"></small>
                                </div>
                            </div>

                            <!-- ID Card -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ID Card Tambahan</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditIdCard">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="idcard">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceIdCard"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadIdCard"></small>
                                </div>
                            </div>

                            <!-- SK Karyawan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SK Karyawan</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditSk">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="sk">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceSk"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadSk"></small>
                                </div>
                            </div>

                            <!-- Slip Gaji -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Slip Gaji Terakhir</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditSlip">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="slip">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceSlip"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadSlip"></small>
                                </div>
                            </div>

                            <!-- Buku Tabungan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Buku Tabungan</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditBukuTabungan">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="bukutabungan">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceBukuTabungan"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadBukuTabungan"></small>
                                </div>
                            </div>

                            <!-- Mutasi Rekening -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mutasi Rekening (3 Bulan)</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditMutasi">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="mutasi">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceMutasi"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadMutasi"></small>
                                </div>
                            </div>

                            <!-- Form SLIK -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Form SLIK</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditSlik">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="slik">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceSlik"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadSlik"></small>
                                </div>
                            </div>

                            <!-- Form Kredit -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Form Kredit</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditKredit">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="kredit">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceKredit"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadKredit"></small>
                                </div>
                            </div>

                            <!-- Form BTA -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Form BTA</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditBta">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="bta">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceBta"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadBta"></small>
                                </div>
                            </div>

                            <!-- Checklist Verifikasi -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Checklist Verifikasi</label>
                                    <div class="alert alert-info p-2 mb-2" id="previewEditChecklist">
                                        <i class="mdi mdi-alert"></i> Belum ada file
                                    </div>
                                    <div class="input-group mt-2">
                                        <button type="button" class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="checklist">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input" id="replaceChecklist"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errEditUploadChecklist"></small>
                                </div>
                            </div>


                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="buttonUpdate">Update Data</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal upload untuk setuju dari analyst --}}

    <div class="modal fade" id="modalUploadSetujuAnalyst" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadSetujuModal">Upload Foto Pemohon & PK yang sudah tertanda
                        tangani
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Foto Pemohon Saat Akad Kredit <small class="text-danger">*</small></label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-primary btn-block"
                                        id="btnUploadFotoAkadKredit">
                                        <i class="mdi mdi-upload"></i> Pilih Foto Saat Akad Kredit
                                    </button>
                                    <input type="file" class="d-none" id="uploadFotoAkadKredit"
                                        accept=".jpg,.jpeg,.png,.pdf" />
                                </div>
                                <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                <small class="text-danger" id="errUploadFotoAkadKredit"></small>
                                <div class="mt-2 preview-container" id="previewUploadFotoAkadKredit"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PK Yang Sudah Tertanda Tangani <small class="text-danger">*</small></label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-primary btn-block"
                                        id="btnUploadPkTertandaTangan">
                                        <i class="mdi mdi-upload"></i> Pilih PK Yang Sudah Tertanda Tangan
                                    </button>
                                    <input type="file" class="d-none" id="uploadPkTertandaTangan"
                                        accept=".jpg,.jpeg,.png,.pdf" />
                                </div>
                                <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                <small class="text-danger" id="errUploadPkTertandaTangan"></small>
                                <div class="mt-2 preview-container" id="previewUploadPkTertandaTangan"></div>
                            </div>
                        </div>
                    </div>



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="buttonSubmitUploadAnalystSetuju">Submit</button>
                </div>
            </div>
        </div>
    </div>


@endsection
@push('js')
    <script>
        $(document).ready(function() {
            let currentFileStatus = {};
            let filesToReplace = {};
            let uploadSetujuAnalyst = '';

            var dataTable = {
                "_token": "{{ csrf_token() }}",
            };
            let editID;


            edit = (id) => {
                console.log(id);
            }


            uploadSetujuiAnalyst = (id) => {
                uploadSetujuAnalyst = id;
                $('#modalUploadSetujuAnalyst').modal('show');
            };


            // upload foto akad kredit dan pk yang sudah tertanda tangan
            $('#buttonSubmitUploadAnalystSetuju').on('click', function() {
                // Disable button dan tampilkan loading
                const $button = $(this);
                $button.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin"></i> Memproses...'
                );

                // Validasi input file
                const uploadFotoAkadKredit = $('#uploadFotoAkadKredit')[0].files[0];
                const uploadPkTertandaTangan = $('#uploadPkTertandaTangan')[0].files[0];

                if (!uploadFotoAkadKredit || !uploadPkTertandaTangan) {
                    toastr.warning('Harap lengkapi semua dokumen yang diperlukan');
                    $button.prop('disabled', false).html('Submit');
                    return;
                }

                // Validasi format file
                const allowedFormats = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                const maxSize = 5120 * 1024; // 5MB dalam bytes

                if (!allowedFormats.includes(uploadFotoAkadKredit.type) || !allowedFormats.includes(
                        uploadPkTertandaTangan.type)) {
                    toastr.error('Format file tidak valid. Gunakan JPG, JPEG, PNG, atau PDF');
                    $button.prop('disabled', false).html('Submit');
                    return;
                }

                if (uploadFotoAkadKredit.size > maxSize || uploadPkTertandaTangan.size > maxSize) {
                    toastr.error('Ukuran file melebihi batas maksimal 5MB');
                    $button.prop('disabled', false).html('Submit');
                    return;
                }

                // Buat FormData baru
                const formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('uploadFotoAkadKredit', uploadFotoAkadKredit);
                formData.append('uploadPkTertandaTangan', uploadPkTertandaTangan);
                formData.append('uploadSetujuAnalyst',
                    uploadSetujuAnalyst); // Pastikan variabel ini sudah didefinisikan

                // AJAX request dengan proper error handling
                $.ajax({
                    url: '{!! route('bta.nasabah.upload.setuju') !!}',
                    type: 'POST',
                    data: formData,
                    processData: false, // Penting untuk file upload
                    contentType: false, // Penting untuk file upload
                    dataType: 'json',
                    timeout: 30000, // Timeout 30 detik
                    success: function(response) {

                        toastr.success(response.message || 'Dokumen berhasil diupload');
                        $('#modalUploadSetujuAnalyst').modal('hide');
                        $('#formUploadAnalyst')[0].reset();

                        $button.prop('disabled', false).html('Submit');

                        tableRevisi.ajax.reload();
                        tableCredam.ajax.reload();
                        tableDitolak.ajax.reload();
                        tableAnalyst.ajax.reload();
                        tableDisetujui.ajax.reload();

                    },
                    error: function(xhr, status, error) {
                        console.error('Upload error:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });

                        $button.prop('disabled', false).html('Submit');

                        if (xhr.status === 413) {
                            toastr.error('File terlalu besar. Maksimal 5MB per file');
                        } else if (xhr.status === 422) {
                            // Validasi error dari Laravel
                            const errors = xhr.responseJSON?.errors;
                            if (errors) {
                                let errorMessage = 'Validasi gagal:\n';
                                $.each(errors, function(key, value) {
                                    errorMessage += `- ${value[0]}\n`;
                                });
                                toastr.error(errorMessage);
                            } else {
                                toastr.error('Validasi data gagal');
                            }
                        } else if (xhr.status === 500) {
                            toastr.error('Terjadi kesalahan server. Silakan coba lagi');
                        } else if (status === 'timeout') {
                            toastr.error('Request timeout. Silakan coba lagi');
                        } else {
                            toastr.error(xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat upload dokumen');
                        }

                        $('#modalUploadSetujuAnalyst').modal('hide');
                    },
                    complete: function() {
                        // Optional: cleanup atau logging
                        console.log('Upload process completed');
                    }
                });
            });



            download = (id) => {
                console.log("Memulai proses download untuk ID:", id);

                $.ajax({
                    url: "{{ route('bta.nasabah.pk.download') }}",
                    method: 'POST',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    data: {
                        'id': id,
                        '_token': "{{ csrf_token() }}",
                    },

                    success: function(data, status, xhr) {
                        const fileName = `dokumen_${id}.pdf`;

                        const blob = new Blob([data], {
                            type: 'application/pdf'
                        });
                        const url = window.URL.createObjectURL(blob);

                        const link = document.createElement('a');
                        link.href = url;
                        link.download = fileName;
                        document.body.append(link);
                        link.click();
                        link.remove();
                        window.URL.revokeObjectURL(url);
                        console.log("Download berhasil!");
                    },
                    error: function(xhr, status, error) {
                        console.error("Gagal mendownload file:", error);
                        alert(
                            "Terjadi kesalahan saat mengunduh file. Pastikan server mengembalikan file PDF."
                        );
                    }
                });
            }






            const tableRevisi = $('#tableRevisi').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,
                                7
                            ] // Hanya kolom data (tanpa Action)
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    }
                ],
                "lengthMenu": [10, 50, 100, 500, 1000],
                processing: true,
                responsive: true,
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('bta.admin.nasabah.revisi.table') !!}',
                    "data": function(d) {
                        return $.extend({}, d, dataTable);
                    },
                },
                columns: [{
                    data: 'no_ktp'
                }, {
                    data: 'nama_lengkap'
                }, {
                    data: 'tanggal_lahir'
                }, {
                    data: 'jenis_kelamin'
                }, {
                    data: 'no_telp'
                }, {
                    data: 'status_perkawinan'
                }, {
                    data: 'nama_perusahaan'
                }, {
                    data: 'status_pegawai'
                }, {
                    data: 'status'
                }, {
                    data: 'catatan_analyst'
                }, {
                    data: 'action'
                }, ]
            });



            const tableCredam = $('#tableCredam').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,
                                7
                            ] // Hanya kolom data (tanpa Action)
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    }
                ],
                "lengthMenu": [10, 50, 100, 500, 1000],
                processing: true,
                responsive: true,
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('bta.admin.nasabah.credam.table') !!}',
                    "data": function(d) {
                        return $.extend({}, d, dataTable);
                    },
                },
                columns: [{
                    data: 'no_ktp'
                }, {
                    data: 'nama_lengkap'
                }, {
                    data: 'tanggal_lahir'
                }, {
                    data: 'jenis_kelamin'
                }, {
                    data: 'no_telp'
                }, {
                    data: 'status_perkawinan'
                }, {
                    data: 'nama_perusahaan'
                }, {
                    data: 'status_pegawai'
                }, {
                    data: 'status'
                }]
            });


            const tableDitolak = $('#tableDitolak').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,
                                7
                            ] // Hanya kolom data (tanpa Action)
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    }
                ],
                "lengthMenu": [10, 50, 100, 500, 1000],
                processing: true,
                responsive: true,
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('bta.admin.nasabah.ditolak.table') !!}',
                    "data": function(d) {
                        return $.extend({}, d, dataTable);
                    },
                },
                columns: [{
                    data: 'no_ktp'
                }, {
                    data: 'nama_lengkap'
                }, {
                    data: 'tanggal_lahir'
                }, {
                    data: 'jenis_kelamin'
                }, {
                    data: 'no_telp'
                }, {
                    data: 'status_perkawinan'
                }, {
                    data: 'nama_perusahaan'
                }, {
                    data: 'status_pegawai'
                }, {
                    data: 'status'
                }, {
                    data: 'catatan_analyst'
                }, ]
            });


            const tableSelesai = $('#tableSelesai').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,
                                7
                            ] // Hanya kolom data (tanpa Action)
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    }
                ],
                "lengthMenu": [10, 50, 100, 500, 1000],
                processing: true,
                responsive: true,
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('bta.admin.nasabah.selesai.table') !!}',
                    "data": function(d) {
                        return $.extend({}, d, dataTable);
                    },
                },
                columns: [{
                    data: 'no_ktp'
                }, {
                    data: 'nama_lengkap'
                }, {
                    data: 'tanggal_lahir'
                }, {
                    data: 'jenis_kelamin'
                }, {
                    data: 'no_telp'
                }, {
                    data: 'status_perkawinan'
                }, {
                    data: 'nama_perusahaan'
                }, {
                    data: 'status_pegawai'
                }, {
                    data: 'status'
                }, {
                    data: 'catatan_analyst'
                }, ]
            });

            const tableAnalyst = $('#tableAnalyst').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,
                                7
                            ] // Hanya kolom data (tanpa Action)
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    }
                ],
                "lengthMenu": [10, 50, 100, 500, 1000],
                processing: true,
                responsive: true,
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('bta.admin.nasabah.analyst.table') !!}',
                    "data": function(d) {
                        return $.extend({}, d, dataTable);
                    },
                },
                columns: [{
                        data: 'no_ktp'
                    }, // No. KTP
                    {
                        data: 'nama_lengkap'
                    }, // Nama Lengkap
                    {
                        data: 'tanggal_lahir'
                    }, // Tanggal Lahir
                    {
                        data: 'jenis_kelamin'
                    }, // Jenis Kelamin
                    {
                        data: 'no_telp'
                    }, // No. Telepon
                    {
                        data: 'status_perkawinan'
                    }, // Status Perkawinan
                    {
                        data: 'nama_perusahaan'
                    }, // Perusahaan
                    {
                        data: 'status_pegawai'
                    }
                ]
            });

            const tableDisetujui = $('#tableAnalystSetuju').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,
                                7
                            ] // Hanya kolom data (tanpa Action)
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    }
                ],
                "lengthMenu": [10, 50, 100, 500, 1000],
                processing: true,
                responsive: true,
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('bta.admin.nasabah.disetujui.table') !!}',
                    "data": function(d) {
                        return $.extend({}, d, dataTable);
                    },
                },
                columns: [{
                        data: 'no_ktp'
                    }, // No. KTP
                    {
                        data: 'nama_lengkap'
                    }, // Nama Lengkap
                    {
                        data: 'tanggal_lahir'
                    }, // Tanggal Lahir
                    {
                        data: 'jenis_kelamin'
                    }, // Jenis Kelamin
                    {
                        data: 'no_telp'
                    }, // No. Telepon
                    {
                        data: 'status_perkawinan'
                    }, // Status Perkawinan
                    {
                        data: 'nama_perusahaan'
                    }, // Perusahaan
                    {
                        data: 'status_pegawai'
                    }, // Perusahaan
                    {
                        data: 'action'
                    }
                ]
            });

        });
    </script>
@endpush
