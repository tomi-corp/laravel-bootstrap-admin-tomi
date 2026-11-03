@extends('layout.app')

@section('title', 'Perusahaan - Karyawan')

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
            <h4 class="page-title">Daftar Perusahaan</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="header-title mb-3">Pilih Perusahaan</h4>
        <div class="table-responsive">
            <table id="tableEmployeeCompanies" class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Perusahaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function () {
        $('#tableEmployeeCompanies').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            pageLength: 10,
            ajax: {
                url: '{{ route('admin.tomi-dana.employees.companies.table') }}',
                type: 'GET'
            },
            language: {
                search: 'Cari:',
                emptyTable: 'Belum ada data perusahaan',
                zeroRecords: 'Perusahaan tidak ditemukan',
                processing: 'Memuat data...'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama', defaultContent: '-' },
                { data: 'status_label', name: 'status', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: '-' }
            ]
        });
    });
</script>
@endpush
