@extends('layout.app')
@php($isTomiDana = request()->routeIs('admin.tomi-dana.perusahaan'))
@section('title', $isTomiDana ? 'Perusahaan TomiDana' : 'Koperasi Simpan Pinjam')
@push('css')
<style>
    #modalAdd .form-section { border: 1px solid #e9ecef; border-radius: 6px; padding: 16px 16px 12px; margin-bottom: 16px; background: #fff; }
    #modalAdd .form-section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; color: #188ae2; border-bottom: 1px dashed #e9ecef; padding-bottom: 8px; margin-bottom: 14px; }
    #modalAdd .form-section-title i { margin-right: 4px; }
    #modalAdd label { font-weight: 600; font-size: 13px; }
    #modalAdd .pks-card { background: #f8f9fa; }
    #modalAdd .modal-body { background: #f5f6f8; }
</style>
@endpush
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $isTomiDana ? 'TomiDana' : 'Tomi' }}</a></li>
                    <li class="breadcrumb-item active">{{ $isTomiDana ? 'Perusahaan' : 'MoU perusahaan lender' }}</li>
                </ol>
            </div>
            <h4 class="page-title">{{ $isTomiDana ? 'Perusahaan TomiDana' : 'MoU perusahaan lender' }}</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">PERUSAHAAN DENGAN STATUS
                                    ACTIVE</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $perusahaanAktif }}</div>

                            </div>
                            <div class="col-auto">
                                <i class="fa fa-check fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">PERUSAHAAN DENGAN STATUS
                                    NON ACTIVE</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $perusahaanTidakAktif }}</div>

                            </div>
                            <div class="col-auto">
                                <i class="fa fa-ban fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">

                <button type="button" class="btn btn-sm btn-primary float-right waves-effect waves-light"
                    data-toggle="modal" data-target="#modalAdd">
                    <i class="mdi mdi-plus-circle"></i> Tambah Perusahaan
                </button>

                <table id="tableListPerusahaan" class="table dt-responsive table-hover mt-4 text-center justify-center"
                    style="width:100%">
                    {{-- <thead>
                        <tr>
                            <th>ID Perusahaan</th>
                            <th>Lender</th>
                            <th>Unique Code</th>
                            <th>PIC Perusahaan</th>
                            <th>PIC HC/HRD</th>
                            <th>Nama Perusahaan</th>
                            <th>Email Perusahaan</th>
                            <th>Telepon</th>
                            <th>Provinsi</th>
                            <th>Kabupaten / Kota</th>
                            <th>Kecamatan</th>
                            <th>Desa</th>
                            <th>Alamat Lengkap</th>
                            <th>Payment Date</th>
                            <th>No. perjanjian kerja sama lender x perusahaan</th>
                            <th>List Karyawan</th>
                            <th>Proses Pengajuan</th>
                            <th>Konfigurasi Pinjaman</th>
                            <th>Status</th>
                        </tr>
                    </thead> --}}

                    <thead>
                        <tr>
                            <!-- 1. Identitas Utama -->
                            <th>Nama Perusahaan</th> <!-- Informasi utama -->
                            <th>PIC Perusahaan</th> <!-- PIC utama -->
                            <th>Nama PIC HC / HRD</th> <!-- PIC tambahan -->
                            <th>Email Perusahaan</th> <!-- Kontak utama -->
                            <th>Telepon</th> <!-- Kontak utama -->

                            <!-- 2. Informasi Operasional/Keuangan -->
                            <th>Payment Date</th> <!-- Jadwal penting -->
                            <th>H-Jatuh Tempo</th> <!-- Jadwal penting -->
                            <th>Status</th> <!-- Status penting -->
                            <th class="none">Lender</th> <!-- Status penting -->

                            <!-- 3. Identifikasi -->
                            <th class="none">Unique Code</th> <!-- ID unik untuk referensi -->
                            <th class="none">ID Perusahaan</th> <!-- ID sistem -->

                            <!-- 4. Detail Alamat -->
                            <th class="none">Provinsi</th>
                            <th class="none">Kabupaten / Kota</th>
                            <th class="none">Kecamatan</th>
                            <th class="none">Desa</th>
                            <th class="none">Alamat Lengkap</th>

                            <!-- 5. Konfigurasi -->
                            <th class="none">Konfigurasi Pinjaman</th> <!-- Fitur penting -->
                            <th class="none">Proses Pengajuan</th> <!-- Proses terkait -->
                            <th class="none">List Karyawan</th> <!-- Data terkait -->
                            <th class="none">Nomor PKS</th> <!-- Dokumen terkait -->

                            <!-- 6. Aksi -->
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>


        </div>
    </div>
</div>




