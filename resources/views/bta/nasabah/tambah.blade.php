@extends('bta.layout.app')
@section('title', 'Form Tambah Nasabah')
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

                    <div class="tab-content">
                        <!-- TAB 1: IDENTITAS UTAMA -->
                        <div class="tab-pane show active" id="tab-identitas" role="tabpanel">
                            <h5 class="mb-4"><i class="mdi mdi-account-circle text-primary"></i> Identitas Utama</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                                        <input class="form-control" id="namaLengkap" name="nama_lengkap"
                                            placeholder="Nama sesuai KTP" />
                                        <small class="text-danger" id="errNamaLengkap"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Singkat</label>
                                        <input class="form-control" id="namaSingkat" name="nama_singkat"
                                            placeholder="Nama panggilan" />
                                        <small class="text-danger" id="errNamaSingkat"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No. KTP <span class="text-danger">*</span></label>
                                        <input class="form-control" id="noKtp" name="no_ktp"
                                            placeholder="Contoh: 3275041234567890" maxlength="16" />
                                        <small class="text-danger" id="errNoKtp"></small>
                                    </div>
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
                                            <input type="file" class="d-none" id="uploadKtp" name="upload_ktp"
                                                accept=".jpg,.jpeg,.png,.pdf" />
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadKtp"></small>
                                        <div class="mt-2 preview-container" id="previewUploadKtp"></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No. NPWP <span class="text-danger">*</span></label>
                                        <input class="form-control" id="noNpwp" name="no_npwp"
                                            placeholder="Contoh: 012345678901234" maxlength="16" />
                                        <small class="text-danger" id="errNoNpwp"></small>
                                    </div>
                                </div>

                                <!-- UPLOAD NPWP -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload NPWP (JPEG/PNG/PDF) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadNpwp">
                                                <i class="mdi mdi-upload"></i> Pilih File NPWP
                                            </button>
                                            <input type="file" class="d-none" id="uploadNpwp" name="upload_npwp"
                                                accept=".jpg,.jpeg,.png,.pdf" />
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadNpwp"></small>
                                        <div class="mt-2 preview-container" id="previewUploadNpwp"></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-control" id="jenisKelamin" name="jenis_kelamin">
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
                                        <input class="form-control" id="tempatLahir" name="tempat_lahir"
                                            placeholder="Contoh: Jakarta" />
                                        <small class="text-danger" id="errTempatLahir"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input class="form-control" id="tanggalLahir" name="tanggal_lahir"
                                            type="date" />
                                        <small class="text-danger" id="errTanggalLahir"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Gadis Ibu Kandung <span class="text-danger">*</span></label>
                                        <input class="form-control" id="namaIbu" name="nama_ibu"
                                            placeholder="Nama ibu kandung" />
                                        <small class="text-danger" id="errNamaIbu"></small>
                                    </div>
                                </div>

                                <!-- UPLOAD KK -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadKk">
                                                <i class="mdi mdi-upload"></i> Pilih File KK
                                            </button>
                                            <input type="file" class="d-none" id="uploadKk" name="upload_kk"
                                                accept=".jpg,.jpeg,.png,.pdf" />
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadKk"></small>
                                        <div class="mt-2 preview-container" id="previewUploadKk"></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No. Telepon <span class="text-danger">*</span></label>
                                        <input class="form-control" id="noTelp" name="no_telp"
                                            placeholder="081234567890" />
                                        <small class="text-danger" id="errNoTelp"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: DATA PRIBADI -->
                        <div class="tab-pane" id="tab-pribadi" role="tabpanel">
                            <h5 class="mb-4"><i class="mdi mdi-information-outline text-primary"></i> Data Pribadi
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Agama <span class="text-danger">*</span></label>
                                        <select class="form-control" id="agama" name="agama">
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
                                        <select class="form-control" id="pendidikan" name="pendidikan">
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
                                        <select class="form-control" id="statusPerkawinan" name="status_perkawinan">
                                            <option value="">Pilih</option>
                                            <option value="Kawin">Kawin</option>
                                            <option value="Tidak Kawin">Tidak Kawin</option>
                                            <option value="Cerai Hidup">Cerai Hidup</option>
                                            <option value="Cerai Mati">Cerai Mati</option>
                                        </select>
                                        <small class="text-danger" id="errStatusPerkawinan"></small>
                                    </div>
                                </div>

                                <!-- DATA PASANGAN (conditional) -->
                                <div class="col-md-6 pasangan-field d-none">
                                    <div class="form-group">
                                        <label>Nama Pasangan</label>
                                        <input class="form-control" id="namaPasangan" name="nama_pasangan"
                                            placeholder="Nama pasangan" />
                                        <small class="text-danger" id="errNamaPasangan"></small>
                                    </div>
                                </div>

                                <div class="col-md-6 pasangan-field d-none">
                                    <div class="form-group">
                                        <label>Tgl Lahir Pasangan</label>
                                        <input class="form-control" id="tglLahirPasangan" name="tgl_lahir_pasangan"
                                            type="date" />
                                        <small class="text-danger" id="errTglLahirPasangan"></small>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Alamat Rumah <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="alamatRumah" name="alamat_rumah" rows="3"
                                            placeholder="Alamat lengkap"></textarea>
                                        <small class="text-danger" id="errAlamatRumah"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Provinsi <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="provinsiSelect" name="provinsi">
                                            <option value="">Pilih</option>
                                            @foreach ($provinsis as $provinsi)
                                            <option value="{{ $provinsi->id }}">{{ $provinsi->provinsi }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger" id="errProvinsi"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kota/Kabupaten <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="kotaSelect" name="kota">
                                            <option value="">Pilih</option>
                                        </select>
                                        <small class="text-danger" id="errKota"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kecamatan <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="kecamatanSelect" name="kecamatan">
                                            <option value="">Pilih</option>
                                        </select>
                                        <small class="text-danger" id="errKecamatan"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kelurahan <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="kelurahanSelect" name="kelurahan">
                                            <option value="">Pilih</option>
                                        </select>
                                        <small class="text-danger" id="errKelurahan"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kode Pos <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" readonly value="" id="kodePos"
                                            placeholder="00000" name="kodePos">
                                        <small class="text-danger" id="errKodePos"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kode DATI <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" readonly value="" id="kodeDati"
                                            name="kode_dati" placeholder="00000">
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
                                            <option value="Karyawan Tetap">Karyawan Tetap</option>
                                            <option value="Karyawan Tidak Tetap">Karyawan Tidak Tetap</option>
                                            <option value="Pensiun">Pensiun</option>
                                        </select>
                                        <small class="text-danger" id="errStatusPegawai"></small>
                                    </div>
                                </div>

                                <div class="col-md-6 pensiun-field">
                                    <div class="form-group">
                                        <label>Nama Perusahaan <span class="text-danger">*</span></label>
                                        <input class="form-control" id="namaPerusahaan" name="nama_perusahaan"
                                            placeholder="Nama perusahaan" />
                                        <small class="text-danger" id="errNamaPerusahaan"></small>
                                    </div>
                                </div>

                                <div class="col-md-6 pensiun-field">
                                    <div class="form-group">
                                        <label>Bidang Usaha <span class="text-danger">*</span></label>
                                        <input class="form-control" id="bidangUsaha" name="bidang_usaha"
                                            placeholder="Contoh: Retail, Konstruksi" />
                                        <small class="text-danger" id="errBidangUsaha"></small>
                                    </div>
                                </div>

                                <div class="col-md-12 pensiun-field">
                                    <div class="form-group">
                                        <label>Alamat Tempat Kerja <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="alamatKerja" name="alamat_kerja" rows="3"
                                            placeholder="Alamat kantor"></textarea>
                                        <small class="text-danger" id="errAlamatKerja"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>NIP (No Induk Pegawai) / KARIP (Kartu Identitas Pensiun)</label>
                                        <input class="form-control" id="nip" name="nip" placeholder="Opsional" />
                                        <small class="text-danger" id="errNip"></small>
                                    </div>
                                </div>

                                <div class="col-md-6 kontrak-field d-none">
                                    <div class="form-group">
                                        <label>Tgl Akhir Kontrak Kerja / Tanggal Pensiun</label>
                                        <input class="form-control" id="tglAkhirKontrak" name="tgl_akhir_kontrak"
                                            type="date" />
                                        <small class="text-danger" id="errTglAkhirKontrak"></small>
                                    </div>
                                </div>

                                <!-- UPLOAD SK Karyawan -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload SK Karyawan / SK Pensiun <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadSk">
                                                <i class="mdi mdi-upload"></i> Pilih File SK
                                            </button>
                                            <input type="file" class="d-none" id="uploadSk" name="upload_sk"
                                                accept=".jpg,.jpeg,.png,.pdf" />
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadSk"></small>
                                        <div class="mt-2 preview-container" id="previewUploadSk"></div>
                                    </div>
                                </div>

                                <!-- UPLOAD Slip Gaji -->
                                <div class="col-md-6 pensiun-field">
                                    <div class="form-group">
                                        <label>Upload Slip Gaji Terakhir <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadSlip">
                                                <i class="mdi mdi-upload"></i> Pilih Slip Gaji
                                            </button>
                                            <input type="file" class="d-none" id="uploadSlip" name="upload_slip"
                                                accept=".jpg,.jpeg,.png,.pdf" />
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadSlip"></small>
                                        <div class="mt-2 preview-container" id="previewUploadSlip"></div>
                                    </div>
                                </div>

                                <!-- upload ID Card -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload ID Card / ID Pensiun (Karip) <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadIdCard">
                                                <i class="mdi mdi-upload"></i> Pilih ID Card
                                            </button>
                                            <input type="file" class="d-none" id="uploadIdCard" name="upload_id_card"
                                                accept=".jpg,.jpeg,.png,.pdf" />
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadIdCard"></small>
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
                                        <select class="form-control select2" id="bankSelect" name="bankSelect">
                                            <option value="">Pilih</option>
                                            @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->code_bank }} -
                                                {{ $bank->description }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger" id="errNamaBank"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Pemilik Rekening <span class="text-danger">*</span></label>
                                        <input class="form-control" id="namaPemilikRek" name="nama_pemilik_rek"
                                            placeholder="Harus sama dengan nama lengkap" />
                                        <small class="text-danger" id="errNamaPemilikRek"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No Rekening Bank <span class="text-danger">*</span></label>
                                        <input class="form-control" id="noRek" name="no_rek" placeholder="1234567890" />
                                        <small class="text-danger" id="errNoRek"></small>
                                    </div>
                                </div>

                                <!-- UPLOAD Buku Tabungan -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Buku Tabungan <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadBukuTabungan">
                                                <i class="mdi mdi-upload"></i> Pilih Buku Tabungan
                                            </button>
                                            <input type="file" class="d-none" id="uploadBukuTabungan"
                                                name="upload_buku_tabungan" accept=".jpg,.jpeg,.png,.pdf" />
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadBukuTabungan"></small>
                                        <div class="mt-2 preview-container" id="previewUploadBukuTabungan"></div>
                                    </div>
                                </div>

                                <!-- UPLOAD Mutasi Rekening -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Mutasi Rekening (3 Bulan) <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadMutasi">
                                                <i class="mdi mdi-upload"></i> Pilih Mutasi Rekening
                                            </button>
                                            <input type="file" class="d-none" id="uploadMutasi" name="upload_mutasi"
                                                accept=".jpg,.jpeg,.png,.pdf" />
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadMutasi"></small>
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
                                            id="plafond" value="" placeholder="Jumlah Pengajuan">
                                        <small class="text-danger" id="errPlafond"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="tipe_bunga">Tipe Bunga: <span class="text-danger">*</span></label>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="customRadio1" name="tipe_bunga"
                                                class="custom-control-input" value="flat" checked>
                                            <label class="custom-control-label" for="customRadio1">Flat</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="customRadio2" name="tipe_bunga"
                                                class="custom-control-input" value="efektif">
                                            <label class="custom-control-label" for="customRadio2">Efektif
                                                (Anuitas)</label>
                                        </div>
                                        <small class="text-danger" id="errTipeBunga"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="jangka_waktu">Jangka Waktu (bulan): <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="jangka_waktu" id="jangka_waktu"
                                            value="" min="1" max="60" placeholder="Jangka Waktu">
                                        <small class="text-danger" id="errJangkaWaktu"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="suku_bunga" class="form-label">Suku Bunga: <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="suku_bunga"
                                                placeholder="Masukkan angka" aria-label="suku_bunga"
                                                aria-describedby="persen-addon" name="suku_bunga" value="">
                                            <span class="input-group-text" id="persen-addon">%</span>
                                        </div>
                                        <small class="text-danger" id="errSukuBunga"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="nilai_angsuran">Nilai Angsuran (PMT): <span
                                                class="text-danger">*</span></label>
                                        <input type="text" readonly class="form-control" name="nilai_angsuran"
                                            id="nilai_angsuran" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="biaya_bank">Biaya Bank: <span class="text-danger">*</span></label>
                                        <input type="text" step="0.01" class="form-control rupiah-format"
                                            name="biaya_bank" id="biaya_bank" value="" placeholder="Masukan angka">
                                    </div>
                                    <div class="form-group">
                                        <label for="biaya_asuransi">Biaya Asuransi: <span
                                                class="text-danger">*</span></label>
                                        <input type="text" step="0.01" class="form-control rupiah-format"
                                            name="biaya_asuransi" id="biaya_asuransi" value=""
                                            placeholder="Masukan angka">
                                    </div>
                                    <div class="form-group">
                                        <label for="biaya_koperasi">Biaya Koperasi:
                                            <span class="text-danger">*</span></label>
                                        <input type="text" step="0.01" class="form-control rupiah-format"
                                            name="biaya_koperasi" id="biaya_koperasi" value=""
                                            placeholder="Masukan angka">
                                    </div>
                                    <div class="form-group">
                                        <label for="simpanan_koperasi">Simpanan Koperasi: <span
                                                class="text-danger">*</span></label>
                                        <input type="text" step="0.01" class="form-control rupiah-format"
                                            name="simpanan_koperasi" id="simpanan_koperasi" value=""
                                            placeholder="Masukan angka">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tujuan_penggunaan">Tujuan Penggunaan: <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" name="tujuan_penggunaan" id="tujuan_penggunaan">
                                            <option value="" disabled selected>-- Pilih Tujuan --</option>
                                            @php
                                            $options = ['Konsumsi', 'Modal Kerja', 'Investasi'];
                                            @endphp
                                            @foreach ($options as $option)
                                            <option value="{{ $option }}">
                                                {{ $option }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger" id="errTujuanPenggunaan"></small>
                                    </div>
                                    <div class="form-group">
                                        <label for="gaji_bersih">Gaji Bersih: <span class="text-danger">*</span></label>
                                        <input type="text" step="0.01" class="form-control rupiah-format"
                                            name="gaji_bersih" id="gaji_bersih" value="" placeholder="Masukan angka">
                                        <small class="text-danger" id="errGajiBersih"></small>
                                    </div>
                                    <div class="form-group">
                                        <label for="mulai_pinjaman">Mulai Pinjaman: <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="mulai_pinjaman"
                                            id="mulai_pinjaman" value="">
                                        <small class="text-danger" id="errMulaiPinjaman"></small>
                                    </div>
                                    <div class="form-group">
                                        <label for="akhir_pinjaman">Akhir Pinjaman: <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" readonly name="akhir_pinjaman"
                                            id="akhir_pinjaman" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="dbr">DBR (Debt Burden Ratio): <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" name="dbr" id="dbr"
                                            value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 6: UPLOAD DOKUMEN -->
                        <div class="tab-pane" id="tab-dokumen" role="tabpanel">
                            <h5 class="mb-4"><i class="mdi mdi-file-document text-primary"></i> Upload Dokumen</h5>

                            <div class="row">
                                <!-- UPLOAD Form SLIK -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Form SLIK <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadSlik">
                                                <i class="mdi mdi-upload"></i> Pilih Form SLIK
                                            </button>
                                            <input type="file" class="d-none" id="uploadSlik" name="upload_slik"
                                                accept=".jpg,.jpeg,.png,.pdf">
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadSlik"></small>
                                        <div class="mt-2 preview-container" id="previewUploadSlik"></div>
                                    </div>
                                </div>

                                <!-- UPLOAD Form Kredit -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Form Kredit <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadKredit">
                                                <i class="mdi mdi-upload"></i> Pilih Form Kredit
                                            </button>
                                            <input type="file" class="d-none" id="uploadKredit" name="upload_kredit"
                                                accept=".jpg,.jpeg,.png,.pdf">
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadKredit"></small>
                                        <div class="mt-2 preview-container" id="previewUploadKredit"></div>
                                    </div>
                                </div>

                                <!-- UPLOAD Form BTA -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Form BTA <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadBta">
                                                <i class="mdi mdi-upload"></i> Pilih Form BTA
                                            </button>
                                            <input type="file" class="d-none" id="uploadBta" name="upload_bta"
                                                accept=".jpg,.jpeg,.png,.pdf">
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadBta"></small>
                                        <div class="mt-2 preview-container" id="previewUploadBta"></div>
                                    </div>
                                </div>

                                <!-- UPLOAD Checklist -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Checklist Verifikasi <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadChecklist">
                                                <i class="mdi mdi-upload"></i> Pilih Checklist
                                            </button>
                                            <input type="file" class="d-none" id="uploadChecklist"
                                                name="upload_checklist" accept=".jpg,.jpeg,.png,.pdf">
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 5 MB | Format: JPG, PNG,
                                            PDF</small>
                                        <small class="text-danger" id="errUploadChecklist"></small>
                                        <div class="mt-2 preview-container" id="previewUploadChecklist"></div>
                                    </div>
                                </div>

                                <!-- ================= VIDEO UPLOAD BARU ================= -->
                                <!-- UPLOAD Video Interview -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Video Interview <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadVideoInterview">
                                                <i class="mdi mdi-video"></i> Pilih Video Interview
                                            </button>
                                            <input type="file" class="d-none" id="uploadVideoInterview"
                                                name="upload_video_interview" accept=".mp4,.mov,.avi,.webm">
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 50 MB | Format: MP4, MOV, AVI,
                                            WEBM</small>
                                        <small class="text-muted d-block">Akan dikompres otomatis sebelum
                                            upload</small>
                                        <small class="text-danger" id="errUploadVideoInterview"></small>
                                        <div class="mt-2 preview-container" id="previewUploadVideoInterview"></div>
                                        <!-- Progress Bar Kompresi -->
                                        <div class="progress mt-2 d-none" id="progressVideoInterview">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- UPLOAD Video Asuransi -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Video Asuransi <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-primary btn-block"
                                                id="btnUploadVideoAsuransi">
                                                <i class="mdi mdi-video"></i> Pilih Video Asuransi
                                            </button>
                                            <input type="file" class="d-none" id="uploadVideoAsuransi"
                                                name="upload_video_asuransi" accept=".mp4,.mov,.avi,.webm">
                                        </div>
                                        <small class="text-muted mt-1 d-block">Maks. 50 MB | Format: MP4, MOV, AVI,
                                            WEBM</small>
                                        <small class="text-muted d-block">Akan dikompres otomatis sebelum
                                            upload</small>
                                        <small class="text-danger" id="errUploadVideoAsuransi"></small>
                                        <div class="mt-2 preview-container" id="previewUploadVideoAsuransi"></div>
                                        <!-- Progress Bar Kompresi -->
                                        <div class="progress mt-2 d-none" id="progressVideoAsuransi">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
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
@endsection

@push('js')
<!-- ⚠️ PENTING: FFmpeg.wasm CDN - VERSI 0.10.0 (TIDAK PERLU SharedArrayBuffer) -->
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
            // Helper untuk update progress bar
            const updateProgress = (progressId) => {
                return ({
                    ratio
                }) => {
                    const progressElement = document.getElementById(progressId);
                    const progressBar = progressElement?.querySelector('.progress-bar');
                    if (progressBar) {
                        const percent = Math.round(ratio * 100);
                        progressBar.style.width = percent + '%';
                        progressBar.setAttribute('aria-valuenow', percent);
                        progressBar.textContent = percent + '%';
                    }
                };
            };

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

        // Compress Video Function
        async function compressVideo(file, type, progressId) {
            const progressElement = document.getElementById(progressId);
            const progressBar = progressElement.querySelector('.progress-bar');

            progressElement.classList.remove('d-none');
            progressBar.style.width = '0%'; // 👈 Reset ke 0% di awal
            progressBar.classList.add('progress-bar-animated');
            progressBar.classList.remove('bg-success');

            try {
                await initFFmpeg(type);
                const ffmpeg = type === 'interview' ? ffmpegInterview : ffmpegAsuransi;

                const fileName = `input_${type}.mp4`;
                const outputName = `output_${type}.mp4`;

                ffmpeg.FS('writeFile', fileName, await fetchFile(file));

                // Kompresi
                await ffmpeg.run(
                    '-i', fileName,
                    '-vf', 'scale=-1:720',
                    '-c:v', 'libx264',
                    '-preset', 'medium',
                    '-crf', '28',
                    '-c:a', 'aac',
                    '-b:a', '128k',
                    outputName
                );

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
                    progressBar.style.width = '0%';
                    progressBar.classList.remove('bg-success');
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
                <div class="alert alert-warning">
                    <i class="mdi mdi-loading mdi-spin mr-2"></i> 
                    Mengkompres video... (${(file.size / 1024 / 1024).toFixed(2)} MB)
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

                // Tampilkan hasil
                $(`#${previewId}`).html(`
                <div class="alert alert-success">
                    <i class="mdi mdi-check-circle mr-2"></i> 
                    Video berhasil dikompres!<br>
                    <small>${result.originalSize}MB → ${result.compressedSize}MB (${result.reduction}% lebih kecil)</small>
                </div>
                <video width="100%" controls class="mt-2">
                    <source src="${URL.createObjectURL(result.blob)}" type="video/mp4">
                    Browser Anda tidak mendukung video tag.
                </video>
            `);

                $(`#${errorId}`).text('');

            } catch (error) {
                console.error('Video compression error:', error);
                $(`#${previewId}`).html(`
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert-circle mr-2"></i> 
                    Gagal mengkompres video. Silakan coba lagi atau upload video yang lebih kecil.
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

        // ================= SELECT WILAYAH =================
        function resetDropdowns(selectors) {
            selectors.forEach(selector => {
                $(selector).empty().append('<option value="">Pilih...</option>').prop(
                    'disabled', true)
                    .trigger('change.select2');
            });
        }

        // 1. PROVINSI -> KOTA
        $('#provinsiSelect').on('change', function () {
            const provId = $(this).val();
            const $kota = $('#kotaSelect');

            resetDropdowns(['#kotaSelect', '#kecamatanSelect', '#kelurahanSelect']);

            if (!provId) return;

            $.ajax({
                url: "{{ route('bta.wilayah.kota.new') }}",
                method: "POST",
                data: {
                    id: provId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    $kota.append(
                        '<option value="">Pilih Kota/Kabupaten</option>');
                    data.forEach(item => {
                        $kota.append(new Option(item.kabupaten, item.id));
                    });
                    $kota.prop('disabled', false);
                }
            });
        });

        // 2. KOTA -> KECAMATAN & DATI
        $('#kotaSelect').on('change', function () {
            const kotaId = $(this).val();
            const $kec = $('#kecamatanSelect');
            const $dati = $('#kodeDati');

            resetDropdowns(['#kecamatanSelect', '#kelurahanSelect']);
            $dati.empty();

            if (!kotaId) return;

            $.ajax({
                url: "{{ route('bta.wilayah.kecamatan.new') }}",
                method: "POST",
                data: {
                    id: kotaId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    $kec.append('<option value="">Pilih Kecamatan</option>');
                    data.forEach(item => {
                        $kec.append(new Option(item.kecamatan, item.id));
                    });
                    $kec.prop('disabled', false);
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
                    console.log(data);
                    $('#kodeDati').val(data.sandi);
                }
            });
        });

        // 3. KECAMATAN -> KELURAHAN
        $('#kecamatanSelect').on('change', function () {
            const kecId = $(this).val();
            const $kel = $('#kelurahanSelect');

            resetDropdowns(['#kelurahanSelect']);

            if (!kecId) return;

            $.ajax({
                url: "{{ route('bta.wilayah.kelurahan.new') }}",
                method: "POST",
                data: {
                    id: kecId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    $kel.append('<option value="">Pilih Kelurahan</option>');
                    data.forEach(item => {
                        $kel.append(new Option(item.desa + ' - ' + item.kodepos,
                            item.id));
                    });
                    $kel.prop('disabled', false);
                }
            });
        });

        $('#kelurahanSelect').on('change', function () {
            const desaId = $(this).val();
            $.ajax({
                url: "{{ route('bta.wilayah.kodepos.new') }}",
                method: "POST",
                data: {
                    id: desaId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    $('#kodePos').val(data.kodepos);
                }
            })
        })

        // ================= TAB NAVIGATION =================
        const totalTabs = 6;
        let currentTabIndex = 0;

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('href');
            currentTabIndex = getTabIndexFromId(target);
            updateTabNavigation();
        });

        function getTabIndexFromId(tabId) {
            const tabs = ['#tab-identitas', '#tab-pribadi', '#tab-pekerjaan', '#tab-bank', '#tab-kta',
                '#tab-dokumen'
            ];
            return tabs.indexOf(tabId);
        }

        function updateTabNavigation() {
            if (currentTabIndex === 0) {
                $('#btnPrevTab').hide();
            } else {
                $('#btnPrevTab').show();
            }

            if (currentTabIndex === totalTabs - 1) {
                $('#btnNextTab').hide();
            } else {
                $('#btnNextTab').show();
            }
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

        // ================= FORM SUBMISSION =================
        $('#formNasabahBertahap').submit(function (e) {
            e.preventDefault();

            if (!validateAllTabs()) {
                return false;
            }

            // Cek apakah video sedang dikompres
            if ($('#progressVideoInterview').hasClass('d-none') === false ||
                $('#progressVideoAsuransi').hasClass('d-none') === false) {
                toastr.warning('Tunggu proses kompresi video selesai', 'Proses Belum Selesai');
                return false;
            }

            // Cek apakah video sudah dikompres
            if (!compressedVideoInterview || !compressedVideoAsuransi) {
                toastr.error('Video Interview dan Video Asuransi wajib dikompres terlebih dahulu',
                    'Video Belum Diproses');
                return false;
            }

            let formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');

            // Tambahkan compressed video ke FormData (replace file asli)
            formData.delete('upload_video_interview');
            formData.delete('upload_video_asuransi');
            formData.append('upload_video_interview', compressedVideoInterview);
            formData.append('upload_video_asuransi', compressedVideoAsuransi);

            $.post({
                url: "{{ route('bta.nasabah.insert') }}",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('#submitBtn').prop('disabled', true).html(
                        '<i class="mdi mdi-loading mdi-spin mr-1"></i> Menyimpan...');
                },
                success: function (response) {
                    toastr.success(response.message || 'Data nasabah berhasil disimpan',
                        'Berhasil!');
                    setTimeout(function () {
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

                    if (Object.keys(errors).length > 0) {
                        displayErrors(errors);
                    }

                    let errorMessageToShow = message;
                    if (!message || message.includes('more errors')) {
                        const firstErrorKey = Object.keys(errors)[0];
                        if (firstErrorKey && errors[firstErrorKey][0]) {
                            errorMessageToShow = errors[firstErrorKey][0];
                        }
                    }

                    toastr.error(errorMessageToShow, 'Gagal!');
                    $('#submitBtn').prop('disabled', false).html(
                        '<i class="mdi mdi-content-save mr-1"></i> Simpan Data');
                }
            });
        });

        // ================= VALIDATION =================
        function validateAllTabs() {
            let allValid = true;
            let firstErrorTab = null;

            for (let i = 0; i < totalTabs; i++) {
                currentTabIndex = i;
                const tabValid = validateCurrentTabSilent();
                if (!tabValid && firstErrorTab === null) {
                    firstErrorTab = i;
                    allValid = false;
                }
            }

            if (firstErrorTab !== null) {
                const errorTab = $('.nav-tabs .nav-item').eq(firstErrorTab).find('a[data-toggle="tab"]');
                errorTab.tab('show');
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
                    if (!$('#uploadKtp')[0].files.length) {
                        markError('uploadKtp', 'Upload KTP wajib diisi');
                        isValid = false;
                    }
                    if (!$('#uploadNpwp')[0].files.length) {
                        markError('uploadNpwp', 'Upload Npwp wajib diisi');
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
                    if (!$('#uploadKk')[0].files.length) {
                        markError('uploadKk', 'Upload KK wajib diisi');
                        isValid = false;
                    }
                    if (!$('#noTelp').val().trim()) {
                        markError('noTelp', 'Nomor telepon wajib diisi');
                        isValid = false;
                    }
                    break;

                case 1: // Data Pribadi
                    if (!$('#agama').val()) {
                        markError('agama', 'Agama wajib dipilih');
                        isValid = false;
                    }
                    if (!$('#pendidikan').val()) {
                        markError('pendidikan', 'Pendidikan terakhir wajib dipilih');
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
                        markError('kodeDati', 'Kode DATI wajib dipilih');
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
                    break;

                case 4: // KTA Multiguna
                    if (!$('#plafond').val().trim()) {
                        markError('plafond', 'Plafond wajib diisi');
                        isValid = false;
                    }
                    if (!$('#jangka_waktu').val() || $('#jangka_waktu').val() <= 0) {
                        markError('jangka_waktu', 'Jangka waktu wajib diisi dan minimal 1 bulan');
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
                        markError('mulai_pinjaman', 'Tanggal mulai pinjaman wajib diisi');
                        isValid = false;
                    }
                    break;

                case 5: // Dokumen
                    if (!$('#uploadSlik')[0].files.length) {
                        markError('uploadSlik', 'Upload Form SLIK wajib diisi');
                        isValid = false;
                    }
                    if (!$('#uploadKredit')[0].files.length) {
                        markError('uploadKredit', 'Upload Form Kredit wajib diisi');
                        isValid = false;
                    }
                    if (!$('#uploadBta')[0].files.length) {
                        markError('uploadBta', 'Upload Form BTA wajib diisi');
                        isValid = false;
                    }
                    if (!$('#uploadChecklist')[0].files.length) {
                        markError('uploadChecklist', 'Upload Checklist Verifikasi wajib diisi');
                        isValid = false;
                    }
                    if (!compressedVideoInterview) {
                        markError('uploadVideoInterview', 'Video Interview wajib diupload dan dikompres');
                        isValid = false;
                    }
                    if (!compressedVideoAsuransi) {
                        markError('uploadVideoAsuransi', 'Video Asuransi wajib diupload dan dikompres');
                        isValid = false;
                    }
                    break;
            }

            return isValid;
        }

        function validateCurrentTab() {
            let isValid = true;
            $('.text-danger').text('');
            $('.form-control').removeClass('is-invalid');

            switch (currentTabIndex) {
                case 0: // Identitas
                    if (!$('#namaLengkap').val().trim()) {
                        showError('namaLengkap', 'Nama lengkap wajib diisi');
                        isValid = false;
                    }
                    if (!$('#noKtp').val().trim() || $('#noKtp').val().length !== 16) {
                        showError('noKtp', 'NIK harus 16 digit');
                        isValid = false;
                    }
                    if (!$('#noNpwp').val().trim() || $('#noNpwp').val().length !== 16) {
                        showError('noNpwp', 'NPWP harus 16 digit');
                        isValid = false;
                    }
                    if (!$('#uploadKtp')[0].files.length) {
                        showError('uploadKtp', 'Upload KTP wajib diisi');
                        isValid = false;
                    }
                    if (!$('#uploadNpwp')[0].files.length) {
                        showError('uploadNpwp', 'Upload Npwp wajib diisi');
                        isValid = false;
                    }
                    if (!$('#jenisKelamin').val()) {
                        showError('jenisKelamin', 'Jenis kelamin wajib dipilih');
                        isValid = false;
                    }
                    if (!$('#tempatLahir').val().trim()) {
                        showError('tempatLahir', 'Tempat lahir wajib diisi');
                        isValid = false;
                    }
                    if (!$('#tanggalLahir').val()) {
                        showError('tanggalLahir', 'Tanggal lahir wajib diisi');
                        isValid = false;
                    }
                    if (!$('#namaIbu').val().trim()) {
                        showError('namaIbu', 'Nama ibu kandung wajib diisi');
                        isValid = false;
                    }
                    if (!$('#uploadKk')[0].files.length) {
                        showError('uploadKk', 'Upload KK wajib diisi');
                        isValid = false;
                    }
                    if (!$('#noTelp').val().trim()) {
                        showError('noTelp', 'Nomor telepon wajib diisi');
                        isValid = false;
                    }
                    break;

                case 1: // Data Pribadi
                    if (!$('#agama').val()) {
                        showError('agama', 'Agama wajib dipilih');
                        isValid = false;
                    }
                    if (!$('#pendidikan').val()) {
                        showError('pendidikan', 'Pendidikan terakhir wajib dipilih');
                        isValid = false;
                    }

                    if ($('#statusPerkawinan').val() === 'Kawin') {
                        if (!$('#namaPasangan').val().trim()) {
                            showError('namaPasangan', 'Nama pasangan wajib diisi');
                            isValid = false;
                        }
                        if (!$('#tglLahirPasangan').val()) {
                            showError('tglLahirPasangan', 'Tanggal lahir pasangan wajib diisi');
                            isValid = false;
                        }
                    }

                    if (!$('#alamatRumah').val().trim()) {
                        showError('alamatRumah', 'Alamat rumah wajib diisi');
                        isValid = false;
                    }
                    if (!$('#provinsiSelect').val()) {
                        showError('provinsi', 'Provinsi wajib dipilih');
                        isValid = false;
                    }
                    if (!$('#kotaSelect').val()) {
                        showError('kota', 'Kota/Kabupaten wajib dipilih');
                        isValid = false;
                    }
                    if (!$('#kecamatanSelect').val()) {
                        showError('kecamatan', 'Kecamatan wajib dipilih');
                        isValid = false;
                    }
                    if (!$('#kelurahanSelect').val()) {
                        showError('kelurahan', 'Kelurahan wajib dipilih');
                        isValid = false;
                    }
                    if (!$('#kodePos').val()) {
                        showError('kodePos', 'Kode pos wajib diisi');
                        isValid = false;
                    }
                    if (!$('#kodeDati').val()) {
                        showError('kodeDati', 'Kode DATI wajib dipilih');
                        isValid = false;
                    }
                    break;

                case 2: // Pekerjaan
                    const statusPegawai = $('#statusPegawai').val();

                    // Validasi field wajib untuk SEMUA status pegawai

                    if (!$('#statusPegawai').val()) {
                        showError('statusPegawai', 'Status pegawai wajib dipilih');
                        isValid = false;
                    }

                    // Validasi conditional berdasarkan status pegawai
                    if (statusPegawai === 'Karyawan Tidak Tetap') {
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

                    } else if (statusPegawai === 'Pensiun') {
                        // Pensiun: wajib NIP/KARIP
                        if (!$('#nip').val().trim()) {
                            showError('nip', 'NIP / No. KARIP wajib diisi untuk pensiunan');
                            isValid = false;
                        }
                    } else if (statusPegawai === "Tetap") {
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
                    break;
                case 3: // Bank
                    if (!$('#bankSelect').val()) {
                        showError('bank', 'Nama bank wajib dipilih');
                        isValid = false;
                    }
                    if (!$('#namaPemilikRek').val().trim()) {
                        showError('namaPemilikRek', 'Nama pemilik rekening wajib diisi');
                        isValid = false;
                    }
                    if (!$('#noRek').val().trim()) {
                        showError('noRek', 'Nomor rekening wajib diisi');
                        isValid = false;
                    }
                    break;

                case 4: // KTA Multiguna
                    if (!$('#plafond').val().trim()) {
                        showError('plafond', 'Plafond wajib diisi');
                        isValid = false;
                    }
                    if (!$('#jangka_waktu').val() || $('#jangka_waktu').val() <= 0) {
                        showError('jangka_waktu', 'Jangka waktu wajib diisi dan minimal 1 bulan');
                        isValid = false;
                    }
                    if (!$('#suku_bunga').val() || $('#suku_bunga').val() <= 0) {
                        showError('suku_bunga', 'Suku bunga wajib diisi');
                        isValid = false;
                    }
                    if (!$('#tujuan_penggunaan').val()) {
                        showError('tujuan_penggunaan', 'Tujuan penggunaan wajib dipilih');
                        isValid = false;
                    }
                    if (!$('#gaji_bersih').val().trim()) {
                        showError('gaji_bersih', 'Gaji bersih wajib diisi');
                        isValid = false;
                    }
                    if (!$('#mulai_pinjaman').val()) {
                        showError('mulai_pinjaman', 'Tanggal mulai pinjaman wajib diisi');
                        isValid = false;
                    }
                    break;

                case 5: // Dokumen
                    if (!$('#uploadSlik')[0].files.length) {
                        showError('uploadSlik', 'Upload Form SLIK wajib diisi');
                        isValid = false;
                    }
                    if (!$('#uploadKredit')[0].files.length) {
                        showError('uploadKredit', 'Upload Form Kredit wajib diisi');
                        isValid = false;
                    }
                    if (!$('#uploadBta')[0].files.length) {
                        showError('uploadBta', 'Upload Form BTA wajib diisi');
                        isValid = false;
                    }
                    if (!$('#uploadChecklist')[0].files.length) {
                        showError('uploadChecklist', 'Upload Checklist Verifikasi wajib diisi');
                        isValid = false;
                    }
                    if (!compressedVideoInterview) {
                        showError('uploadVideoInterview', 'Video Interview wajib diupload dan dikompres');
                        isValid = false;
                    }
                    if (!compressedVideoAsuransi) {
                        showError('uploadVideoAsuransi', 'Video Asuransi wajib diupload dan dikompres');
                        isValid = false;
                    }
                    break;
            }

            return isValid;
        }

        function markError(fieldId, message) {
            $(`#${fieldId}`).addClass('is-invalid');
            const fieldName = fieldId.charAt(0).toUpperCase() + fieldId.slice(1);
            $(`#err${fieldName}`).text(message);
        }

        function showError(fieldId, message) {
            try {
                const $field = $(`#${fieldId}`);
                if (!$field.length) {
                    console.warn(`Field dengan ID "${fieldId}" tidak ditemukan`);
                    return;
                }

                const cleanFieldId = fieldId.replace(/[^a-zA-Z0-9]/g, '');
                const errorId = `err${cleanFieldId.charAt(0).toUpperCase() + cleanFieldId.slice(1)}`;
                const $errorElement = $(`#${errorId}`);

                $field.addClass('is-invalid');

                if ($errorElement.length) {
                    $errorElement.text(message);
                }

                $('html, body').animate({
                    scrollTop: $field.offset().top - 100
                }, 500, function () {
                    $field.focus();
                });

            } catch (error) {
                console.error('Error di showError function:', error);
            }
        }

        function displayErrors(errors) {
            $.each(errors, function (key, value) {
                $(`[name="${key}"]`).addClass('is-invalid');
                const fieldName = key.charAt(0).toUpperCase() + key.slice(1);
                if ($(`#err${fieldName}`).length) {
                    $(`#err${fieldName}`).text(value[0]);
                }
            });
        }

        // ================= EVENT HANDLERS =================
        $('.form-control').on('input', function () {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
                $(this).siblings('.text-danger').text('');
            }
        });

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

        // File preview
        $('.d-none[type="file"]').on('change', function () {
            const id = $(this).attr('id');
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $(`#preview${id.charAt(0).toUpperCase() + id.slice(1)}`).html(`
                    <div class="alert alert-info">
                        <i class="mdi mdi-file mr-2"></i> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                    </div>
                `);
                };
                reader.readAsDataURL(file);
            }
        });

        // Status Perkawinan change
        $('#statusPerkawinan').change(function () {
            if ($(this).val() === 'Kawin') {
                $('.pasangan-field').removeClass('d-none');
            } else {
                $('.pasangan-field').addClass('d-none');
                $('#namaPasangan').val('');
                $('#tglLahirPasangan').val('');
            }
        });

        // Status Pegawai change
        $('#statusPegawai').change(function () {
            if ($(this).val() === 'Karyawan Tidak Tetap') {
                $('.kontrak-field').removeClass('d-none');
                $('.pensiun-field').removeClass('d-none');
            } else if ($(this).val() === 'Pensiun') {
                $('.kontrak-field').removeClass('d-none');
                $('.pensiun-field').addClass('d-none');
            } else {
                $('.kontrak-field').addClass('d-none');
                $('.pensiun-field').removeClass('d-none');
                $('#tglAkhirKontrak').val('');
            }
        });

        // ================= FORMAT RUPIAH =================
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

        // ================= CALCULATE ANGSURAN (FLAT & EFEKTIF) =================
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

        // Calculate Akhir Pinjaman
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

        // Event listeners untuk perhitungan otomatis
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

        // Initialize
        updateTabNavigation();
    });
</script>
@endpush