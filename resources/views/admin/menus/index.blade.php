@extends('layout.app')

@section('title', 'Admin Menu')

@section('content')
@php
    $hasProject = in_array('project_id', $menuColumns, true);
    $hasFeature = in_array('feature_id', $menuColumns, true);
    $hasLabel = in_array('label', $menuColumns, true);
    $hasIcon = in_array('icon', $menuColumns, true);
    $hasRoute = in_array('route', $menuColumns, true);
    $hasType = in_array('type', $menuColumns, true);
    $hasSortOrder = in_array('sort_order', $menuColumns, true);
    $hasStatus = in_array('status', $menuColumns, true);
    $hasIsActive = in_array('is_active', $menuColumns, true);
    $statusOptions = [
        'show' => 'Show',
        'hidden' => 'Hidden',
        'develop' => 'Develop',
        'maintenance' => 'Maintenance',
        'coming_soon' => 'Coming Soon',
        'disabled' => 'Disabled',
    ];
    $statusBadges = [
        'show' => 'success',
        'hidden' => 'secondary',
        'develop' => 'info',
        'maintenance' => 'warning',
        'coming_soon' => 'primary',
        'disabled' => 'danger',
    ];
@endphp
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
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Menu
                    </button>
                </div>

                <table id="menusTable" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            @if ($hasLabel)
                            <th>Label</th>
                            @endif
                            <th>Nama Menu</th>
                            @if ($hasProject)
                            <th>Project</th>
                            @endif
                            @if ($hasFeature)
                            <th>Fitur</th>
                            @endif
                            <th>Parent</th>
                            @if ($hasType)
                            <th>Type</th>
                            @endif
                            @if ($hasIcon)
                            <th>Icon</th>
                            @endif
                            @if ($hasRoute)
                            <th>Route Name</th>
                            @endif
                            @if ($hasStatus || $hasIsActive)
                            <th>Status</th>
                            @endif
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($menus as $menu)
                            <tr id="menu-row-{{ $menu->id }}">
                                <td>{{ $loop->iteration }}</td>
                                @if ($hasLabel)
                                <td>{{ $menu->label }}</td>
                                @endif
                                <td>{{ $menu->name }}</td>
                                @if ($hasProject)
                                <td>{{ $menu->project?->name ?? '-' }}</td>
                                @endif
                                @if ($hasFeature)
                                <td>{{ $menu->feature?->name ?? '-' }}</td>
                                @endif
                                <td>{{ $menu->parent ? ($hasLabel ? $menu->parent->label : $menu->parent->name) : '-' }}</td>
                                @if ($hasType)
                                <td>
                                    <span class="badge badge-{{ $menu->type === 'group' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($menu->type) }}
                                    </span>
                                </td>
                                @endif
                                @if ($hasIcon)
                                <td>{{ $menu->icon ?? '-' }}</td>
                                @endif
                                @if ($hasRoute)
                                <td>{{ $menu->route ?? '-' }}</td>
                                @endif
                                @if ($hasStatus)
                                <td>
                                    <span class="badge badge-{{ $statusBadges[$menu->status] ?? 'secondary' }}">
                                        {{ $statusOptions[$menu->status] ?? ucfirst(str_replace('_', ' ', $menu->status)) }}
                                    </span>
                                </td>
                                @elseif ($hasIsActive)
                                <td>
                                    <span class="badge badge-{{ $menu->is_active ? 'success' : 'danger' }}">
                                        {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                @endif
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
                            <label>Nama Menu</label>
                            <input class="form-control" name="name" id="name" placeholder="Dashboard">
                            <small class="text-danger error-name"></small>
                        </div>
                        @if ($hasProject)
                        <div class="col-md-6 form-group">
                            <label>Project</label>
                            <select class="form-control" name="project_id" id="project_id">
                                <option value="">Tidak Ada Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->code }})</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-project_id"></small>
                        </div>
                        @endif
                        @if ($hasFeature)
                        <div class="col-md-6 form-group">
                            <label>Fitur</label>
                            <select class="form-control" name="feature_id" id="feature_id">
                                <option value="">Tidak Ada Fitur</option>
                                @foreach ($features as $feature)
                                    <option value="{{ $feature->id }}">{{ $feature->name }} ({{ $feature->code }})</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-feature_id"></small>
                        </div>
                        @endif
                        <div class="col-md-6 form-group">
                            <label>Parent Menu</label>
                            <select class="form-control" name="parent_id" id="parent_id">
                                <option value="">Tidak Ada Parent</option>
                                @foreach ($parentMenus as $parentMenu)
                                    <option value="{{ $parentMenu->id }}">
                                        {{ $hasLabel ? $parentMenu->label : $parentMenu->name }} ({{ $parentMenu->name }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-parent_id"></small>
                        </div>
                        @if ($hasLabel)
                        <div class="col-md-6 form-group">
                            <label>Label</label>
                            <input class="form-control" name="label" id="label">
                            <small class="text-danger error-label"></small>
                        </div>
                        @endif
                        @if ($hasIcon)
                        <div class="col-md-6 form-group">
                            <label>Icon</label>
                            <input class="form-control" name="icon" id="icon" placeholder="fa-solid fa-gauge-high">
                            <small class="text-danger error-icon"></small>
                        </div>
                        @endif
                        @if ($hasRoute)
                        <div class="col-md-6 form-group">
                            <label>Route Name</label>
                            <input class="form-control" name="route" id="route" placeholder="admin.dashboard.index">
                            <small class="text-danger error-route"></small>
                        </div>
                        @endif
                        @if ($hasType)
                        <div class="col-md-6 form-group">
                            <label>Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="menu">Menu</option>
                                <option value="group">Group</option>
                            </select>
                            <small class="text-danger error-type"></small>
                        </div>
                        @endif
                        @if ($hasSortOrder)
                        <div class="col-md-6 form-group">
                            <label>Sort Order</label>
                            <input class="form-control" type="number" min="0" name="sort_order" id="sort_order" value="0">
                            <small class="text-danger error-sort_order"></small>
                        </div>
                        @endif
                        @if ($hasStatus)
                        <div class="col-md-6 form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" id="status">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-status"></small>
                        </div>
                        @elseif ($hasIsActive)
                        <div class="col-md-6 form-group">
                            <label>Status</label>
                            <select class="form-control" name="is_active" id="is_active">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                            <small class="text-danger error-is_active"></small>
                        </div>
                        @endif
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
        @if ($hasSortOrder)
        $('#sort_order').val(0);
        @endif
        @if ($hasStatus)
        $('#status').val('show');
        @elseif ($hasIsActive)
        $('#is_active').val('1');
        @endif
        @if ($hasType)
        $('#type').val('menu');
        @endif
        @if ($hasProject)
        $('#project_id').val('');
        @endif
        @if ($hasFeature)
        $('#feature_id').val('');
        @endif
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
                @if ($hasProject)
                $('#project_id').val(menu.project_id || '');
                @endif
                @if ($hasFeature)
                $('#feature_id').val(menu.feature_id || '');
                @endif
                $('#parent_id').val(menu.parent_id || '');
                $('#name').val(menu.name);
                @if ($hasLabel)
                $('#label').val(menu.label);
                @endif
                @if ($hasIcon)
                $('#icon').val(menu.icon);
                @endif
                @if ($hasRoute)
                $('#route').val(menu.route);
                @endif
                @if ($hasType)
                $('#type').val(menu.type);
                @endif
                @if ($hasSortOrder)
                $('#sort_order').val(menu.sort_order);
                @endif
                @if ($hasStatus)
                $('#status').val(menu.status || 'show');
                @elseif ($hasIsActive)
                $('#is_active').val(menu.is_active ? '1' : '0');
                @endif
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
