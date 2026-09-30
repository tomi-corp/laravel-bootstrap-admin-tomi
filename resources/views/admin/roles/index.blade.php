@extends('layout.app')

@section('title', 'Admin Role')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Role</li>
                </ol>
            </div>
            <h4 class="page-title">Role</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Role
                    </button>
                </div>

                <table id="rolesTable" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Project</th>
                            <th>Nama Role</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->project?->name ?? '-' }}</td>
                                <td>{{ strtoupper($role->name) }}</td>
                                <td>{{ $role->description ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $role->is_active ? 'success' : 'danger' }}">
                                        {{ $role->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="editRole({{ $role->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteRole({{ $role->id }})">
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

<div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">Tambah Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="roleForm">
                <div class="modal-body">
                    <input type="hidden" id="roleId">

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Project</label>
                            <select class="form-control" name="project_id" id="project_id">
                                <option value="">Pilih Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->code }})</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-project_id"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Role</label>
                            <input class="form-control" name="name" id="name" placeholder="Manager">
                            <small class="text-danger error-name"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Deskripsi</label>
                            <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                            <small class="text-danger error-description"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status</label>
                            <select class="form-control" name="is_active" id="is_active">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                            <small class="text-danger error-is_active"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveRoleButton">
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
    $('#rolesTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        language: {
            emptyTable: 'Belum ada role.',
            zeroRecords: 'Role tidak ditemukan.',
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
        $('#roleForm')[0].reset();
        $('#roleId').val('');
        $('#project_id').val('');
        $('#is_active').val('1');
        $('#roleModalTitle').text('Tambah Role');
        $('#roleModal').modal('show');
    }

    function editRole(id) {
        clearErrors();
        $('#roleForm')[0].reset();
        $('#roleId').val(id);
        $('#roleModalTitle').text('Ubah Role');

        $.get('{{ route('admin.roles.show', ':id') }}'.replace(':id', id))
            .done(function (response) {
                var role = response.data;
                $('#project_id').val(role.project_id);
                $('#name').val(role.name);
                $('#description').val(role.description);
                $('#is_active').val(role.is_active ? '1' : '0');
                $('#roleModal').modal('show');
            })
            .fail(function () {
                toastr.error('Gagal mengambil detail role.');
            });
    }

    function deleteRole(id) {
        if (!confirm('Hapus role ini?')) {
            return;
        }

        $.ajax({
            url: '{{ route('admin.roles.destroy', ':id') }}'.replace(':id', id),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                toastr.success(response.message);
                window.location.reload();
            },
            error: function () {
                toastr.error('Gagal menghapus role.');
            }
        });
    }

    $('#roleForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#roleId').val();
        var url = id ? '{{ route('admin.roles.update', ':id') }}'.replace(':id', id) : '{{ route('admin.roles.store') }}';
        var method = id ? 'PUT' : 'POST';

        $('#saveRoleButton').attr('disabled', true);
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

                toastr.error('Gagal menyimpan role.');
            },
            complete: function () {
                $('#saveRoleButton').attr('disabled', false);
                $('#saveSpinner').removeClass('spinner-border spinner-border-sm');
            }
        });
    });

    function clearErrors() {
        $('[class*="error-"]').text('');
    }
</script>
@endpush
