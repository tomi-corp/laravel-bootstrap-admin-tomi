@extends('layout.app')

@section('title', 'Admin Perusahaan')

@push('css')
<style>
    #companyModal .modal-content {
        max-height: calc(100vh - 3.5rem);
    }

    #companyModal .company-modal-body {
        max-height: calc(100vh - 12rem);
        overflow-y: auto;
    }

    #companyModal .modal-footer {
        flex-shrink: 0;
        border-top: 1px solid #dee2e6;
        background-color: #fff;
    }

    #companyModal .company-feature-tabs {
        border-bottom: 1px solid #dee2e6;
        margin: .5rem 0 1rem;
    }

    #companyModal .company-feature-tabs .nav-link {
        border: 0;
        border-bottom: 2px solid transparent;
        color: #6c757d;
        font-weight: 600;
        padding: .75rem 1rem;
    }

    #companyModal .company-feature-tabs .nav-link.active {
        border-bottom-color: #536de6;
        color: #536de6;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Perusahaan</li>
                </ol>
            </div>
            <h4 class="page-title">Perusahaan</h4>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Perusahaan Aktif</div>
                <div class="h4 mb-0">{{ $activeCompanies }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Perusahaan Nonaktif</div>
                <div class="h4 mb-0">{{ $inactiveCompanies }}</div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-sm btn-primary float-right mb-3" onclick="openCreateModal()">
                    <i class="fa-solid fa-circle-plus"></i> Tambah Perusahaan
                </button>

                <table id="companiesTable" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>PIC Perusahaan</th>
                            <th>PIC Internal</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Lender</th>
                            <th class="none">Kode Unik</th>
                            <th class="none">ID Perusahaan</th>
                            <th class="none">Payment Date</th>
                            <th class="none">H-Min Jatuh Tempo</th>
                            <th class="none">Provinsi</th>
                            <th class="none">Kota</th>
                            <th class="none">Kecamatan</th>
                            <th class="none">Kalurahan</th>
                            <th class="none">Alamat</th>
                            <th class="none">Bank</th>
                            <th class="none">No Rekening</th>
                            <th class="none">Atas Nama Rekening</th>
                            <th class="none">Direktur Utama</th>
                            <th class="none">Nomor PKS</th>
                            <th class="none">Referral</th>
                            <th class="none">Set Pinjaman</th>
                            <th class="none">Tanggal Aktif</th>
                            <th class="none">Max Plafon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="companyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="companyModalTitle">Tambah Perusahaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="companyForm">
                <div class="modal-body company-modal-body">
                    <input type="hidden" id="companyId">
                    <div class="row">
                        <div class="col-12 form-group">
                            <label>Nama Perusahaan</label>
                            <input class="form-control text-uppercase" name="nama" id="nama">
                            <small class="text-danger error-nama"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Email Perusahaan</label>
                            <input class="form-control" name="email_perusahaan" id="email_perusahaan" type="email">
                            <small class="text-danger error-email_perusahaan"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>PIC Perusahaan</label>
                            <input class="form-control text-uppercase" name="pic_perusahaan" id="pic_perusahaan">
                            <small class="text-danger error-pic_perusahaan"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>PIC Internal</label>
                            <input class="form-control text-uppercase" name="pic_internal" id="pic_internal">
                            <small class="text-danger error-pic_internal"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Telepon</label>
                            <input class="form-control" name="telp" id="telp">
                            <small class="text-danger error-telp"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Provinsi</label>
                            <select class="form-control" name="id_provinsi" id="id_provinsi">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->provinsi }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-id_provinsi"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kota</label>
                            <select class="form-control" name="id_kabupaten" id="id_kabupaten"></select>
                            <small class="text-danger error-id_kabupaten"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kecamatan</label>
                            <select class="form-control" name="id_kecamatan" id="id_kecamatan"></select>
                            <small class="text-danger error-id_kecamatan"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kalurahan</label>
                            <select class="form-control" name="id_desa" id="id_desa"></select>
                            <small class="text-danger error-id_desa"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Alamat Lengkap</label>
                            <textarea class="form-control text-uppercase" name="alamat_lengkap" id="alamat_lengkap" rows="2"></textarea>
                            <small class="text-danger error-alamat_lengkap"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Bank</label>
                            <select class="form-control" name="bank" id="bank">
                                <option value="">Pilih Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->description }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-bank"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>No Rekening</label>
                            <input class="form-control" name="no_rekening" id="no_rekening">
                            <small class="text-danger error-no_rekening"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Atas Nama Rekening</label>
                            <input class="form-control text-uppercase" name="atas_nama_rekening" id="atas_nama_rekening">
                            <small class="text-danger error-atas_nama_rekening"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Direktur Utama</label>
                            <input class="form-control text-uppercase" name="nama_direktur_utama" id="nama_direktur_utama">
                            <small class="text-danger error-nama_direktur_utama"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nomor PKS</label>
                            <input class="form-control text-uppercase" name="nomor_pks" id="nomor_pks">
                            <small class="text-danger error-nomor_pks"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Lender</label>
                            <select class="form-control" name="id_lender" id="id_lender">
                                <option value="">Pilih Lender</option>
                                @foreach ($lenders as $lender)
                                    <option value="{{ $lender->id }}">{{ $lender->nama }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-id_lender"></small>
                        </div>
                        <div class="col-12">
                            <ul class="nav company-feature-tabs" id="companyFeatureTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="tomi-dana-tab" data-toggle="tab" href="#tomi-dana-pane" role="tab" aria-controls="tomi-dana-pane" aria-selected="true">Tomi Dana</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tomi-dana-plus-tab" data-toggle="tab" href="#tomi-dana-plus-pane" role="tab" aria-controls="tomi-dana-plus-pane" aria-selected="false">Tomi Dana Plus</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tomi-gawai-tab" data-toggle="tab" href="#tomi-gawai-pane" role="tab" aria-controls="tomi-gawai-pane" aria-selected="false">Tomi Gawai</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tomi-gaji-tab" data-toggle="tab" href="#tomi-gaji-pane" role="tab" aria-controls="tomi-gaji-pane" aria-selected="false">Tomi Gaji</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tomi-invoice-tab" data-toggle="tab" href="#tomi-invoice-pane" role="tab" aria-controls="tomi-invoice-pane" aria-selected="false">Tomi Invoice</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tomi-dana-pane" role="tabpanel" aria-labelledby="tomi-dana-tab">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Status</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="active">Active</option>
                                                <option value="nonactive">Nonactive</option>
                                            </select>
                                            <small class="text-danger error-status"></small>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Referral</label>
                                            <select class="form-control" name="active_profesional_fee" id="active_profesional_fee">
                                                <option value="active">Active</option>
                                                <option value="nonactive">Nonactive</option>
                                            </select>
                                            <small class="text-danger error-active_profesional_fee"></small>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Set Pinjaman</label>
                                            <select class="form-control" name="active_set_pinjaman" id="active_set_pinjaman">
                                                <option value="active">Active</option>
                                                <option value="nonactive">Nonactive</option>
                                            </select>
                                            <small class="text-danger error-active_set_pinjaman"></small>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Tanggal Aktif</label>
                                            <input class="form-control" name="active_tanggal" id="active_tanggal" type="number" min="0" max="31">
                                            <small class="text-danger error-active_tanggal"></small>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Payment Date</label>
                                            <input class="form-control" name="payment_date" id="payment_date" type="number" min="1" max="31">
                                            <small class="text-danger error-payment_date"></small>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>H-Min Jatuh Tempo</label>
                                            <input class="form-control" name="h_min_jatuh_tempo" id="h_min_jatuh_tempo" type="number" min="0" max="31">
                                            <small class="text-danger error-h_min_jatuh_tempo"></small>
                                        </div>
                                        <div class="col-md-6 form-group tomi-dana-referral-field">
                                            <label>Refferal 1</label>
                                            <select class="form-control feature-referral-select" name="tomi_dana_referral_1_id" id="tomi_dana_referral_1_id">
                                                <option value="">Pilih Refferal 1</option>
                                                @foreach ($referrals as $referral)
                                                    <option value="{{ $referral->id }}">
                                                        {{ $referral->name }}{{ $referral->national_id_number ? ' - '.$referral->national_id_number : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-tomi_dana_referral_1_id"></small>
                                        </div>
                                        <div class="col-md-6 form-group tomi-dana-referral-field">
                                            <label>Refferal 2</label>
                                            <select class="form-control feature-referral-select" name="tomi_dana_referral_2_id" id="tomi_dana_referral_2_id">
                                                <option value="">Pilih Refferal 2</option>
                                                @foreach ($referrals as $referral)
                                                    <option value="{{ $referral->id }}">
                                                        {{ $referral->name }}{{ $referral->national_id_number ? ' - '.$referral->national_id_number : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-tomi_dana_referral_2_id"></small>
                                        </div>
                                        <div class="col-12 form-group d-none" id="tomi_dana_configuration_wrapper">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="mb-0">Konfigurasi Tomi Dana</label>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="addTenorConfigButton" onclick="openTenorConfigModal()">
                                                    <i class="fa-solid fa-plus"></i> Tambah Konfigurasi
                                                </button>
                                            </div>
                                            <div class="alert alert-info mb-2 d-none" id="tomi_dana_configuration_empty">
                                                Simpan data perusahaan terlebih dahulu, lalu konfigurasi Tomi Dana bisa ditambahkan di sini.
                                            </div>
                                            <div class="table-responsive">
                                                <table id="companyTenorTable" class="table table-hover table-bordered nowrap text-center" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>Tenor</th>
                                                            <th>Rate</th>
                                                            <th>Asuransi</th>
                                                            <th>Provisi</th>
                                                            <th>Biaya Admin</th>
                                                            <th>Fee Tomi</th>
                                                            <th>Refferal 1</th>
                                                            <th>Refferal 2</th>
                                                            <th>Lender</th>
                                                            <th>Biaya Layanan</th>
                                                            <th>Minimal Nominal</th>
                                                            <th>Maximal Nominal</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tomi-dana-plus-pane" role="tabpanel" aria-labelledby="tomi-dana-plus-tab">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Status</label>
                                            <select class="form-control" id="tomi_dana_plus_status">
                                                <option value="active">Active</option>
                                                <option value="nonactive">Nonactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Referral</label>
                                            <select class="form-control" id="tomi_dana_plus_referral">
                                                <option value="active">Active</option>
                                                <option value="nonactive">Nonactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Set Pinjaman</label>
                                            <select class="form-control" id="tomi_dana_plus_set_pinjaman">
                                                <option value="active">Active</option>
                                                <option value="nonactive">Nonactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Tanggal Aktif</label>
                                            <input class="form-control" id="tomi_dana_plus_active_tanggal" type="number" min="0" max="31">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Payment Date</label>
                                            <input class="form-control" id="tomi_dana_plus_payment_date" type="number" min="1" max="31">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>H-Min Jatuh Tempo</label>
                                            <input class="form-control" id="tomi_dana_plus_h_min_jatuh_tempo" type="number" min="0" max="31">
                                        </div>
                                        <div class="col-md-6 form-group tomi-dana-plus-referral-field">
                                            <label>Refferal 1</label>
                                            <select class="form-control feature-referral-select" name="tomi_dana_plus_referral_1_id" id="tomi_dana_plus_referral_1_id">
                                                <option value="">Pilih Refferal 1</option>
                                                @foreach ($referrals as $referral)
                                                    <option value="{{ $referral->id }}">
                                                        {{ $referral->name }}{{ $referral->national_id_number ? ' - '.$referral->national_id_number : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-tomi_dana_plus_referral_1_id"></small>
                                        </div>
                                        <div class="col-md-6 form-group tomi-dana-plus-referral-field">
                                            <label>Refferal 2</label>
                                            <select class="form-control feature-referral-select" name="tomi_dana_plus_referral_2_id" id="tomi_dana_plus_referral_2_id">
                                                <option value="">Pilih Refferal 2</option>
                                                @foreach ($referrals as $referral)
                                                    <option value="{{ $referral->id }}">
                                                        {{ $referral->name }}{{ $referral->national_id_number ? ' - '.$referral->national_id_number : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-tomi_dana_plus_referral_2_id"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tomi-gawai-pane" role="tabpanel" aria-labelledby="tomi-gawai-tab">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Referral</label>
                                            <select class="form-control feature-referral-toggle" id="tomi_gawai_referral">
                                                <option value="active">Active</option>
                                                <option value="nonactive">Nonactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group tomi-gawai-referral-field">
                                            <label>Refferal 1</label>
                                            <select class="form-control feature-referral-select" name="tomi_gawai_referral_1_id" id="tomi_gawai_referral_1_id">
                                                <option value="">Pilih Refferal 1</option>
                                                @foreach ($referrals as $referral)
                                                    <option value="{{ $referral->id }}">
                                                        {{ $referral->name }}{{ $referral->national_id_number ? ' - '.$referral->national_id_number : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-tomi_gawai_referral_1_id"></small>
                                        </div>
                                        <div class="col-md-6 form-group tomi-gawai-referral-field">
                                            <label>Refferal 2</label>
                                            <select class="form-control feature-referral-select" name="tomi_gawai_referral_2_id" id="tomi_gawai_referral_2_id">
                                                <option value="">Pilih Refferal 2</option>
                                                @foreach ($referrals as $referral)
                                                    <option value="{{ $referral->id }}">
                                                        {{ $referral->name }}{{ $referral->national_id_number ? ' - '.$referral->national_id_number : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-tomi_gawai_referral_2_id"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tomi-gaji-pane" role="tabpanel" aria-labelledby="tomi-gaji-tab">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Max Plafon</label>
                                            <input class="form-control rupiah-display-input" id="max_plafon_view" inputmode="numeric" placeholder="Rp. 0">
                                            <input type="hidden" name="max_plafon" id="max_plafon">
                                            <small class="text-danger error-max_plafon"></small>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Referral</label>
                                            <select class="form-control feature-referral-toggle" id="tomi_gaji_referral">
                                                <option value="active">Active</option>
                                                <option value="nonactive">Nonactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group tomi-gaji-referral-field">
                                            <label>Refferal 1</label>
                                            <select class="form-control feature-referral-select" name="tomi_gaji_referral_1_id" id="tomi_gaji_referral_1_id">
                                                <option value="">Pilih Refferal 1</option>
                                                @foreach ($referrals as $referral)
                                                    <option value="{{ $referral->id }}">
                                                        {{ $referral->name }}{{ $referral->national_id_number ? ' - '.$referral->national_id_number : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-tomi_gaji_referral_1_id"></small>
                                        </div>
                                        <div class="col-md-6 form-group tomi-gaji-referral-field">
                                            <label>Refferal 2</label>
                                            <select class="form-control feature-referral-select" name="tomi_gaji_referral_2_id" id="tomi_gaji_referral_2_id">
                                                <option value="">Pilih Refferal 2</option>
                                                @foreach ($referrals as $referral)
                                                    <option value="{{ $referral->id }}">
                                                        {{ $referral->name }}{{ $referral->national_id_number ? ' - '.$referral->national_id_number : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-tomi_gaji_referral_2_id"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tomi-invoice-pane" role="tabpanel" aria-labelledby="tomi-invoice-tab">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Max Plafon</label>
                                            <input class="form-control rupiah-display-input" id="tomi_invoice_max_plafon" inputmode="numeric" placeholder="Rp. 0">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Referral</label>
                                            <select class="form-control feature-referral-toggle" id="tomi_invoice_referral">
                                                <option value="active">Active</option>
                                                <option value="nonactive">Nonactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group tomi-invoice-referral-field">
                                            <label>Refferal 1</label>
                                            <select class="form-control feature-referral-select" name="tomi_invoice_referral_1_id" id="tomi_invoice_referral_1_id">
                                                <option value="">Pilih Refferal 1</option>
                                                @foreach ($referrals as $referral)
                                                    <option value="{{ $referral->id }}">
                                                        {{ $referral->name }}{{ $referral->national_id_number ? ' - '.$referral->national_id_number : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-tomi_invoice_referral_1_id"></small>
                                        </div>
                                        <div class="col-md-6 form-group tomi-invoice-referral-field">
                                            <label>Refferal 2</label>
                                            <select class="form-control feature-referral-select" name="tomi_invoice_referral_2_id" id="tomi_invoice_referral_2_id">
                                                <option value="">Pilih Refferal 2</option>
                                                @foreach ($referrals as $referral)
                                                    <option value="{{ $referral->id }}">
                                                        {{ $referral->name }}{{ $referral->national_id_number ? ' - '.$referral->national_id_number : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-tomi_invoice_referral_2_id"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit" id="saveCompanyButton">
                        Simpan <span id="saveSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="tenorConfigModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tenorConfigModalTitle">Tambah Konfigurasi Tomi Dana</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="tenorConfigForm">
                <div class="modal-body">
                    <input type="hidden" id="tenor_config_id">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Tenor</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_tenor" type="number" min="1">
                                <div class="input-group-append"><span class="input-group-text">bulan</span></div>
                            </div>
                            <small class="text-danger cfg-error-tenor"></small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Rate</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_rate" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-rate"></small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Asuransi</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_asuransi" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-asuransi"></small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Provisi</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_provisi" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-provisi"></small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Fee Tomi</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_fee_tomi" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-fee_tomi"></small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Biaya Admin</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_biaya_admin" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-biaya_admin"></small>
                        </div>
                        <div class="col-md-4 form-group tenor-referral-config-field">
                            <label>Refferal 1</label>
                            <div class="input-group">
                                <input class="form-control cfg-admin-fee-input" id="cfg_profesional_fee_1" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-profesional_fee_1"></small>
                        </div>
                        <div class="col-md-4 form-group tenor-referral-config-field">
                            <label>Refferal 2</label>
                            <div class="input-group">
                                <input class="form-control cfg-admin-fee-input" id="cfg_profesional_fee_2" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-profesional_fee_2"></small>
                        </div>
                        <div class="col-md-4 form-group tenor-referral-config-field">
                            <label>Lender</label>
                            <div class="input-group">
                                <input class="form-control cfg-admin-fee-input" id="cfg_ksp" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-ksp"></small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Biaya Layanan</label>
                            <input class="form-control rupiah-display-input" id="cfg_biaya_layanan_view" inputmode="numeric" placeholder="Rp. 0">
                            <input type="hidden" id="cfg_biaya_layanan">
                            <small class="text-danger cfg-error-biaya_layanan"></small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Minimal Nominal</label>
                            <input class="form-control rupiah-display-input" id="cfg_minimal_nominal_view" inputmode="numeric" placeholder="Rp. 0">
                            <input type="hidden" id="cfg_minimal_nominal">
                            <small class="text-danger cfg-error-minimal_nominal"></small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Maximal Nominal</label>
                            <input class="form-control rupiah-display-input" id="cfg_maximal_nominal_view" inputmode="numeric" placeholder="Rp. 0">
                            <input type="hidden" id="cfg_maximal_nominal">
                            <small class="text-danger cfg-error-maximal_nominal"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit" id="saveTenorConfigButton">
                        Simpan <span id="saveTenorConfigSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var isHydratingForm = false;
    var currentCompanyId = null;
    var companyTenorTable = null;
    var table = $('#companiesTable').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [
            { extend: 'excel', className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1', text: '<i class="fa-solid fa-file-excel"></i><span class="pl-1">Excel</span>' },
            { extend: 'copy', className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1', text: '<i class="fa-solid fa-copy"></i><span class="pl-1">Copy</span>' },
            { extend: 'print', className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1', text: '<i class="fa-solid fa-print"></i><span class="pl-1">Print</span>' }
        ],
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            type: 'POST',
            url: '{{ route('admin.companies.table') }}',
            data: function (d) {
                d._token = '{{ csrf_token() }}';
                return d;
            }
        },
        columns: [
            { data: 'nama', name: 'nama', defaultContent: '-' },
            { data: 'pic_perusahaan', name: 'pic_perusahaan', defaultContent: '-' },
            { data: 'pic_internal', name: 'pic_internal', defaultContent: '-' },
            { data: 'email_perusahaan', name: 'email_perusahaan', defaultContent: '-' },
            { data: 'telp', name: 'telp', defaultContent: '-' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false, defaultContent: '-' },
            { data: 'lender_name', name: 'lender_name', orderable: false, defaultContent: '-' },
            { data: 'unique_code', name: 'unique_code', defaultContent: '-' },
            { data: 'id_code_perusahaan', name: 'id_code_perusahaan', defaultContent: '-' },
            { data: 'payment_date', name: 'payment_date', defaultContent: '-' },
            { data: 'h_min_jatuh_tempo', name: 'h_min_jatuh_tempo', defaultContent: '-' },
            { data: 'province_name', name: 'province_name', orderable: false, defaultContent: '-' },
            { data: 'city_name', name: 'city_name', orderable: false, defaultContent: '-' },
            { data: 'district_name', name: 'district_name', orderable: false, defaultContent: '-' },
            { data: 'village_name', name: 'village_name', orderable: false, defaultContent: '-' },
            { data: 'alamat_lengkap', name: 'alamat_lengkap', defaultContent: '-' },
            { data: 'bank', name: 'bank', defaultContent: '-' },
            { data: 'no_rekening', name: 'no_rekening', defaultContent: '-' },
            { data: 'atas_nama_rekening', name: 'atas_nama_rekening', defaultContent: '-' },
            { data: 'nama_direktur_utama', name: 'nama_direktur_utama', defaultContent: '-' },
            { data: 'nomor_pks', name: 'nomor_pks', defaultContent: '-' },
            { data: 'active_profesional_fee', name: 'active_profesional_fee', defaultContent: '-' },
            { data: 'active_set_pinjaman', name: 'active_set_pinjaman', defaultContent: '-' },
            { data: 'active_tanggal', name: 'active_tanggal', defaultContent: '-' },
            { data: 'max_plafon', name: 'max_plafon', defaultContent: '-', render: function (data) {
                return data ? formatRupiah(data) : '-';
            } },
            { data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: '-' }
        ]
    });

    $('#id_lender, #id_provinsi, #id_kabupaten, #id_kecamatan, #id_desa, #bank, .feature-referral-select').select2({ width: '100%' });

    $(document).on('input', '.text-uppercase', function () {
        var cursorPosition = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(cursorPosition, cursorPosition);
    });

    function openCreateModal() {
        clearErrors();
        $('#companyForm')[0].reset();
        $('#companyId').val('');
        currentCompanyId = null;
        $('#id_lender, #id_provinsi, #bank, .feature-referral-select').val('').trigger('change');
        $('#id_kabupaten, #id_kecamatan, #id_desa').empty().trigger('change');
        $('#status, #active_profesional_fee, #active_set_pinjaman').val('active');
        $('#tomi_dana_plus_status, #tomi_dana_plus_referral, #tomi_dana_plus_set_pinjaman, #tomi_gawai_referral, #tomi_gaji_referral, #tomi_invoice_referral').val('active');
        $('#payment_date').val(25);
        $('#h_min_jatuh_tempo').val(9);
        $('#active_tanggal').val(1);
        $('#tomi_dana_plus_active_tanggal').val(1);
        $('#tomi_dana_plus_payment_date').val(25);
        $('#tomi_dana_plus_h_min_jatuh_tempo').val(9);
        setRupiahField('#max_plafon_view', '#max_plafon', '');
        setRupiahField('#tomi_invoice_max_plafon', null, '');
        syncAllFeatureReferralFields();
        toggleTomiDanaConfiguration();
        $('#tomi-dana-tab').tab('show');
        $('#companyModalTitle').text('Tambah Perusahaan');
        $('#companyModal').modal('show');
    }

    function editCompany(id) {
        clearErrors();
        $.get('{{ route('admin.companies.show', ':id') }}'.replace(':id', id), function (response) {
            var company = response.data;
            isHydratingForm = true;
            $('#companyId').val(company.id);
            currentCompanyId = company.id;
            $('#companyModalTitle').text('Ubah Perusahaan');

            fillOptions('#id_kabupaten', response.cities, 'id', 'kabupaten');
            fillOptions('#id_kecamatan', response.districts, 'id', 'kecamatan');
            fillOptions('#id_desa', response.villages, 'id', 'desa', 'kodepos');

            Object.keys(company).forEach(function (key) {
                $('#' + key).val(company[key]).trigger('change');
            });

            setRupiahField('#max_plafon_view', '#max_plafon', company.max_plafon);
            syncFeaturePreviewFields(company);
            syncAllFeatureReferralFields();
            toggleTomiDanaConfiguration();
            isHydratingForm = false;
            $('#tomi-dana-tab').tab('show');
            $('#companyModal').modal('show');
        });
    }

    function syncFeaturePreviewFields(company) {
        $('#tomi_dana_plus_status').val(company.status || 'active');
        $('#tomi_dana_plus_referral').val(company.active_profesional_fee || 'active');
        $('#tomi_dana_plus_set_pinjaman').val(company.active_set_pinjaman || 'active');
        $('#tomi_dana_plus_active_tanggal').val(company.active_tanggal || 1);
        $('#tomi_dana_plus_payment_date').val(company.payment_date || 25);
        $('#tomi_dana_plus_h_min_jatuh_tempo').val(company.h_min_jatuh_tempo || 9);
        setRupiahField('#tomi_invoice_max_plafon', null, company.max_plafon);
    }

    function toggleTomiDanaReferralFields() {
        var isActive = $('#active_profesional_fee').val() === 'active';
        $('.tomi-dana-referral-field').toggleClass('d-none', !isActive);

        if (!isActive) {
            $('#tomi_dana_referral_1_id, #tomi_dana_referral_2_id').val('').trigger('change');
        }

        syncTenorReferralConfigFields();
    }

    function toggleFeatureReferralFields(toggleSelector, fieldSelector, selectSelector) {
        var isActive = $(toggleSelector).val() === 'active';
        $(fieldSelector).toggleClass('d-none', !isActive);

        if (!isActive) {
            $(selectSelector).val('').trigger('change');
        }
    }

    function syncAllFeatureReferralFields() {
        toggleTomiDanaReferralFields();
        toggleFeatureReferralFields('#tomi_dana_plus_referral', '.tomi-dana-plus-referral-field', '#tomi_dana_plus_referral_1_id, #tomi_dana_plus_referral_2_id');
        toggleFeatureReferralFields('#tomi_gawai_referral', '.tomi-gawai-referral-field', '#tomi_gawai_referral_1_id, #tomi_gawai_referral_2_id');
        toggleFeatureReferralFields('#tomi_gaji_referral', '.tomi-gaji-referral-field', '#tomi_gaji_referral_1_id, #tomi_gaji_referral_2_id');
        toggleFeatureReferralFields('#tomi_invoice_referral', '.tomi-invoice-referral-field', '#tomi_invoice_referral_1_id, #tomi_invoice_referral_2_id');
    }

    function toggleTomiDanaConfiguration() {
        $('#tomi_dana_configuration_wrapper').removeClass('d-none');

        if (currentCompanyId) {
            $('#addTenorConfigButton').prop('disabled', false);
            $('#tomi_dana_configuration_empty').addClass('d-none');
            $('#companyTenorTable_wrapper, #companyTenorTable').removeClass('d-none');
            initCompanyTenorTable();
            return;
        }

        $('#addTenorConfigButton').prop('disabled', true);
        $('#tomi_dana_configuration_empty').removeClass('d-none');
        $('#companyTenorTable_wrapper, #companyTenorTable').addClass('d-none');
        if (companyTenorTable) {
            companyTenorTable.clear().draw();
        }
    }

    $('#active_profesional_fee').on('change', toggleTomiDanaReferralFields);
    $('#tomi_dana_plus_referral, #tomi_gawai_referral, #tomi_gaji_referral, #tomi_invoice_referral').on('change', syncAllFeatureReferralFields);

    function initCompanyTenorTable() {
        if (companyTenorTable) {
            companyTenorTable.ajax.reload();
            return;
        }

        companyTenorTable = $('#companyTenorTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: false,
            pageLength: 5,
            scrollX: true,
            ajax: {
                type: 'POST',
                url: '{{ route('admin.ksp.konfigurasi.table') }}',
                data: function (d) {
                    d._token = '{{ csrf_token() }}';
                    d.id = currentCompanyId;
                    return d;
                }
            },
            columns: [
                { data: 'tenor', defaultContent: '-' },
                { data: 'rate', defaultContent: '-' },
                { data: 'asuransi', defaultContent: '-' },
                { data: 'provisi', defaultContent: '-' },
                { data: 'biaya_admin', defaultContent: '-' },
                { data: 'fee_tomi', defaultContent: '-' },
                { data: 'profesional_fee_1', defaultContent: '-' },
                { data: 'profesional_fee_2', defaultContent: '-' },
                { data: 'ksp', defaultContent: '-' },
                { data: 'biaya_layanan', defaultContent: '-' },
                { data: 'minimal_nominal', defaultContent: '-' },
                { data: 'maximal_nominal', defaultContent: '-' },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        var id = row.id || data;
                        return '<div class="btn-group">'
                            + '<button type="button" class="btn btn-sm btn-primary" onclick="editTenorConfig(' + id + ')"><i class="fa-solid fa-pen"></i></button>'
                            + '<button type="button" class="btn btn-sm btn-danger" onclick="deleteTenorConfig(' + id + ')"><i class="fa-solid fa-trash-can"></i></button>'
                            + '</div>';
                    }
                }
            ],
            language: {
                emptyTable: 'Belum ada konfigurasi Tomi Dana untuk perusahaan ini.',
                zeroRecords: 'Belum ada konfigurasi Tomi Dana yang sesuai.',
                processing: 'Memuat konfigurasi Tomi Dana...'
            }
        });
    }

    function clearTenorConfigErrors() {
        $('[class*="cfg-error-"]').text('');
    }

    function resetTenorConfigForm() {
        $('#tenorConfigForm')[0].reset();
        $('#tenor_config_id').val('');
        setRupiahField('#cfg_biaya_layanan_view', '#cfg_biaya_layanan', '');
        setRupiahField('#cfg_minimal_nominal_view', '#cfg_minimal_nominal', '');
        setRupiahField('#cfg_maximal_nominal_view', '#cfg_maximal_nominal', '');
        clearTenorConfigErrors();
        syncTenorReferralConfigFields();
    }

    function openTenorConfigModal() {
        if (!currentCompanyId) {
            toastr.warning('Simpan perusahaan terlebih dahulu sebelum menambah konfigurasi.');
            return;
        }

        resetTenorConfigForm();
        $('#tenorConfigModalTitle').text('Tambah Konfigurasi Tomi Dana');
        $('#tenorConfigModal').modal('show');
    }

    function editTenorConfig(id) {
        resetTenorConfigForm();
        $('#tenorConfigModalTitle').text('Edit Konfigurasi Tomi Dana');

        $.post('{{ route('admin.ksp.konfigurasi.detail') }}', {
            _token: '{{ csrf_token() }}',
            id: id,
        }).done(function (response) {
            var data = response.data;
            $('#tenor_config_id').val(id);
            $('#cfg_tenor').val(data.tenor);
            $('#cfg_rate').val(data.rate);
            $('#cfg_asuransi').val(data.asuransi);
            $('#cfg_provisi').val(data.provisi);
            $('#cfg_biaya_admin').val(data.biaya_admin);
            $('#cfg_fee_tomi').val(data.fee_tomi);
            $('#cfg_profesional_fee_1').val(data.profesional_fee_1);
            $('#cfg_profesional_fee_2').val(data.profesional_fee_2);
            $('#cfg_ksp').val(data.ksp);
            setRupiahField('#cfg_biaya_layanan_view', '#cfg_biaya_layanan', data.biaya_layanan);
            setRupiahField('#cfg_minimal_nominal_view', '#cfg_minimal_nominal', data.minimal_nominal);
            setRupiahField('#cfg_maximal_nominal_view', '#cfg_maximal_nominal', data.maximal_nominal);
            syncTenorReferralConfigFields();
            $('#tenorConfigModal').modal('show');
        }).fail(function () {
            toastr.error('Gagal mengambil detail konfigurasi.');
        });
    }

    function deleteTenorConfig(id) {
        if (!confirm('Hapus konfigurasi tenor ini?')) {
            return;
        }

        $.post('{{ route('admin.ksp.konfigurasi.delete') }}', {
            _token: '{{ csrf_token() }}',
            id: id,
        }).done(function (response) {
            toastr.success(response.message);
            companyTenorTable.ajax.reload();
        }).fail(function () {
            toastr.error('Gagal menghapus konfigurasi tenor.');
        });
    }

    function syncTenorReferralConfigFields() {
        var isActive = $('#active_profesional_fee').val() === 'active';
        $('.tenor-referral-config-field').toggleClass('d-none', !isActive);
        $('#cfg_biaya_admin').prop('readonly', isActive);

        if (!isActive) {
            $('#cfg_profesional_fee_1, #cfg_profesional_fee_2, #cfg_ksp').val(0);
        }

        calculateTenorAdminFee();
    }

    function calculateTenorAdminFee() {
        if ($('#active_profesional_fee').val() !== 'active') {
            return;
        }

        var fee1 = parseFloat($('#cfg_profesional_fee_1').val()) || 0;
        var fee2 = parseFloat($('#cfg_profesional_fee_2').val()) || 0;
        var ksp = parseFloat($('#cfg_ksp').val()) || 0;
        $('#cfg_biaya_admin').val((fee1 + fee2 + ksp).toFixed(2));
    }

    $(document).on('input', '.cfg-admin-fee-input', calculateTenorAdminFee);

    $('#tenorConfigForm').on('submit', function (event) {
        event.preventDefault();
        clearTenorConfigErrors();

        var id = $('#tenor_config_id').val();
        var url = id ? '{{ route('admin.ksp.konfigurasi.edit') }}' : '{{ route('admin.ksp.konfigurasi.add') }}';

        $('#saveTenorConfigButton').attr('disabled', true);
        $('#saveTenorConfigSpinner').addClass('spinner-border spinner-border-sm');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                id: id || currentCompanyId,
                tenor: $('#cfg_tenor').val(),
                rate: $('#cfg_rate').val(),
                asuransi: $('#cfg_asuransi').val(),
                provisi: $('#cfg_provisi').val(),
                biaya_admin: $('#cfg_biaya_admin').val(),
                fee_tomi: $('#cfg_fee_tomi').val(),
                profesional_fee_1: $('#cfg_profesional_fee_1').val(),
                profesional_fee_2: $('#cfg_profesional_fee_2').val(),
                ksp: $('#cfg_ksp').val(),
                biaya_layanan: $('#cfg_biaya_layanan').val(),
                minimal_nominal: $('#cfg_minimal_nominal').val(),
                maximal_nominal: $('#cfg_maximal_nominal').val(),
            },
            success: function (response) {
                toastr.success(response.message);
                $('#tenorConfigModal').modal('hide');
                companyTenorTable.ajax.reload();
            },
            error: function (xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : {};
                Object.keys(errors).forEach(function (key) {
                    $('.cfg-error-' + key).text(errors[key][0]);
                });

                if (!Object.keys(errors).length) {
                    toastr.error('Gagal menyimpan konfigurasi tenor.');
                }
            },
            complete: function () {
                $('#saveTenorConfigButton').attr('disabled', false);
                $('#saveTenorConfigSpinner').removeClass('spinner-border spinner-border-sm');
            }
        });
    });

    function deleteCompany(id) {
        if (!confirm('Hapus perusahaan ini?')) {
            return;
        }

        $.ajax({
            url: '{{ route('admin.companies.destroy', ':id') }}'.replace(':id', id),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                toastr.success(response.message);
                table.ajax.reload();
            },
            error: function () {
                toastr.error('Gagal menghapus data perusahaan');
            }
        });
    }

    $('#id_provinsi').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('admin.companies.cities') }}', { province_id: this.value }, '#id_kabupaten', 'kabupaten');
        $('#id_kecamatan, #id_desa').empty().trigger('change');
    });

    $('#id_kabupaten').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('admin.companies.districts') }}', { city_id: this.value }, '#id_kecamatan', 'kecamatan');
        $('#id_desa').empty().trigger('change');
    });

    $('#id_kecamatan').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('admin.companies.villages') }}', { district_id: this.value }, '#id_desa', 'desa', 'kodepos');
    });

    $('#companyForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#companyId').val();
        var method = id ? 'PUT' : 'POST';
        var url = id ? '{{ route('admin.companies.update', ':id') }}'.replace(':id', id) : '{{ route('admin.companies.store') }}';

        $('#saveCompanyButton').attr('disabled', true);
        $('#saveSpinner').addClass('spinner-border spinner-border-sm');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function (response) {
                toastr.success(response.message);
                if (!id && response.data && response.data.id) {
                    $('#companyId').val(response.data.id);
                    currentCompanyId = response.data.id;
                    $('#companyModalTitle').text('Ubah Perusahaan');
                    toggleTomiDanaConfiguration();
                }
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                        $('.error-' + key).text(xhr.responseJSON.errors[key][0]);
                    });
                    return;
                }

                toastr.error('Gagal menyimpan data perusahaan');
            },
            complete: function () {
                $('#saveCompanyButton').attr('disabled', false);
                $('#saveSpinner').removeClass('spinner-border spinner-border-sm');
            }
        });
    });

    function loadOptions(url, data, selector, labelKey, secondaryLabelKey) {
        if (!Object.values(data)[0]) {
            $(selector).empty().trigger('change');
            return;
        }

        $.get(url, data, function (items) {
            fillOptions(selector, items, 'id', labelKey, secondaryLabelKey);
        });
    }

    function fillOptions(selector, items, valueKey, labelKey, secondaryLabelKey) {
        $(selector).empty().append(new Option('Pilih', '', false, false));
        items.forEach(function (item) {
            var label = item[labelKey];
            if (secondaryLabelKey && item[secondaryLabelKey]) {
                label += ' - ' + item[secondaryLabelKey];
            }
            $(selector).append(new Option(label, item[valueKey], false, false));
        });
        $(selector).trigger('change');
    }

    function clearErrors() {
        $('[class*="error-"]').text('');
    }

    function onlyDigits(value) {
        return String(value || '').replace(/\D/g, '');
    }

    function formatThousands(value) {
        var digits = onlyDigits(value);
        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function formatRupiah(value) {
        var formatted = formatThousands(value);
        return formatted ? 'Rp. ' + formatted : '';
    }

    function setRupiahField(displaySelector, rawSelector, value) {
        var raw = onlyDigits(value);
        $(displaySelector).val(formatRupiah(raw));
        if (rawSelector) {
            $(rawSelector).val(raw);
        }
    }

    $(document).on('input', '.rupiah-display-input', function () {
        var raw = onlyDigits(this.value);
        this.value = formatRupiah(raw);

        if (this.id === 'max_plafon_view') {
            $('#max_plafon').val(raw);
        } else if (this.id === 'cfg_biaya_layanan_view') {
            $('#cfg_biaya_layanan').val(raw);
        } else if (this.id === 'cfg_minimal_nominal_view') {
            $('#cfg_minimal_nominal').val(raw);
        } else if (this.id === 'cfg_maximal_nominal_view') {
            $('#cfg_maximal_nominal').val(raw);
        }
    });
</script>
@endpush
