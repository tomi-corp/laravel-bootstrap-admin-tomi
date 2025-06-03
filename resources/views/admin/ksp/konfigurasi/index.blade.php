@extends('layout.app')
@section('title', 'KSP Konfigurasi')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">KSP Konfigurasi</li>
                    </ol>
                </div>
                <h4 class="page-title">KSP Konfigurasi</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Minimal Pinjaman</label>
                        <input type="number" class="form-control" id="minimal_pinjaman" required
                            placeholder="Minimal Pinjaman" value="{{ $amount->minimal }}" />
                        <small class="text-danger" id="errMinimal_pinjaman"></small>
                    </div>

                    <div class="form-group">
                        <label>Maximal Pinjaman</label>
                        <input type="number" class="form-control" id="maximal_pinjaman" required
                            placeholder="Maximal Pinjaman" value="{{ $amount->maximal }}" />
                        <small class="text-danger" id="errMaximal_pinjaman"></small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary float-right waves-effect waves-light"
                        onclick="amountPinjamanSave()">
                        Save
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-sm btn-primary float-right waves-effect waves-light"
                        data-toggle="modal" data-target="#modalAdd">
                        <i class="mdi mdi-plus-circle"></i> Tambah Konfigurasi
                    </button>


                    <table id="tableTenorPinjaman" class="table dt-responsive table-hover mt-4 text-center justify-center"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Tenor</th>
                                <th>Rate</th>
                                <th>Biaya Admin</th>
                                <th>Iuran Anggota</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>



    {{-- modal tambah --}}
    <div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class=" modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Tambah Konfigurasi
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>Tenor</label>
                        <input class="form-control" id="tenor" placeholder="Tenor" type="number"></input>
                        <small class="text-danger" id="errTenor"></small>
                    </div>

                    <div class="form-group">
                        <label>Rate</label>
                        <input class="form-control" id="rate" placeholder="Rate" type="number"></input>
                        <small class="text-danger" id="errRate"></small>
                    </div>


                    <div class="form-group">
                        <label>Biaya Admin</label>
                        <input class="form-control" id="biayaadmin" placeholder="Biaya Admin" type="number"></input>
                        <small class="text-danger" id="errBiayaAdmin"></small>
                    </div>

                    <div class="form-group">
                        <label>Iuran Anggota</label>
                        <input class="form-control" id="iurananggota" placeholder="Iuran Anggota" type="number"></input>
                        <small class="text-danger" id="errIuranAnggota"></small>
                    </div>



                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="button" id="buttonSubmit" onclick="buttonSubmit()">Save <div
                            id="loadingText">
                        </div></button>
                </div>
            </div>
        </div>
    </div>


    {{-- modal edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class=" modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Edit Konfigurasi
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>Tenor</label>
                        <input class="form-control" id="tenorEdit" placeholder="Tenor" type="number"></input>
                        <small class="text-danger" id="errTenorEdit"></small>
                    </div>

                    <div class="form-group">
                        <label>Rate</label>
                        <input class="form-control" id="rateEdit" placeholder="Rate" type="number"></input>
                        <small class="text-danger" id="errRateEdit"></small>
                    </div>


                    <div class="form-group">
                        <label>Biaya Admin</label>
                        <input class="form-control" id="biayaadminEdit" placeholder="Biaya Admin"
                            type="number"></input>
                        <small class="text-danger" id="errBiayaAdminEdit"></small>
                    </div>

                    <div class="form-group">
                        <label>Iuran Anggota</label>
                        <input class="form-control" id="iurananggotaEdit" placeholder="Iuran Anggota"
                            type="number"></input>
                        <small class="text-danger" id="errIuranAnggotaEdit"></small>
                    </div>



                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="button" id="buttonSubmitEdit"
                        onclick="buttonSubmitEdit()">Save
                        <div id="loadingTextEdit">
                        </div>
                    </button>
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
                    <h5 class="modal-title" id="exampleModalCenterTitle">Delete Konfigurasi
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                </div>
                <div class="modal-body">
                    <div class="text-center align-middle">
                        <span>Apakah anda yakin menghapus konfigurasi ini?</span>

                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" type="button" id="buttonDelete" onclick="buttonDelete()">Delete <div
                            id="loadingTextDelete">
                        </div></button>
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

        var dataTable = {
            "_token": "{{ csrf_token() }}",
        };
        var table = $('#tableTenorPinjaman').DataTable({
            dom: 'B<"clear">lfrtip',
            buttons: [{
                    extend: 'excel',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                    header: false,
                    exportOptions: {
                        columns: [0, 1, 2]
                    },
                },
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                    header: false,
                    exportOptions: {
                        columns: [0, 1, 2]
                    },
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                    header: false,
                    exportOptions: {
                        columns: [0, 1, 2]
                    },
                }
            ],
            "lengthMenu": [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
            processing: true,
            responsive: true,
            serverSide: true,
            language: {
                processing: '<i class="dripicons-loading">autorenew</i>'
            },
            ajax: {
                "type": "POST",
                "url": '{!! route('admin.ksp.konfigurasi.table') !!}',
                "data": function(xx) {
                    return $.extend(xx, dataTable);
                },
            },
            columns: [{
                data: 'tenor'
            }, {
                data: 'rate'
            }, {
                data: 'biaya_admin'
            }, {
                data: 'iuran_anggota'
            }, {
                data: 'action'
            }]
        });

        deleteKonfigurasi = (id) => {
            deleteID = id;
            $('#modalDelete').modal('show');
        }

        edit = (id) => {
            editID = id;
            $.ajax({
                url: '{!! route('admin.ksp.konfigurasi.detail') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': id
                },
                type: 'POST',
                dataType: 'json',
                success: function(detail) {
                    $('#tenorEdit').val(detail.data.tenor);
                    $('#rateEdit').val(detail.data.rate);
                    $('#biayaadminEdit').val(detail.data.biaya_admin);
                    $('#iurananggotaEdit').val(detail.data.iuran_anggota);
                    $('#modalEdit').modal('show');
                }
            });

        }

        buttonDelete = () => {
            $.ajax({
                url: '{!! route('admin.ksp.konfigurasi.delete') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': deleteID
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonDelete').attr("disabled", true);
                    $('#loadingTextDelete').addClass("spinner-border");
                },
                success: function(saveConfig) {
                    toastr.success(saveConfig.message)
                    // listMenu();
                    table.ajax.reload();
                    $("#modalDelete").modal('hide');
                    $('#buttonDelete').attr("disabled", false);
                    $('#loadingTextDelete').removeClass("spinner-border");
                },
                error: function(saveConfig) {
                    $('#buttonDelete').attr("disabled", false);
                    $('#loadingTextDelete').removeClass("spinner-border");
                }
            });
        }

        buttonSubmit = () => {
            $.ajax({
                url: '{!! route('admin.ksp.konfigurasi.add') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'tenor': $("#tenor").val(),
                    'rate': $("#rate").val(),
                    'biaya_admin': $("#biayaadmin").val(),
                    'iuran_anggota': $("#iurananggota").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmit').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                },
                success: function(saveConfig) {
                    toastr.success(saveConfig.message)
                    // listMenu();
                    table.ajax.reload();
                    $("#modalAdd").modal('hide');
                    $('#errTenor').text();
                    $('#errRate').text();
                    $('#errBiayaAdmin').text();
                    $('#errIuranAnggota').text();
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                },
                error: function(saveConfig) {
                    $('#errTenor').text(saveConfig.responseJSON.errors.tenor);
                    $('#errRate').text(saveConfig.responseJSON.errors.rate);
                    $('#errBiayaAdmin').text(saveConfig.responseJSON.errors.biaya_admin);
                    $('#errIuranAnggota').text(saveConfig.responseJSON.errors.iuran_anggota);
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                }
            });
        }

        buttonSubmitEdit = () => {
            $.ajax({
                url: '{!! route('admin.ksp.konfigurasi.edit') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': editID,
                    'tenor': $("#tenorEdit").val(),
                    'rate': $("#rateEdit").val(),
                    'biaya_admin': $("#biayaadminEdit").val(),
                    'iuran_anggota': $("#iurananggotaEdit").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmitEdit').attr("disabled", true);
                    $('#loadingTextEdit').addClass("spinner-border");
                },
                success: function(saveConfig) {
                    toastr.success(saveConfig.message)
                    // listMenu();
                    table.ajax.reload();
                    $("#modalEdit").modal('hide');
                    $('#errTenorEdit').text();
                    $('#errRateEdit').text();
                    $('#errBiayaAdminEdit').text();
                    $('#errIuranAnggotaEdit').text();
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                },
                error: function(saveConfig) {
                    $('#errTenorEdit').text(saveConfig.responseJSON.errors.tenor);
                    $('#errRateEdit').text(saveConfig.responseJSON.errors.rate);
                    $('#errBiayaAdminEdit').text(saveConfig.responseJSON.errors.biaya_admin);
                    $('#errIuranAnggotaEdit').text(saveConfig.responseJSON.errors.iuran_anggota);
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                }
            });
        }

        amountPinjamanSave = () => {
            $.ajax({
                url: '{!! route('admin.ksp.konfigurasi.amount') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'minimal': $("#minimal_pinjaman").val(),
                    'maximal': $("#maximal_pinjaman").val(),
                },
                type: 'POST',
                dataType: 'json',
                success: function(amountSubmit) {
                    toastr.success(amountSubmit.message)
                },
                error: function(amountSubmitError) {
                    $('#errMinimal_pinjaman').text(amountSubmitError.responseJSON.errors.minimal);
                    $('#errMaximal_pinjaman').text(amountSubmitError.responseJSON.errors.maximal);
                }
            });
        }
    </script>
@endpush
