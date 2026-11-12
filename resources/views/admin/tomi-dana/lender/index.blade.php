@extends('layout.app')

@section('title', 'Lender TomiDana')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">TomiDana</li>
                    <li class="breadcrumb-item active">Lender</li>
                </ol>
            </div>
            <h4 class="page-title">Lender TomiDana</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="header-title">Daftar Lender TomiDana</h4>
        <p class="text-muted">Daftar lender yang memiliki fitur TomiDana pada pengaturan fitur lender.</p>
        <div>
            <table id="tableLenderTomiDana" class="table table-hover table-striped text-center nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Lender</th>
                        <th>Nama Lender</th>
                        <th>PIC Lender</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Bank</th>
                        <th>Nama Rekening</th>
                        <th>No. Rekening</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let lenderTable;

    $(function () {
        lenderTable = $('#tableLenderTomiDana').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            dom: 'B<"row mt-2"<"col-md-6"l><"col-md-6"f>>rtip',
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: '<i class="mdi mdi-content-copy"></i> Copy',
                    className: 'btn btn-sm btn-secondary mr-1',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="mdi mdi-file-excel"></i> Excel',
                    className: 'btn btn-sm btn-success mr-1',
                    title: 'Daftar Lender TomiDana',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'print',
                    text: '<i class="mdi mdi-printer"></i> Print',
                    className: 'btn btn-sm btn-info mr-1',
                    title: 'Daftar Lender TomiDana',
                    exportOptions: { columns: ':not(:last-child)' }
                }
            ],
            ajax: {
                url: '{{ route('admin.tomi-dana.lender.table') }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'id_code_lender', name: 'id_code_lender', defaultContent: '-' },
                { data: 'nama', name: 'nama', defaultContent: '-' },
                { data: 'pic_lender', name: 'pic_lender', defaultContent: '-' },
                { data: 'email_lender', name: 'email_lender', defaultContent: '-' },
                { data: 'telp', name: 'telp', defaultContent: '-' },
                { data: 'bank', name: 'bank', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'nama_rekening', name: 'nama_rekening', defaultContent: '-' },
                { data: 'no_rekening', name: 'no_rekening', defaultContent: '-' },
                { data: 'status_label', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
