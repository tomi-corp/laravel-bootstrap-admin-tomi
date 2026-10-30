@extends('layout.app')

@section('title', 'Karyawan - TomiDana')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.tomi-dana.companies') }}">TomiDana</a></li>
                    <li class="breadcrumb-item active">Karyawan</li>
                </ol>
            </div>
            <h4 class="page-title">{{ $company ? 'Karyawan - ' . strtoupper($company->nama) : 'Karyawan' }}</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="header-title mb-0">{{ $company ? 'Daftar Karyawan ' . strtoupper($company->nama) : 'Daftar Karyawan' }}</h4>
            @if ($company)
                <div class="d-flex justify-content-end flex-wrap">
                    <a href="{{ route('admin.tomi-dana.employees.template') }}" class="btn btn-outline-success mr-1 mb-1">
                        <i class="mdi mdi-download mr-1"></i>Unduh Template
                    </a>
                    <button type="button" class="btn btn-success mr-1 mb-1" data-toggle="modal" data-target="#importEmployeeModal">
                        <i class="mdi mdi-file-excel mr-1"></i>Impor Excel
                    </button>
                    <button type="button" class="btn btn-primary mb-1" onclick="openEmployeeModal()">
                        <i class="mdi mdi-plus mr-1"></i>Tambah Karyawan
                    </button>
                </div>
            @endif
        </div>
        <div class="table-responsive">
            <table id="tableEmployees" class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Karyawan</th>
                        <th>Perusahaan</th>
                        <th>NIK</th>
                        <th>NIP</th>
                        <th>WhatsApp</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@if ($company)
<div class="modal fade" id="importEmployeeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="importEmployeeForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Impor Data Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_list_perusahaan" value="{{ $companyId }}">
                    <div class="form-group">
                        <label for="employeeImportFile">File Excel</label>
                        <input type="file" class="form-control" id="employeeImportFile" name="file" accept=".xlsx,.xls,.csv" required>
                        <small class="form-text text-muted">Format: XLSX, XLS, atau CSV. Maksimal 10 MB.</small>
                    </div>
                    <div class="alert alert-info mb-0">
                        Header kolom: <code>nama_karyawan</code>, <code>tanggal_bergabung</code>, <code>nik</code>, <code>nip</code>, <code>whatsapp</code>, <code>alamat</code>, <code>divisi</code>, <code>departemen</code>, <code>jabatan</code>, <code>status</code>, <code>status_sp</code>, <code>nama_rekening</code>, <code>nomor_bank</code>, <code>max_plafon</code>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Impor Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="employeeForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalTitle">Tambah Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="employeeId">
                    <input type="hidden" name="id_list_perusahaan" value="{{ $companyId }}">
                    <div class="form-row">
                        <div class="form-group col-md-6"><label>Nama Karyawan *</label><input class="form-control" name="nama_karyawan" required></div>
                        <div class="form-group col-md-6"><label>Tanggal Bergabung</label><input type="date" class="form-control" name="tanggal_bergabung"></div>
                        <div class="form-group col-md-6"><label>NIK</label><input class="form-control" name="nik"></div>
                        <div class="form-group col-md-6"><label>NIP</label><input class="form-control" name="nip"></div>
                        <div class="form-group col-md-6"><label>WhatsApp</label><input class="form-control" name="whatsapp"></div>
                        <div class="form-group col-md-6"><label>Jabatan</label><input class="form-control" name="jabatan"></div>
                        <div class="form-group col-md-6"><label>Divisi</label><input class="form-control" name="divisi"></div>
                        <div class="form-group col-md-6"><label>Departemen</label><input class="form-control" name="departement"></div>
                        <div class="form-group col-md-6"><label>Status</label><input class="form-control" name="status" value="active"></div>
                        <div class="form-group col-md-6"><label>Status SP</label><input class="form-control" name="status_sp"></div>
                        <div class="form-group col-md-6"><label>Nomor Rekening</label><input class="form-control" name="nomor_bank"></div>
                        <div class="form-group col-md-6"><label>Maksimal Plafon</label><input type="number" step="1" class="form-control" name="max_plafon"></div>
                        <div class="form-group col-12"><label>Alamat</label><textarea class="form-control" name="alamat" rows="2"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('js')
<script>
    $(function () {
        $('#tableEmployees').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 10,
            ajax: {
                url: '{{ route('admin.tomi-dana.employees.table') }}',
                type: 'GET',
                data: function (data) {
                    data.company_id = @json($companyId);
                }
            },
            language: {
                search: 'Cari:',
                emptyTable: 'Belum ada data karyawan',
                zeroRecords: 'Karyawan tidak ditemukan',
                processing: 'Memuat data...'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_karyawan', name: 'nama_karyawan', defaultContent: '-' },
                { data: 'perusahaan', name: 'perusahaan', orderable: false, defaultContent: '-' },
                { data: 'nik', name: 'nik', defaultContent: '-' },
                { data: 'nip', name: 'nip', defaultContent: '-' },
                { data: 'whatsapp', name: 'whatsapp', defaultContent: '-' },
                { data: 'status_label', name: 'status', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: '-' }
            ]
        });

        window.employeeTable = $('#tableEmployees').DataTable();

        window.openEmployeeModal = function () {
            $('#employeeForm')[0].reset();
            $('#employeeId').val('');
            $('#employeeModalTitle').text('Tambah Karyawan');
            $('#employeeModal').modal('show');
        };

        window.editEmployee = function (id) {
            $.get('{{ url('/backoffice/tomi-dana/employees/detail') }}/' + id, function (response) {
                var data = response.data;
                $('#employeeForm')[0].reset();
                Object.keys(data).forEach(function (key) {
                    $('#employeeForm [name="' + key + '"]').val(data[key] || '');
                });
                $('#employeeId').val(data.id);
                $('#employeeModalTitle').text('Ubah Karyawan');
                $('#employeeModal').modal('show');
            });
        };

        window.deleteEmployee = function (id) {
            if (!confirm('Hapus data karyawan ini?')) return;
            $.post('{{ route('admin.tomi-dana.employees.delete') }}', {
                _token: '{{ csrf_token() }}', id: id
            }).done(function (response) {
                toastr.success(response.message);
                employeeTable.ajax.reload(null, false);
            }).fail(function (xhr) {
                toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Data karyawan gagal dihapus.');
            });
        };

        $('#employeeForm').on('submit', function (event) {
            event.preventDefault();
            var formData = $(this).serialize();
            var url = $('#employeeId').val() ? '{{ route('admin.tomi-dana.employees.update') }}' : '{{ route('admin.tomi-dana.employees.store') }}';
            $.post(url, formData + '&_token={{ csrf_token() }}').done(function (response) {
                toastr.success(response.message);
                $('#employeeModal').modal('hide');
                employeeTable.ajax.reload(null, false);
            }).fail(function (xhr) {
                toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Data karyawan gagal disimpan.');
            });
        });

        $('#importEmployeeForm').on('submit', function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route('admin.tomi-dana.employees.import') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    toastr.success(response.message);
                    $('#importEmployeeModal').modal('hide');
                    $('#importEmployeeForm')[0].reset();
                    employeeTable.ajax.reload(null, false);
                },
                error: function (xhr) {
                    var response = xhr.responseJSON || {};
                    toastr.error(response.message || 'File Excel gagal diimpor.');
                }
            });
        });
    });
</script>
@endpush
