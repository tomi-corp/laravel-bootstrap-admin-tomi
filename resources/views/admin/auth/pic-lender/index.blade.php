@extends('layout.app')
@section('title', 'Manage PIC Lender')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">Manage PIC Lender</li>
                    </ol>
                </div>
                <h4 class="page-title">Manage PIC Lender</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">List PIC Lender</h4>
                        <button class="btn btn-primary" type="button" onclick="openAddModal()">
                            <i class="mdi mdi-plus"></i> Tambah PIC Lender
                        </button>
                    </div>

                    <table id="tablePicLender" class="table dt-responsive table-hover mt-4 text-center justify-center"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Lender</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formTitle">Tambah PIC Lender</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pic_lender_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lender</label>
                                <select class="form-control" id="id_lender">
                                    <option value="">Pilih Lender</option>
                                    @foreach ($lenders as $lender)
                                        <option value="{{ $lender->id }}">{{ $lender->nama }}</option>
                                    @endforeach
                                </select>
                                <small id="errIdLender" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama</label>
                                <input class="form-control" id="nama" type="text" placeholder="Nama PIC">
                                <small id="errNama" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Username</label>
                                <input class="form-control" id="username" type="text" placeholder="Username">
                                <small id="errUsername" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" id="email" type="email" placeholder="Email">
                                <small id="errEmail" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password</label>
                                <div class="input-group">
                                    <input class="form-control" id="password" type="password" placeholder="Password">
                                    <div class="input-group-append">
                                        <span class="input-group-text" onclick="togglePassword('password', this)">
                                            <i class="mdi mdi-eye-off"></i>
                                        </span>
                                    </div>
                                </div>
                                <small id="errPassword" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Konfirmasi Password</label>
                                <div class="input-group">
                                    <input class="form-control" id="password_confirmation" type="password"
                                        placeholder="Konfirmasi Password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"
                                            onclick="togglePassword('password_confirmation', this)">
                                            <i class="mdi mdi-eye-off"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="button" id="buttonSubmit">
                        Save <div id="loadingText"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete PIC Lender</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    <div class="text-center align-middle">
                        <p>Apakah anda yakin menghapus PIC lender ini?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" type="button" id="buttonDelete" onclick="buttonDelete()">
                        Delete <div id="loadingTextDelete"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        var deleteID = "";
        var dataTable = {
            "_token": "{{ csrf_token() }}",
        };

        togglePassword = (inputId, el) => {
            const input = document.getElementById(inputId);
            const icon = el.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("mdi-eye-off");
                icon.classList.add("mdi-eye");
            } else {
                input.type = "password";
                icon.classList.remove("mdi-eye");
                icon.classList.add("mdi-eye-off");
            }
        }

        clearErrors = () => {
            $('#errIdLender').text('');
            $('#errNama').text('');
            $('#errUsername').text('');
            $('#errEmail').text('');
            $('#errPassword').text('');
        }

        resetForm = () => {
            $('#pic_lender_id').val('');
            $('#id_lender').val('');
            $('#nama').val('');
            $('#username').val('');
            $('#email').val('');
            $('#password').val('');
            $('#password_confirmation').val('');
            $('#formTitle').text('Tambah PIC Lender');
            $('#buttonSubmit').html('Save <div id="loadingText"></div>');
            clearErrors();
        }

        openAddModal = () => {
            resetForm();
            $('#modalForm').modal('show');
        }

        var tablePicLender = $('#tablePicLender').DataTable({
            dom: 'B<"clear">lfrtip',
            buttons: [{
                    extend: 'excel',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                    header: true,
                    title: 'List PIC Lender',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    },
                },
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                    header: true,
                    title: 'List PIC Lender',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    },
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                    header: true,
                    title: 'List PIC Lender',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    },
                }
            ],
            lengthMenu: [10, 50, 100, 500, 1000],
            processing: true,
            responsive: true,
            serverSide: true,
            language: {
                processing: '<i class="dripicons-loading">autorenew</i>'
            },
            ajax: {
                type: "POST",
                url: '{!! route('admin.manage.pic-lender.table') !!}',
                data: function(xx) {
                    return $.extend(xx, dataTable);
                },
            },
            columns: [{
                data: 'created_at'
            }, {
                data: 'updated_at'
            }, {
                data: 'lender'
            }, {
                data: 'nama'
            }, {
                data: 'username'
            }, {
                data: 'email'
            }, {
                data: 'action'
            }]
        });

        $('#buttonSubmit').on('click', function() {
            clearErrors();

            let id = $('#pic_lender_id').val();
            let url = id ? '{!! route('admin.manage.pic-lender.update') !!}' : '{!! route('admin.manage.pic-lender.add') !!}';
            let payload = {
                '_token': "{{ csrf_token() }}",
                'id': id,
                'id_lender': $('#id_lender').val(),
                'nama': $('#nama').val(),
                'username': $('#username').val(),
                'email': $('#email').val(),
                'password': $('#password').val(),
                'password_confirmation': $('#password_confirmation').val(),
            };

            $.ajax({
                url: url,
                data: payload,
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmit').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                },
                success: function(response) {
                    toastr.success(response.message);
                    tablePicLender.ajax.reload();
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                    $('#modalForm').modal('hide');
                    resetForm();
                },
                error: function(err) {
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");

                    if (err.responseJSON && err.responseJSON.errors) {
                        $('#errIdLender').text(err.responseJSON.errors.id_lender);
                        $('#errNama').text(err.responseJSON.errors.nama);
                        $('#errUsername').text(err.responseJSON.errors.username);
                        $('#errEmail').text(err.responseJSON.errors.email);
                        $('#errPassword').text(err.responseJSON.errors.password);
                    }
                }
            });
        });

        editPicLender = (id) => {
            resetForm();

            $.ajax({
                url: '{!! route('admin.manage.pic-lender.detail') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': id,
                },
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    $('#pic_lender_id').val(response.data.id);
                    $('#id_lender').val(response.data.id_lender);
                    $('#nama').val(response.data.nama);
                    $('#username').val(response.data.username);
                    $('#email').val(response.data.email);
                    $('#formTitle').text('Edit PIC Lender');
                    $('#buttonSubmit').html('Update <div id="loadingText"></div>');
                    $('#modalForm').modal('show');
                }
            });
        }

        deletePicLender = (id) => {
            deleteID = id;
            $('#modalDelete').modal('show');
        }

        buttonDelete = () => {
            $.ajax({
                url: '{!! route('admin.manage.pic-lender.delete') !!}',
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
                success: function(response) {
                    toastr.success(response.message);
                    tablePicLender.ajax.reload();
                    $('#modalDelete').modal('hide');
                    $('#buttonDelete').attr("disabled", false);
                    $('#loadingTextDelete').removeClass("spinner-border");
                },
                error: function() {
                    toastr.error('PIC Lender gagal dihapus');
                    $('#modalDelete').modal('hide');
                    $('#buttonDelete').attr("disabled", false);
                    $('#loadingTextDelete').removeClass("spinner-border");
                }
            });
        }
    </script>
@endpush
