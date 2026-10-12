@extends('layout.app')

@section('title', 'Admin Hak Akses Menu')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Hak Akses Menu</li>
                </ol>
            </div>
            <h4 class="page-title">Hak Akses Menu</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Hak Akses
                    </button>
                </div>

                <table id="roleMenuPermissionsTable" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Peran</th>
                            <th>Menu</th>
                            <th>Hak Akses Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roleMenuPermissions as $roleMenuPermission)
                            @php
                                $activePermissions = collect($permissions)
                                    ->filter(fn ($permission) => (bool) $roleMenuPermission->{$permission})
                                    ->map(fn ($permission) => str_replace('can_', '', $permission));
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">
                                    <strong>{{ $roleMenuPermission->role?->name ?? '-' }}</strong>
                                    <div class="text-muted small">{{ $roleMenuPermission->role?->project?->name ?? '-' }}</div>
                                </td>
                                <td class="text-left">
                                    <strong>{{ $roleMenuPermission->menu?->name ?? '-' }}</strong>
                                    <div class="text-muted small">{{ $roleMenuPermission->menu?->route ?? '-' }}</div>
                                </td>
                                <td class="text-left">
                                    @forelse ($activePermissions as $permission)
                                        <span class="badge badge-success mb-1">{{ $permission }}</span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="editRoleMenuPermission({{ $roleMenuPermission->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteRoleMenuPermission({{ $roleMenuPermission->id }})">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="roleMenuPermissionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleMenuPermissionModalTitle">Tambah Hak Akses</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="roleMenuPermissionForm">
                <div class="modal-body">
                    <input type="hidden" id="roleMenuPermissionId">

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Peran</label>
                            <select class="form-control" name="role_id" id="role_id">
                                <option value="">Pilih Peran</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }} - {{ $role->project?->name ?? '-' }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-role_id"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Menu</label>
                            <select class="form-control" name="menu_id" id="menu_id">
                                <option value="">Pilih Menu</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }} - {{ $menu->project?->name ?? '-' }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-menu_id"></small>
                        </div>
                    </div>

                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-3 col-sm-4 col-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input permission-checkbox" name="{{ $permission }}" id="{{ $permission }}" value="1">
                                    <label class="custom-control-label" for="{{ $permission }}">{{ ucwords(str_replace(['can_', '_'], ['', ' '], $permission)) }}</label>
                                </div>
                                <small class="text-danger error-{{ $permission }}"></small>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveRoleMenuPermissionButton">
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
    var permissions = @json($permissions);

    $('#roleMenuPermissionsTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        language: {
            emptyTable: 'Belum ada hak akses menu.',
            zeroRecords: 'Hak akses menu tidak ditemukan.',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            paginate: {
                previous: 'Sebelumnya',
                next: 'Berikutnya'
            }
        }
    });

    function openCreateModal() {
        clearErrors();
        $('#roleMenuPermissionForm')[0].reset();
        $('#roleMenuPermissionId').val('');
        $('.permission-checkbox').prop('checked', false);
        $('#roleMenuPermissionModalTitle').text('Tambah Hak Akses');
        $('#roleMenuPermissionModal').modal('show');
    }

    function editRoleMenuPermission(id) {
        clearErrors();
        $('#roleMenuPermissionForm')[0].reset();
        $('#roleMenuPermissionId').val(id);
        $('.permission-checkbox').prop('checked', false);
        $('#roleMenuPermissionModalTitle').text('Ubah Hak Akses');

        $.get('{{ route('admin.role-menu-permissions.show', ':id') }}'.replace(':id', id))
            .done(function (response) {
                var roleMenuPermission = response.data;
                $('#role_id').val(roleMenuPermission.role_id);
                $('#menu_id').val(roleMenuPermission.menu_id);

                permissions.forEach(function (permission) {
                    $('#' + permission).prop('checked', Boolean(roleMenuPermission[permission]));
                });

                $('#roleMenuPermissionModal').modal('show');
            })
            .fail(function () {
                toastr.error('Gagal mengambil detail hak akses.');
            });
    }

    function deleteRoleMenuPermission(id) {
        if (!confirm('Hapus hak akses ini?')) {
            return;
        }

        $.ajax({
            url: '{{ route('admin.role-menu-permissions.destroy', ':id') }}'.replace(':id', id),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                toastr.success(response.message);
                window.location.reload();
            },
            error: function () {
                toastr.error('Gagal menghapus hak akses.');
            }
        });
    }

    $('#roleMenuPermissionForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#roleMenuPermissionId').val();
        var url = id
            ? '{{ route('admin.role-menu-permissions.update', ':id') }}'.replace(':id', id)
            : '{{ route('admin.role-menu-permissions.store') }}';
        var method = id ? 'PUT' : 'POST';

        $('#saveRoleMenuPermissionButton').attr('disabled', true);
        $('#saveSpinner').addClass('spinner-border spinner-border-sm');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function (response) {
                toastr.success(response.message);
                window.location.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                        $('.error-' + key).text(xhr.responseJSON.errors[key][0]);
                    });
                    return;
                }

                toastr.error('Gagal menyimpan hak akses.');
            },
            complete: function () {
                $('#saveRoleMenuPermissionButton').attr('disabled', false);
                $('#saveSpinner').removeClass('spinner-border spinner-border-sm');
            }
        });
    });

    function clearErrors() {
        $('[class*="error-"]').text('');
    }
</script>
@endpush
