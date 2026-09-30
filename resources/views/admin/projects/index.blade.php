@extends('layout.app')

@section('title', 'Admin Project')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Project</li>
                </ol>
            </div>
            <h4 class="page-title">Project</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-sm btn-primary float-right mb-3" onclick="openCreateModal()">
                    <i class="fa-solid fa-circle-plus"></i> Tambah Project
                </button>

                <table id="projectsTable" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ strtoupper($project->name) }}</td>
                                <td>{{ $project->code }}</td>
                                <td>{{ $project->description ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $project->is_active ? 'success' : 'danger' }}">
                                        {{ $project->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="editProject({{ $project->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteProject({{ $project->id }})">
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

<div class="modal fade" id="projectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectModalTitle">Tambah Project</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="projectForm">
                <div class="modal-body">
                    <input type="hidden" id="projectId">

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama</label>
                            <input class="form-control" name="name" id="name">
                            <small class="text-danger error-name"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kode</label>
                            <input class="form-control" name="code" id="code">
                            <small class="text-danger error-code"></small>
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
                    <button type="submit" class="btn btn-primary" id="saveProjectButton">
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
    $('#projectsTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        language: {
            emptyTable: 'Belum ada project.',
            zeroRecords: 'Project tidak ditemukan.',
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
        $('#projectForm')[0].reset();
        $('#projectId').val('');
        $('#is_active').val('1');
        $('#projectModalTitle').text('Tambah Project');
        $('#projectModal').modal('show');
    }

    function editProject(id) {
        clearErrors();
        $('#projectForm')[0].reset();
        $('#projectId').val(id);
        $('#projectModalTitle').text('Ubah Project');

        $.get('{{ route('admin.projects.show', ':id') }}'.replace(':id', id))
            .done(function (response) {
                var project = response.data;
                $('#name').val(project.name);
                $('#code').val(project.code);
                $('#description').val(project.description);
                $('#is_active').val(project.is_active ? '1' : '0');
                $('#projectModal').modal('show');
            })
            .fail(function () {
                toastr.error('Gagal mengambil detail project.');
            });
    }

    function deleteProject(id) {
        if (!confirm('Hapus project ini?')) {
            return;
        }

        $.ajax({
            url: '{{ route('admin.projects.destroy', ':id') }}'.replace(':id', id),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                toastr.success(response.message);
                window.location.reload();
            },
            error: function () {
                toastr.error('Gagal menghapus project.');
            }
        });
    }

    $('#projectForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#projectId').val();
        var url = id ? '{{ route('admin.projects.update', ':id') }}'.replace(':id', id) : '{{ route('admin.projects.store') }}';
        var method = id ? 'PUT' : 'POST';

        $('#saveProjectButton').attr('disabled', true);
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

                toastr.error('Gagal menyimpan project.');
            },
            complete: function () {
                $('#saveProjectButton').attr('disabled', false);
                $('#saveSpinner').removeClass('spinner-border spinner-border-sm');
            }
        });
    });

    function clearErrors() {
        $('[class*="error-"]').text('');
    }
</script>
@endpush
