@extends('layout.app')
@section('title', 'Koperasi Simpan Pinjam')

@push('css')
<style>
    .lender-table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    #tablelistLender {
        width: 100% !important;
        min-width: 2300px;
    }

    #tablelistLender th,
    #tablelistLender td {
        white-space: nowrap !important;
        vertical-align: middle;
    }

    #lenderPicFormModal {
        z-index: 1065;
    }

    .modal-backdrop.lender-pic-form-backdrop {
        z-index: 1060;
    }

    #lenderPicFormModal .modal-content {
        max-height: calc(100vh - 3.5rem);
    }

    #lenderPicFormModal .modal-body {
        max-height: calc(100vh - 12rem);
        overflow-y: auto;
    }

</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                    <li class="breadcrumb-item active">MoU Lender x TOMI</li>
                </ol>
            </div>
            <h4 class="page-title">MoU Lender x TOMI</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <button type="button" class="btn btn-sm btn-primary float-right waves-effect waves-light"
                    data-toggle="modal" data-target="#modalAdd">
                    <i class="mdi mdi-plus-circle"></i> Tambah Lender
                </button>

                <div class="table-responsive lender-table-wrapper">
                    <table id="tablelistLender"
                        class="table table-hover mt-4 text-center justify-center nowrap"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Last Updated</th>
                                <th>Expired Update</th>
                                <th>ID Lender</th>
                                <th>Nama Lender</th>
                                <th>PKS</th>
                                <th>Logo</th>
                                <th>No. perjanjian kerja sama lender x TOMI</th>
                                <th>PIC Lender</th>
                                <th>PIC Internal</th>
                                <th>Email Lender</th>
                                <th>Telepon</th>
                                <th>Provinsi</th>
                                <th>Kabupaten / Kota</th>
                                <th>Kecamatan</th>
                                <th>Desa</th>
                                <th>Alamat Lengkap</th>
                                <th>Nama BANK</th>
                                <th>A.N Rekening</th>
                                <th>No. Rekening</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>


        </div>
    </div>

</div>


