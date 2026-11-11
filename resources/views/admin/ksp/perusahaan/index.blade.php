@extends('layout.app')
@php($isTomiDana = request()->routeIs('admin.tomi-dana.perusahaan'))
@section('title', $isTomiDana ? 'Perusahaan TomiDana' : 'Koperasi Simpan Pinjam')
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
<div class="modal fade" id="modalAdd" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class=" modal-dialog modal-lg modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Tambah Perusahaan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            </div>
            <div class="modal-body">

                <div class="row">


                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama PIC Perusahaan</label>
                            <input class="form-control" id="namaPicPerusahaan" placeholder="PIC Perushaan"></input>
                            <small class="text-danger" id="errNamaPicPerusahaan"></small>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama PIC HC / HRD</label>
                            <input class="form-control" id="namaPicInternal" placeholder="PIC Internal"></input>
                            <small class="text-danger" id="errNamaPicInternal"></small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nama Perusahaan</label>
                            <input class="form-control" id="namaPerusahaan" placeholder="Nama Perusahaan"></input>
                            <small class="text-danger" id="errNamaPerusahaan"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email Perusahaan</label>
                            <input class="form-control" id="emailPerusahaan" placeholder="Email Perusahaan"
                                type="email"></input>
                            <small class="text-danger" id="errEmailPerusahaan"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Telepon</label>
                            <input class="form-control" id="telepon" placeholder="62535252" type="number"></input>
                            <small class="text-danger" id="errTelepon"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Provinsi</label>
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
                            <label>Kabupaten / Kota</label>
                            <select class="form-control" id="kabupatenSelect" style="width: 100%">
                                <option disabled>Select</option>
                            </select>
                            <small class="text-danger" id="errKabupaten_kota"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kecamatan</label>
                            <select class="form-control" id="kecamatanSelect" style="width: 100%">
                                <option disabled>Select</option>
                            </select>
                            <small class="text-danger" id="errKecamatan"></small>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Desa</label>
                            <select class="form-control" id="desaSelect" style="width: 100%">
                                <option disabled>Select</option>
                            </select>
                            <small class="text-danger" id="errDesa"></small>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Alamat Lengkap</label>
                            <textarea class="form-control" id="alamatLengkap" style="height: 70px;"
                                placeholder="Alamat Lengkap"></textarea>
                            <small class="text-danger" id="errAlamatLengkap"></small>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Date</label>
                            <select class="form-control" id="paymentDate" placeholder="Alamat Lengkap" type="date">
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
                            <small class="text-danger" id="errPaymentDate"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>H-Jatuh Tempo</label>
                            <input class="form-control" id="hMinJatuhTempo" type="number" min="0" value="0">
                            <small class="text-danger" id="errHMinJatuhTempo"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="status">
                                <option value="active">ACTIVE</option>
                                <option value="nonactive">NONACTIVE</option>
                            </select>
                            <small class="text-danger" id="errStatus"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Profesional Fee</label>
                            <select class="form-control" id="activeProfesionalFee">
                                <option value="active">ACTIVE</option>
                                <option value="nonactive">NONACTIVE</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Set Pinjaman</label>
                            <select class="form-control" id="activeSetPinjaman">
                                <option value="active">ACTIVE</option>
                                <option value="nonactive">NONACTIVE</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Aktif Tanggal</label>
                            <input class="form-control" id="activeTanggal" type="number" min="0" value="0">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Maksimal Plafon</label>
                            <input class="form-control" id="maxPlafon" type="number" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button type="button" class="btn btn-outline-info btn-sm mb-3" id="buttonTogglePksAdd">
                            <i class="mdi mdi-plus-circle"></i> Tambah PKS
                        </button>
                        <div class="card border-info d-none" id="cardPksAdd">
                            <div class="card-body py-3">
                                <h6 class="text-info">Form PKS</h6>
                                <div class="form-group mb-0">
                                    <label for="nomor_pks">Nomor PKS</label>
                                    <input class="form-control" id="nomor_pks" placeholder="012/KSPDUMI-FIT/VIII/2025"
                                        type="text" maxlength="255">
                                    <small class="text-danger" id="errNomor_pks"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Lender</label>
                            <select class="form-control" id="lenderSelect">
                                <option value="">Tanpa Lender</option>
                                @foreach ($lenders as $lender)
                                <option value="{{ $lender->id }}">{{ $lender->nama }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="errLender"></small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-0">
                            <label>Feature</label>
                            <ul class="nav nav-tabs nav-bordered" role="tablist">
                                @forelse ($features as $feature)
                                    <li class="nav-item">
                                        <a href="#feature-add-{{ $feature->id }}" data-toggle="tab" role="tab"
                                            class="nav-link {{ $loop->first ? 'active' : '' }}">
                                            {{ $feature->name }}
                                        </a>
                                    </li>
                                @empty
                                    <li class="nav-item">
                                        <span class="nav-link disabled">Feature belum tersedia</span>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>


                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="button" id="buttonSubmit">Save <div id="loadingText">
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
                        <button type="button" class="btn btn-outline-info btn-sm mb-3" id="buttonTogglePksEdit">
                            <i class="mdi mdi-plus-circle"></i> Tambah PKS
                        </button>
                        <div class="card border-info d-none" id="cardPksEdit">
                            <div class="card-body py-3">
                                <h6 class="text-info">Form PKS</h6>
                                <div class="form-group mb-0">
                                    <label for="nomor_pksEdit">Nomor PKS</label>
                                    <input class="form-control" id="nomor_pksEdit" placeholder="012/KSPDUMI-FIT/VIII/2025"
                                        type="text" maxlength="255">
                                    <small class="text-danger" id="errNomor_pksEdit"></small>
                                </div>
                            </div>
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
                    <div class="col-md-12">
                        <div class="form-group mb-0">
                            <label>Feature</label>
                            <ul class="nav nav-tabs nav-bordered" role="tablist">
                                @forelse ($features as $feature)
                                    <li class="nav-item">
                                        <a href="#feature-edit-{{ $feature->id }}" data-toggle="tab" role="tab"
                                            class="nav-link {{ $loop->first ? 'active' : '' }}">
                                            {{ $feature->name }}
                                        </a>
                                    </li>
                                @empty
                                    <li class="nav-item">
                                        <span class="nav-link disabled">Feature belum tersedia</span>
                                    </li>
                                @endforelse
                            </ul>
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

    $('#buttonTogglePksAdd').on('click', function () {
        $('#cardPksAdd').toggleClass('d-none');
        $('#nomor_pks').trigger('focus');
    });

    $('#buttonTogglePksEdit').on('click', function () {
        $('#cardPksEdit').toggleClass('d-none');
        $('#nomor_pksEdit').trigger('focus');
    });

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
                'nomor_pks': $("#nomor_pks").val(),
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
                $('#errNomor_pks').text(err.responseJSON.errors.nomor_pks);
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
                $("#nomor_pksEdit").val(detail.data.nomor_pks);
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
                'nomor_pks': $("#nomor_pksEdit").val(),
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
                $('#errNomor_pksEdit').text(err.responseJSON.errors.nomor_pks);
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