{{-- modal tambah --}}
<div class="modal fade" id="modalAdd" role="dialog" aria-labelledby="modalAddTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddTitle"><i class="mdi mdi-domain mr-1"></i> Tambah Perusahaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">

                {{-- 1. Informasi Perusahaan --}}
                <div class="form-section">
                    <h6 class="form-section-title"><i class="mdi mdi-card-account-details-outline"></i> Informasi Perusahaan</h6>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="namaPerusahaan">Nama Perusahaan <span class="text-danger">*</span></label>
                                <input class="form-control" id="namaPerusahaan" placeholder="NAMA PERUSAHAAN">
                                <small class="text-danger" id="errNamaPerusahaan"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="namaPicPerusahaan">Nama PIC Perusahaan <span class="text-danger">*</span></label>
                                <input class="form-control" id="namaPicPerusahaan" placeholder="NAMA PIC PERUSAHAAN">
                                <small class="text-danger" id="errNamaPicPerusahaan"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="namaPicInternal">Nama PIC HC / HRD <span class="text-danger">*</span></label>
                                <input class="form-control" id="namaPicInternal" placeholder="NAMA PIC HC / HRD">
                                <small class="text-danger" id="errNamaPicInternal"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label for="emailPerusahaan">Email Perusahaan <span class="text-danger">*</span></label>
                                <input class="form-control" id="emailPerusahaan" placeholder="email@perusahaan.com" type="email">
                                <small class="text-danger" id="errEmailPerusahaan"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="telepon">Telepon <span class="text-danger">*</span></label>
                                <input class="form-control" id="telepon" placeholder="628123456789" type="number">
                                <small class="text-danger" id="errTelepon"></small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Alamat --}}
                <div class="form-section">
                    <h6 class="form-section-title"><i class="mdi mdi-map-marker-outline"></i> Alamat Perusahaan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="provinsiSelect">Provinsi <span class="text-danger">*</span></label>
                                <select class="form-control" id="provinsiSelect" style="width: 100%">
                                    <option disabled>Select</option>
                                    @foreach ($provinsis as $provinsi)
                                    <option value="{{ $provinsi->provinsi }}">{{ $provinsi->provinsi }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="errProvinsi"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kabupatenSelect">Kabupaten / Kota <span class="text-danger">*</span></label>
                                <select class="form-control" id="kabupatenSelect" style="width: 100%">
                                    <option disabled>Select</option>
                                </select>
                                <small class="text-danger" id="errKabupaten_kota"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kecamatanSelect">Kecamatan <span class="text-danger">*</span></label>
                                <select class="form-control" id="kecamatanSelect" style="width: 100%">
                                    <option disabled>Select</option>
                                </select>
                                <small class="text-danger" id="errKecamatan"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="desaSelect">Desa <span class="text-danger">*</span></label>
                                <select class="form-control" id="desaSelect" style="width: 100%">
                                    <option disabled>Select</option>
                                </select>
                                <small class="text-danger" id="errDesa"></small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <label for="alamatLengkap">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamatLengkap" rows="3"
                                    placeholder="NAMA JALAN, NOMOR, RT/RW"></textarea>
                                <small class="text-danger" id="errAlamatLengkap"></small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Pembayaran & Konfigurasi --}}
                <div class="form-section">
                    <h6 class="form-section-title"><i class="mdi mdi-cog-outline"></i> Pembayaran &amp; Konfigurasi Pinjaman</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="paymentDate">Payment Date <span class="text-danger">*</span></label>
                                <select class="form-control" id="paymentDate">
                                    @for ($day = 1; $day <= 31; $day++)
                                    <option value="{{ $day }}">TANGGAL {{ $day }}</option>
                                    @endfor
                                </select>
                                <small class="text-danger" id="errPaymentDate"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hMinJatuhTempo">H-Jatuh Tempo</label>
                                <div class="input-group">
                                    <input class="form-control" id="hMinJatuhTempo" type="number" min="0" value="0">
                                    <div class="input-group-append"><span class="input-group-text">HARI</span></div>
                                </div>
                                <small class="text-danger" id="errHMinJatuhTempo"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status">
                                    <option value="active">ACTIVE</option>
                                    <option value="nonactive">NONACTIVE</option>
                                </select>
                                <small class="text-danger" id="errStatus"></small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group mb-lg-0">
                                <label for="activeProfesionalFee">Profesional Fee</label>
                                <select class="form-control" id="activeProfesionalFee">
                                    <option value="active">ACTIVE</option>
                                    <option value="nonactive">NONACTIVE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group mb-lg-0">
                                <label for="activeSetPinjaman">Set Pinjaman</label>
                                <select class="form-control" id="activeSetPinjaman">
                                    <option value="active">ACTIVE</option>
                                    <option value="nonactive">NONACTIVE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group mb-md-0">
                                <label for="activeTanggal">Aktif Tanggal</label>
                                <input class="form-control" id="activeTanggal" type="number" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="form-group mb-0">
                                <label for="maxPlafon">Maksimal Plafon</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp.</span></div>
                                    <input class="form-control" id="maxPlafon" type="number" min="0" step="1" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Lender & PKS --}}
                <div class="form-section mb-0">
                    <h6 class="form-section-title"><i class="mdi mdi-file-document-outline"></i> Lender &amp; PKS</h6>
                    <div class="form-group">
                        <label for="lenderSelect">Lender</label>
                        <select class="form-control" id="lenderSelect">
                            <option value="">Tanpa Lender</option>
                            @foreach ($lenders as $lender)
                            <option value="{{ $lender->id }}">{{ $lender->nama }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="errLender"></small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="mb-0">Daftar PKS</label>
                        <button type="button" class="btn btn-outline-info btn-sm" id="buttonTogglePksAdd">
                            <i class="mdi mdi-plus-circle"></i> Tambah PKS
                        </button>
                    </div>
                    <div id="pksCardsAdd">
                        <div class="card border pks-card mb-2" id="cardPksAdd">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="text-info mb-0">Form PKS</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="featurePksAdd">Feature</label>
                                            <select class="form-control pks-feature" id="featurePksAdd">
                                                <option value="">Pilih Feature</option>
                                                @forelse ($features as $feature)
                                                    <option value="{{ $feature->id }}">{{ $feature->name }}</option>
                                                @empty
                                                    <option value="" disabled>Feature belum tersedia</option>
                                                @endforelse
                                            </select>
                                            <small class="text-danger" id="errFeaturePksAdd"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pksNumberAdd">Nomor PKS</label>
                                            <input class="form-control pks-number" id="pksNumberAdd" placeholder="012/KSPDUMI-FIT/VIII/2025"
                                                type="text" maxlength="255">
                                            <small class="text-danger" id="errPksNumberAdd"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group mb-md-0">
                                            <label for="pksDateAdd">Tanggal PKS</label>
                                            <input class="form-control pks-date" id="pksDateAdd" type="date">
                                            <small class="text-danger" id="errPksDateAdd"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group mb-md-0">
                                            <label for="pksExpiredAtAdd">Tanggal Berakhir PKS</label>
                                            <input class="form-control pks-expired-at" id="pksExpiredAtAdd" type="date">
                                            <small class="text-danger" id="errPksExpiredAtAdd"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="custom-control custom-switch mb-2">
                                            <input class="custom-control-input pks-is-active" id="pksIsActiveAdd" type="checkbox" checked>
                                            <label class="custom-control-label" for="pksIsActiveAdd">Aktif</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="button" id="buttonSubmit">Simpan <div id="loadingText">
                    </div></button>
            </div>
        </div>
    </div>
</div>

{{-- modal edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class=" modal-dialog modal-lg modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Ubah Perusahaan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label>Nama PIC Perusahaan</label>
                            <input class="form-control" id="namaPicPerusahaanEdit" placeholder="PIC Perushaan"></input>
                            <small class="text-danger" id="errNamaPicPerusahaanEdit"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama PIC HC / HRD</label>
                            <input class="form-control" id="namaPicInternalEdit" placeholder="PIC Internal"></input>
                            <small class="text-danger" id="errNamaPicInternalEdit"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Perusahaan</label>
                            <input class="form-control" id="namaPerusahaanEdit" placeholder="Nama Perusahaan"></input>
                            <small class="text-danger" id="errNamaPerusahaanEdit"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email Perusahaan</label>
                            <input class="form-control" id="emailPerusahaanEdit" type="email">
                            <small class="text-danger" id="errEmailPerusahaanEdit"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Telepon</label>
                            <input class="form-control" id="teleponEdit" placeholder="62535252" type="number"></input>
                            <small class="text-danger" id="errTeleponEdit"></small>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Provinsi</label>
                            <select class="form-control" id="provinsiSelectEdit" style="width: 100%">
                                <option disabled>Select</option>
                                @foreach ($provinsis as $provinsi)
                                <option value="{{ $provinsi->provinsi }}">{{ $provinsi->provinsi }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="errProvinsiEdit"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kabupaten / Kota</label>
                            <select class="form-control" id="kabupatenSelectEdit" style="width: 100%">
                                <option disabled>Select</option>
                                {{-- @foreach ($kotas as $kota)
                                <option value="{{ $kota->kabupaten }}">{{ $kota->kabupaten }}</option>
                                @endforeach --}}
                            </select>
                            <small class="text-danger" id="errKabupaten_kotaEdit"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kecamatan</label>
                            <select class="form-control" id="kecamatanSelectEdit" style="width: 100%">
                                <option disabled>Select</option>
                                {{-- @foreach ($kecamatans as $kecamatan)
                                <option value="{{ $kecamatan->kecamatan }}-{{ $kecamatan->kodepos }}">
                                    {{ $kecamatan->kecamatan }} - {{ $kecamatan->kodepos }}</option>
                                @endforeach --}}
                            </select>
                            <small class="text-danger" id="errKecamatanEdit"></small>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Desa</label>
                            <select class="form-control" id="desaSelectEdit" style="width: 100%">
                                <option disabled>Select</option>
                                {{-- @foreach ($kelurahans as $kelurahan)
                                <option value="{{ $kelurahan->kecamatan }}">{{ $kelurahan->kecamatan }}</option>
                                @endforeach --}}
                            </select>
                            <small class="text-danger" id="errDesaEdit"></small>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Alamat Lengkap</label>
                            <textarea class="form-control" id="alamatEdit" style="height: 70px;"
                                placeholder="Alamat Lengkap"></textarea>
                            <small class="text-danger" id="errAlamatEdit"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Date</label>
                            <select class="form-control" id="paymentDateEdit" placeholder="Alamat Lengkap" type="date">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                                <option value="25">25</option>
                                <option value="26">26</option>
                                <option value="27">27</option>
                                <option value="28">28</option>
                                <option value="29">29</option>
                                <option value="30">30</option>
                                <option value="31">31</option>
                            </select>
                            <small class="text-danger" id="errPaymentDateEdit"></small>
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>H-Jatuh Tempo</label>
                            <input class="form-control" id="hMinJatuhTempoEdit" type="number" min="0">
                            <small class="text-danger" id="errHMinJatuhTempoEdit"></small>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Status </label>
                            <select class="form-control" id="statusEdit">
                                <option value="active">ACTIVE</option>
                                <option value="nonactive">NONACTIVE</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Profesional Fee</label>
                            <select class="form-control" id="activeProfesionalFeeEdit">
                                <option value="active">ACTIVE</option>
                                <option value="nonactive">NONACTIVE</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Set Pinjaman</label>
                            <select class="form-control" id="activeSetPinjamanEdit">
                                <option value="active">ACTIVE</option>
                                <option value="nonactive">NONACTIVE</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Aktif Tanggal</label>
                            <input class="form-control" id="activeTanggalEdit" type="number" min="0">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Maksimal Plafon</label>
                            <input class="form-control" id="maxPlafonEdit" type="number" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Lender</label>
                            <select class="form-control" id="lenderSelectEdit">
                                <option value="">Tanpa Lender</option>
                                @foreach ($lenders as $lender)
                                <option value="{{ $lender->id }}">{{ $lender->nama }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="errLenderEdit"></small>
                        </div>
                    </div>
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-outline-info btn-sm mb-3" id="buttonTogglePksEdit">
                            <i class="mdi mdi-plus-circle"></i> Tambah PKS
                        </button>
                        <div id="pksCardsEdit">
                        <div class="card border-info pks-card" id="cardPksEdit">
                            <div class="card-body py-3 text-left">
                                <h6 class="text-info text-left">Form PKS</h6>
                                <div class="row text-left">
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="featurePksEdit">Feature</label>
                                            <select class="form-control pks-feature" id="featurePksEdit">
                                                <option value="">Pilih Feature</option>
                                                @forelse ($features as $feature)
                                                    <option value="{{ $feature->id }}">{{ $feature->name }}</option>
                                                @empty
                                                    <option value="" disabled>Feature belum tersedia</option>
                                                @endforelse
                                            </select>
                                            <small class="text-danger" id="errFeaturePksEdit"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="pksNumberEdit">Nomor PKS</label>
                                            <input class="form-control pks-number" id="pksNumberEdit" placeholder="012/KSPDUMI-FIT/VIII/2025"
                                                type="text" maxlength="255">
                                            <small class="text-danger" id="errPksNumberEdit"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="pksDateEdit">Tanggal PKS</label>
                                            <input class="form-control pks-date" id="pksDateEdit" type="date">
                                            <small class="text-danger" id="errPksDateEdit"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="pksExpiredAtEdit">Tanggal Berakhir PKS</label>
                                            <input class="form-control pks-expired-at" id="pksExpiredAtEdit" type="date">
                                            <small class="text-danger" id="errPksExpiredAtEdit"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="custom-control custom-switch">
                                            <input class="custom-control-input pks-is-active" id="pksIsActiveEdit" type="checkbox">
                                            <label class="custom-control-label" for="pksIsActiveEdit">PKS Aktif</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                </div>



            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="button" id="buttonSubmitEdit">Save <div id="loadingTextEdit">
                    </div></button>
            </div>
        </div>
    </div>
</div>


{{-- modal delete --}}
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class=" modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Delete Perusahaan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="text-center align-middle">
                    <p>Apakah anda yakin menghapus perusahaan ini?, ini akan menghapus semua data karyawan dan
                        pinjaman
                        yang bersangkutan dengan perusahaan ini</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" type="button" id="buttonDelete" onclick="buttonDelete()">Delete
                    <div id="loadingTextDelete">
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    var mulai = "";
    var akhir = "";
    var editID = "";
    var deleteID = "";

    function addPksCard(type) {
        var container = $('#pksCards' + type);
        var index = container.find('.pks-card').length;
        var card = container.find('.pks-card').first().clone();

        card.attr('id', 'cardPks' + type + index);
        card.find('[id]').each(function () {
            $(this).attr('id', $(this).attr('id') + index);
        });
        card.find('select').val('');
        card.find('input[type="text"], input[type="date"]').val('');
        card.find('input[type="checkbox"]').prop('checked', true);
        card.find('.text-danger').text('');
        card.find('h6').after('<button type="button" class="btn btn-sm btn-outline-danger float-right pks-remove mb-2">Hapus PKS</button>');
        container.append(card);
        card.find('.pks-number').trigger('focus');
    }

    $('#buttonTogglePksAdd').on('click', function () {
        addPksCard('Add');
    });

    $('#buttonTogglePksEdit').on('click', function () {
        addPksCard('Edit');
    });

    $(document).on('click', '.pks-remove', function () {
        $(this).closest('.pks-card').remove();
    });

    function collectPksCards(containerId) {
        return $(containerId).find('.pks-card').map(function () {
            var card = $(this);
            return {
                feature_id: card.find('.pks-feature').val(),
                pks_number: card.find('.pks-number').val(),
                pks_date: card.find('.pks-date').val(),
                pks_expired_at: card.find('.pks-expired-at').val(),
                is_active: card.find('.pks-is-active').is(':checked') ? 1 : 0
            };
        }).get().filter(function (item) {
            return item.feature_id || item.pks_number || item.pks_date || item.pks_expired_at;
        });
    }

    // Ubah semua data teks yang diketik/ditempel menjadi huruf kapital, kecuali email.
    $(document).on('input', '#modalAdd input:not([type="email"]):not([type="number"]), #modalAdd textarea, #modalEdit input:not([type="email"]):not([type="number"]), #modalEdit textarea', function () {
        var cursorPosition = this.selectionStart;
        this.value = this.value.toUpperCase();

        if (cursorPosition !== null && typeof this.setSelectionRange === 'function') {
            this.setSelectionRange(cursorPosition, cursorPosition);
        }
    });

    var dataTable = {
        "_token": "{{ csrf_token() }}",
    };

    $('#provinsiSelect').select2();
    $('#kabupatenSelect').select2();
    $('#kecamatanSelect').select2();
    $('#desaSelect').select2();


    $('#provinsiSelectEdit').select2();
    $('#kabupatenSelectEdit').select2();
    $('#kecamatanSelectEdit').select2();
    $('#desaSelectEdit').select2();

    $('#provinsiSelect').on('change', function () {
        $.ajax({
            url: '{!! route('admin.ksp.kabupaten') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'provinsi': this.value,
            },
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                $("#kabupatenSelect").empty();
                result.map(item => {
                    var option = new Option(item.kabupaten, item.kabupaten, true, true);
                    $('#kabupatenSelect').append(option);
                })
            }
        });
    });

    $('#kabupatenSelect').on('change', function () {
        $.ajax({
            url: '{!! route('admin.ksp.kecamatan') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'kabupaten': this.value,
            },
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                $("#kecamatanSelect").empty();
                result.map(item => {
                    var option = new Option(item.kecamatan, item.kecamatan, true, true);
                    $('#kecamatanSelect').append(option);
                })
            }
        });
    });

    $('#kecamatanSelect').on('change', function () {
        $.ajax({
            url: '{!! route('admin.ksp.desa') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'kecamatan': this.value,
            },
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                $("#desaSelect").empty();
                result.map(item => {
                    var option = new Option(item.desa + ' - ' + item.kodepos, item.desa +
                        '-' + item.kodepos,
                        true,
                        true);
                    $('#desaSelect').append(option);
                })
            }
        });
    });
    var table = $('#tableListPerusahaan').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                header: true,
                title: 'List Perusahaan',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                },
            },
            {
                extend: 'copy',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                header: true,
                title: 'List Perusahaan',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                },
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                header: true,
                title: 'List Perusahaan',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                },
            }
        ],

        lengthMenu: [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
        processing: true,
        responsive: true,
        serverSide: true,

        language: {
            processing: '<i class="dripicons-loading">autorenew</i>'
        },

        ajax: {
            type: "POST",
            url: '{!! route('admin.ksp.perusahaan.table') !!}',
            data: function (d) {
                d._token = "{{ csrf_token() }}";
                return d;
            },
            error: function (xhr) {
                console.log('STATUS:', xhr.status);
                console.log('RESPONSE:', xhr.responseText);

                toastr.error('Gagal mengambil data perusahaan. Cek console atau laravel.log');
            }
        },

        columns: [
            {
                data: 'nama',
                name: 'nama',
                defaultContent: '-'
            },
            {
                data: 'pic_perusahaan',
                name: 'pic_perusahaan',
                defaultContent: '-'
            },
            {
                data: 'pic_internal',
                name: 'pic_internal',
                defaultContent: '-'
            },
            {
                data: 'email_perusahaan',
                name: 'email_perusahaan',
                defaultContent: '-'
            },
            {
                data: 'telp',
                name: 'telp',
                defaultContent: '-'
            },
            {
                data: 'payment_date',
                name: 'payment_date',
                defaultContent: '-'
            },
            {
                data: 'h_min_jatuh_tempo',
                name: 'h_min_jatuh_tempo',
                defaultContent: '-'
            },
            {
                data: 'status',
                name: 'status',
                orderable: false,
                searchable: false,
                defaultContent: '-'
            },
            {
                data: 'lender',
                name: 'lender',
                orderable: false,
                searchable: false,
                defaultContent: '-'
            },
            {
                data: 'unique_code',
                name: 'unique_code',
                defaultContent: '-'
            },
            {
                data: 'id_code_perusahaan',
                name: 'id_code_perusahaan',
                defaultContent: '-'
            },
            {
                data: 'provinsi',
                name: 'provinsi',
                orderable: false,
                searchable: false,
                defaultContent: '-'
            },
            {
                data: 'kabupaten',
                name: 'kabupaten',
                orderable: false,
                searchable: false,
                defaultContent: '-'
            },
            {
                data: 'kecamatan',
                name: 'kecamatan',
                orderable: false,
                searchable: false,
                defaultContent: '-'
            },
            {
                data: 'desa',
                name: 'desa',
                orderable: false,
                searchable: false,
                defaultContent: '-'
            },
            {
                data: 'alamat_lengkap',
                name: 'alamat_lengkap',
                defaultContent: '-'
            },
            {
                data: 'konfigurasi_pinjaman',
                name: 'konfigurasi_pinjaman',
                orderable: false,
                searchable: false,
                defaultContent: '-'
            },
            {
                data: 'proses_pengajuan',
                name: 'proses_pengajuan',
                orderable: false,
                searchable: false,
                defaultContent: '-'
            },
            {
                data: 'list_karyawan',
                name: 'list_karyawan',
                orderable: false,
                searchable: false,
                defaultContent: '-'
            },
            {
                data: 'nomor_pks',
                name: 'nomor_pks',
                defaultContent: '-'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                defaultContent: '-'
            }
        ]
    });

    detail = (id) => {
        $.ajax({
            url: '{!! route('admin.ksp.detail') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': id,
            },
            type: 'POST',
            dataType: 'json',
            success: function (detail) {
                $("#modalShow").modal('show');
                $("#namePeminjam").text(detail.pengajuan.nama);
                $("#nik").text(detail.pengajuan.nik);
                $("#tanggalGabung").text(detail.pengajuan.tanggal_terdaftar);
                $("#alasanPengajuan").text(detail.pengajuan.tujuan);
                $("#idPengajuan").text(detail.pengajuan.id_pinjaman);
                $("#tanggalPengajuan").text(detail.pengajuan.tanggal_pengajuan);
                $("#statusPengajuan").text(detail.pengajuan.status);
                $("#plafonPinjaman").text(detail.pengajuan.plafon);
                $("#tenor").text(detail.pengajuan.tenor);
                $("#biayaAdmin").text(detail.pengajuan.percent);
                $("#angsuranPerbulan").text(detail.pengajuan.angsuran_perbulan);
                $("#jumlahDiterima").text(detail.pengajuan.jumlah_diterima);
            }
        });
    }

    approve = (id) => {
        edit = id;
        $("#modalApprove").modal('show');
    }

    $('#saveVerif').on('click', function () {
        $.ajax({
            url: '{!! route('admin.ksp.verifikasi') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': edit,
                'status': $("#statusVerifikasi").val(),
                'note': $("#pesan").val(),
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#saveVerif').attr("disabled", true);
                $('#loadingText').addClass("spinner-border");
            },
            success: function (approve) {
                toastr.success(approve.message)
                table.ajax.reload();
                $("#modalApprove").modal('hide');
                $('#saveVerif').attr("disabled", false);
                $('#loadingText').removeClass("spinner-border");
            }
        });
    })

    $('#buttonSubmit').on('click', function () {
        $.ajax({
            url: '{!! route('admin.ksp.add.perusahaan') !!}',
            data: {
                '_token': "{{ csrf_token() }}",

                'pic_perusahaan': $("#namaPicPerusahaan").val(),
                'pic_internal': $("#namaPicInternal").val(),
                'nama_perusahaan': $("#namaPerusahaan").val(),
                'id_lender': $("#lenderSelect").val(),
                'email_perusahaan': $("#emailPerusahaan").val(),
                'telepon': $("#telepon").val(),
                'provinsi': $("#provinsiSelect").val(),
                'kabupaten': $("#kabupatenSelect").val(),
                'kecamatan': $("#kecamatanSelect").val(),
                'desa': $("#desaSelect").val(),
                'alamat_lengkap': $("#alamatLengkap").val(),
                'payment_date': $("#paymentDate").val(),
                'h_min_jatuh_tempo': $("#hMinJatuhTempo").val(),
                'status': $("#status").val(),
                'active_profesional_fee': $("#activeProfesionalFee").val(),
                'active_set_pinjaman': $("#activeSetPinjaman").val(),
                'active_tanggal': $("#activeTanggal").val(),
                'max_plafon': $("#maxPlafon").val(),
                'nomor_pks': $("#pksNumberAdd").val(),
                'company_features': collectPksCards('#pksCardsAdd'),
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#buttonSubmit').attr("disabled", true);
                $('#loadingText').addClass("spinner-border");
            },
            success: function (add) {
                toastr.success(add.message)
                table.ajax.reload();
                $("#modalAdd").modal('hide');
                $('#buttonSubmit').attr("disabled", false);
                $('#loadingText').removeClass("spinner-border");
            },
            error: function (err) {
                $('#buttonSubmit').attr("disabled", false);
                $('#loadingText').removeClass("spinner-border");
                $('#errNamaPicPerusahaan').text(err.responseJSON.errors.nama_perusahaan);
                $('#errNamaPicInternal').text(err.responseJSON.errors.nama_perusahaan);
                $('#errNamaPerusahaan').text(err.responseJSON.errors.nama_perusahaan);
                $('#errEmailPerusahaan').text(err.responseJSON.errors.email_perusahaan);
                $('#errTelepon').text(err.responseJSON.errors.telepon);
                $('#errAlamatLengkap').text(err.responseJSON.errors.alamat_lengkap);
                $('#errPksNumberAdd').text(err.responseJSON.errors.pks_number);
                $('#errPksDateAdd').text(err.responseJSON.errors.pks_date);
                $('#errPksExpiredAtAdd').text(err.responseJSON.errors.pks_expired_at);
                $('#errFeaturePksAdd').text(err.responseJSON.errors.feature_id);
                $('#errProvinsi').text(err.responseJSON.errors.provinsi);
                $('#errKabupaten').text(err.responseJSON.errors.kabupaten);
                $('#errKecamatan').text(err.responseJSON.errors.kecamatan);
                $('#errDesa').text(err.responseJSON.errors.desa);
                $('#errPaymentDate').text(err.responseJSON.errors.payment_date);
                $('#errHMinJatuhTempo').text(err.responseJSON.errors.h_min_jatuh_tempo);
                $('#errStatus').text(err.responseJSON.errors.status);
            }
        });
    })


    edit = (id) => {
        editID = id;
        $.ajax({
            url: '{!! route('admin.ksp.detail.perusahaan') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': editID,
            },
            type: 'POST',
            dataType: 'json',
            success: function (detail) {
                $("#namaPicPerusahaanEdit").val(detail.data.pic_perusahaan);
                $("#namaPicInternalEdit").val(detail.data.pic_internal);
                $("#namaPerusahaanEdit").val(detail.data.nama);
                $("#emailPerusahaanEdit").val(detail.data.email_perusahaan);
                $("#lenderSelectEdit").val(detail.data.id_lender);
                $("#teleponEdit").val(detail.data.telp);
                $("#alamatEdit").val(detail.data.alamat_lengkap);
                $('#pksCardsEdit .pks-card:not(:first)').remove();
                var companyFeatures = detail.data.company_features || [];
                if (companyFeatures.length === 0) {
                    $("#pksNumberEdit").val(detail.data.nomor_pks || '');
                    $("#featurePksEdit").val('');
                    $("#pksDateEdit").val('');
                    $("#pksExpiredAtEdit").val('');
                    $("#pksIsActiveEdit").prop('checked', true);
                } else {
                    companyFeatures.forEach(function (companyFeature, index) {
                        if (index > 0) {
                            addPksCard('Edit');
                        }
                        var card = $('#pksCardsEdit .pks-card').eq(index);
                        card.find('.pks-number').val(companyFeature.pks_number || '');
                        card.find('.pks-feature').val(companyFeature.feature_id || '');
                        card.find('.pks-date').val(companyFeature.pks_date || '');
                        card.find('.pks-expired-at').val(companyFeature.pks_expired_at || '');
                        card.find('.pks-is-active').prop('checked', Boolean(companyFeature.is_active));
                    });
                }
                $("#statusEdit").val(detail.data.status);
                $("#paymentDateEdit").val(detail.data.payment_date);
                $("#hMinJatuhTempoEdit").val(detail.data.h_min_jatuh_tempo);
                $("#activeProfesionalFeeEdit").val(detail.data.active_profesional_fee);
                $("#activeSetPinjamanEdit").val(detail.data.active_set_pinjaman);
                $("#activeTanggalEdit").val(detail.data.active_tanggal);
                $("#maxPlafonEdit").val(detail.data.max_plafon);
                $("#provinsiSelectEdit").val(detail.data.provinsi).trigger('change');
                $("#kabupatenSelectEdit").empty();

                detail.data.list_kabupaten.map(item => {
                    var option = new Option(item.kabupaten, item.kabupaten, true, true);
                    $('#kabupatenSelectEdit').append(option);
                })
                $("#kabupatenSelectEdit").val(detail.data.kabupaten).trigger('change');


                $("#kecamatanSelectEdit").empty();

                detail.data.list_kecamatan.map(item => {
                    var option = new Option(item.kecamatan, item.kecamatan, true, true);
                    $('#kecamatanSelectEdit').append(option);
                })
                $("#kecamatanSelectEdit").val(detail.data.kecamatan).trigger('change');

                $("#desaSelectEdit").empty();

                detail.data.list_desa.map(item => {
                    var option = new Option(item.desa + ' - ' + item.kodepos, item.desa +
                        '-' + item.kodepos,
                        true,
                        true);
                    $('#desaSelectEdit').append(option);
                })

                $("#desaSelectEdit").val(detail.data.desa).trigger('change');
                $('#modalEdit').modal('show');
            }
        });
    }


    $('#buttonSubmitEdit').on('click', function () {
        $.ajax({
            url: '{!! route('admin.ksp.edit.perusahaan') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': editID,
                'pic_perusahaan': $("#namaPicPerusahaanEdit").val(),
                'pic_internal': $("#namaPicInternalEdit").val(),
                'nama_perusahaan': $("#namaPerusahaanEdit").val(),
                'email_perusahaan': $("#emailPerusahaanEdit").val(),
                'id_lender': $("#lenderSelectEdit").val(),
                'telepon': $("#teleponEdit").val(),
                'provinsi': $("#provinsiSelectEdit").val(),
                'kabupaten': $("#kabupatenSelectEdit").val(),
                'kecamatan': $("#kecamatanSelectEdit").val(),
                'desa': $("#desaSelectEdit").val(),
                'alamat_lengkap': $("#alamatEdit").val(),
                'payment_date': $("#paymentDateEdit").val(),
                'h_min_jatuh_tempo': $("#hMinJatuhTempoEdit").val(),
                'active_profesional_fee': $("#activeProfesionalFeeEdit").val(),
                'active_set_pinjaman': $("#activeSetPinjamanEdit").val(),
                'active_tanggal': $("#activeTanggalEdit").val(),
                'max_plafon': $("#maxPlafonEdit").val(),
                'nomor_pks': $("#pksNumberEdit").val(),
                'company_features': collectPksCards('#pksCardsEdit'),
                'status': $("#statusEdit").val(),
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#buttonSubmitEdit').attr("disabled", true);
                $('#loadingTextEdit').addClass("spinner-border");
            },
            success: function (add) {
                toastr.success(add.message)
                table.ajax.reload();
                $("#modalEdit").modal('hide');
                $('#buttonSubmitEdit').attr("disabled", false);
                $('#loadingTextEdit').removeClass("spinner-border");
            },
            error: function (err) {
                $('#buttonSubmitEdit').attr("disabled", false);
                $('#loadingTextEdit').removeClass("spinner-border");
                $('#errNamaPicPerusahaanEdit').text(err.responseJSON.errors.pic_perusahaan);
                $('#errNamaPicInternalEdit').text(err.responseJSON.errors.pic_internal);
                $('#errNamaPerusahaanEdit').text(err.responseJSON.errors.nama_perusahaan);
                $('#errEmailPerusahaanEdit').text(err.responseJSON.errors.email_perusahaan);
                $('#errTeleponEdit').text(err.responseJSON.errors.telepon);
                $('#errAlamatEdit').text(err.responseJSON.errors.alamat_lengkap);
                $('#errPksNumberEdit').text(err.responseJSON.errors.pks_number);
                $('#errPksDateEdit').text(err.responseJSON.errors.pks_date);
                $('#errPksExpiredAtEdit').text(err.responseJSON.errors.pks_expired_at);
                $('#errFeaturePksEdit').text(err.responseJSON.errors.feature_id);
            }
        });
    })

    deletePerusahaan = (id) => {
        deleteID = id;
        $('#modalDelete').modal('show');
    }

    buttonDelete = () => {
        $.ajax({
            url: '{!! route('admin.ksp.delete.perusahaan') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': deleteID,
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#buttonDelete').attr("disabled", true);
                $('#loadingTextDelete').addClass("spinner-border");
            },
            success: function (deleteSave) {
                toastr.success(deleteSave.message)
                table.ajax.reload();
                $("#modalDelete").modal('hide');
                $('#buttonDelete').attr("disabled", false);
                $('#loadingTextDelete').removeClass("spinner-border");
            }
        });
    }
</script>
@endpush
