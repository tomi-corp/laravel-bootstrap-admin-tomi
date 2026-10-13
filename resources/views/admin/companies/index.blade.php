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

    #tenorConfigModal {
        z-index: 1065;
    }

    #referralFormModal {
        z-index: 1065;
    }

    .modal-backdrop.tenor-config-backdrop {
        z-index: 1060;
    }

    .modal-backdrop.referral-form-backdrop {
        z-index: 1060;
    }

    #tenorConfigModal .modal-content {
        max-height: calc(100vh - 3.5rem);
    }

    #referralFormModal .modal-content {
        max-height: calc(100vh - 3.5rem);
    }

    #tenorConfigModal .tenor-config-modal-body {
        max-height: calc(100vh - 12rem);
        overflow-y: auto;
    }

    #referralFormModal .modal-body {
        max-height: calc(100vh - 12rem);
        overflow-y: auto;
    }

    #tenorConfigModal .modal-footer {
        flex-shrink: 0;
        border-top: 1px solid #dee2e6;
        background-color: #fff;
    }

    #referralFormModal .modal-footer {
        flex-shrink: 0;
        border-top: 1px solid #dee2e6;
        background-color: #fff;
    }

    .referral-form-actions {
        align-items: center;
        display: flex;
        gap: .5rem;
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
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Perusahaan
                    </button>
                </div>

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
                        <div class="col-12 form-group">
                            <label>Email Perusahaan</label>
                            <input class="form-control" name="email_perusahaan" id="email_perusahaan" type="email">
                            <small class="text-danger error-email_perusahaan"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>PIC Perusahaan</label>
                            <input class="form-control text-uppercase" name="pic_perusahaan" id="pic_perusahaan">
                            <small class="text-danger error-pic_perusahaan"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>PIC Internal</label>
                            <input class="form-control text-uppercase" name="pic_internal" id="pic_internal">
                            <small class="text-danger error-pic_internal"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Telepon</label>
                            <input class="form-control" name="telp" id="telp">
                            <small class="text-danger error-telp"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Provinsi</label>
                            <select class="form-control" name="id_provinsi" id="id_provinsi">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->provinsi }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-id_provinsi"></small>
                        </div>
                        <div class="col-12 form-group">
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
                                        <div class="col-12 form-group feature-referral-wrapper" data-prefix="tomi_dana">
                                            <div class="referral-form-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary add-referral-button" data-prefix="tomi_dana">
                                                    <i class="fa-solid fa-plus"></i> Tambah Refferal
                                                </button>
                                                <small class="text-muted">Maksimal 2 refferal</small>
                                            </div>
                                            <div class="table-responsive mt-2">
                                                <table class="table table-sm table-bordered text-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Refferal</th>
                                                            <th>Nama</th>
                                                            <th>NIK</th>
                                                            <th>Telepon</th>
                                                            <th>Email</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tomi_dana_referral_table_body">
                                                        <tr class="referral-empty-row"><td colspan="6">Belum ada refferal</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
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
                                        <div class="feature-referral-slot d-none" data-prefix="tomi_dana" data-index="1">
                                            <div class="d-none referral-slot-storage">
                                                @include('admin.companies.partials.referral-form', ['prefix' => 'tomi_dana', 'index' => 1])
                                            </div>
                                        </div>
                                        <div class="feature-referral-slot d-none" data-prefix="tomi_dana" data-index="2">
                                            <div class="d-none referral-slot-storage">
                                                @include('admin.companies.partials.referral-form', ['prefix' => 'tomi_dana', 'index' => 2])
                                            </div>
                                        </div>
                                        <div class="col-12 form-group d-none" id="tomi_dana_configuration_wrapper">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="mb-0">Konfigurasi Tomi Dana</label>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="addTenorConfigButton" onclick="openTenorConfigModal()">
                                                    <i class="fa-solid fa-plus"></i> Tambah Konfigurasi
                                                </button>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="companyTenorTable" class="table table-hover table-bordered nowrap text-center" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>Tenor</th>
                                                            <th>Biaya Bunga</th>
                                                            <th>Biaya Asuransi</th>
                                                            <th>Biaya Provisi</th>
                                                            <th>Biaya Layanan</th>
                                                            <th>Biaya Admin</th>
                                                            <th>Lender</th>
                                                            <th>Tomi</th>
                                                            <th>Refferal 1</th>
                                                            <th>Refferal 2</th>
                                                            <th>Maximal Nominal</th>
                                                            <th>Minimal Nominal</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="pendingTenorConfigBody"></tbody>
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
                                            <div class="referral-form-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary add-referral-button" data-prefix="tomi_dana_plus">
                                                    <i class="fa-solid fa-plus"></i> Tambah Refferal
                                                </button>
                                                <small class="text-muted">Maksimal 2 refferal</small>
                                            </div>
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
                                        <div class="col-12 form-group feature-referral-wrapper" data-prefix="tomi_dana_plus">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered text-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Refferal</th>
                                                            <th>Nama</th>
                                                            <th>NIK</th>
                                                            <th>Telepon</th>
                                                            <th>Email</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tomi_dana_plus_referral_table_body">
                                                        <tr class="referral-empty-row"><td colspan="6">Belum ada refferal</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="feature-referral-slot d-none" data-prefix="tomi_dana_plus" data-index="1">
                                            <div class="d-none referral-slot-storage">
                                                @include('admin.companies.partials.referral-form', ['prefix' => 'tomi_dana_plus', 'index' => 1])
                                            </div>
                                        </div>
                                        <div class="feature-referral-slot d-none" data-prefix="tomi_dana_plus" data-index="2">
                                            <div class="d-none referral-slot-storage">
                                                @include('admin.companies.partials.referral-form', ['prefix' => 'tomi_dana_plus', 'index' => 2])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tomi-gawai-pane" role="tabpanel" aria-labelledby="tomi-gawai-tab">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Referral</label>
                                            <div class="referral-form-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary add-referral-button" data-prefix="tomi_gawai">
                                                    <i class="fa-solid fa-plus"></i> Tambah Refferal
                                                </button>
                                                <small class="text-muted">Maksimal 2 refferal</small>
                                            </div>
                                        </div>
                                        <div class="col-12 form-group feature-referral-wrapper" data-prefix="tomi_gawai">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered text-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Refferal</th>
                                                            <th>Nama</th>
                                                            <th>NIK</th>
                                                            <th>Telepon</th>
                                                            <th>Email</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tomi_gawai_referral_table_body">
                                                        <tr class="referral-empty-row"><td colspan="6">Belum ada refferal</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="feature-referral-slot d-none" data-prefix="tomi_gawai" data-index="1">
                                            <div class="d-none referral-slot-storage">
                                                @include('admin.companies.partials.referral-form', ['prefix' => 'tomi_gawai', 'index' => 1])
                                            </div>
                                        </div>
                                        <div class="feature-referral-slot d-none" data-prefix="tomi_gawai" data-index="2">
                                            <div class="d-none referral-slot-storage">
                                                @include('admin.companies.partials.referral-form', ['prefix' => 'tomi_gawai', 'index' => 2])
                                            </div>
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
                                            <div class="referral-form-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary add-referral-button" data-prefix="tomi_gaji">
                                                    <i class="fa-solid fa-plus"></i> Tambah Refferal
                                                </button>
                                                <small class="text-muted">Maksimal 2 refferal</small>
                                            </div>
                                        </div>
                                        <div class="col-12 form-group feature-referral-wrapper" data-prefix="tomi_gaji">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered text-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Refferal</th>
                                                            <th>Nama</th>
                                                            <th>NIK</th>
                                                            <th>Telepon</th>
                                                            <th>Email</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tomi_gaji_referral_table_body">
                                                        <tr class="referral-empty-row"><td colspan="6">Belum ada refferal</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="feature-referral-slot d-none" data-prefix="tomi_gaji" data-index="1">
                                            <div class="d-none referral-slot-storage">
                                                @include('admin.companies.partials.referral-form', ['prefix' => 'tomi_gaji', 'index' => 1])
                                            </div>
                                        </div>
                                        <div class="feature-referral-slot d-none" data-prefix="tomi_gaji" data-index="2">
                                            <div class="d-none referral-slot-storage">
                                                @include('admin.companies.partials.referral-form', ['prefix' => 'tomi_gaji', 'index' => 2])
                                            </div>
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
                                            <div class="referral-form-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary add-referral-button" data-prefix="tomi_invoice">
                                                    <i class="fa-solid fa-plus"></i> Tambah Refferal
                                                </button>
                                                <small class="text-muted">Maksimal 2 refferal</small>
                                            </div>
                                        </div>
                                        <div class="col-12 form-group feature-referral-wrapper" data-prefix="tomi_invoice">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered text-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Refferal</th>
                                                            <th>Nama</th>
                                                            <th>NIK</th>
                                                            <th>Telepon</th>
                                                            <th>Email</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tomi_invoice_referral_table_body">
                                                        <tr class="referral-empty-row"><td colspan="6">Belum ada refferal</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="feature-referral-slot d-none" data-prefix="tomi_invoice" data-index="1">
                                            <div class="d-none referral-slot-storage">
                                                @include('admin.companies.partials.referral-form', ['prefix' => 'tomi_invoice', 'index' => 1])
                                            </div>
                                        </div>
                                        <div class="feature-referral-slot d-none" data-prefix="tomi_invoice" data-index="2">
                                            <div class="d-none referral-slot-storage">
                                                @include('admin.companies.partials.referral-form', ['prefix' => 'tomi_invoice', 'index' => 2])
                                            </div>
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

