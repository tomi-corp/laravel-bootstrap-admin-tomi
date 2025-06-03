@extends('layout.app')
@section('title', 'Koperasi Simpan Pinjam')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">KSP Perusahaan</li>
                    </ol>
                </div>
                <h4 class="page-title">KSP Perusahaan</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <button type="button" class="btn btn-sm btn-primary float-right waves-effect waves-light"
                        data-toggle="modal" data-target="#modalAdd">
                        <i class="mdi mdi-plus-circle"></i> Tambah Perusahaan
                    </button>



                    <table id="tableListPerusahaan" class="table dt-responsive table-hover mt-4 text-center justify-center"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>ID Perusahaan</th>
                                <th>PIC Perusahaan</th>
                                <th>PIC Internal</th>
                                <th>Nama Perusahaan</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th>Nomor PKS</th>
                                <th>List Karyawan</th>
                                <th>Proses Pengajuan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>


            </div>
        </div>
    </div>




    {{-- modal tambah --}}
    <div class="modal fade" id="modalAdd" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class=" modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Tambah Perusahaan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama PIC Perusahaan</label>
                        <input class="form-control" id="namaPicPerusahaan" placeholder="PIC Perushaan"></input>
                        <small class="text-danger" id="errNamaPicPerusahaan"></small>
                    </div>

                    <div class="form-group">
                        <label>Nama PIC Internal</label>
                        <input class="form-control" id="namaPicInternal" placeholder="PIC Internal"></input>
                        <small class="text-danger" id="errNamaPicInternal"></small>
                    </div>

                    <div class="form-group">
                        <label>Nama Perusahaan</label>
                        <input class="form-control" id="namaPerusahaan" placeholder="Nama Perusahaan"></input>
                        <small class="text-danger" id="errNamaPerusahaan"></small>
                    </div>

                    <div class="form-group">
                        <label>Telepon</label>
                        <input class="form-control" id="telepon" placeholder="62535252" type="number"></input>
                        <small class="text-danger" id="errTelepon"></small>
                    </div>


                    <div class="form-group row">
                        <div class="col-6">
                            <label>Provinsi</label>
                            <select class="form-control" id="provinsiSelect" style="width: 100%">
                                <option disabled>Select</option>
                                @foreach ($provinsis as $provinsi)
                                    <option value="{{ $provinsi->provinsi }}">{{ $provinsi->provinsi }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="errProvinsi"></small>
                        </div>

                        <div class="col-6">
                            <label>Kabupaten / Kota</label>
                            <select class="form-control" id="kabupatenSelect" style="width: 100%">
                                <option disabled>Select</option>
                                {{-- @foreach ($provinsis as $provinsi)
                                    <option value="{{ $provinsi->provinsi }}">{{ $provinsi->provinsi }}</option>
                                @endforeach --}}
                            </select>
                            <small class="text-danger" id="errAlamat"></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" id="alamat" style="height: 70px;" placeholder="Alamat Lengkap"></textarea>
                        <small class="text-danger" id="errAlamat"></small>
                    </div>

                    <div class="form-group">
                        <label>Nomor PKS</label>
                        <input class="form-control" id="nomor_pks" placeholder="42151231" type="number"></input>
                        <small class="text-danger" id="errNomor_pks"></small>
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
        <div class=" modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Tambah Perusahaan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama PIC Perusahaan</label>
                        <input class="form-control" id="namaPicPerusahaanEdit" placeholder="PIC Perushaan"></input>
                        <small class="text-danger" id="errNamaPicPerusahaanEdit"></small>
                    </div>

                    <div class="form-group">
                        <label>Nama PIC Internal</label>
                        <input class="form-control" id="namaPicInternalEdit" placeholder="PIC Internal"></input>
                        <small class="text-danger" id="errNamaPicInternalEdit"></small>
                    </div>


                    <div class="form-group">
                        <label>Nama Perusahaan</label>
                        <input class="form-control" id="namaPerusahaanEdit" placeholder="Nama Perusahaan"></input>
                        <small class="text-danger" id="errNamaPerusahaanEdit"></small>
                    </div>

                    <div class="form-group">
                        <label>Telepon</label>
                        <input class="form-control" id="teleponEdit" placeholder="62535252" type="number"></input>
                        <small class="text-danger" id="errTeleponEdit"></small>

                    </div>


                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" id="alamatEdit" style="height: 70px;" placeholder="Alamat Lengkap"></textarea>
                        <small class="text-danger" id="errAlamatEdit"></small>

                    </div>


                    <div class="form-group">
                        <label>Nomor PKS</label>
                        <input class="form-control" id="nomor_pksEdit" placeholder="42151231" type="number"></input>
                        <small class="text-danger" id="errNomor_pksEdit"></small>
                    </div>

                    <div class="form-group">
                        <label>Status </label>
                        <select class="form-control" id="statusEdit">
                            <option value="active">ACTIVE</option>
                            <option value="nonactive">NONACTIVE</option>
                        </select>
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
                        <p>Apakah anda yakin menghapus perusahaan ini?, ini akan menghapus semua data karyawan dan pinjaman
                            yang bersangkutan dengan perusahaan ini</p>
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

        $('#provinsiSelect').select2();
        $('#kabupatenSelect').select2();

        $('#provinsiSelect').on('change', function() {
            $.ajax({
                url: '{!! route('admin.ksp.kabupaten') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'provinsi': this.value,
                },
                type: 'POST',
                dataType: 'json',
                success: function(result) {
                    // var option = new Option(result.kabupaten, result.kabupaten, true, true);
                    // console.log(option);
                    // $('#kabupatenSelect').append(option).trigger('change');
                    // Create a DOM Option and pre-select by default
                    var newOption = new Option(result.kabupaten, result.kabupaten, true, true);
                    // Append it to the select
                    console.log(newOption)
                    $('#kabupatenSelect').append(newOption).trigger('change');

                    // toastr.success(approve.message)
                    // table.ajax.reload();
                    // $("#modalApprove").modal('hide');
                    // $('#saveVerif').attr("disabled", false);
                    // $('#loadingText').removeClass("spinner-border");
                }
            });
        });




        var table = $('#tableListPerusahaan').DataTable({
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
                "url": '{!! route('admin.ksp.perusahaan.table') !!}',
                "data": function(xx) {
                    return $.extend(xx, dataTable);
                },
            },
            columns: [{
                data: 'id_code_perusahaan'
            }, {
                data: 'pic_perusahaan'
            }, {
                data: 'pic_internal'
            }, {
                data: 'nama'
            }, {
                data: 'telp'
            }, {
                data: 'alamat'
            }, {
                data: 'nomor_pks'
            }, {
                data: 'list_karyawan'
            }, {
                data: 'proses_pengajuan'
            }, {
                data: 'status'
            }, {
                data: 'action'
            }]
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
                success: function(detail) {
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

        $('#saveVerif').on('click', function() {
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
                beforeSend: function() {
                    $('#saveVerif').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                },
                success: function(approve) {
                    toastr.success(approve.message)
                    table.ajax.reload();
                    $("#modalApprove").modal('hide');
                    $('#saveVerif').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                }
            });
        })

        $('#buttonSubmit').on('click', function() {
            $.ajax({
                url: '{!! route('admin.ksp.add.perusahaan') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'pic_perusahaan': $("#namaPicPerusahaan").val(),
                    'pic_internal': $("#namaPicInternal").val(),
                    'nama_perusahaan': $("#namaPerusahaan").val(),
                    'nama_perusahaan': $("#namaPerusahaan").val(),
                    'telepon': $("#telepon").val(),
                    'alamat': $("#alamat").val(),
                    'nomor_pks': $("#nomor_pks").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmit').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                },
                success: function(add) {
                    toastr.success(add.message)
                    table.ajax.reload();
                    $("#modalAdd").modal('hide');
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                },
                error: function(err) {
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                    $('#errNamaPicPerusahaan').text(err.responseJSON.errors.nama_perusahaan);
                    $('#errNamaPicInternal').text(err.responseJSON.errors.nama_perusahaan);
                    $('#errNamaPerusahaan').text(err.responseJSON.errors.nama_perusahaan);
                    $('#errTelepon').text(err.responseJSON.errors.telepon);
                    $('#errAlamat').text(err.responseJSON.errors.alamat);
                    $('#errNomor_pks').text(err.responseJSON.errors.nomor_pks);
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
                success: function(detail) {

                    $("#namaPicPerusahaanEdit").val(detail.data.pic_perusahaan);
                    $("#namaPicInternalEdit").val(detail.data.pic_internal);
                    $("#namaPerusahaanEdit").val(detail.data.nama);
                    $("#teleponEdit").val(detail.data.telp);
                    $("#alamatEdit").val(detail.data.alamat);
                    $("#nomor_pksEdit").val(detail.data.nomor_pks);
                    $("#statusEdit").val(detail.data.status);
                    $('#modalEdit').modal('show');

                }
            });
        }


        $('#buttonSubmitEdit').on('click', function() {
            $.ajax({
                url: '{!! route('admin.ksp.edit.perusahaan') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': editID,
                    'nama_perusahaan': $("#namaPerusahaanEdit").val(),
                    'telepon': $("#teleponEdit").val(),
                    'alamat': $("#alamatEdit").val(),
                    'nomor_pks': $("#nomor_pksEdit").val(),
                    'status': $("#statusEdit").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmitEdit').attr("disabled", true);
                    $('#loadingTextEdit').addClass("spinner-border");
                },
                success: function(add) {
                    toastr.success(add.message)
                    table.ajax.reload();
                    $("#modalEdit").modal('hide');
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                },
                error: function(err) {
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                    $('#errNamaPerusahaanEdit').text(err.responseJSON.errors.nama_perusahaan);
                    $('#errTeleponEdit').text(err.responseJSON.errors.telepon);
                    $('#errAlamatEdit').text(err.responseJSON.errors.alamat);
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
                beforeSend: function() {
                    $('#buttonDelete').attr("disabled", true);
                    $('#loadingTextDelete').addClass("spinner-border");
                },
                success: function(deleteSave) {
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
