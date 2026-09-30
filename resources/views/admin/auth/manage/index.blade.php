@extends('layout.app')

@section('title', 'Manage Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Manage Admin</li>
                </ol>
            </div>
            <h4 class="page-title">Manage Admin</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Admin
                    </button>
                </div>

                <table id="tableAdmin" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Auth 2FA</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Audit Admin</h5>
                <table id="tableAudit" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
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

<div class="modal fade" id="adminModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminModalTitle">Tambah Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="adminForm">
                <div class="modal-body">
                    <input type="hidden" id="adminId">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Username</label>
                            <input class="form-control" name="username" id="username" placeholder="Nama Admin">
                            <small class="text-danger error-username"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Email</label>
                            <input class="form-control" name="email" id="email" type="email" placeholder="email@domain.com">
                            <small class="text-danger error-email"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Type Admin</label>
                            <select class="form-control" name="type_admin" id="type_admin">
                                <option value="admin">Admin</option>
                                <option value="master">Master</option>
                            </select>
                            <small class="text-danger error-type_admin"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Password</label>
                            <div class="input-group">
                                <input class="form-control" name="password" id="password" type="password" placeholder="Password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                                        <i class="mdi mdi-eye-off"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted password-help d-none">Kosongkan jika password tidak diubah.</small>
                            <small class="text-danger error-password"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Konfirmasi Password</label>
                            <div class="input-group">
                                <input class="form-control" name="password_confirmation" id="password_confirmation" type="password" placeholder="Konfirmasi Password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', this)">
                                        <i class="mdi mdi-eye-off"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-danger error-password_confirmation"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveAdminButton">
                        <span id="saveSpinner" role="status" aria-hidden="true"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var adminTable = $('#tableAdmin').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            url: '{{ route('admin.manage.admin.table') }}',
            data: { _token: '{{ csrf_token() }}' }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'username' },
            { data: 'email' },
            { data: 'type', orderable: false, searchable: false },
            { data: 'google2fa_secret', orderable: false, searchable: false },
            { data: 'created_at' },
            { data: 'updated_at' },
            { data: 'action', orderable: false, searchable: false }
        ],
        language: {
            emptyTable: 'Belum ada admin.',
            zeroRecords: 'Admin tidak ditemukan.',
            processing: 'Memuat data admin...'
        }
    });

    $('#tableAudit').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            url: '{{ route('admin.manage.admin.audit.table') }}',
            data: { _token: '{{ csrf_token() }}' }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'created_at' },
            { data: 'username' },
            { data: 'activity_type', orderable: false, searchable: false },
            { data: 'description' },
            { data: 'ip_address' },
            { data: 'user_agent' }
        ],
        language: {
            emptyTable: 'Belum ada audit admin.',
            zeroRecords: 'Audit admin tidak ditemukan.',
            processing: 'Memuat audit admin...'
        }
    });

    function openCreateModal() {
        clearErrors();
        $('#adminForm')[0].reset();
        $('#adminId').val('');
        $('#type_admin').val('admin');
        $('.password-help').addClass('d-none');
        $('#adminModalTitle').text('Tambah Admin');
        $('#adminModal').modal('show');
    }

    function editAdmin(id) {
        clearErrors();
        $('#adminForm')[0].reset();
        $('#adminId').val(id);
        $('.password-help').removeClass('d-none');
        $('#adminModalTitle').text('Ubah Admin');

        $.post('{{ route('admin.manage.admin.detail') }}', {
            _token: '{{ csrf_token() }}',
            id: id
        }).done(function (response) {
            var admin = response.data;
            $('#username').val(admin.username);
            $('#email').val(admin.email);
            $('#type_admin').val(admin.type);
            $('#adminModal').modal('show');
        }).fail(function () {
            toastr.error('Gagal mengambil detail admin.');
        });
    }

    function deleteAdmin(id) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus admin?',
            text: 'Data admin akan dihapus.',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            $.post('{{ route('admin.manage.admin.delete') }}', {
                _token: '{{ csrf_token() }}',
                id: id
            }).done(function (response) {
                toastr.success(response.message);
                adminTable.ajax.reload();
            }).fail(function () {
                toastr.error('Gagal menghapus admin.');
            });
        });
    }

    $('#adminForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#adminId').val();
        var url = id ? '{{ route('admin.manage.admin.update') }}' : '{{ route('admin.manage.admin.add') }}';
        var payload = $(this).serialize() + '&_token={{ csrf_token() }}';

        if (id) {
            payload += '&id=' + id;
        }

        $('#saveAdminButton').attr('disabled', true);
        $('#saveSpinner').addClass('spinner-border spinner-border-sm');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (response) {
                toastr.success(response.message);
                $('#adminModal').modal('hide');
                adminTable.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                        $('.error-' + key).text(xhr.responseJSON.errors[key][0]);
                    });
                    return;
                }

                toastr.error('Gagal menyimpan admin.');
            },
            complete: function () {
                $('#saveAdminButton').attr('disabled', false);
                $('#saveSpinner').removeClass('spinner-border spinner-border-sm');
            }
        });
    });

    function togglePassword(inputId, button) {
        var input = document.getElementById(inputId);
        var icon = button.querySelector('i');
        var isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('mdi-eye-off', !isPassword);
        icon.classList.toggle('mdi-eye', isPassword);
    }

    function clearErrors() {
        $('[class*="error-"]').text('');
    }
</script>
@endpush