<div class="modal fade" id="referralFormModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="referralFormModalTitle">Form Refferal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <div class="modal-body" id="referralFormModalBody"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="button" onclick="saveReferralFormModal()">Simpan Refferal</button>
            </div>
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
                <div class="modal-body tenor-config-modal-body">
                    <input type="hidden" id="tenor_config_id">
                    <div class="row">
                        <div class="col-12 form-group">
                            <label>Tenor</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_tenor" type="number" min="1">
                                <div class="input-group-append"><span class="input-group-text">bulan</span></div>
                            </div>
                            <small class="text-danger cfg-error-tenor"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Biaya Bunga</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_rate" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-rate"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Biaya Asuransi</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_asuransi" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-asuransi"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Biaya Provisi</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_provisi" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-provisi"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Biaya Layanan</label>
                            <input class="form-control rupiah-display-input" id="cfg_biaya_layanan_view" inputmode="numeric" placeholder="Rp. 0">
                            <input type="hidden" id="cfg_biaya_layanan">
                            <small class="text-danger cfg-error-biaya_layanan"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Biaya Admin</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_biaya_admin" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-biaya_admin"></small>
                        </div>
                        <div class="col-12 form-group tenor-referral-config-field">
                            <label>Lender</label>
                            <div class="input-group">
                                <input class="form-control cfg-admin-fee-input" id="cfg_ksp" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-ksp"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Tomi</label>
                            <div class="input-group">
                                <input class="form-control" id="cfg_fee_tomi" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-fee_tomi"></small>
                        </div>
                        <div class="col-12 form-group tenor-referral-config-field">
                            <label>Refferal 1</label>
                            <div class="input-group">
                                <input class="form-control cfg-admin-fee-input" id="cfg_profesional_fee_1" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-profesional_fee_1"></small>
                        </div>
                        <div class="col-12 form-group tenor-referral-config-field">
                            <label>Refferal 2</label>
                            <div class="input-group">
                                <input class="form-control cfg-admin-fee-input" id="cfg_profesional_fee_2" type="number" step="0.01" min="0">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                            <small class="text-danger cfg-error-profesional_fee_2"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Maximal Nominal</label>
                            <input class="form-control rupiah-display-input" id="cfg_maximal_nominal_view" inputmode="numeric" placeholder="Rp. 0">
                            <input type="hidden" id="cfg_maximal_nominal">
                            <small class="text-danger cfg-error-maximal_nominal"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Minimal Nominal</label>
                            <input class="form-control rupiah-display-input" id="cfg_minimal_nominal_view" inputmode="numeric" placeholder="Rp. 0">
                            <input type="hidden" id="cfg_minimal_nominal">
                            <small class="text-danger cfg-error-minimal_nominal"></small>
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
    var pendingTenorConfigs = [];
    var pendingTenorConfigKey = 1;
    var activeReferralModal = null;
    var referralFormFields = [
        'id', 'national_id_number', 'name', 'is_active', 'birth_place', 'birth_date', 'gender', 'blood_type',
        'address', 'rt', 'rw', 'province_id', 'city_id', 'district_id', 'village_id', 'postal_code',
        'religion', 'marital_status', 'occupation', 'nationality', 'phone_number', 'email',
        'bank_id', 'bank_account_number', 'bank_account_name', 'notes'
    ];
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

    $('#id_lender, #id_provinsi, #id_kabupaten, #id_kecamatan, #id_desa, #bank').select2({ width: '100%' });

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
        pendingTenorConfigs = [];
        pendingTenorConfigKey = 1;
        $('#id_lender, #id_provinsi, #bank').val('').trigger('change');
        $('#id_kabupaten, #id_kecamatan, #id_desa').empty().trigger('change');
        $('#status, #active_profesional_fee, #active_set_pinjaman').val('active');
        $('#tomi_dana_plus_status, #tomi_dana_plus_set_pinjaman').val('active');
        $('#payment_date').val(25);
        $('#h_min_jatuh_tempo').val(9);
        $('#active_tanggal').val(1);
        $('#tomi_dana_plus_active_tanggal').val(1);
        $('#tomi_dana_plus_payment_date').val(25);
        $('#tomi_dana_plus_h_min_jatuh_tempo').val(9);
        setRupiahField('#max_plafon_view', '#max_plafon', '');
        setRupiahField('#tomi_invoice_max_plafon', null, '');
        resetAllReferralForms();
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
            hydrateAllReferralForms(response.referral_forms || {});
            toggleTomiDanaConfiguration();
            isHydratingForm = false;
            $('#tomi-dana-tab').tab('show');
            $('#companyModal').modal('show');
        });
    }

    function syncFeaturePreviewFields(company) {
        $('#tomi_dana_plus_status').val(company.status || 'active');
        $('#tomi_dana_plus_set_pinjaman').val(company.active_set_pinjaman || 'active');
        $('#tomi_dana_plus_active_tanggal').val(company.active_tanggal || 1);
        $('#tomi_dana_plus_payment_date').val(company.payment_date || 25);
        $('#tomi_dana_plus_h_min_jatuh_tempo').val(company.h_min_jatuh_tempo || 9);
        setRupiahField('#tomi_invoice_max_plafon', null, company.max_plafon);
    }

    function toggleTomiDanaReferralFields() {
        var isActive = $('#active_profesional_fee').val() === 'active';
        $('.feature-referral-wrapper[data-prefix="tomi_dana"]').toggleClass('d-none', !isActive);

        if (!isActive) {
            resetReferralForm('tomi_dana');
        }

        updateReferralButton('tomi_dana');
        syncTenorReferralConfigFields();
    }

    function referralSelect(prefix, index) {
        return $('#' + prefix + '_referral_' + index + '_id');
    }

    function visibleReferralCount(prefix) {
        return $('.feature-referral-slot[data-prefix="' + prefix + '"]:not(.d-none)').length;
    }

    function updateReferralButton(prefix) {
        $('.add-referral-button[data-prefix="' + prefix + '"]').prop('disabled', visibleReferralCount(prefix) >= 2);
    }

    function showReferralSlot(prefix, index) {
        $('.feature-referral-slot[data-prefix="' + prefix + '"][data-index="' + index + '"]').removeClass('d-none');
        updateReferralButton(prefix);
        renderReferralTable(prefix);
    }

    function addReferralSlot(prefix) {
        var count = visibleReferralCount(prefix);

        if (count >= 2) {
            return;
        }

        if (prefix === 'tomi_dana') {
            $('#active_profesional_fee').val('active');
            syncTenorReferralConfigFields();
        }

        showReferralSlot(prefix, count + 1);
        openReferralFormModal(prefix, count + 1);
    }

    function resetReferralForm(prefix) {
        [1, 2].forEach(function (index) {
            clearReferralSlot(prefix, index);
        });
        $('.feature-referral-slot[data-prefix="' + prefix + '"]').addClass('d-none');
        updateReferralButton(prefix);
        renderReferralTable(prefix);
    }

    function clearReferralSlot(prefix, index) {
        var fieldPrefix = prefix + '_referral_' + index;

        referralFormFields.forEach(function (field) {
            $('#' + fieldPrefix + '_' + field).val(field === 'nationality' ? 'WNI' : '');
        });

        $('#' + fieldPrefix + '_is_active').val('1');
        resetReferralRegionSelect('#' + fieldPrefix + '_city_id', 'Pilih Kota/Kabupaten');
        resetReferralRegionSelect('#' + fieldPrefix + '_district_id', 'Pilih Kecamatan');
        resetReferralRegionSelect('#' + fieldPrefix + '_village_id', 'Pilih Kelurahan/Desa');
        renderReferralTable(prefix);
    }

    function fillReferralSlot(prefix, index, referral) {
        var fieldPrefix = prefix + '_referral_' + index;

        referralFormFields.forEach(function (field) {
            $('#' + fieldPrefix + '_' + field).val(referral[field] || '');
        });

        if (referral.province_id) {
            loadReferralCities(fieldPrefix, referral.province_id, referral.city_id)
                .then(function () {
                    return loadReferralDistricts(fieldPrefix, referral.city_id, referral.district_id);
                })
                .then(function () {
                    return loadReferralVillages(fieldPrefix, referral.district_id, referral.village_id);
                });
        }

        renderReferralTable(prefix);
    }

    function hydrateReferralForm(prefix, referrals) {
        resetReferralForm(prefix);

        [1, 2].forEach(function (index) {
            var referral = referrals[index - 1] || null;
            if (referral) {
                showReferralSlot(prefix, index);
                fillReferralSlot(prefix, index, referral);
            }
        });
    }

    function resetAllReferralForms() {
        ['tomi_dana', 'tomi_dana_plus', 'tomi_gawai', 'tomi_gaji', 'tomi_invoice'].forEach(resetReferralForm);
        $('#active_profesional_fee').val('active');
        $('.feature-referral-wrapper[data-prefix="tomi_dana"]').removeClass('d-none');
        syncTenorReferralConfigFields();
    }

    function hydrateAllReferralForms(referralForms) {
        ['tomi_dana', 'tomi_dana_plus', 'tomi_gawai', 'tomi_gaji', 'tomi_invoice'].forEach(function (prefix) {
            hydrateReferralForm(prefix, referralForms[prefix] || []);
        });

        $('#active_profesional_fee').val(
            !visibleReferralCount('tomi_dana') ? 'nonactive' : 'active'
        );
        toggleTomiDanaReferralFields();
        syncTenorReferralConfigFields();
    }

    function escapeHtml(value) {
        return $('<div>').text(value || '').html();
    }

    function referralFieldValue(prefix, index, field) {
        var fieldPrefix = prefix + '_referral_' + index;
        return $('#' + fieldPrefix + '_' + field).val() || '';
    }

    function renderReferralTable(prefix) {
        var body = $('#' + prefix + '_referral_table_body');
        var rows = [];

        [1, 2].forEach(function (index) {
            var slot = $('.feature-referral-slot[data-prefix="' + prefix + '"][data-index="' + index + '"]');

            if (slot.hasClass('d-none')) {
                return;
            }

            rows.push(
                '<tr>'
                + '<td>Refferal ' + index + '</td>'
                + '<td>' + escapeHtml(referralFieldValue(prefix, index, 'name') || '-') + '</td>'
                + '<td>' + escapeHtml(referralFieldValue(prefix, index, 'national_id_number') || '-') + '</td>'
                + '<td>' + escapeHtml(referralFieldValue(prefix, index, 'phone_number') || '-') + '</td>'
                + '<td>' + escapeHtml(referralFieldValue(prefix, index, 'email') || '-') + '</td>'
                + '<td><div class="btn-group">'
                + '<button type="button" class="btn btn-sm btn-primary" onclick="openReferralFormModal(\'' + prefix + '\', ' + index + ')"><i class="fa-solid fa-pen"></i></button>'
                + '<button type="button" class="btn btn-sm btn-danger" onclick="deleteReferralSlot(\'' + prefix + '\', ' + index + ')"><i class="fa-solid fa-trash-can"></i></button>'
                + '</div></td>'
                + '</tr>'
            );
        });

        body.html(rows.length ? rows.join('') : '<tr class="referral-empty-row"><td colspan="6">Belum ada refferal</td></tr>');
    }

    function deleteReferralSlot(prefix, index) {
        clearReferralSlot(prefix, index);
        $('.feature-referral-slot[data-prefix="' + prefix + '"][data-index="' + index + '"]').addClass('d-none');
        updateReferralButton(prefix);
        renderReferralTable(prefix);
    }

    function returnReferralFormToSlot() {
        if (!activeReferralModal) {
            return;
        }

        $('#referralFormModalBody .referral-form-fields')
            .appendTo(activeReferralModal.storage)
            .addClass('d-none');

        activeReferralModal = null;
        $('#referralFormModalBody').empty();
    }

    function openReferralFormModal(prefix, index) {
        returnReferralFormToSlot();

        var slot = $('.feature-referral-slot[data-prefix="' + prefix + '"][data-index="' + index + '"]');
        var storage = slot.find('.referral-slot-storage');
        var fields = storage.find('.referral-form-fields');

        activeReferralModal = {
            prefix: prefix,
            index: index,
            storage: storage,
        };

        $('#referralFormModalTitle').text('Form Refferal ' + index);
        fields.appendTo('#referralFormModalBody').removeClass('d-none');
        $('#referralFormModal').modal('show');
    }

    function saveReferralFormModal() {
        if (activeReferralModal) {
            renderReferralTable(activeReferralModal.prefix);
        }

        $('#referralFormModal').modal('hide');
    }

    $('#referralFormModal')
        .on('show.bs.modal', function () {
            $(this).appendTo('body');
        })
        .on('shown.bs.modal', function () {
            $('.modal-backdrop').last().addClass('referral-form-backdrop');
        })
        .on('hidden.bs.modal', function () {
            returnReferralFormToSlot();
            if ($('#companyModal').hasClass('show')) {
                $('body').addClass('modal-open');
            }
        });

    function resetReferralRegionSelect(selector, placeholder) {
        $(selector).html('<option value="">' + placeholder + '</option>');
    }

    function fillReferralRegionSelect(selector, rows, valueKey, labelKey, placeholder, selectedValue, secondaryLabelKey) {
        resetReferralRegionSelect(selector, placeholder);

        rows.forEach(function (row) {
            var label = row[labelKey] || '';
            if (secondaryLabelKey && row[secondaryLabelKey]) {
                label += ' - ' + row[secondaryLabelKey];
            }

            $(selector).append(new Option(label, row[valueKey], false, false));
        });

        if (selectedValue) {
            $(selector).val(String(selectedValue));
        }
    }

    function loadReferralCities(fieldPrefix, provinceId, selectedValue) {
        resetReferralRegionSelect('#' + fieldPrefix + '_city_id', 'Pilih Kota/Kabupaten');
        resetReferralRegionSelect('#' + fieldPrefix + '_district_id', 'Pilih Kecamatan');
        resetReferralRegionSelect('#' + fieldPrefix + '_village_id', 'Pilih Kelurahan/Desa');

        if (!provinceId) {
            return $.Deferred().resolve().promise();
        }

        return $.get('{{ route('admin.companies.cities') }}', { province_id: provinceId }, function (rows) {
            fillReferralRegionSelect('#' + fieldPrefix + '_city_id', rows, 'id', 'kabupaten', 'Pilih Kota/Kabupaten', selectedValue);
        });
    }

    function loadReferralDistricts(fieldPrefix, cityId, selectedValue) {
        resetReferralRegionSelect('#' + fieldPrefix + '_district_id', 'Pilih Kecamatan');
        resetReferralRegionSelect('#' + fieldPrefix + '_village_id', 'Pilih Kelurahan/Desa');

        if (!cityId) {
            return $.Deferred().resolve().promise();
        }

        return $.get('{{ route('admin.companies.districts') }}', { city_id: cityId }, function (rows) {
            fillReferralRegionSelect('#' + fieldPrefix + '_district_id', rows, 'id', 'kecamatan', 'Pilih Kecamatan', selectedValue);
        });
    }

    function loadReferralVillages(fieldPrefix, districtId, selectedValue) {
        resetReferralRegionSelect('#' + fieldPrefix + '_village_id', 'Pilih Kelurahan/Desa');

        if (!districtId) {
            return $.Deferred().resolve().promise();
        }

        return $.get('{{ route('admin.companies.villages') }}', { district_id: districtId }, function (rows) {
            fillReferralRegionSelect('#' + fieldPrefix + '_village_id', rows, 'id', 'desa', 'Pilih Kelurahan/Desa', selectedValue, 'kodepos');
        });
    }

    function toggleTomiDanaConfiguration() {
        $('#tomi_dana_configuration_wrapper').removeClass('d-none');
        $('#addTenorConfigButton').prop('disabled', false);

        if (currentCompanyId) {
            $('#companyTenorTable_wrapper, #companyTenorTable').removeClass('d-none');
            initCompanyTenorTable();
            return;
        }

        $('#companyTenorTable_wrapper, #companyTenorTable').removeClass('d-none');
        if (companyTenorTable) {
            companyTenorTable.destroy();
            companyTenorTable = null;
        }

        renderPendingTenorConfigs();
    }

    function renderPendingTenorConfigs() {
        var rows = pendingTenorConfigs.map(function (config) {
            return '<tr>'
                + '<td>' + config.tenor + ' bulan</td>'
                + '<td>' + config.rate + ' %</td>'
                + '<td>' + config.asuransi + ' %</td>'
                + '<td>' + config.provisi + ' %</td>'
                + '<td>' + formatRupiah(config.biaya_layanan) + '</td>'
                + '<td>' + config.biaya_admin + ' %</td>'
                + '<td>' + config.ksp + ' %</td>'
                + '<td>' + config.fee_tomi + ' %</td>'
                + '<td>' + config.profesional_fee_1 + ' %</td>'
                + '<td>' + config.profesional_fee_2 + ' %</td>'
                + '<td>' + formatRupiah(config.maximal_nominal) + '</td>'
                + '<td>' + formatRupiah(config.minimal_nominal) + '</td>'
                + '<td><div class="btn-group">'
                + '<button type="button" class="btn btn-sm btn-primary" onclick="editPendingTenorConfig(' + config.key + ')"><i class="fa-solid fa-pen"></i></button>'
                + '<button type="button" class="btn btn-sm btn-danger" onclick="deletePendingTenorConfig(' + config.key + ')"><i class="fa-solid fa-trash-can"></i></button>'
                + '</div></td>'
                + '</tr>';
        }).join('');

        if (!rows) {
            rows = '<tr><td colspan="13">Belum ada konfigurasi Tomi Dana untuk perusahaan ini.</td></tr>';
        }

        $('#pendingTenorConfigBody').html(rows);
    }

    function editPendingTenorConfig(key) {
        var config = pendingTenorConfigs.find(function (item) {
            return item.key === key;
        });

        if (!config) {
            return;
        }

        resetTenorConfigForm();
        $('#tenorConfigModalTitle').text('Edit Konfigurasi Tomi Dana');
        $('#tenor_config_id').val('pending-' + key);
        $('#cfg_tenor').val(config.tenor);
        $('#cfg_rate').val(config.rate);
        $('#cfg_asuransi').val(config.asuransi);
        $('#cfg_provisi').val(config.provisi);
        $('#cfg_biaya_admin').val(config.biaya_admin);
        $('#cfg_fee_tomi').val(config.fee_tomi);
        $('#cfg_profesional_fee_1').val(config.profesional_fee_1);
        $('#cfg_profesional_fee_2').val(config.profesional_fee_2);
        $('#cfg_ksp').val(config.ksp);
        setRupiahField('#cfg_biaya_layanan_view', '#cfg_biaya_layanan', config.biaya_layanan);
        setRupiahField('#cfg_minimal_nominal_view', '#cfg_minimal_nominal', config.minimal_nominal);
        setRupiahField('#cfg_maximal_nominal_view', '#cfg_maximal_nominal', config.maximal_nominal);
        syncTenorReferralConfigFields();
        $('#tenorConfigModal').modal('show');
    }

    function deletePendingTenorConfig(key) {
        pendingTenorConfigs = pendingTenorConfigs.filter(function (item) {
            return item.key !== key;
        });
        renderPendingTenorConfigs();
    }

    function tenorConfigPayload() {
        return {
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
        };
    }

    function savePendingTenorConfigs(companyId) {
        if (!pendingTenorConfigs.length) {
            return $.Deferred().resolve().promise();
        }

        var chain = $.Deferred().resolve().promise();

        pendingTenorConfigs.forEach(function (config) {
            chain = chain.then(function () {
                return $.post('{{ route('admin.ksp.konfigurasi.add') }}', $.extend({
                    _token: '{{ csrf_token() }}',
                    id: companyId,
                }, config));
            });
        });

        return chain.then(function () {
            pendingTenorConfigs = [];
            renderPendingTenorConfigs();
        });
    }

    function activateServerTenorTable() {
        $('#pendingTenorConfigBody').empty();

        if (companyTenorTable) {
            companyTenorTable.clear().draw();
        }

        initCompanyTenorTable();
    }

    $(document).on('click', '.add-referral-button', function () {
        addReferralSlot($(this).data('prefix'));
    });

    $('#active_profesional_fee').on('change', function () {
        toggleTomiDanaReferralFields();
    });

    $('#tenorConfigModal').on('show.bs.modal', function () {
        $(this).appendTo('body');
    }).on('shown.bs.modal', function () {
        $('.modal-backdrop').last().addClass('tenor-config-backdrop');
    }).on('hidden.bs.modal', function () {
        if ($('#companyModal').hasClass('show')) {
            $('body').addClass('modal-open');
        }
    });

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
                { data: 'biaya_layanan', defaultContent: '-' },
                { data: 'biaya_admin', defaultContent: '-' },
                { data: 'ksp', defaultContent: '-' },
                { data: 'fee_tomi', defaultContent: '-' },
                { data: 'profesional_fee_1', defaultContent: '-' },
                { data: 'profesional_fee_2', defaultContent: '-' },
                { data: 'maximal_nominal', defaultContent: '-' },
                { data: 'minimal_nominal', defaultContent: '-' },
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

        if (!isActive) {
            $('#cfg_profesional_fee_1, #cfg_profesional_fee_2, #cfg_ksp').val(0);
        }
    }

    $('#tenorConfigForm').on('submit', function (event) {
        event.preventDefault();
        clearTenorConfigErrors();

        var id = $('#tenor_config_id').val();
        var url = id ? '{{ route('admin.ksp.konfigurasi.edit') }}' : '{{ route('admin.ksp.konfigurasi.add') }}';
        var payload = tenorConfigPayload();

        if (!currentCompanyId) {
            if (id && id.indexOf('pending-') === 0) {
                var pendingKey = parseInt(id.replace('pending-', ''), 10);
                pendingTenorConfigs = pendingTenorConfigs.map(function (config) {
                    return config.key === pendingKey ? $.extend({ key: pendingKey }, payload) : config;
                });
            } else {
                pendingTenorConfigs.push($.extend({ key: pendingTenorConfigKey++ }, payload));
            }

            $('#tenorConfigModal').modal('hide');
            renderPendingTenorConfigs();
            return;
        }

        $('#saveTenorConfigButton').attr('disabled', true);
        $('#saveTenorConfigSpinner').addClass('spinner-border spinner-border-sm');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                id: id || currentCompanyId,
                tenor: payload.tenor,
                rate: payload.rate,
                asuransi: payload.asuransi,
                provisi: payload.provisi,
                biaya_admin: payload.biaya_admin,
                fee_tomi: payload.fee_tomi,
                profesional_fee_1: payload.profesional_fee_1,
                profesional_fee_2: payload.profesional_fee_2,
                ksp: payload.ksp,
                biaya_layanan: payload.biaya_layanan,
                minimal_nominal: payload.minimal_nominal,
                maximal_nominal: payload.maximal_nominal,
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

    $(document).on('change', '.referral-province-select', function () {
        loadReferralCities($(this).data('prefix'), this.value);
    });

    $(document).on('change', '.referral-city-select', function () {
        loadReferralDistricts($(this).data('prefix'), this.value);
    });

    $(document).on('change', '.referral-district-select', function () {
        loadReferralVillages($(this).data('prefix'), this.value);
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
                    savePendingTenorConfigs(currentCompanyId)
                        .done(function () {
                            activateServerTenorTable();
                            table.ajax.reload();
                        })
                        .fail(function () {
                            toastr.error('Perusahaan tersimpan, tetapi konfigurasi Tomi Dana gagal disimpan.');
                        });
                    return;
                }

                if (currentCompanyId) {
                    activateServerTenorTable();
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
