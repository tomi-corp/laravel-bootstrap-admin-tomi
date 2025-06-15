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
                <h4 class="page-title">KSP Konfigurasi {{ $list_perusahaan->nama }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">

                    <div class="form-group">
                        <label>Iuran Anggota</label>
                        <input type="number" class="form-control" id="iuran_anggota" required placeholder="Iuran Anggota"
                            value={{ $list_perusahaan->amount_pinjaman ? $list_perusahaan->amount_pinjaman->iuran_anggota : 0 }} />
                        <small class="text-danger" id="errIuran_anggota"></small>
                    </div>

                    <div class="form-group">
                        <label>Minimal Pinjaman</label>
                        <input type="number" class="form-control" id="minimal_pinjaman" required
                            placeholder="Minimal Pinjaman"
                            value={{ $list_perusahaan->amount_pinjaman ? $list_perusahaan->amount_pinjaman->minimal : 0 }} />
                        <small class="text-danger" id="errMinimal_pinjaman"></small>
                    </div>

                    <div class="form-group">
                        <label>Maximal Pinjaman</label>
                        <input type="number" class="form-control" id="maximal_pinjaman" required
                            placeholder="Maximal Pinjaman"
                            value={{ $list_perusahaan->amount_pinjaman ? $list_perusahaan->amount_pinjaman->maximal : 0 }} />
                        <small class="text-danger" id="errMaximal_pinjaman"></small>
                    </div>

                    <div class="form-group">
                        <label>Maximal Persentase</label>
                        <input type="number" class="form-control" id="maximal_persentase" required
                            placeholder="Maximal Persentase"
                            value={{ $list_perusahaan->amount_pinjaman ? $list_perusahaan->amount_pinjaman->maximal_persentase : 0 }} />
                        <small class="text-danger" id="errMaximal_persentase"></small>
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
                                <th>Biaya Layanan</th>
                                <th>Minimal Nominal</th>
                                <th>Maximal Nominal</th>
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
                        <label>Biaya Layanan</label>
                        <input class="form-control" id="biayalayanan" placeholder="Biaya Layanan" type="number"></input>
                        <small class="text-danger" id="errBiayalayanan"></small>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Minimal Nominal</label>
                                <input class="form-control" id="minimalNominalTenor" placeholder="Minimal Nominal Tenor"
                                    type="number"></input>
                                <small class="text-danger" id="errMinimalNominalTenor"></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Maximal Nominal</label>
                                <input class="form-control" id="maximalNominalTenor" placeholder="Maximal Nominal Tenor"
                                    type="number"></input>
                                <small class="text-danger" id="errMaximalNominalTenor"></small>
                            </div>
                        </div>
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
                        <label>Biaya Layanan</label>
                        <input class="form-control" id="biayalayananEdit" placeholder="Biaya Layanan"
                            type="number"></input>
                        <small class="text-danger" id="errBiayaLayananEdit"></small>
                    </div>


                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Minimal Nominal</label>
                                <input class="form-control" id="minimalNominalTenorEdit"
                                    placeholder="Minimal Nominal Tenor" type="number"></input>
                                <small class="text-danger" id="errMinimalNominalTenorEdit"></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Maximal Nominal</label>
                                <input class="form-control" id="maximalNominalTenorEdit"
                                    placeholder="Maximal Nominal Tenor" type="number"></input>
                                <small class="text-danger" id="errMaximalNominalTenorEdit"></small>
                            </div>
                        </div>
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
            "id": "{{ $list_perusahaan->id }}",
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
                data: 'biaya_layanan'
            }, {
                data: 'minimal_nominal'
            }, {
                data: 'maximal_nominal'
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
                    $('#biayalayananEdit').val(detail.data.biaya_layanan);
                    $('#minimalNominalTenorEdit').val(detail.data.minimal_nominal);
                    $('#maximalNominalTenorEdit').val(detail.data.maximal_nominal);
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
                    'id': "{{ $list_perusahaan->id }}",
                    'tenor': $("#tenor").val(),
                    'rate': $("#rate").val(),
                    'biaya_admin': $("#biayaadmin").val(),
                    'biaya_layanan': $("#biayalayanan").val(),
                    'minimal_nominal': $("#minimalNominalTenor").val(),
                    'maximal_nominal': $("#maximalNominalTenor").val(),
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
                    $('#errBiayalayanan').text();
                    $('#errMinimalNominalTenor').text();
                    $('#errMaximalNominalTenor').text();
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                },
                error: function(saveConfig) {
                    $('#errTenor').text(saveConfig.responseJSON.errors.tenor);
                    $('#errRate').text(saveConfig.responseJSON.errors.rate);
                    $('#errBiayaAdmin').text(saveConfig.responseJSON.errors.biaya_admin);
                    $('#errBiayaLayanan').text(saveConfig.responseJSON.errors.biaya_layanan);
                    $('#errMinimalNominalTenor').text(saveConfig.responseJSON.errors.minimal_nominal);
                    $('#errMaximalNominalTenor').text(saveConfig.responseJSON.errors.maximal_nominal);
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
                    'biaya_layanan': $("#biayalayananEdit").val(),
                    'minimal_nominal': $("#minimalNominalTenorEdit").val(),
                    'maximal_nominal': $("#maximalNominalTenorEdit").val(),
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
                    $('#errBiayaLayananEdit').text();

                    $('#errMinimalNominalTenorEdit').text();
                    $('#errMaximalNominalTenorEdit').text();

                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                },
                error: function(saveConfig) {
                    $('#errTenorEdit').text(saveConfig.responseJSON.errors.tenor);
                    $('#errRateEdit').text(saveConfig.responseJSON.errors.rate);
                    $('#errBiayaAdminEdit').text(saveConfig.responseJSON.errors.biaya_admin);
                    $('#errBiayaLayananEdit').text(saveConfig.responseJSON.errors.biaya_layanan);
                    $('#errMinimalNominalTenorEdit').text(saveConfig.responseJSON.errors.minimal_nominal);
                    $('#errMaximalNominalTenorEdit').text(saveConfig.responseJSON.errors.maximal_nominal);
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
                    'id': "{{ $list_perusahaan->id }}",
                    'iuran_anggota': $("#iuran_anggota").val(),
                    'minimal': $("#minimal_pinjaman").val(),
                    'maximal': $("#maximal_pinjaman").val(),
                    'maximal_persentase': $("#maximal_persentase").val(),
                },
                type: 'POST',
                dataType: 'json',
                success: function(amountSubmit) {
                    toastr.success(amountSubmit.message)
                },
                error: function(amountSubmitError) {
                    $('#errIuran_anggota').text(amountSubmitError.responseJSON.errors.iuran_anggota);
                    $('#errMinimal_pinjaman').text(amountSubmitError.responseJSON.errors.minimal);
                    $('#errMaximal_pinjaman').text(amountSubmitError.responseJSON.errors.maximal);
                    $('#errMaximal_persentase').text(amountSubmitError.responseJSON.errors
                        .maximal_persentase);
                }
            });
        }
    </script>
@endpush
