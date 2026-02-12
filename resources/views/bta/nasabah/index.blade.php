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


                        <button type="button" class="btn btn-sm btn-primary float-right waves-effect waves-light"
                            data-toggle="modal" data-target="#modalAdd">
                            <i class="mdi mdi-plus-circle"></i> Tambah Nasabah
                        </button>
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
                                <a href="#disetujui-analyst" data-toggle="tab" class="nav-link">Disetujui Analyst</a>
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
    <!-- Modal -->
    <div class="modal fade" id="modalAdd" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nasabahModalLabel">Formulir Data Nasabah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formNasabah" enctype="multipart/form-data">
                        <div class="row">

                            <!-- IDENTITAS UTAMA -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                    <input class="form-control" id="namaLengkap" placeholder="Nama sesuai KTP" />
                                    <small class="text-danger" id="errNamaLengkap"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Singkat</label>
                                    <input class="form-control" id="namaSingkat" placeholder="Nama panggilan" />
                                    <small class="text-danger" id="errNamaSingkat"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. KTP <span class="text-danger">*</span></label>
                                    <input class="form-control" id="noKtp" placeholder="Contoh: 3275041234567890"
                                        maxlength="16" />
                                    <small class="text-danger" id="errNoKtp"></small>
                                </div>
                            </div>

                            <!-- NO. NPWP -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. NPWP</label>
                                    <input class="form-control" id="noNpwp" placeholder="Contoh: 012345678901234"
                                        maxlength="16" />
                                    <small class="text-danger" id="errNoNpwp"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control" id="jenisKelamin">
                                        <option value="">Pilih</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                    <small class="text-danger" id="errJenisKelamin"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tempat Lahir <span class="text-danger">*</span></label>
                                    <input class="form-control" id="tempatLahir" placeholder="Contoh: Jakarta" />
                                    <small class="text-danger" id="errTempatLahir"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input class="form-control" id="tanggalLahir" type="date" />
                                    <small class="text-danger" id="errTanggalLahir"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Gadis Ibu Kandung <span class="text-danger">*</span></label>
                                    <input class="form-control" id="namaIbu" placeholder="Nama ibu kandung" />
                                    <small class="text-danger" id="errNamaIbu"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. Telepon <span class="text-danger">*</span></label>
                                    <input class="form-control" id="noTelp" placeholder="081234567890" />
                                    <small class="text-danger" id="errNoTelp"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Agama <span class="text-danger">*</span></label>
                                    <select class="form-control" id="agama">
                                        <option value="">Pilih</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                    <small class="text-danger" id="errAgama"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pendidikan Terakhir <span class="text-danger">*</span></label>
                                    <select class="form-control" id="pendidikan">
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
                                    <small class="text-danger" id="errPendidikan"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status Perkawinan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="statusPerkawinan">
                                        <option value="">Pilih</option>
                                        <option value="Kawin">Kawin</option>
                                        <option value="Tidak Kawin">Tidak Kawin</option>
                                    </select>
                                    <small class="text-danger" id="errStatusPerkawinan"></small>
                                </div>
                            </div>

                            <!-- DATA PASANGAN (conditional) -->
                            <div class="col-md-6 pasangan-field d-none">
                                <div class="form-group">
                                    <label>Nama Pasangan</label>
                                    <input class="form-control" id="namaPasangan" placeholder="Nama pasangan" />
                                    <small class="text-danger" id="errNamaPasangan"></small>
                                </div>
                            </div>

                            <div class="col-md-6 pasangan-field d-none">
                                <div class="form-group">
                                    <label>Tgl Lahir Pasangan</label>
                                    <input class="form-control" id="tglLahirPasangan" type="date" />
                                    <small class="text-danger" id="errTglLahirPasangan"></small>
                                </div>
                            </div>

                            <!-- Alamat Rumah -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat Rumah <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="alamatRumah" rows="2" placeholder="Alamat lengkap"></textarea>
                                    <small class="text-danger" id="errAlamatRumah"></small>
                                </div>
                            </div>

                            <!-- Provinsi, Kota, Kecamatan, Kelurahan, Kode Pos -->

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Provinsi <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="provinsiSelect">
                                        <option value="">Pilih</option>
                                        <!-- Isi via AJAX atau static -->
                                    </select>
                                    <small class="text-danger" id="errProvinsi"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kota/Kabupaten <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="kotaSelect">
                                        <option value="">Pilih</option>
                                    </select>
                                    <small class="text-danger" id="errKota"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kecamatan <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="kecamatanSelect">
                                        <option value="">Pilih</option>
                                    </select>
                                    <small class="text-danger" id="errKecamatan"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelurahan <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="kelurahanSelect">
                                        <option value="">Pilih</option>
                                    </select>
                                    <small class="text-danger" id="errKelurahan"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Pos <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="kodePos">
                                        <option value="">Pilih</option>
                                    </select>
                                    <small class="text-danger" id="errKodePos"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode DATI <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="kodeDati">
                                        <option value="">Pilih</option>
                                    </select>
                                    <small class="text-danger" id="errKodeDati"></small>
                                </div>
                            </div>

                            <!-- BANK -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Bank <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="bankSelect">
                                        <option value="">Pilih</option>
                                    </select>
                                    <small class="text-danger" id="errNamaBank"></small>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Pemilik Rekening <span class="text-danger">*</span></label>
                                    <input class="form-control" id="namaPemilikRek"
                                        placeholder="Harus sama dengan nama lengkap" />
                                    <small class="text-danger" id="errNamaPemilikRek"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No Rekening Bank <span class="text-danger">*</span></label>
                                    <input class="form-control" id="noRek" placeholder="1234567890" />
                                    <small class="text-danger" id="errNoRek"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Perusahaan <span class="text-danger">*</span></label>
                                    <input class="form-control" id="namaPerusahaan" placeholder="Nama perusahaan" />
                                    <small class="text-danger" id="errNamaPerusahaan"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bidang Usaha <span class="text-danger">*</span></label>
                                    <input class="form-control" id="bidangUsaha"
                                        placeholder="Contoh: Retail, Konstruksi" />
                                    <small class="text-danger" id="errBidangUsaha"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat Tempat Kerja <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="alamatKerja" rows="2" placeholder="Alamat kantor"></textarea>
                                    <small class="text-danger" id="errAlamatKerja"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIP (No Induk Pegawai) <span class="text-danger">*</span></label>
                                    <input class="form-control" id="nip" placeholder="Opsional" />
                                    <small class="text-danger" id="errNip"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status Pegawai <span class="text-danger">*</span></label>
                                    <select class="form-control" id="statusPegawai">
                                        <option value="">Pilih</option>
                                        <option value="Karyawan Tetap">Karyawan Tetap</option>
                                        <option value="Karyawan Tidak Tetap">Karyawan Tidak Tetap</option>
                                    </select>
                                    <small class="text-danger" id="errStatusPegawai"></small>
                                </div>
                            </div>

                            <div class="col-md-6 kontrak-field d-none">
                                <div class="form-group">
                                    <label>Tgl Akhir Kontrak Kerja</label>
                                    <input class="form-control" id="tglAkhirKontrak" type="date" />
                                    <small class="text-danger" id="errTglAkhirKontrak"></small>
                                </div>
                            </div>

                            <!-- Upload Section -->
                            <div class="col-12 mt-4">
                                <h6><i class="mdi mdi-file-document"></i> Dokumen Wajib</h6>
                                <hr>
                            </div>

                            <!-- UPLOAD KTP -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload KTP (JPEG/PNG/PDF) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadKtp">
                                            <i class="mdi mdi-upload"></i> Pilih File KTP
                                        </button>
                                        <input type="file" class="d-none" id="uploadKtp"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadKtp"></small>
                                    <div class="mt-2 preview-container" id="previewUploadKtp"></div>
                                </div>
                            </div>

                            <!-- UPLOAD NPWP -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload NPWP (JPEG/PNG/PDF)</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadNpwp">
                                            <i class="mdi mdi-upload"></i> Pilih File NPWP
                                        </button>
                                        <input type="file" class="d-none" id="uploadNpwp"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadNpwp"></small>
                                    <div class="mt-2 preview-container" id="previewUploadNpwp"></div>
                                </div>
                            </div>

                            <!-- UPLOAD Selfie -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Selfie dengan KTP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadSelfie">
                                            <i class="mdi mdi-upload"></i> Pilih File Selfie
                                        </button>
                                        <input type="file" class="d-none" id="uploadSelfie"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadSelfie"></small>
                                    <div class="mt-2 preview-container" id="previewUploadSelfie"></div>
                                </div>
                            </div>

                            <!-- UPLOAD KK -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Kartu Keluarga (KK)</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadKk">
                                            <i class="mdi mdi-upload"></i> Pilih File KK
                                        </button>
                                        <input type="file" class="d-none" id="uploadKk"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadKk"></small>
                                    <div class="mt-2 preview-container" id="previewUploadKk"></div>
                                </div>
                            </div>

                            <!-- UPLOAD ID Card (Opsional) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload ID Card Tambahan</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadIdCard">
                                            <i class="mdi mdi-upload"></i> Pilih File ID Card
                                        </button>
                                        <input type="file" class="d-none" id="uploadIdCard"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadIdCard"></small>
                                    <div class="mt-2 preview-container" id="previewUploadIdCard"></div>
                                </div>
                            </div>

                            <!-- UPLOAD SK Karyawan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload SK Karyawan</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadSk">
                                            <i class="mdi mdi-upload"></i> Pilih File SK
                                        </button>
                                        <input type="file" class="d-none" id="uploadSk"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadSk"></small>
                                    <div class="mt-2 preview-container" id="previewUploadSk"></div>
                                </div>
                            </div>

                            <!-- UPLOAD Slip Gaji -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Slip Gaji Terakhir</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadSlip">
                                            <i class="mdi mdi-upload"></i> Pilih Slip Gaji
                                        </button>
                                        <input type="file" class="d-none" id="uploadSlip"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadSlip"></small>
                                    <div class="mt-2 preview-container" id="previewUploadSlip"></div>
                                </div>
                            </div>

                            <!-- UPLOAD Buku Tabungan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Buku Tabungan</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadBukuTabungan">
                                            <i class="mdi mdi-upload"></i> Pilih Buku Tabungan
                                        </button>
                                        <input type="file" class="d-none" id="uploadBukuTabungan"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadBukuTabungan"></small>
                                    <div class="mt-2 preview-container" id="previewUploadBukuTabungan"></div>
                                </div>
                            </div>

                            <!-- UPLOAD Mutasi Rekening -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Mutasi Rekening (3 Bulan)</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadMutasi">
                                            <i class="mdi mdi-upload"></i> Pilih Mutasi Rekening
                                        </button>
                                        <input type="file" class="d-none" id="uploadMutasi"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadMutasi"></small>
                                    <div class="mt-2 preview-container" id="previewUploadMutasi"></div>
                                </div>
                            </div>

                            <!-- UPLOAD Form SLIK -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Form SLIK</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadSlik">
                                            <i class="mdi mdi-upload"></i> Pilih Form SLIK
                                        </button>
                                        <input type="file" class="d-none" id="uploadSlik"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadSlik"></small>
                                    <div class="mt-2 preview-container" id="previewUploadSlik"></div>
                                </div>
                            </div>

                            <!-- UPLOAD Form Kredit -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Form Kredit</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadKredit">
                                            <i class="mdi mdi-upload"></i> Pilih Form Kredit
                                        </button>
                                        <input type="file" class="d-none" id="uploadKredit"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadKredit"></small>
                                    <div class="mt-2 preview-container" id="previewUploadKredit"></div>
                                </div>
                            </div>

                            <!-- UPLOAD Form BTA -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Form BTA</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadBta">
                                            <i class="mdi mdi-upload"></i> Pilih Form BTA
                                        </button>
                                        <input type="file" class="d-none" id="uploadBta"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadBta"></small>
                                    <div class="mt-2 preview-container" id="previewUploadBta"></div>
                                </div>
                            </div>

                            <!-- UPLOAD Checklist -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Checklist Verifikasi</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-primary btn-block"
                                            id="btnUploadChecklist">
                                            <i class="mdi mdi-upload"></i> Pilih Checklist
                                        </button>
                                        <input type="file" class="d-none" id="uploadChecklist"
                                            accept=".jpg,.jpeg,.png,.pdf" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG, PDF</small>
                                    <small class="text-danger" id="errUploadChecklist"></small>
                                    <div class="mt-2 preview-container" id="previewUploadChecklist"></div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="buttonSubmit">Simpan</button>
                </div>
            </div>
        </div>
    </div>

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
                                        <button type="button"
                                            class="btn btn-outline-warning btn-block btn-replace-file"
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
                                        <button type="button"
                                            class="btn btn-outline-warning btn-block btn-replace-file"
                                            data-field="bukutabungan">
                                            <i class="mdi mdi-refresh"></i> Ganti File
                                        </button>
                                        <input type="file" class="d-none replace-file-input"
                                            id="replaceBukuTabungan" accept=".jpg,.jpeg,.png,.pdf" />
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
                                        <button type="button"
                                            class="btn btn-outline-warning btn-block btn-replace-file"
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
                                        <button type="button"
                                            class="btn btn-outline-warning btn-block btn-replace-file"
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
                                        <button type="button"
                                            class="btn btn-outline-warning btn-block btn-replace-file"
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
                                        <button type="button"
                                            class="btn btn-outline-warning btn-block btn-replace-file" data-field="bta">
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
                                        <button type="button"
                                            class="btn btn-outline-warning btn-block btn-replace-file"
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
                    <h5 class="modal-title" id="uploadSetujuModal">Upload Foto Pemohon & PK yang sudah tertanda tangani
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
                    <button type="button" class="btn btn-primary"
                        id="buttonSubmitUploadAnalystSetuju">Submit</button>
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

            // Event delegation untuk button "Ganti File"
            $(document).on('click', '.btn-replace-file:not(:disabled)', function() {
                const field = $(this).data('field');

                // Mapping field ke ID input yang sesuai
                const fieldMapping = {
                    'ktp': 'Ktp',
                    'npwp': 'Npwp',
                    'selfie': 'Selfie',
                    'kk': 'Kk',
                    'idcard': 'IdCard',
                    'sk': 'Sk',
                    'slip': 'Slip',
                    'bukutabungan': 'BukuTabungan',
                    'mutasi': 'Mutasi',
                    'slik': 'Slik',
                    'kredit': 'Kredit',
                    'bta': 'Bta',
                    'checklist': 'Checklist'
                };

                const capitalizedField = fieldMapping[field.toLowerCase()] || field.charAt(0)
                    .toUpperCase() + field.slice(1);
                const inputId = `replace${capitalizedField}`;

                // Trigger file input
                $(`#${inputId}`).trigger('click');
            });

            // Handle file selection untuk replace file
            $(document).on('change', '.replace-file-input', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Validasi file
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                const maxSize = 5 * 1024 * 1024; // 5 MB

                if (!validTypes.includes(file.type)) {
                    toastr.error('Format file tidak valid. Hanya JPG, PNG, PDF yang diperbolehkan.');
                    $(this).val('');
                    return;
                }

                if (file.size > maxSize) {
                    toastr.error('Ukuran file terlalu besar. Maksimal 5 MB.');
                    $(this).val('');
                    return;
                }

                // Extract field dari ID input (misal: replaceKtp -> Ktp)
                const inputId = $(this).attr('id');
                const field = inputId.replace('replace', '');

                // Simpan file ke object filesToReplace
                const fieldNameMap = {
                    'Ktp': 'ktp',
                    'Npwp': 'npwp',
                    'Selfie': 'selfie',
                    'Kk': 'kk',
                    'IdCard': 'idcard',
                    'Sk': 'sk',
                    'Slip': 'slip',
                    'BukuTabungan': 'bukutabungan',
                    'Mutasi': 'mutasi',
                    'Slik': 'slik',
                    'Kredit': 'kredit',
                    'Bta': 'bta',
                    'Checklist': 'checklist'
                };

                console.log("fields");
                console.log(fieldNameMap[field]);

                const fieldName = fieldNameMap[field];
                if (fieldName) {
                    filesToReplace[fieldName] = file;
                }

                // Tampilkan preview file baru
                $(`#previewEdit${field}`)
                    .removeClass('alert-warning alert-success alert-danger')
                    .addClass('alert-info')
                    .html(`
            <i class="mdi ${file.type.includes('pdf') ? 'mdi-file-pdf' : 'mdi-file-image'}"></i> 
            <span>${file.name}</span>
            <span class="badge badge-primary ml-2"><i class="mdi mdi-check-circle"></i> File Baru (Belum Disimpan)</span>
        `);
            });


            var dataTable = {
                "_token": "{{ csrf_token() }}",
            };
            let editID;

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


            // review edit nasabah
            edit = (id) => {
                editID = id;

                // Reset form dan error messages
                $('#formEditNasabah')[0].reset();
                $('.text-danger').text('');
                $('.pasangan-field-edit').addClass('d-none');
                $('.kontrak-field-edit').addClass('d-none');

                $.ajax({
                    url: '{!! route('bta.nasabah.detail') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': editID,
                    },
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#modalEdit').modal('show');
                        $('#buttonUpdate').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin"></i> Memuat data...');
                    },
                    success: function(detail) {

                        if (detail.data) {
                            const data = detail.data;
                            // Simpan ID
                            $('#editNasabahId').val(data.id);

                            // === IDENTITAS UTAMA ===
                            $("#editNamaLengkap").val(data.nama_lengkap || '');
                            $("#editNamaSingkat").val(data.nama_singkat || '');
                            $("#editNoKtp").val(data.no_ktp || '');
                            $("#editNoNpwp").val(data.no_npwp || '');
                            $("#editJenisKelamin").val(data.jenis_kelamin || '');
                            $("#editTempatLahir").val(data.tempat_lahir || '');
                            $("#editTanggalLahir").val(data.tanggal_lahir || '');
                            $("#editNamaIbu").val(data.nama_ibu || '');
                            $("#editNoTelp").val(data.no_telp || '');
                            $("#editAgama").val(data.agama || '');
                            $("#editPendidikan").val(data.pendidikan_terakhir || '');
                            $("#editStatusPerkawinan").val(data.status_perkawinan || '').trigger(
                                'change');

                            // Tampilkan/hide field pasangan berdasarkan status perkawinan
                            if (data.status_perkawinan === 'Kawin') {
                                $('.pasangan-field-edit').removeClass('d-none');
                                $("#editNamaPasangan").val(data.nama_pasangan || '');
                                $("#editTglLahirPasangan").val(data.tgl_lahir_pasangan || '');
                            } else {
                                $('.pasangan-field-edit').addClass('d-none');
                                $("#editNamaPasangan").val('');
                                $("#editTglLahirPasangan").val('');
                            }

                            // === ALAMAT RUMAH ===
                            $("#editAlamatRumah").val(data.alamat_rumah || '');

                            // === WILAYAH ===
                            loadWilayahEdit(data);
                            loadBank(data);

                            // === BANK ===
                            $("#editBankSelect").val(data.bank_id || '');
                            $("#editNamaPemilikRek").val(data.nama_pemilik_rek || '');
                            $("#editNoRek").val(data.no_rek || '');

                            // === PEKERJAAN ===
                            $("#editNamaPerusahaan").val(data.nama_perusahaan || '');
                            $("#editBidangUsaha").val(data.bidang_usaha || '');
                            $("#editAlamatKerja").val(data.alamat_kerja || '');
                            $("#editNip").val(data.nip || '');
                            $("#editStatusPegawai").val(data.status_pegawai || '').trigger(
                                'change');

                            // Tampilkan/hide field kontrak berdasarkan status pegawai
                            if (data.status_pegawai === 'Karyawan Tidak Tetap') {
                                $('.kontrak-field-edit').removeClass('d-none');
                                $("#editTglAkhirKontrak").val(data.tgl_akhir_kontrak || '');
                            } else {
                                $('.kontrak-field-edit').addClass('d-none');
                                $("#editTglAkhirKontrak").val('');
                            }

                            setAllFilePreviews(detail.file, detail.data);

                            function setAllFilePreviews(data, note) {
                                const dokumenFields = [{
                                        field: 'Ktp',
                                        fileName: data.ktp,
                                        status: note.status_ktp,
                                        note: note.note_ktp
                                    },
                                    {
                                        field: 'Npwp',
                                        fileName: data.npwp,
                                        status: note.status_npwp,
                                        note: note.note_npwp
                                    },
                                    {
                                        field: 'Selfie',
                                        fileName: data.selfie,
                                        status: note.status_selfie,
                                        note: note.note_selfie
                                    },
                                    {
                                        field: 'Kk',
                                        fileName: data.kk,
                                        status: note.status_kk,
                                        note: note.note_kk
                                    },
                                    {
                                        field: 'IdCard',
                                        fileName: data.id_card,
                                        status: note.status_id_card,
                                        note: note.note_id_card
                                    },
                                    {
                                        field: 'Sk',
                                        fileName: data.sk_karyawan,
                                        status: note.status_sk_karyawan,
                                        note: note.note_sk_karyawan
                                    },
                                    {
                                        field: 'Slip',
                                        fileName: data.slip_gaji,
                                        status: note.status_slip_gaji,
                                        note: note.note_slip_gaji
                                    },
                                    {
                                        field: 'BukuTabungan',
                                        fileName: data.buku_tabungan,
                                        status: note.status_buku_tabungan,
                                        note: note.note_buku_tabungan
                                    },
                                    {
                                        field: 'Mutasi',
                                        fileName: data.mutasi_rekening,
                                        status: note.status_mutasi_rekening,
                                        note: note.note_mutasi_rekening
                                    },
                                    {
                                        field: 'Slik',
                                        fileName: data.form_slik,
                                        status: note.status_form_slik,
                                        note: note.note_form_slik
                                    },
                                    {
                                        field: 'Kredit',
                                        fileName: data.form_kredit,
                                        status: note.status_form_kredit,
                                        note: note.note_form_kredit
                                    },
                                    {
                                        field: 'Bta',
                                        fileName: data.form_bta,
                                        status: note.status_form_bta,
                                        note: note.note_form_bta
                                    },
                                    {
                                        field: 'Checklist',
                                        fileName: data.form_checklist,
                                        status: note.status_form_checklist,
                                        note: note.note_form_checklist
                                    }
                                ];

                                dokumenFields.forEach(doc => {
                                    setFilePreview(doc.field, doc.fileName, doc.status, doc
                                        .note);
                                });
                            }


                            // === ENABLE BUTTON ===
                            $('#buttonUpdate').prop('disabled', false).text('Update Data');

                        } else {
                            toastr.error(detail.message || 'Gagal memuat data');
                            $('#modalEdit').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Terjadi kesalahan saat memuat data');
                        $('#modalEdit').modal('hide');
                    }
                });
            }

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

            // Helper: Load wilayah dengan chaining
            function loadWilayahEdit(data) {
                $.ajax({
                    url: "{{ route('bta.wilayah.provinsi') }}",
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $('#editProvinsiSelect').empty().append('<option value="">Pilih</option>');
                        response.forEach(prov => {
                            $('#editProvinsiSelect').append(
                                `<option value="${prov.id}">${prov.provinsi}</option>`);
                        });
                        $('#editProvinsiSelect').val(data.provinsi_id || '').trigger('change');
                        // Set timeout untuk load kota setelah provinsi
                        setTimeout(() => loadKotaEdit(data), 300);
                    }
                });
            }

            function loadBank(data) {
                $.ajax({
                    url: "{{ route('bta.wilayah.bank') }}",
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $('#editBankSelect').empty().append('<option value="">Pilih</option>');
                        response.forEach(bank => {
                            $('#editBankSelect').append(
                                `<option value="${bank.id}">${bank.code_bank} - ${bank
                                .description}</option>`);
                        });
                        $('#editBankSelect').val(data.bank_id || '').trigger('change');
                    }
                });
            }

            function loadKotaEdit(data) {
                const provId = $('#editProvinsiSelect').val();
                if (!provId) return;

                $.ajax({
                    url: "{{ route('bta.wilayah.kota.edit') }}",
                    type: 'POST',
                    data: {
                        id: provId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#editKotaSelect').empty().append('<option value="">Pilih</option>');
                        response.forEach(kota => {
                            $('#editKotaSelect').append(
                                `<option value="${kota.id}">${kota.kabupaten}</option>`);
                        });
                        $('#editKotaSelect').val(data.kota_id || '').trigger('change');
                        setTimeout(() => loadKecamatanEdit(data), 300);
                    }
                });
            }

            function loadKecamatanEdit(data) {
                const kotaId = $('#editKotaSelect').val();
                if (!kotaId) return;

                $.ajax({
                    url: "{{ route('bta.wilayah.kecamatan.edit') }}",
                    method: "POST",
                    data: {
                        id: kotaId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#editKecamatanSelect').empty().append('<option value="">Pilih</option>');
                        response.kecamatan.forEach(kec => {
                            $('#editKecamatanSelect').append(
                                `<option value="${kec.id}">${kec.kecamatan}</option>`);
                        });
                        $('#editKodeDati').val(response.dati.sandi || '');
                        $('#editKecamatanSelect').val(data.kecamatan_id || '').trigger('change');
                        setTimeout(() => loadKelurahanEdit(data), 300);
                    }
                });
            }

            function loadKelurahanEdit(data) {
                const kecId = $('#editKecamatanSelect').val();
                if (!kecId) return;

                $.ajax({
                    url: "{{ route('bta.wilayah.kelurahan.edit') }}",
                    method: "POST",
                    data: {
                        id: kecId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#editKelurahanSelect').empty().append('<option value="">Pilih</option>');
                        response.forEach(kel => {
                            $('#editKelurahanSelect').append(
                                `<option value="${kel.id}">${kel.desa} - ${kel.kodepos}</option>`
                            );
                        });
                        $('#editKelurahanSelect').val(data.kelurahan_id || '');
                    }
                });
            }

            function setFilePreview(field, fileName, status = null, note = null) {
                console.log("sadasdsadsadsadsadas");
                console.log(field);
                console.log(fileName);
                console.log(status);
                console.log(note);

                if (!fileName) {
                    $(`#previewEdit${field}`).addClass('alert-warning').removeClass(
                            'alert-info alert-success alert-danger')
                        .html(`<i class="mdi mdi-alert"></i> Belum ada file`);

                    // Simpan status ke variable tracking
                    currentFileStatus[field.toLowerCase()] = null;

                    // Enable button jika belum ada file
                    $(`button.btn-replace-file[data-field="${field.toLowerCase()}"]`)
                        .prop('disabled', false)
                        .removeClass('btn-secondary')
                        .addClass('btn-outline-warning')
                        .html(`<i class="mdi mdi-upload"></i> Upload File`);
                    return;
                }

                // Deteksi apakah fileName sudah berupa URL lengkap
                const isFullUrl = fileName.startsWith('http://') || fileName.startsWith('https://') || fileName
                    .startsWith('/bta/secure-file/');
                const filePath = isFullUrl ? fileName : `/bta/secure-file/${fileName}`;

                // Extract ekstensi dari URL atau filename
                let ext = '';
                try {
                    const url = new URL(isFullUrl && !fileName.startsWith('/') ? fileName :
                        `http://localhost${fileName}`);
                    const path = url.pathname;
                    ext = path.split('.').pop().toLowerCase();
                } catch (e) {
                    // Fallback jika bukan URL valid
                    ext = fileName.split('.').pop().toLowerCase();
                }

                const icon = ext === 'pdf' ? 'mdi-file-pdf' : 'mdi-file-image';

                // Tentukan class alert berdasarkan status
                let alertClass = 'alert-info';
                let statusBadge = '';
                let isApproved = false;

                if (status) {
                    if (status === 'approve') {
                        alertClass = 'alert-success';
                        statusBadge =
                            `<span class="badge badge-success ml-2"><i class="mdi mdi-check"></i> Disetujui</span>`;
                        isApproved = true;
                    } else if (status === 'reject') {
                        alertClass = 'alert-danger';
                        statusBadge =
                            `<span class="badge badge-danger ml-2"><i class="mdi mdi-close"></i> Ditolak</span>`;
                    } else if (status === 'pending') {
                        alertClass = 'alert-warning';
                        statusBadge =
                            `<span class="badge badge-warning ml-2"><i class="mdi mdi-timer-sand"></i> Pending</span>`;
                    }
                }

                // Tampilkan note jika ada
                let noteHtml = '';
                if (note) {
                    noteHtml =
                        `<div class="mt-1"><small class="text-muted"><i class="mdi mdi-comment-outline"></i> ${note}</small></div>`;
                }

                // Simpan status ke variable tracking
                currentFileStatus[field.toLowerCase()] = status;

                $(`#previewEdit${field}`)
                    .removeClass('alert-info alert-success alert-danger alert-warning')
                    .addClass(alertClass)
                    .html(`
            <i class="mdi ${icon}"></i> 
            <span>${field.toLowerCase()}</span>
            <a href="${filePath}" target="_blank" class="ml-2 text-primary view-file" data-field="${field.toLowerCase()}">
                <i class="mdi mdi-eye"></i> Lihat
            </a>
            ${statusBadge}
            ${noteHtml}
        `);

                // Disable/Enable button "Ganti File" berdasarkan status
                const replaceButton = $(`button.btn-replace-file[data-field="${field.toLowerCase()}"]`);

                if (isApproved) {
                    // Disable button jika sudah disetujui - TIDAK BISA DIGANTI
                    replaceButton
                        .prop('disabled', true)
                        .removeClass('btn-outline-warning')
                        .addClass('btn-secondary')
                        .html(`<i class="mdi mdi-lock"></i> File Disetujui`);
                } else {
                    // Enable button jika pending atau reject - BISA DIGANTI
                    replaceButton
                        .prop('disabled', false)
                        .removeClass('btn-secondary')
                        .addClass('btn-outline-warning')
                        .html(`<i class="mdi mdi-refresh"></i> Ganti File`);
                }
            }



            // Event listener untuk tombol Update
            $('#buttonUpdate').on('click', function() {
                if (!validateEditForm()) return;

                const formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('id', $('#editNasabahId').val());

                // Append semua field data
                formData.append('nama_lengkap', $('#editNamaLengkap').val());
                formData.append('nama_singkat', $('#editNamaSingkat').val());
                formData.append('no_ktp', $('#editNoKtp').val());
                formData.append('no_npwp', $('#editNoNpwp').val());
                formData.append('jenis_kelamin', $('#editJenisKelamin').val());
                formData.append('tempat_lahir', $('#editTempatLahir').val());
                formData.append('tanggal_lahir', $('#editTanggalLahir').val());
                formData.append('nama_ibu', $('#editNamaIbu').val());
                formData.append('no_telp', $('#editNoTelp').val());
                formData.append('agama', $('#editAgama').val());
                formData.append('pendidikan_terakhir', $('#editPendidikan').val());
                formData.append('status_perkawinan', $('#editStatusPerkawinan').val());
                formData.append('nama_pasangan', $('#editNamaPasangan').val());
                formData.append('tgl_lahir_pasangan', $('#editTglLahirPasangan').val());
                formData.append('alamat_rumah', $('#editAlamatRumah').val());
                formData.append('provinsi_id', $('#editProvinsiSelect').val());
                formData.append('kota_id', $('#editKotaSelect').val());
                formData.append('kecamatan_id', $('#editKecamatanSelect').val());
                formData.append('kelurahan_id', $('#editKelurahanSelect').val());
                formData.append('kode_dati', $('#editKodeDati').val());
                formData.append('bank_id', $('#editBankSelect').val());
                formData.append('nama_pemilik_rek', $('#editNamaPemilikRek').val());
                formData.append('no_rek', $('#editNoRek').val());
                formData.append('nama_perusahaan', $('#editNamaPerusahaan').val());
                formData.append('bidang_usaha', $('#editBidangUsaha').val());
                formData.append('alamat_kerja', $('#editAlamatKerja').val());
                formData.append('nip', $('#editNip').val());
                formData.append('status_pegawai', $('#editStatusPegawai').val());
                formData.append('tgl_akhir_kontrak', $('#editTglAkhirKontrak').val());

                // Append file yang diganti
                Object.keys(filesToReplace).forEach(key => {
                    formData.append(key, filesToReplace[key]);
                });

                $(this).prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin"></i> Mengupdate...');

                $.ajax({
                    url: '{{ route('bta.nasabah.update') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.message || 'Data berhasil diupdate');
                        // Reset filesToReplace
                        filesToReplace = {};
                        // Tutup modal setelah 1.5 detik
                        setTimeout(() => {
                            $('#modalEdit').modal('hide');
                            // Reload datatable
                            if (typeof table !== 'undefined') {
                                table.ajax.reload();
                            }
                        }, 1500);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Gagal mengupdate data');
                        $('#buttonUpdate').prop('disabled', false).text('Update Data');
                        // Tampilkan error validation
                        if (xhr.responseJSON?.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $(`#errEdit${key.charAt(0).toUpperCase() + key.slice(1)}`)
                                    .text(value[0]);
                            });
                        }
                    }
                });
            });


            function validateEditForm() {
                let valid = true;
                $('.text-danger').text('');

                // Validasi field wajib (data pribadi)
                if (!$('#editNamaLengkap').val()) {
                    $('#errEditNamaLengkap').text('Nama lengkap wajib diisi');
                    valid = false;
                }
                if (!$('#editJenisKelamin').val()) {
                    $('#errEditJenisKelamin').text('Jenis kelamin wajib diisi');
                    valid = false;
                }
                if (!$('#editTempatLahir').val()) {
                    $('#errEditTempatLahir').text('Tempat lahir wajib diisi');
                    valid = false;
                }
                if (!$('#editTanggalLahir').val()) {
                    $('#errEditTanggalLahir').text('Tanggal lahir wajib diisi');
                    valid = false;
                }
                if (!$('#editNamaIbu').val()) {
                    $('#errEditNamaIbu').text('Nama ibu wajib diisi');
                    valid = false;
                }
                if (!$('#editNoTelp').val()) {
                    $('#errEditNoTelp').text('No. telepon wajib diisi');
                    valid = false;
                }
                if (!$('#editAgama').val()) {
                    $('#errEditAgama').text('Agama wajib diisi');
                    valid = false;
                }
                if (!$('#editPendidikan').val()) {
                    $('#errEditPendidikan').text('Pendidikan terakhir wajib diisi');
                    valid = false;
                }
                if (!$('#editStatusPerkawinan').val()) {
                    $('#errEditStatusPerkawinan').text('Status perkawinan wajib diisi');
                    valid = false;
                }
                if ($('#editStatusPerkawinan').val() === 'Kawin') {
                    if (!$('#editNamaPasangan').val()) {
                        $('#errEditNamaPasangan').text('Nama pasangan wajib diisi');
                        valid = false;
                    }
                    if (!$('#editTglLahirPasangan').val()) {
                        $('#errEditTglLahirPasangan').text('Tanggal lahir pasangan wajib diisi');
                        valid = false;
                    }
                }
                if (!$('#editAlamatRumah').val()) {
                    $('#errEditAlamatRumah').text('Alamat rumah wajib diisi');
                    valid = false;
                }
                if (!$('#editProvinsiSelect').val()) {
                    $('#errEditProvinsi').text('Provinsi wajib diisi');
                    valid = false;
                }
                if (!$('#editKotaSelect').val()) {
                    $('#errEditKota').text('Kota wajib diisi');
                    valid = false;
                }
                if (!$('#editKecamatanSelect').val()) {
                    $('#errEditKecamatan').text('Kecamatan wajib diisi');
                    valid = false;
                }
                if (!$('#editKelurahanSelect').val()) {
                    $('#errEditKelurahan').text('Kelurahan wajib diisi');
                    valid = false;
                }
                if (!$('#editKodeDati').val()) {
                    $('#errEditKodeDati').text('Kode DATI wajib diisi');
                    valid = false;
                }
                if (!$('#editNamaPerusahaan').val()) {
                    $('#errEditNamaPerusahaan').text('Nama perusahaan wajib diisi');
                    valid = false;
                }
                if (!$('#editBidangUsaha').val()) {
                    $('#errEditBidangUsaha').text('Bidang usaha wajib diisi');
                    valid = false;
                }
                if (!$('#editAlamatKerja').val()) {
                    $('#errEditAlamatKerja').text('Alamat kerja wajib diisi');
                    valid = false;
                }
                if (!$('#editStatusPegawai').val()) {
                    $('#errEditStatusPegawai').text('Status pegawai wajib diisi');
                    valid = false;
                }
                if ($('#editStatusPegawai').val() === 'Karyawan Tidak Tetap' && !$('#editTglAkhirKontrak')
                    .val()) {
                    $('#errEditTglAkhirKontrak').text('Tanggal akhir kontrak wajib diisi');
                    valid = false;
                }
                if (!$('#editBankSelect').val()) {
                    $('#errEditNamaBank').text('Nama bank wajib diisi');
                    valid = false;
                }
                if (!$('#editNoRek').val()) {
                    $('#errEditNoRek').text('No. rekening wajib diisi');
                    valid = false;
                }
                if (!$('#editNamaPemilikRek').val()) {
                    $('#errEditNamaPemilikRek').text('Nama pemilik rekening wajib diisi');
                    valid = false;
                }

                // Validasi FILE YANG DITOLAK harus diganti
                const rejectedFiles = [];
                const fieldNames = {
                    'Ktp': 'ktp',
                    'Npwp': 'npwp',
                    'Selfie': 'selfie',
                    'Kk': 'kk',
                    'IdCard': 'id_card',
                    'Sk': 'sk_karyawan',
                    'Slip': 'slip_gaji',
                    'BukuTabungan': 'buku_tabungan',
                    'Mutasi': 'mutasi_rekening',
                    'Slik': 'form_slik',
                    'Kredit': 'form_kredit',
                    'Bta': 'form_bta',
                    'Checklist': 'form_checklist'
                };

                // Cek setiap field apakah statusnya reject dan belum diganti
                Object.keys(currentFileStatus).forEach(fieldKey => {

                    const status = currentFileStatus[fieldKey];
                    const fieldName = fieldNames[fieldKey];

                    if (status === 'reject') {
                        console.log("filesToReplace");
                        console.log(filesToReplace);
                        console.log("currentFileStatus");
                        console.log(currentFileStatus);
                        // console.log(status);
                        // console.log(fieldKey);
                        // Cek apakah file ini sudah diganti


                        const isReplaced = filesToReplace.hasOwnProperty(fieldKey);

                        if (!isReplaced) {
                            console.log(fieldName);
                            rejectedFiles.push(fieldName);
                            valid = false;
                        }
                    }
                });

                // Tampilkan pesan error jika ada file yang ditolak belum diganti
                if (rejectedFiles.length > 0) {
                    console.log("rejectedFiles");
                    console.log(rejectedFiles);
                    toastr.error(`File yang ditolak harus diganti: ${rejectedFiles.join(', ')}`);
                    // Highlight field yang belum diganti
                    rejectedFiles.forEach(fieldName => {
                        // Mapping field name ke ID error element
                        const fieldIdMap = {
                            'KTP': 'errEditUploadKtp',
                            'NPWP': 'errEditUploadNpwp',
                            'Selfie KTP': 'errEditUploadSelfie',
                            'Kartu Keluarga': 'errEditUploadKk',
                            'ID Card': 'errEditUploadIdCard',
                            'SK Karyawan': 'errEditUploadSk',
                            'Slip Gaji': 'errEditUploadSlip',
                            'Buku Tabungan': 'errEditUploadBukuTabungan',
                            'Mutasi Rekening': 'errEditUploadMutasi',
                            'Form SLIK': 'errEditUploadSlik',
                            'Form Kredit': 'errEditUploadKredit',
                            'Form BTA': 'errEditUploadBta',
                            'Checklist Verifikasi': 'errEditUploadChecklist'
                        };

                        const errorId = fieldIdMap[fieldName];
                        if (errorId) {
                            $(`#${errorId}`).text('File ini ditolak, silakan ganti dengan file baru');
                        }
                    });
                }

                return valid;
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



            $('.select2').select2({
                placeholder: "Pilih...",
                allowClear: true,
                width: '100%'
            });

            // Trigger file input saat tombol diklik
            const uploadButtons = [
                'btnUploadKtp', 'btnUploadNpwp', 'btnUploadSelfie', 'btnUploadKk',
                'btnUploadIdCard', 'btnUploadSk', 'btnUploadSlip',
                'btnUploadBukuTabungan', 'btnUploadMutasi',
                'btnUploadSlik', 'btnUploadKredit', 'btnUploadBta', 'btnUploadChecklist',
                'btnUploadFotoAkadKredit', 'btnUploadPkTertandaTangan'
            ];

            uploadButtons.forEach(btnId => {
                const btn = document.getElementById(btnId);
                if (!btn) return;
                const fileId = btnId.replace('btnU', 'u');
                const fileInput = document.getElementById(fileId);
                if (fileInput) {
                    btn.addEventListener('click', () => fileInput.click());
                }
            });

            // === PROVINSI ===
            $.ajax({
                url: "{{ route('bta.wilayah.provinsi') }}",
                method: "POST",
                data: {
                    '_token': "{{ csrf_token() }}",
                },
                success: function(data) {
                    const $select = $('#provinsiSelect');
                    data.forEach(item => {
                        const option = new Option(item.provinsi, item.provinsi,
                            false, false);
                        $select.append(option);
                    });
                    $select.trigger('change');
                }
            });

            $.ajax({
                url: "{{ route('bta.wilayah.bank') }}",
                method: "POST",
                data: {
                    '_token': "{{ csrf_token() }}",
                },
                success: function(data) {
                    const $select = $('#bankSelect');
                    data.forEach(item => {
                        const option = new Option(item.code_bank + ' - ' + item
                            .description,
                            item.id,
                            false, false);
                        $select.append(option);
                    });
                    $select.trigger('change');
                }
            });

            // // === KOTA (saat provinsi dipilih) ===
            // $('#provinsiSelect').on('change', function() {

            //     const provId = $(this).val();
            //     const $kota = $('#kotaSelect');
            //     const $kec = $('#kecamatanSelect');
            //     const $kel = $('#kelurahanSelect');

            //     // Reset & disable
            //     $kota.empty().trigger('change').prop('disabled', true);
            //     $kec.empty().trigger('change').prop('disabled', true);
            //     $kel.empty().trigger('change').prop('disabled', true);

            //     if (!provId) return;

            //     $.ajax({
            //         url: "{{ route('bta.wilayah.kota') }}",
            //         method: "POST",
            //         data: {
            //             nama: provId,
            //             '_token': "{{ csrf_token() }}",
            //         },
            //         success: function(data) {
            //             data.forEach(item => {
            //                 const option = new Option(item.kabupaten, item.kabupaten,
            //                     false,
            //                     false);
            //                 $kota.append(option);
            //             });
            //             $kota.prop('disabled', false).trigger('change');
            //         }
            //     });





            // });

            // // === KECAMATAN (saat kota dipilih) ===
            // $('#kotaSelect').on('change', function() {
            //     const kotaId = $(this).val();

            //     const $kec = $('#kecamatanSelect');
            //     const $kel = $('#kelurahanSelect');
            //     const $dat = $('#kodeDati');

            //     $dat.empty().trigger('change').prop('disabled', true);
            //     $kec.empty().trigger('change').prop('disabled', true);
            //     $kel.empty().trigger('change').prop('disabled', true);

            //     if (!kotaId) return;

            //     $.ajax({
            //         url: "{{ route('bta.wilayah.kecamatan') }}",
            //         method: "POST",
            //         data: {
            //             nama: kotaId,
            //             '_token': "{{ csrf_token() }}",
            //         },
            //         success: function(data) {
            //             data.forEach(item => {
            //                 const option = new Option(item.kecamatan, item.kecamatan,
            //                     false,
            //                     false);
            //                 $kec.append(option);
            //             });
            //             $kec.prop('disabled', false).trigger('change');
            //         }
            //     });

            //     $.ajax({
            //         url: "{{ route('bta.wilayah.dati') }}",
            //         method: "POST",
            //         data: {
            //             nama: kotaId,
            //             '_token': "{{ csrf_token() }}",
            //         },
            //         success: function(data) {
            //             const $dati = $('#kodeDati');
            //             data.forEach(item => {
            //                 const option = new Option(item.sandi, item.sandi, false,
            //                     false);
            //                 $dati.append(option);
            //             });
            //             $dati.trigger('change');
            //         }
            //     });
            // });

            // // === KELURAHAN (saat kecamatan dipilih) ===
            // $('#kecamatanSelect').on('change', function() {
            //     const kecId = $(this).val();
            //     const $kel = $('#kelurahanSelect');

            //     $kel.empty().trigger('change').prop('disabled', true);

            //     if (!kecId) return;

            //     $.ajax({
            //         url: "{{ route('bta.wilayah.kelurahan') }}",
            //         method: "POST",
            //         data: {
            //             nama: kecId,
            //             '_token': "{{ csrf_token() }}",
            //         },
            //         success: function(data) {
            //             console.log(data);

            //             data.forEach(item => {
            //                 const option = new Option(item.desa, item.desa, false,
            //                     false);
            //                 $kel.append(option);
            //             });

            //             $kel.prop('disabled', false).trigger('change');
            //         }
            //     });
            // });

            // $('#kelurahanSelect').on('change', function() {
            //     const desaId = $(this).val();
            //     const kecamatanId = $('#kecamatanSelect').val();
            //     const $kodepos = $('#kodePos');

            //     $kodepos.empty().trigger('change').prop('disabled', true);

            //     if (!desaId || !kecamatanId) return;

            //     console.log(desaId)
            //     console.log(kecamatanId)

            //     $.ajax({
            //         url: "{{ route('bta.wilayah.kodepos') }}",
            //         method: "POST",
            //         data: {
            //             kecamatan: desaId,
            //             desa: desaId,
            //             '_token': "{{ csrf_token() }}",
            //         },
            //         success: function(data) {

            //             data.forEach(item => {
            //                 const optionkodepos = new Option(item.kodepos, item.kodepos,
            //                     false,
            //                     false);
            //                 console.log(optionkodepos);
            //                 $kodepos.append(optionkodepos);
            //             });

            //             $kodepos.prop('disabled', false).trigger('change');
            //         }
            //     });
            // });


            function resetDropdowns(selectors) {
                selectors.forEach(selector => {
                    $(selector).empty().append('<option value="">Pilih...</option>').prop(
                            'disabled', true)
                        .trigger('change.select2');
                });
            }

            // 1. PROVINSI -> KOTA
            $('#provinsiSelect').on('change', function() {
                const provId = $(this).val();
                const $kota = $('#kotaSelect');

                // Reset semua anak di bawahnya
                resetDropdowns(['#kotaSelect', '#kecamatanSelect', '#kelurahanSelect',
                    '#kodeDati',
                    '#kodePos'
                ]);

                if (!provId) return;

                $.ajax({
                    url: "{{ route('bta.wilayah.kota') }}",
                    method: "POST",
                    data: {
                        nama: provId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $kota.append(
                            '<option value="">Pilih Kota/Kabupaten</option>');
                        data.forEach(item => {
                            $kota.append(new Option(item.kabupaten, item
                                .kabupaten));
                        });
                        $kota.prop('disabled', false);
                    }
                });
            });

            // 2. KOTA -> KECAMATAN & DATI
            $('#kotaSelect').on('change', function() {
                const kotaId = $(this).val();
                const $kec = $('#kecamatanSelect');
                const $dati = $('#kodeDati');

                resetDropdowns(['#kecamatanSelect', '#kelurahanSelect', '#kodePos']);
                $dati.empty(); // Dati biasanya bukan select manual, tapi otomatis

                if (!kotaId) return;

                // Load Kecamatan
                $.ajax({
                    url: "{{ route('bta.wilayah.kecamatan') }}",
                    method: "POST",
                    data: {
                        nama: kotaId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $kec.append('<option value="">Pilih Kecamatan</option>');
                        data.forEach(item => {
                            $kec.append(new Option(item.kecamatan, item
                                .kecamatan));
                        });
                        $kec.prop('disabled', false);
                    }
                });

                // Load Kode Dati
                $.ajax({
                    url: "{{ route('bta.wilayah.dati') }}",
                    method: "POST",
                    data: {
                        nama: kotaId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        data.forEach(item => {
                            $dati.append(new Option(item.sandi, item
                                .sandi));
                        });
                    }
                });
            });

            // 3. KECAMATAN -> KELURAHAN
            $('#kecamatanSelect').on('change', function() {
                const kecId = $(this).val();
                const $kel = $('#kelurahanSelect');

                resetDropdowns(['#kelurahanSelect', '#kodePos']);

                if (!kecId) return;

                $.ajax({
                    url: "{{ route('bta.wilayah.kelurahan') }}",
                    method: "POST",
                    data: {
                        nama: kecId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $kel.append('<option value="">Pilih Kelurahan</option>');
                        data.forEach(item => {
                            $kel.append(new Option(item.desa, item.desa));
                        });
                        $kel.prop('disabled', false);
                    }
                });
            });

            // 4. KELURAHAN -> KODEPOS
            $('#kelurahanSelect').on('change', function() {
                const desaId = $(this).val();
                const kecId = $('#kecamatanSelect')
                    .val(); // Ambil ID kecamatan yang sedang aktif
                const $kodepos = $('#kodePos');

                $kodepos.empty().prop('disabled', true);

                if (!desaId || !kecId) return;

                $.ajax({
                    url: "{{ route('bta.wilayah.kodepos') }}",
                    method: "POST",
                    data: {
                        kecamatan: kecId, // Perbaikan: gunakan kecId, bukan desaId
                        desa: desaId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        data.forEach(item => {
                            $kodepos.append(new Option(item.kodepos, item
                                .kodepos));
                        });
                        $kodepos.prop('disabled', false);
                    }
                });
            });

            // --- Logika toggle field (sama seperti sebelumnya) ---
            $('#statusPerkawinan').on('change', function() {
                if ($(this).val() === 'Kawin') {
                    $('.pasangan-field').removeClass('d-none');
                } else {
                    $('.pasangan-field').addClass('d-none');
                    $('#namaPasangan, #tglLahirPasangan').val('');
                }
            });

            $('#statusPegawai').on('change', function() {
                if ($(this).val() === 'Karyawan Tidak Tetap') {
                    $('.kontrak-field').removeClass('d-none');
                } else {
                    $('.kontrak-field').addClass('d-none');
                    $('#tglAkhirKontrak').val('');
                }
            });

            // --- Preview file (sama seperti sebelumnya) ---
            function previewFile(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                preview.innerHTML = '';

                if (!input.files || !input.files[0]) return;

                const file = input.files[0];
                const fileType = file.type;
                const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const validPdfType = 'application/pdf';

                if (validImageTypes.includes(fileType)) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML =
                            `<img src="${e.target.result}" class="img-thumbnail" style="max-height:150px;">`;
                    };
                    reader.readAsDataURL(file);
                } else if (fileType === validPdfType) {
                    preview.innerHTML = `
                    <a href="${URL.createObjectURL(file)}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="mdi mdi-file-pdf-outline"></i> Lihat PDF
                    </a>
                `;
                } else {
                    preview.innerHTML = '<span class="text-danger">Format tidak didukung</span>';
                }
            }

            const fileInputs = [
                'uploadKtp', 'uploadNpwp', 'uploadSelfie', 'uploadKk',
                'uploadIdCard', 'uploadSk', 'uploadSlip',
                'uploadBukuTabungan', 'uploadMutasi',
                'uploadSlik', 'uploadKredit', 'uploadBta', 'uploadChecklist',
                'uploadFotoAkadKredit', 'uploadPkTertandaTangan'
            ];

            fileInputs.forEach(id => {
                $(`#${id}`).on('change', function() {
                    const previewId = 'preview' + id.charAt(0).toUpperCase() + id.slice(
                        1);
                    previewFile(id, previewId);
                });
            });

            // --- SUBMIT VIA AJAX ---
            $('#buttonSubmit').on('click', function() {
                console.log("sadads");
                const btn = $(this);
                const loadingText = $('#loadingText');

                // Reset semua error
                $('small.text-danger').text('');

                // Validasi client-side minimal
                let valid = true;
                // Validasi No. KTP: hanya angka, 16 digit
                const noKtp = $('#noKtp').val();
                if (noKtp && (!/^\d{16}$/.test(noKtp))) {
                    toastr.info('No. KTP harus 16 digit angka');
                    $('#errNoKtp').text('No. KTP harus 16 digit angka');
                    valid = false;
                }
                const requiredFields = [{
                        id: 'namaLengkap',
                        err: 'errNamaLengkap'
                    },
                    {
                        id: 'jenisKelamin',
                        err: 'errJenisKelamin'
                    },
                    {
                        id: 'tempatLahir',
                        err: 'errTempatLahir'
                    },
                    {
                        id: 'tanggalLahir',
                        err: 'errTanggalLahir'
                    },
                    {
                        id: 'namaIbu',
                        err: 'errNamaIbu'
                    },
                    {
                        id: 'noTelp',
                        err: 'errNoTelp'
                    },
                    {
                        id: 'agama',
                        err: 'errAgama'
                    },
                    {
                        id: 'pendidikan',
                        err: 'errPendidikan'
                    },
                    {
                        id: 'statusPerkawinan',
                        err: 'errStatusPerkawinan'
                    },
                    {
                        id: 'alamatRumah',
                        err: 'errAlamatRumah'
                    },
                    {
                        id: 'provinsiSelect',
                        err: 'errProvinsi'
                    },
                    {
                        id: 'kotaSelect',
                        err: 'errKota'
                    },
                    {
                        id: 'kecamatanSelect',
                        err: 'errKecamatan'
                    },
                    {
                        id: 'kelurahanSelect',
                        err: 'errKelurahan'
                    },
                    {
                        id: 'kodePos',
                        err: 'errKodePos'
                    },
                    {
                        id: 'kodeDati',
                        err: 'errKodeDati'
                    },
                    {
                        id: 'namaPerusahaan',
                        err: 'errNamaPerusahaan'
                    },
                    {
                        id: 'bidangUsaha',
                        err: 'errBidangUsaha'
                    },
                    {
                        id: 'alamatKerja',
                        err: 'errAlamatKerja'
                    },
                    {
                        id: 'statusPegawai',
                        err: 'errStatusPegawai'
                    },
                    {
                        id: 'bankSelect',
                        err: 'errNamaBank'
                    },
                    {
                        id: 'noRek',
                        err: 'errNoRek'
                    },
                    {
                        id: 'namaPemilikRek',
                        err: 'errNamaPemilikRek'
                    },
                    {
                        id: 'noKtp',
                        err: 'errNoKtp'
                    },
                ];

                requiredFields.forEach(field => {
                    if (!$(`#${field.id}`).val()) {
                        toastr.info("Pastikan form inputan sesuai")
                        $(`#${field.err}`).text('Wajib diisi');
                        valid = false;
                    }
                });

                // Validasi file wajib
                const requiredFiles = [{
                        id: 'uploadKtp',
                        err: 'errUploadKtp'
                    },
                    {
                        id: 'uploadSelfie',
                        err: 'errUploadSelfie'
                    },
                    {
                        id: 'uploadKk',
                        err: 'errUploadKk'
                    },
                    {
                        id: 'uploadSlip',
                        err: 'errUploadSlip'
                    },
                    {
                        id: 'uploadBukuTabungan',
                        err: 'errUploadBukuTabungan'
                    }
                ];

                requiredFiles.forEach(file => {
                    if (!$(`#${file.id}`)[0].files.length) {
                        $(`#${file.err}`).text('Wajib upload');
                        valid = false;
                    }
                });

                if (!valid) return;

                // Siapkan FormData
                let formData = new FormData();

                // Tambahkan field teks
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('nama_lengkap', $('#namaLengkap').val());
                formData.append('nama_singkat', $('#namaSingkat').val());
                formData.append('jenis_kelamin', $('#jenisKelamin').val());
                formData.append('tempat_lahir', $('#tempatLahir').val());
                formData.append('tanggal_lahir', $('#tanggalLahir').val());
                formData.append('nama_ibu', $('#namaIbu').val());
                formData.append('no_telp', $('#noTelp').val());
                formData.append('agama', $('#agama').val());
                formData.append('pendidikan_terakhir', $('#pendidikan').val());
                formData.append('status_perkawinan', $('#statusPerkawinan').val());

                if ($('#statusPerkawinan').val() === 'Kawin') {
                    formData.append('nama_pasangan', $('#namaPasangan').val());
                    formData.append('tgl_lahir_pasangan', $('#tglLahirPasangan').val());
                }

                formData.append('alamat_rumah', $('#alamatRumah').val());
                formData.append('provinsi', $('#provinsiSelect').val());
                formData.append('kota', $('#kotaSelect').val());
                formData.append('kecamatan', $('#kecamatanSelect').val());
                formData.append('kelurahan', $('#kelurahanSelect').val());
                formData.append('kode_pos', $('#kodePos').val());
                formData.append('kode_dati', $('#kodeDati').val());

                formData.append('nama_perusahaan', $('#namaPerusahaan').val());
                formData.append('bidang_usaha', $('#bidangUsaha').val());
                formData.append('alamat_kerja', $('#alamatKerja').val());
                formData.append('nip', $('#nip').val());
                formData.append('status_pegawai', $('#statusPegawai').val());

                if ($('#statusPegawai').val() === 'Karyawan Tidak Tetap') {
                    formData.append('tgl_akhir_kontrak', $('#tglAkhirKontrak').val());
                }

                formData.append('bank_id', $('#bankSelect').val());
                formData.append('no_rek', $('#noRek').val());
                formData.append('nama_pemilik_rek', $('#namaPemilikRek').val());
                formData.append('no_ktp', $('#noKtp').val());
                formData.append('no_npwp', $('#noNpwp').val());
                // Tambahkan file
                const fileMap = {
                    'uploadKtp': 'ktp',
                    'uploadNpwp': 'npwp',
                    'uploadSelfie': 'selfie',
                    'uploadKk': 'kk',
                    'uploadIdCard': 'id_card',
                    'uploadSk': 'sk_karyawan',
                    'uploadSlip': 'slip_gaji',
                    'uploadBukuTabungan': 'buku_tabungan',
                    'uploadMutasi': 'mutasi_rekening',
                    'uploadSlik': 'form_slik',
                    'uploadKredit': 'form_kredit',
                    'uploadBta': 'form_bta',
                    'uploadChecklist': 'form_checklist'
                };

                Object.keys(fileMap).forEach(key => {
                    const fileInput = $(`#${key}`)[0];
                    if (fileInput.files.length > 0) {
                        formData.append(fileMap[key], fileInput.files[0]);
                    }
                });

                // Nonaktifkan tombol & tampilkan loading
                btn.prop('disabled', true);
                loadingText.html('<i class="fas fa-spinner fa-spin"></i>');

                // Kirim via AJAX
                $.ajax({
                    url: "{{ route('bta.nasabah.insert') }}", // Sesuaikan dengan route Anda
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Swal.fire('Berhasil!', response.message ||
                        //     'Data nasabah berhasil disimpan.', 'success');
                        toastr.success(response.message)
                        $('#modalAdd').modal('hide');
                        tableRevisi.ajax.reload();
                        tableCredam.ajax.reload();
                        tableDitolak.ajax.reload();
                        tableAnalyst.ajax.reload();
                        tableDisetujui.ajax.reload();
                        tableSelesai.ajax.reload();
                        resetForm();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            toastr.info('Periksa kembali inputan anda')
                            const errors = xhr.responseJSON?.errors;
                            if (errors) {
                                // Mapping error ke field
                                mapErrorsToFields(errors);
                            }
                        } else {
                            // Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data.',
                            //     'error');
                            toastr.info(response.message)
                        }
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                        loadingText.empty();
                    }
                });
            });

            // Fungsi reset form
            function resetForm() {
                $('#formNasabah')[0].reset();
                $('small.text-danger').text('');
                $('.preview-container').empty();
                $('.pasangan-field, .kontrak-field').addClass('d-none');
            }

            // Fungsi mapping error Laravel ke field
            function mapErrorsToFields(errors) {
                const fieldMap = {
                    'nama_lengkap': 'errNamaLengkap',
                    'jenis_kelamin': 'errJenisKelamin',
                    'tempat_lahir': 'errTempatLahir',
                    'tanggal_lahir': 'errTanggalLahir',
                    'nama_ibu': 'errNamaIbu',
                    'no_telp': 'errNoTelp',
                    'agama': 'errAgama',
                    'pendidikan_terakhir': 'errPendidikan',
                    'status_perkawinan': 'errStatusPerkawinan',
                    'nama_pasangan': 'errNamaPasangan',
                    'tgl_lahir_pasangan': 'errTglLahirPasangan',
                    'alamat_rumah': 'errAlamatRumah',
                    'provinsi': 'errProvinsi',
                    'kota': 'errKota',
                    'kecamatan': 'errKecamatan',
                    'kelurahan': 'errKelurahan',
                    'kode_pos': 'errKodePos',
                    'kode_dati': 'errKodeDati',
                    'nama_perusahaan': 'errNamaPerusahaan',
                    'bidang_usaha': 'errBidangUsaha',
                    'alamat_kerja': 'errAlamatKerja',
                    'status_pegawai': 'errStatusPegawai',
                    'tgl_akhir_kontrak': 'errTglAkhirKontrak',
                    'bank_id': 'errNamaBank',
                    'no_rek': 'errNoRek',
                    'nama_pemilik_rek': 'errNamaPemilikRek',
                    'ktp': 'errUploadKtp',
                    'selfie': 'errUploadSelfie',
                    'kk': 'errUploadKk',
                    'slip_gaji': 'errUploadSlip',
                    'buku_tabungan': 'errUploadBukuTabungan'
                };

                Object.keys(fieldMap).forEach(key => {
                    if (errors[key]) {
                        $(`#${fieldMap[key]}`).text(errors[key][0]);
                    }
                });
            }
        });
    </script>
@endpush