{{-- modal tambah --}}
<div class="modal fade" id="modalAdd" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class=" modal-dialog modal-xl modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Tambah Lender
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Nama Lender</label>
                                    <input class="form-control" id="namaLender" placeholder="Nama Lender"></input>
                                    <small class="text-danger" id="errNamaLender"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Email Lender</label>
                                    <input class="form-control" id="emailLender" placeholder="Email Lender"></input>
                                    <small class="text-danger" id="errEmailLender"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Telepon</label>
                                    <input class="form-control" id="telepon" placeholder="62535252"
                                        type="number"></input>
                                    <small class="text-danger" id="errTelepon"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                @include('admin.ksp.lender.partials.pic-table', ['prefix' => 'add_pic_lender', 'title' => 'PIC Lender'])
                                <small class="text-danger" id="errNamaPicLender"></small>
                            </div>


                            <div class="col-md-12">
                                @include('admin.ksp.lender.partials.pic-table', ['prefix' => 'add_pic_internal', 'title' => 'PIC HC / HRD'])
                                <small class="text-danger" id="errNamaPicInternal"></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Provinsi</label>
                                    <select class="form-control" id="provinsiSelect" style="width: 100%">
                                        <option disabled>Select</option>
                                        @foreach ($data['provinsis'] as $provinsi)
                                        <option value="{{ $provinsi->provinsi }}">{{ $provinsi->provinsi }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger" id="errProvinsi"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Kabupaten / Kota</label>
                                    <select class="form-control" id="kabupatenSelect" style="width: 100%">
                                        <option disabled>Select</option>
                                    </select>
                                    <small class="text-danger" id="errKabupaten_kota"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <select class="form-control" id="kecamatanSelect" style="width: 100%">
                                        <option disabled>Select</option>
                                    </select>
                                    <small class="text-danger" id="errKecamatan"></small>
                                </div>
                            </div>


                            <div class="col-md-12">
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

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>No. perjanjian kerja sama Lender x TOMI</label>
                                    <input class="form-control" id="nomor_pks" placeholder="012/TOMIKSP-FIT/VIII/2025"
                                        type="text"></input>
                                    <small class="text-danger" id="errNomor_pks"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>File PKS</label>
                                    <input class="form-control" id="filePKS" type="file"></input>
                                    <small class="text-danger" id="errPKS"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Logo Lender</label>
                                    <input class="form-control" id="logoLender" type="file"></input>
                                    <small class="text-danger" id="errLogoLender"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label>Tema Warna Lender</label>
                                <input class="form-control" type="color" id="colorLender" name="color" value="#188ae2">
                                <small class="text-danger" id="errColorLender"></small>

                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Nama Bank</label>
                                <div class="form-group">
                                    <select class="form-control" id="bank">
                                        @foreach ($data['bank'] as $bank)
                                        <option value="{{ $bank->id }}">
                                            {{ $bank->code_bank }}
                                        </option>
                                        @endforeach
                                        <small id="errBank"></small>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>A.N Rekening</label>
                                    <input class="form-control" type="text" id="an_rekening" value=""></input>
                                    <small id="errANRekening"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>No Rekening</label>
                                    <input class="form-control" type="number" id="no_rekening" value=""></input>
                                    <small id="errNoRekening"></small>
                                </div>
                            </div>
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
    <div class=" modal-dialog modal-xl modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Ubah Lender
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            </div>
            <div class="modal-body">


                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Nama Lender</label>
                                    <input class="form-control" id="namaLenderEdit" placeholder="Nama Lender"></input>
                                    <small class="text-danger" id="errNamaLenderEdit"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Email Lender</label>
                                    <input class="form-control" id="emailLenderEdit" placeholder="Email Lender"></input>
                                    <small class="text-danger" id="errEmailLenderEdit"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Telepon</label>
                                    <input class="form-control" id="teleponEdit" placeholder="62535252"
                                        type="number"></input>
                                    <small class="text-danger" id="errTeleponEdit"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                @include('admin.ksp.lender.partials.pic-table', ['prefix' => 'edit_pic_lender', 'title' => 'PIC Lender'])
                                <small class="text-danger" id="errNamaPicLenderEdit"></small>
                            </div>


                            <div class="col-md-12">
                                @include('admin.ksp.lender.partials.pic-table', ['prefix' => 'edit_pic_internal', 'title' => 'PIC HC / HRD'])
                                <small class="text-danger" id="errNamaPicInternalEdit"></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Provinsi</label>
                                    <select class="form-control" id="provinsiSelectEdit" style="width: 100%">
                                        <option disabled>Select</option>
                                        @foreach ($data['provinsis'] as $provinsi)
                                        <option value="{{ $provinsi->provinsi }}">{{ $provinsi->provinsi }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger" id="errProvinsiEdit"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Kabupaten / Kota</label>
                                    <select class="form-control" id="kabupatenSelectEdit" style="width: 100%">
                                        <option disabled>Select</option>
                                    </select>
                                    <small class="text-danger" id="errKabupaten_kotaEdit"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <select class="form-control" id="kecamatanSelectEdit" style="width: 100%">
                                        <option disabled>Select</option>
                                    </select>
                                    <small class="text-danger" id="errKecamatanEdit"></small>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Desa</label>
                                    <select class="form-control" id="desaSelectEdit" style="width: 100%">
                                        <option disabled>Select</option>
                                    </select>
                                    <small class="text-danger" id="errDesaEdit"></small>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat Lengkap</label>
                                    <textarea class="form-control" id="alamatLengkapEdit" style="height: 70px;"
                                        placeholder="Alamat Lengkap"></textarea>
                                    <small class="text-danger" id="errAlamatLengkapEdit"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>No. perjanjian kerja sama Lender x TOMI</label>
                                    <input class="form-control" id="nomor_pksEdit"
                                        placeholder="012/TOMIKSP-FIT/VIII/2025" type="text"></input>
                                    <small class="text-danger" id="errNomor_pksEdit"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>File PKS</label>
                                    <input class="form-control" id="filePKSEdit" type="file"></input>
                                    <small class="text-danger" id="errPKSEdit"></small>
                                    <small class="text-small">Note: Biarkan kosong jika anda tidak ingin mengubah File
                                        PKS</small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Logo Lender</label>
                                    <input class="form-control" id="logoLenderEdit" type="file"></input>
                                    <small class="text-danger" id="errLogoLenderEdit"></small>
                                    <small class="text-small">Note: Biarkan kosong jika anda tidak ingin mengubah logo
                                        Lender</small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label>Tema Warna Lender</label>
                                <input class="form-control" type="color" id="colorLenderEdit" name="color"
                                    value="#188ae2">
                                <small class="text-danger" id="errColorLenderEdit"></small>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Nama Bank</label>
                                <div class="form-group">
                                    <select class="form-control" id="bankEdit">
                                        @foreach ($data['bank'] as $bank)
                                        <option value="{{ $bank->id }}">
                                            {{ $bank->code_bank }}
                                        </option>
                                        @endforeach
                                        <small id="errBankEdit"></small>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>A.N Rekening</label>
                                    <input class="form-control" type="text" id="an_rekeningEdit" value=""></input>
                                    <small id="errANRekeningEdit"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>No Rekening</label>
                                    <input class="form-control" type="number" id="no_rekeningEdit" value=""></input>
                                    <small id="errNoRekeningEdit"></small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Status Lender :</label>
                                    <select class="form-control" id="statusEdit">
                                        <option value="active">ACTIVE</option>
                                        <option value="nonactive">NONACTIVE</option>
                                    </select>
                                    <small id="errStatusEdit"></small>
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


<div class="modal fade" id="lenderPicFormModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lenderPicFormModalTitle">Form PIC</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <div class="modal-body" id="lenderPicFormModalBody"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="button" onclick="saveLenderPicFormModal()">Simpan</button>
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
                <h5 class="modal-title" id="exampleModalCenterTitle">Delete Lender
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="text-center align-middle">
                    <p>Apakah anda yakin menghapus lender ini?, ini akan berkaitan semua data karyawan dan
                        pinjaman
                        yang bersangkutan dengan lender ini</p>
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

{{-- modal Approve --}}
<div class="modal fade" id="modalApproveEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class=" modal-dialog modal-md modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Detail Perubahan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h6>Lender yang diubah</h6>
                <div class="row">
                    <div class="col-4">
                        <span>ID Lender :</span>
                    </div>
                    <div class="col-8">
                        <span id="id_lender_detail">Test</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <span>Nama Lender :</span>
                    </div>
                    <div class="col-8">
                        <span id="nama_lender_detail"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <span>Status Pertama :</span>
                    </div>
                    <div class="col-8">
                        <span id="status_pertama"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <span>Status Kedua :</span>
                    </div>
                    <div class="col-8">
                        <span id="status_kedua"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <span>Data yang diubah</span>
                        <div id="changedData"></div>
                    </div>
                </div>

                <div class="form-group pt-2">
                    <label>Action</label>
                    <select class="form-control" id="approveAction">
                        <option value="approve">APPROVE</option>
                        <option value="reject">REJECT</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="button" id="buttonSubmitApprove">Save <div
                        id="loadingTextApprove">
                    </div></button>
            </div>
        </div>
    </div>
</div>

{{-- modal history update --}}
<div class="modal fade" id="modalHistoryUpdate" tabindex="-1" role="dialog" aria-labelledby="modalHistoryTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistoryTitle">Update History
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- modal untuk detail logo --}}

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Detail Gambar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="image-container">
                    <p>Memuat gambar...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
    var activeLenderPicModal = null;
    var lenderPicTitles = {
        add_pic_lender: 'PIC Lender',
        add_pic_internal: 'PIC HC / HRD',
        edit_pic_lender: 'PIC Lender',
        edit_pic_internal: 'PIC HC / HRD',
    };
    var lenderPicFields = [
        'national_id_number', 'birth_place', 'birth_date', 'gender', 'blood_type',
        'address', 'rt', 'rw', 'province_id', 'city_id', 'district_id', 'village_id',
        'postal_code', 'religion', 'marital_status', 'occupation', 'nationality',
        'phone_number', 'email', 'password'
    ];

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

    function escapeHtml(value) {
        return $('<div>').text(value || '').html();
    }

    function lenderPicFieldValue(prefix, field) {
        return $('#' + prefix + '_' + field).val() || '';
    }

    function renderLenderPicTable(prefix) {
        var title = lenderPicTitles[prefix];
        var name = lenderPicFieldValue(prefix, 'name');
        var nik = lenderPicFieldValue(prefix, 'national_id_number');
        var phone = lenderPicFieldValue(prefix, 'phone_number');
        var email = lenderPicFieldValue(prefix, 'email');
        var body = $('#' + prefix + '_table_body');

        if (!name && !nik && !phone && !email) {
            body.html('<tr><td colspan="5">Belum ada ' + title + '</td></tr>');
            return;
        }

        body.html(
            '<tr>'
            + '<td>' + escapeHtml(name || '-') + '</td>'
            + '<td>' + escapeHtml(nik || '-') + '</td>'
            + '<td>' + escapeHtml(phone || '-') + '</td>'
            + '<td>' + escapeHtml(email || '-') + '</td>'
            + '<td><button type="button" class="btn btn-sm btn-primary" onclick="openLenderPicFormModal(\'' + prefix + '\')"><i class="fa-solid fa-pen"></i></button></td>'
            + '</tr>'
        );
    }

    function resetLenderPicForm(prefix) {
        $('#' + prefix + '_name').val('');
        lenderPicFields.forEach(function (field) {
            $('#' + prefix + '_' + field).val(field === 'nationality' ? 'WNI' : '');
        });
        resetLenderPicRegionSelect('#' + prefix + '_city_id', 'Pilih Kota/Kabupaten');
        resetLenderPicRegionSelect('#' + prefix + '_district_id', 'Pilih Kecamatan');
        resetLenderPicRegionSelect('#' + prefix + '_village_id', 'Pilih Kelurahan/Desa');
        renderLenderPicTable(prefix);
    }

    function fillLenderPicForm(prefix, pic) {
        $('#' + prefix + '_name').val(pic.name || '');
        lenderPicFields.forEach(function (field) {
            $('#' + prefix + '_' + field).val(pic[field] || '');
        });

        if (pic.province_id) {
            loadLenderPicCities(prefix, pic.province_id, pic.city_id)
                .then(function () {
                    return loadLenderPicDistricts(prefix, pic.city_id, pic.district_id);
                })
                .then(function () {
                    return loadLenderPicVillages(prefix, pic.district_id, pic.village_id);
                });
        }

        renderLenderPicTable(prefix);
    }

    function appendLenderPicFormData(formData, prefix, requestPrefix) {
        formData.append(requestPrefix, lenderPicFieldValue(prefix, 'name'));
        lenderPicFields.forEach(function (field) {
            formData.append(requestPrefix + '_' + field, lenderPicFieldValue(prefix, field));
        });
    }

    function returnLenderPicFormToStorage() {
        if (!activeLenderPicModal) {
            return;
        }

        $('#lenderPicFormModalBody .lender-pic-form-fields')
            .appendTo(activeLenderPicModal.storage)
            .addClass('d-none');

        activeLenderPicModal = null;
        $('#lenderPicFormModalBody').empty();
    }

    function openLenderPicFormModal(prefix) {
        returnLenderPicFormToStorage();

        var storage = $('.lender-pic-form-storage[data-prefix="' + prefix + '"]');
        var fields = storage.find('.lender-pic-form-fields');

        activeLenderPicModal = {
            prefix: prefix,
            storage: storage,
        };

        $('#lenderPicFormModalTitle').text('Form ' + lenderPicTitles[prefix]);
        fields.appendTo('#lenderPicFormModalBody').removeClass('d-none');
        $('#lenderPicFormModal').modal('show');
    }

    function saveLenderPicFormModal() {
        if (activeLenderPicModal) {
            renderLenderPicTable(activeLenderPicModal.prefix);
        }

        $('#lenderPicFormModal').modal('hide');
    }

    $('#lenderPicFormModal')
        .on('show.bs.modal', function () {
            $(this).appendTo('body');
        })
        .on('shown.bs.modal', function () {
            $('.modal-backdrop').last().addClass('lender-pic-form-backdrop');
        })
        .on('hidden.bs.modal', function () {
            returnLenderPicFormToStorage();
            if ($('#modalAdd').hasClass('show') || $('#modalEdit').hasClass('show')) {
                $('body').addClass('modal-open');
            }
        });

    $('#modalAdd').on('show.bs.modal', function () {
        resetLenderPicForm('add_pic_lender');
        resetLenderPicForm('add_pic_internal');
    });

    function resetLenderPicRegionSelect(selector, placeholder) {
        $(selector).html('<option value="">' + placeholder + '</option>');
    }

    function fillLenderPicRegionSelect(selector, rows, valueKey, labelKey, placeholder, selectedValue, secondaryLabelKey) {
        resetLenderPicRegionSelect(selector, placeholder);

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

    function loadLenderPicCities(prefix, provinceId, selectedValue) {
        resetLenderPicRegionSelect('#' + prefix + '_city_id', 'Pilih Kota/Kabupaten');
        resetLenderPicRegionSelect('#' + prefix + '_district_id', 'Pilih Kecamatan');
        resetLenderPicRegionSelect('#' + prefix + '_village_id', 'Pilih Kelurahan/Desa');

        if (!provinceId) {
            return $.Deferred().resolve().promise();
        }

        return $.get('{{ route('admin.companies.cities') }}', { province_id: provinceId }, function (rows) {
            fillLenderPicRegionSelect('#' + prefix + '_city_id', rows, 'id', 'kabupaten', 'Pilih Kota/Kabupaten', selectedValue);
        });
    }

    function loadLenderPicDistricts(prefix, cityId, selectedValue) {
        resetLenderPicRegionSelect('#' + prefix + '_district_id', 'Pilih Kecamatan');
        resetLenderPicRegionSelect('#' + prefix + '_village_id', 'Pilih Kelurahan/Desa');

        if (!cityId) {
            return $.Deferred().resolve().promise();
        }

        return $.get('{{ route('admin.companies.districts') }}', { city_id: cityId }, function (rows) {
            fillLenderPicRegionSelect('#' + prefix + '_district_id', rows, 'id', 'kecamatan', 'Pilih Kecamatan', selectedValue);
        });
    }

    function loadLenderPicVillages(prefix, districtId, selectedValue) {
        resetLenderPicRegionSelect('#' + prefix + '_village_id', 'Pilih Kelurahan/Desa');

        if (!districtId) {
            return $.Deferred().resolve().promise();
        }

        return $.get('{{ route('admin.companies.villages') }}', { district_id: districtId }, function (rows) {
            fillLenderPicRegionSelect('#' + prefix + '_village_id', rows, 'id', 'desa', 'Pilih Kelurahan/Desa', selectedValue, 'kodepos');
        });
    }

    $(document).on('change', '.lender-pic-province-select', function () {
        loadLenderPicCities($(this).data('prefix'), this.value);
    });

    $(document).on('change', '.lender-pic-city-select', function () {
        loadLenderPicDistricts($(this).data('prefix'), this.value);
    });

    $(document).on('change', '.lender-pic-district-select', function () {
        loadLenderPicVillages($(this).data('prefix'), this.value);
    });

    function toggleLenderPicPassword(inputId, button) {
        var input = document.getElementById(inputId);
        var icon = button.querySelector('i');
        var isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('mdi-eye-off', !isPassword);
        icon.classList.toggle('mdi-eye', isPassword);
    }

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


    var table = $('#tablelistLender').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [{
            extend: 'excel',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
            header: true,
            title: 'List Lender',
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
        "lengthMenu": [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
        processing: true,
        responsive: false,
        scrollX: true,
        autoWidth: false,
        serverSide: true,
        columnDefs: [{
            targets: '_all',
            className: 'text-nowrap'
        }],
        language: {
            processing: '<i class="dripicons-loading">autorenew</i>'
        },
        ajax: {
            "type": "POST",
            "url": '{!! route('admin.lender.table') !!}',
            "data": function (xx) {
                return $.extend(xx, dataTable);
            },
        },
        columns: [{
            data: 'last_updated'
        }, {
            data: 'expired_updated'
        }, {
            data: 'id_code_lender'
        }, {
            data: 'nama'
        }, {
            data: 'pks'
        }, {
            data: 'logo'
        }, {
            data: 'nomor_pks'
        }, {
            data: 'pic_lender'
        }, {
            data: 'pic_internal'
        }, {
            data: 'email_lender'
        }, {
            data: 'telp'
        }, {
            data: 'provinsi'
        }, {
            data: 'kabupaten'
        }, {
            data: 'kecamatan'
        }, {
            data: 'desa'
        }, {
            data: 'alamat_lengkap'
        }, {
            data: 'bank'
        }, {
            data: 'nama_rekening'
        }, {
            data: 'no_rekening'
        }, {
            data: 'status'
        }, {
            data: 'action'
        }]
    });

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
        // Clear previous error messages
        $('.text-danger').text('');

        // Create a FormData object to handle both text and file inputs
        const formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");

        // Append all form data using the new IDs
        appendLenderPicFormData(formData, 'add_pic_lender', 'pic_lender');
        appendLenderPicFormData(formData, 'add_pic_internal', 'pic_internal');
        formData.append('nama_lender', $("#namaLender").val()); // Changed from namaPerusahaan
        formData.append('email_lender', $("#emailLender").val()); // Changed from emailPerusahaan
        formData.append('telepon', $("#telepon").val());
        formData.append('provinsi', $("#provinsiSelect").val());
        formData.append('kabupaten', $("#kabupatenSelect")
            .val()); // The HTML has errKabupaten_kota, but the field is named kabupatenSelect
        formData.append('kecamatan', $("#kecamatanSelect").val());
        formData.append('desa', $("#desaSelect").val());
        formData.append('alamat_lengkap', $("#alamatLengkap").val());
        formData.append('nomor_pks', $("#nomor_pks").val());
        formData.append('file_pks', $('#filePKS')[0].files[0]); // Append the file
        formData.append('logoLender', $('#logoLender')[0].files[0]); // Append the file
        formData.append('colorLender', $("#colorLender").val()); // Append the file
        // Check for bank details
        formData.append('bank', $("#bank").val());
        formData.append('an_rekening', $("#an_rekening").val());
        formData.append('no_rekening', $("#no_rekening").val());

        $.ajax({
            url: '{!! route('admin.lender.add') !!}',
            data: formData,
            type: 'POST',
            dataType: 'json',
            contentType: false, // Required for FormData
            processData: false, // Required for FormData
            beforeSend: function () {
                $('#buttonSubmit').attr("disabled", true);
                $('#loadingText').addClass("spinner-border");
            },
            success: function (add) {
                toastr.success(add.message);
                table.ajax.reload();
                $("#modalAdd").modal('hide');
                $('#buttonSubmit').attr("disabled", false);
                $('#loadingText').removeClass("spinner-border");
            },
            error: function (err) {
                $('#buttonSubmit').attr("disabled", false);
                $('#loadingText').removeClass("spinner-border");

                // Map the errors to the new, correct IDs
                const errors = err.responseJSON.errors;
                if (errors) {
                    $('#errNamaPicLender').text(errors.pic_lender ? errors.pic_lender[0] :
                        '');
                    $('#errNamaPicInternal').text(errors.pic_internal ? errors.pic_internal[0] :
                        '');
                    $('#errNamaLender').text(errors.nama_lender ? errors.nama_lender[0] :
                        '');
                    $('#errEmailLender').text(errors.email_lender ? errors.email_lender[0] :
                        '');
                    $('#errTelepon').text(errors.telepon ? errors.telepon[0] : '');
                    $('#errAlamatLengkap').text(errors.alamat_lengkap ? errors.alamat_lengkap[0] :
                        '');
                    $('#errNomor_pks').text(errors.nomor_pks ? errors.nomor_pks[0] : '');
                    $('#errPKS').text(errors.file_pks ? errors.file_pks[0] :
                        '');
                    $('#errLogoLender').text(errors.logoLender ? errors.logoLender[0] :
                        '');
                    $('#errColorLender').text(errors.colorLender ? errors.colorLender[0] :
                        '');
                    $('#errProvinsi').text(errors.provinsi ? errors.provinsi[0] : '');
                    $('#errKabupaten_kota').text(errors.kabupaten ? errors.kabupaten[0] : '');
                    $('#errKecamatan').text(errors.kecamatan ? errors.kecamatan[0] : '');
                    $('#errDesa').text(errors.desa ? errors.desa[0] : '');
                    $('#errBank').text(errors.bank ? errors.bank[0] : '');
                    $('#errANRekening').text(errors.an_rekening ? errors.an_rekening[0] : '');
                    $('#errNoRekening').text(errors.no_rekening ? errors.no_rekening[0] : '');
                }
            }
        });
    });

    editLender = (id) => {
        editID = id;
        $.ajax({
            url: '{!! route('admin.lender.detail') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': editID,
            },
            type: 'POST',
            dataType: 'json',
            success: function (detail) {
                fillLenderPicForm('edit_pic_lender', detail.data.pic_forms.pic_lender || { name: detail.data.pic_lender });
                fillLenderPicForm('edit_pic_internal', detail.data.pic_forms.pic_internal || { name: detail.data.pic_internal });
                $("#namaLenderEdit").val(detail.data.nama);
                $("#emailLenderEdit").val(detail.data.email_lender);
                $("#teleponEdit").val(detail.data.telp);
                $("#alamatLengkapEdit").val(detail.data.alamat_lengkap);
                $("#nomor_pksEdit").val(detail.data.nomor_pks);
                $("#statusEdit").val(detail.data.status);
                $("#bankEdit").val(detail.data.id_bank);
                $("#an_rekeningEdit").val(detail.data.nama_rekening);
                $("#no_rekeningEdit").val(detail.data.no_rekening);
                $("#colorLenderEdit").val(detail.data.color);
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
        // Clear previous error messages
        $('.text-danger').text('');

        // Create a FormData object to handle both text and file inputs
        const formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");
        // Append all form data using the new IDs
        formData.append('id', editID); // Changed from namaPicPerusahaan
        appendLenderPicFormData(formData, 'edit_pic_lender', 'pic_lender');
        appendLenderPicFormData(formData, 'edit_pic_internal', 'pic_internal');
        formData.append('nama_lender', $("#namaLenderEdit").val()); // Changed from namaPerusahaan
        formData.append('email_lender', $("#emailLenderEdit").val()); // Changed from emailPerusahaan
        formData.append('telepon', $("#teleponEdit").val());
        formData.append('provinsi', $("#provinsiSelectEdit").val());
        formData.append('kabupaten', $("#kabupatenSelectEdit")
            .val()); // The HTML has errKabupaten_kota, but the field is named kabupatenSelect
        formData.append('kecamatan', $("#kecamatanSelectEdit").val());
        formData.append('desa', $("#desaSelectEdit").val());
        formData.append('alamat_lengkap', $("#alamatLengkapEdit").val());
        formData.append('nomor_pks', $("#nomor_pksEdit").val());
        formData.append("file_pks", $('#filePKSEdit')[0].files[0] ?? "");
        formData.append("logoLender", $('#logoLenderEdit')[0].files[0] ?? "");
        formData.append('colorLender', $("#colorLenderEdit").val()); // Append the file
        // Check for bank details
        formData.append('bank', $("#bankEdit").val());
        formData.append('an_rekening', $("#an_rekeningEdit").val());
        formData.append('no_rekening', $("#no_rekeningEdit").val());
        formData.append('status', $("#statusEdit").val());

        $.ajax({
            url: '{!! route('admin.lender.update') !!}',
            data: formData,
            type: 'POST',
            dataType: 'json',
            contentType: false, // Required for FormData
            processData: false, // Required for FormData
            beforeSend: function () {
                $('#buttonSubmitEdit').attr("disabled", true);
                $('#loadingTextEdit').addClass("spinner-border");
            },
            success: function (add) {
                toastr.success(add.message);
                table.ajax.reload();
                $("#modalEdit").modal('hide');
                $('#buttonSubmitEdit').attr("disabled", false);
                $('#loadingTextEdit').removeClass("spinner-border");
            },
            error: function (err) {
                $('#buttonSubmitEdit').attr("disabled", false);
                $('#loadingTextEdit').removeClass("spinner-border");

                // Map the errors to the new, correct IDs
                const errors = err.responseJSON.errors;
                if (errors) {
                    $('#errNamaPicLenderEdit').text(errors.pic_lender ? errors.pic_lender[
                        0] :
                        '');
                    $('#errNamaPicInternalEdit').text(errors.pic_internal ? errors.pic_internal[0] :
                        '');
                    $('#errNamaLenderEdit').text(errors.nama_lender ? errors.nama_lender[
                        0] :
                        '');
                    $('#errEmailLenderEdit').text(errors.email_lender ? errors.email_lender[
                        0] :
                        '');
                    $('#errTeleponEdit').text(errors.telepon ? errors.telepon[0] : '');
                    $('#errAlamatLengkapEdit').text(errors.alamat_lengkap ? errors.alamat_lengkap[
                        0] :
                        '');
                    $('#errNomor_pksEdit').text(errors.nomor_pks ? errors.nomor_pks[0] : '');
                    $('#errPKSEdit').text(errors.file_pks ? errors.file_pks[0] :
                        '');
                    $('#errLogoLenderEdit').text(errors.logoLender ? errors.logoLender[0] :
                        '');
                    $('#errColorLenderEdit').text(errors.colorLender ? errors.colorLender[0] :
                        '');
                    $('#errProvinsiEdit').text(errors.provinsi ? errors.provinsi[0] : '');
                    $('#errKabupaten_kotaEdit').text(errors.kabupaten ? errors.kabupaten[0] : '');
                    $('#errKecamatanEdit').text(errors.kecamatan ? errors.kecamatan[0] : '');
                    $('#errDesaEdit').text(errors.desa ? errors.desa[0] : '');
                    $('#errBankEdit').text(errors.bank ? errors.bank[0] : '');
                    $('#errANRekeningEdit').text(errors.an_rekening ? errors.an_rekening[0] : '');
                    $('#errNoRekeningEdit').text(errors.no_rekening ? errors.no_rekening[0] : '');
                    $('#errStatusEdit').text(errors.status ? errors.status[0] : '');
                }
            }
        });
    });

    deleteLender = (id) => {
        deleteID = id;
        $('#modalDelete').modal('show');
    }

    buttonDelete = () => {
        $.ajax({
            url: '{!! route('admin.lender.delete') !!}',
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
            },
            error: function (err) {
                toastr.success(err.message)
                table.ajax.reload();
                $("#modalDelete").modal('hide');
                $('#buttonDelete').attr("disabled", false);
                $('#loadingTextDelete').removeClass("spinner-border");
            }
        });
    }

    filePks = (id) => {
        $.ajax({
            url: '{!! route('admin.lender.pks.show') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': id,
            },
            type: 'POST',
            xhrFields: {
                responseType: 'blob'
            },
            success: function (res) {
                const blob = new Blob([res], {
                    type: 'application/pdf'
                });
                const url = window.URL.createObjectURL(blob);
                window.open(url, '_blank');
            },
            error: function (xhr, status, error) {
                console.error("Terjadi kesalahan:", status, error);
                alert("Gagal menampilkan dokumen. Silakan coba lagi.");
            }
        });
    }

    const imageLogo = (id) => {

        $('#image-container').html(
            '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
        );
        $('#imageModal').modal('show');

        $.ajax({
            // Ganti ini dengan route Laravel yang akan dibuat
            url: '{!! route('admin.lender.image.show') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': id,
            },
            type: 'POST',
            dataType: 'json',

            success: function (response) {
                if (response.url) {
                    console.log(response.url);
                    const imageUrl = response.url;
                    const imageHtml = `
                        <img src="${imageUrl}" 
                                alt="Detail Gambar ID: ${id}" 
                                class="img-fluid" 
                                style="max-height: 80vh; max-width: 100%;">
                    `;
                    $('#image-container').html(imageHtml);
                } else {
                    $('#image-container').html(
                        '<p class="text-danger">URL Gambar tidak ditemukan.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                $('#image-container').html(
                    '<p class="text-danger">Gagal memuat gambar. Silakan coba lagi.</p>');
            }
        });
    }


    historyUpdate = (id) => {
        $('#modalHistoryUpdate').modal('show');
        const modalBody = $('#modalHistoryUpdate .modal-body');
        modalBody.html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);

        $.ajax({
            url: '{!! route('admin.lender.update.history') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': id,
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response && response.data && response.data.length > 0) {
                    let fullHtml = '';

                    // Loop melalui setiap item dalam array 'data'
                    response.data.forEach(historyItem => {
                        const datetime = new Date(historyItem.datetime).toLocaleString();
                        const changes = historyItem.data;

                        let tableHtml = `
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Changes from: ${datetime}</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Old Data</th>
                                                    <th>New Data</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                        `;

                        // Loop untuk setiap perubahan dalam item history
                        for (const key in changes) {
                            const row = changes[key];
                            tableHtml += `
                                <tr>
                                    <td><strong>${key.replace(/_/g, ' ').toUpperCase()}</strong></td>
                                    <td>${row.old_data}</td>
                                    <td>${row.new_data}</td>
                                </tr>
                            `;
                        }

                        tableHtml += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        `;

                        fullHtml += tableHtml;
                    });

                    modalBody.html(fullHtml);
                } else {
                    modalBody.html('<p class="text-center text-muted">No update history found.</p>');
                }
            },
            error: function (err) {
                console.error("Error fetching history:", err);
                modalBody.html(
                    '<p class="text-center text-danger">Failed to load data. Please try again.</p>');
            }
        });
    }
</script>
@endpush
