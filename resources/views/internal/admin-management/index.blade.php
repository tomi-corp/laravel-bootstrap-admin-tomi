@extends('internal.layouts.app')

@section('title', 'Kelola Admin')

@push('css')
    <style>
        #adminTable th, #adminTable td,
        #auditTable th, #auditTable td { white-space: nowrap; vertical-align: middle; }
        #auditTable td.audit-wrap { white-space: normal; min-width: 240px; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title mb-1">Kelola Admin</h4>
                <p class="text-muted mb-0">Akun yang bisa masuk ke panel: instansi, role, dan entitas (lender/perusahaan/referral) yang diwakili.</p>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabAdmin" role="tab">Daftar Admin</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabAudit" role="tab" id="tabAuditLink">Log Aktivitas</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabAdmin" role="tabpanel">
                            <div class="d-flex justify-content-end mb-3">
                                <button class="btn btn-primary" type="button" onclick="openCreateAdmin()">
                                    <i class="mdi mdi-plus-circle mr-1"></i>Tambah Admin
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="adminTable" class="table table-hover nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Instansi</th>
                                            <th>Role</th>
                                            <th>Entitas</th>
                                            <th>2FA</th>
                                            <th>Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tabAudit" role="tabpanel">
                            <div class="table-responsive">
                                <table id="auditTable" class="table table-hover nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Username</th>
                                            <th>Aktivitas</th>
                                            <th>Keterangan</th>
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
    </div>

    {{-- Modal tambah / ubah admin --}}
    <div class="modal fade" id="adminModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminModalTitle">Tambah Admin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                </div>
                <form id="adminForm" autocomplete="off">
                    <div class="modal-body">
                        <input type="hidden" id="adminId" name="id">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="adminName">Nama</label>
                                <input class="form-control" id="adminName" name="name" placeholder="Nama lengkap">
                                <small class="text-danger" data-error="name"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="adminUsername">Username</label>
                                <input class="form-control" id="adminUsername" name="username" placeholder="Username untuk login">
                                <small class="text-danger" data-error="username"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="adminEmail">Email</label>
                                <input class="form-control" id="adminEmail" name="email" type="email" placeholder="email@contoh.com">
                                <small class="text-danger" data-error="email"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="adminInstansi">Instansi</label>
                                <select class="form-control" id="adminInstansi" name="instansi_id">
                                    <option value="">Pilih instansi</option>
                                    @foreach ($instansis as $instansi)
                                        <option value="{{ $instansi->id }}" data-code="{{ $instansi->code }}">{{ $instansi->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" data-error="instansi_id"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="adminRole">Role</label>
                                <select class="form-control" id="adminRole" name="role_id">
                                    <option value="">Pilih instansi dulu</option>
                                </select>
                                <small class="text-danger" data-error="role_id"></small>
                            </div>
                            <div class="col-md-6 form-group d-none" id="entityGroup">
                                <label for="adminEntity" id="entityLabel">Entitas</label>
                                <select class="form-control" id="adminEntity" name="entity_id"></select>
                                <small class="text-danger" data-error="entity_id"></small>
                            </div>
                            <div class="col-12"><hr class="mt-0"><p class="text-muted small mb-2" id="passwordHint">Minimal 8 karakter, berisi huruf besar, huruf kecil, angka, dan simbol.</p></div>
                            <div class="col-md-6 form-group">
                                <label for="adminPassword">Password</label>
                                <div class="input-group">
                                    <input class="form-control" id="adminPassword" name="password" type="password" autocomplete="new-password">
                                    <div class="input-group-append"><button class="btn btn-outline-secondary" type="button" onclick="togglePassword('adminPassword', this)" title="Tampilkan"><i class="mdi mdi-eye-off"></i></button></div>
                                </div>
                                <small class="text-danger" data-error="password"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="adminPasswordConfirmation">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input class="form-control" id="adminPasswordConfirmation" name="password_confirmation" type="password" autocomplete="new-password">
                                    <div class="input-group-append"><button class="btn btn-outline-secondary" type="button" onclick="togglePassword('adminPasswordConfirmation', this)" title="Tampilkan"><i class="mdi mdi-eye-off"></i></button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit" id="saveAdminButton">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const roles = @json($roles);
        const entities = @json($entities);
        const dtLanguage = {
            processing: 'Memuat...',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 - 0 dari 0 data',
            infoFiltered: '(disaring dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
        };
        let adminTable;
        let auditTable;
        let editingAdmin = false;

        $(function () {
            adminTable = $('#adminTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: '{{ route('internal.admin-management.table') }}', type: 'POST', data: { _token: '{{ csrf_token() }}' } },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'username', name: 'username' },
                    { data: 'email', name: 'email' },
                    { data: 'instansi', name: 'instansi', orderable: false },
                    { data: 'role', name: 'role', orderable: false },
                    { data: 'entity', name: 'entity', orderable: false, searchable: false },
                    { data: 'two_factor', name: 'two_factor', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at', searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[8, 'desc']],
                language: { ...dtLanguage, emptyTable: 'Belum ada admin' }
            });

            // Log aktivitas baru dimuat saat tab-nya dibuka.
            $('#tabAuditLink').on('shown.bs.tab', function () {
                if (auditTable) { auditTable.columns.adjust(); return; }
                auditTable = $('#auditTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: { url: '{{ route('internal.admin-management.audit.table') }}', type: 'POST', data: { _token: '{{ csrf_token() }}' } },
                    columns: [
                        { data: 'created_at', name: 'created_at', searchable: false },
                        { data: 'username', name: 'username', orderable: false, searchable: false },
                        { data: 'activity_type', name: 'activity_type' },
                        { data: 'description', name: 'description', className: 'audit-wrap' },
                        { data: 'ip_address', name: 'ip_address' },
                        { data: 'user_agent', name: 'user_agent', className: 'audit-wrap' }
                    ],
                    order: [[0, 'desc']],
                    language: { ...dtLanguage, emptyTable: 'Belum ada log aktivitas' }
                });
            });

            $('#adminInstansi').on('change', function () { fillRoleAndEntity(); });
            $('#adminForm').on('submit', submitAdmin);
        });

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('mdi-eye', show);
            icon.classList.toggle('mdi-eye-off', !show);
        }

        /** Isi pilihan role sesuai instansi, lalu tampilkan pilihan entitas untuk instansi lender/perusahaan/referral. */
        function fillRoleAndEntity(roleId = '', entityId = '') {
            const option = $('#adminInstansi option:selected');
            const instansiId = Number(option.val());
            const instansiCode = option.data('code');

            const roleOptions = roles.filter(role => role.instansi_id === instansiId)
                .map(role => `<option value="${role.id}">${escapeHtml(role.name)}</option>`);
            $('#adminRole').html(`<option value="">${instansiId ? 'Pilih role' : 'Pilih instansi dulu'}</option>` + roleOptions.join('')).val(roleId);

            const entity = entities[instansiCode];
            if (!entity) {
                $('#entityGroup').addClass('d-none');
                $('#adminEntity').empty();
                return;
            }
            // Referral hanya boleh dimiliki satu akun: tampilkan yang belum punya akun atau milik admin yang sedang diubah.
            const currentAdminId = String($('#adminId').val() || '');
            const items = entity.items.filter(item => item.admin_id === undefined || item.admin_id === null
                || String(item.admin_id) === currentAdminId);
            $('#entityLabel').text(entity.label);
            $('#adminEntity').html(`<option value="">Pilih ${entity.label.toLowerCase()}</option>` + items
                .map(item => `<option value="${item.id}">${escapeHtml(item.name)}</option>`).join('')).val(entityId);
            $('#entityGroup').removeClass('d-none');
        }

        function clearErrors() {
            $('#adminForm [data-error]').text('');
        }

        function openCreateAdmin() {
            editingAdmin = false;
            $('#adminForm')[0].reset();
            $('#adminId').val('');
            clearErrors();
            fillRoleAndEntity();
            $('#adminModalTitle').text('Tambah Admin');
            $('#saveAdminButton').text('Simpan');
            $('#passwordHint').text('Minimal 8 karakter, berisi huruf besar, huruf kecil, angka, dan simbol.');
            $('#adminModal').modal('show');
        }

        function editAdmin(id) {
            $.post('{{ route('internal.admin-management.detail') }}', { id, _token: '{{ csrf_token() }}' })
                .done(response => {
                    const item = response.data;
                    editingAdmin = true;
                    $('#adminForm')[0].reset();
                    clearErrors();
                    $('#adminId').val(item.id);
                    $('#adminName').val(item.name);
                    $('#adminUsername').val(item.username);
                    $('#adminEmail').val(item.email);
                    $('#adminInstansi').val(item.instansi_id ?? '');
                    fillRoleAndEntity(item.role_id ?? '', item.entity_id ?? '');
                    $('#adminModalTitle').text('Ubah Admin');
                    $('#saveAdminButton').text('Perbarui');
                    $('#passwordHint').text('Kosongkan password jika tidak ingin menggantinya.');
                    $('#adminModal').modal('show');
                })
                .fail(error => toastr.error(error.responseJSON?.message || 'Gagal memuat data admin.'));
        }

        function submitAdmin(event) {
            event.preventDefault();
            clearErrors();
            const button = $('#saveAdminButton');
            const label = button.text();
            const url = editingAdmin ? '{{ route('internal.admin-management.update') }}' : '{{ route('internal.admin-management.add') }}';

            button.prop('disabled', true).text('Menyimpan...');
            $.post(url, $(this).serialize() + '&_token={{ csrf_token() }}')
                .done(response => {
                    $('#adminModal').modal('hide');
                    adminTable.ajax.reload(null, false);
                    toastr.success(response.message);
                })
                .fail(error => {
                    const errors = error.responseJSON?.errors;
                    if (errors) {
                        Object.entries(errors).forEach(([field, messages]) => $(`#adminForm [data-error="${field}"]`).text(messages[0]));
                    } else {
                        toastr.error(error.responseJSON?.message || 'Gagal menyimpan admin.');
                    }
                })
                .always(() => button.prop('disabled', false).text(label));
        }

        function deleteAdmin(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus admin ini?')) return;
            $.post('{{ route('internal.admin-management.delete') }}', { id, _token: '{{ csrf_token() }}' })
                .done(response => { adminTable.ajax.reload(null, false); toastr.success(response.message); })
                .fail(error => toastr.error(error.responseJSON?.message || 'Gagal menghapus admin.'));
        }
    </script>
@endpush
