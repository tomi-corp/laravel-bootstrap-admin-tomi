@extends('layout.app')
@section('title', 'Manage Admin')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">Manage Admin</li>
                    </ol>
                </div>
                <h4 class="page-title">Manage Admin</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4>Tambah Admin</h4>

                            <div class="form-group">
                                <label>Username</label>
                                <input class="form-control" name="nama" id="username" type="text"
                                    placeholder="Nama Admin">
                                </input>
                                <small id="errUsername" class="text-danger"></small>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" name="email" id="email" type="email"
                                    placeholder="Email">
                                </input>
                                <small id="errEmail" class="text-danger"></small>
                            </div>

                            <div class="form-group">
                                <label>Type Admin</label>
                                <select class="form-control" id="type_admin">
                                    <option value="master">Master</option>
                                    <option value="master">Admin</option>
                                </select>
                                <small id="errTypeAdmin" class="text-danger"></small>
                            </div>

                            <div class="form-group">
                                <label>password</label>
                                <div class="input-group">
                                    <input class="form-control" name="password" id="password" type="password"
                                        placeholder="Password">
                                    <div class="input-group-append">
                                        <span class="input-group-text" onclick="togglePassword('password', this)">
                                            <i class="mdi mdi-eye-off"></i>
                                        </span>
                                    </div>
                                </div>
                                <small id="errPassword" class="text-danger"></small>
                            </div>

                            <div class="form-group">
                                <label>Konfirmasi password</label>
                                <div class="input-group">
                                    <input class="form-control" name="password_confirmation" id="password_confirmation"
                                        type="password" placeholder="Konfirmasi Password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"
                                            onclick="togglePassword('password_confirmation', this)">
                                            <i class="mdi mdi-eye-off"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-primary float-right" type="button" id="buttonSubmit">Save <div
                                    id="loadingText">
                                </div></button>

                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">

                            <table id="tableAdmin" class="table dt-responsive table-hover mt-4 text-center justify-center"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Created AT</th>
                                        <th>Last Updated AT</th>
                                        <th>Auth 2 FA</th>
                                        <th>Username</th>
                                        <th>email</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <table id="tableAudit" class="table dt-responsive table-hover mt-4 text-center justify-center"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>DateTime</th>
                                        <th>Username</th>
                                        <th>Activity</th>
                                        <th>Description</th>
                                        <th>IP Address</th>
                                        <th>User Agent</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
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
                    <h5 class="modal-title" id="exampleModalCenterTitle">Delete Admin
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="text-center align-middle">
                        <p>Apakah anda yakin menghapus admin ini?</p>
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
        var editID = "";
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


        var table = $('#tableAdmin').DataTable({
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
            responsive: true,
            serverSide: true,
            language: {
                processing: '<i class="dripicons-loading">autorenew</i>'
            },
            ajax: {
                "type": "POST",
                "url": '{!! route('admin.manage.admin.table') !!}',
                "data": function(xx) {
                    return $.extend(xx, dataTable);
                },
            },
            columns: [{
                data: 'created_at'
            }, {
                data: 'updated_at'
            }, {
                data: 'google2fa_secret'
            }, {
                data: 'username'
            }, {
                data: 'email'
            }, {
                data: 'type'
            }, {
                data: 'action'
            }]
        });

        var table = $('#tableAudit').DataTable({
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
            responsive: true,
            serverSide: true,
            language: {
                processing: '<i class="dripicons-loading">autorenew</i>'
            },
            ajax: {
                "type": "POST",
                "url": '{!! route('admin.manage.admin.audit.table') !!}',
                "data": function(xx) {
                    return $.extend(xx, dataTable);
                },
            },
            columns: [{
                data: 'created_at'
            }, {
                data: 'username'
            }, {
                data: 'activity_type'
            }, {
                data: 'description'
            }, {
                data: 'ip_address'
            }, {
                data: 'user_agent'
            }]
        });



        $('#buttonSubmit').on('click', function() {
            $.ajax({
                url: '{!! route('admin.manage.admin.add') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",

                    'username': $("#username").val(),
                    'email': $("#email").val(),
                    'type_admin': $("#type_admin").val(),
                    'password': $("#password").val(),
                    'password_confirmation': $("#password_confirmation").val(),
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
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                    $("#username").val('');
                    $("#email").val('');
                    $("#type_admin").val('');
                    $("#password").val('');
                    $("#password_confirmation").val('');

                },
                error: function(err) {
                    console.log(err.responseJSON.errors.password);
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                    $('#errUsername').text(err.responseJSON.errors.username);
                    $('#errEmail').text(err.responseJSON.errors.email);
                    $('#errTypeAdmin').text(err.responseJSON.errors.type_admin);
                    $('#errPassword').text(err.responseJSON.errors.password);
                }
            });
        })


        deleteAdmin = (id) => {
            deleteID = id;
            $('#modalDelete').modal('show');
        }

        buttonDelete = () => {
            $.ajax({
                url: '{!! route('admin.manage.admin.delete') !!}',
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
                },
                error: function(err) {
                    toastr.success(err.message)
                    table.ajax.reload();
                    $("#modalDelete").modal('hide');
                    $('#buttonDelete').attr("disabled", false);
                    $('#loadingTextDelete').removeClass("spinner-border");
                }
            });
        }
    </script>
@endpush
