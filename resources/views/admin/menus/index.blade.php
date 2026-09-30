@extends('layout.app')

@section('title', 'Admin Menu')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Menu</li>
                </ol>
            </div>
            <h4 class="page-title">Menu</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-sm btn-primary float-right mb-3" onclick="openCreateModal()">
                    <i class="fa-solid fa-circle-plus"></i> Tambah Menu
                </button>

                <table id="menusTable" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>Urutan</th>
                            <th>Label</th>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>Type</th>
                            <th>Icon</th>
                            <th>Route</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($menus as $menu)
                            <tr id="menu-row-{{ $menu->id }}">
                                <td>{{ $menu->sort_order }}</td>
                                <td>{{ $menu->label }}</td>
                                <td>{{ $menu->name }}</td>
                                <td>{{ $menu->parent?->label ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $menu->type === 'group' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($menu->type) }}
                                    </span>
                                </td>
                                <td>{{ $menu->icon ?? '-' }}</td>
                                <td>{{ $menu->route ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $menu->is_active ? 'success' : 'danger' }}">
                                        {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="editMenu({{ $menu->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteMenu({{ $menu->id }})">
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

<div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="menuModalTitle">Tambah Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="menuForm">
                <div class="modal-body">
                    <input type="hidden" id="menuId">

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Parent</label>
                            <select class="form-control" name="parent_id" id="parent_id">
                                <option value="">Tidak Ada Parent</option>
                                @foreach ($parentMenus as $parentMenu)
                                    <option value="{{ $parentMenu->id }}">{{ $parentMenu->label }} ({{ $parentMenu->name }})</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-parent_id"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="menu">Menu</option>
                                <option value="group">Group</option>
                            </select>
                            <small class="text-danger error-type"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Name</label>
                            <input class="form-control" name="name" id="name">
                            <small class="text-danger error-name"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Label</label>
                            <input class="form-control" name="label" id="label">
                            <small class="text-danger error-label"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Icon</label>
                            <input class="form-control" name="icon" id="icon" placeholder="fa-solid fa-house">
                            <small class="text-danger error-icon"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Route</label>
                            <input class="form-control" name="route" id="route" placeholder="admin.dashboard.index">
                            <small class="text-danger error-route"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Urutan</label>
                            <input class="form-control" type="number" min="0" name="sort_order" id="sort_order" value="0">
                            <small class="text-danger error-sort_order"></small>
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
                    <button type="submit" class="btn btn-primary" id="saveMenuButton">
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
    var table = $('#menusTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        language: {
            emptyTable: 'Belum ada menu.',
            zeroRecords: 'Menu tidak ditemukan.',
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
        $('#menuForm')[0].reset();
        $('#menuId').val('');
        $('#sort_order').val(0);
        $('#is_active').val('1');
        $('#type').val('menu');
        $('#parent_id option').prop('disabled', false);
        $('#menuModalTitle').text('Tambah Menu');
        $('#menuModal').modal('show');
    }

    function editMenu(id) {
        clearErrors();
        $('#menuForm')[0].reset();
        $('#menuId').val(id);
        $('#parent_id option').prop('disabled', false);
        $('#parent_id option[value="' + id + '"]').prop('disabled', true);
        $('#menuModalTitle').text('Ubah Menu');

        $.get('{{ route('admin.menus.show', ':id') }}'.replace(':id', id))
            .done(function (response) {
                var menu = response.data;
                $('#parent_id').val(menu.parent_id || '');
                $('#name').val(menu.name);
                $('#label').val(menu.label);
                $('#icon').val(menu.icon);
                $('#route').val(menu.route);
                $('#type').val(menu.type);
                $('#sort_order').val(menu.sort_order);
                $('#is_active').val(menu.is_active ? '1' : '0');
                $('#menuModal').modal('show');
            })
            .fail(function () {
                toastr.error('Gagal mengambil detail menu.');
            });
    }

    function deleteMenu(id) {
        if (!confirm('Hapus menu ini? Submenu di bawahnya juga akan terhapus.')) {
            return;
        }

        $.ajax({
            url: '{{ route('admin.menus.destroy', ':id') }}'.replace(':id', id),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                toastr.success(response.message);
                window.location.reload();
            },
            error: function () {
                toastr.error('Gagal menghapus menu.');
            }
        });
    }

    $('#menuForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#menuId').val();
        var url = id ? '{{ route('admin.menus.update', ':id') }}'.replace(':id', id) : '{{ route('admin.menus.store') }}';
        var method = id ? 'PUT' : 'POST';

        $('#saveMenuButton').attr('disabled', true);
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

                toastr.error('Gagal menyimpan menu.');
            },
            complete: function () {
                $('#saveMenuButton').attr('disabled', false);
                $('#saveSpinner').removeClass('spinner-border spinner-border-sm');
            }
        });
    });

    function clearErrors() {
        $('[class*="error-"]').text('');
    }
</script>
@endpush
