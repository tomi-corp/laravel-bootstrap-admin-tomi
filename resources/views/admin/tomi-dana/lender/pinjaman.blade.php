@extends('layout.app')

@section('title', 'Seluruh Pinjaman TomiDana')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">TomiDana</li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tomi-dana.lender') }}">Lender</a></li>
                    <li class="breadcrumb-item active">Seluruh Pinjaman</li>
                </ol>
            </div>
            <h4 class="page-title">{{ $lender ? 'Pinjaman Lender ' . strtoupper($lender->nama) : 'Pinjaman TomiDana' }}</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <a href="{{ route('admin.tomi-dana.lender') }}" class="btn btn-sm btn-secondary float-right mb-3">
            <i class="mdi mdi-arrow-left"></i> Kembali ke Lender
        </a>
        <h4 class="header-title">Daftar Periode Pinjaman</h4>
        <p class="text-muted">Tampilkan ringkasan pinjaman berdasarkan periode pengajuan.</p>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="filterStartMonth">Bulan Awal</label>
                <input type="month" id="filterStartMonth" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="filterEndMonth">Bulan Akhir</label>
                <input type="month" id="filterEndMonth" class="form-control">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" id="resetLoanFilter" class="btn btn-secondary">
                    <i class="mdi mdi-refresh"></i> Reset Filter
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="tableLenderLoans" class="table table-hover table-striped text-center" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Periode</th>
                        <th>Jumlah Pinjaman</th>
                        <th>Total Nominal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBorrowers" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Peminjam <span id="borrowerCompanyName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableBorrowers" class="table table-hover table-striped text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Peminjam</th>
                                <th>Perusahaan</th>
                                <th>Nominal</th>
                                <th>Tenor</th>
                                <th>Status</th>
                                <th>Tanggal Pengajuan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function () {
        const loanTable = $('#tableLenderLoans').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route('admin.tomi-dana.lender.pinjaman.table') }}',
                type: 'POST',
                data: function (data) {
                    data.start_month = $('#filterStartMonth').val();
                    data.end_month = $('#filterEndMonth').val();
                    data._token = '{{ csrf_token() }}';
                    data.lender_id = @json(request('lender_id'));
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'bulan_label', name: 'bulan', searchable: false },
                { data: 'total_pinjaman', name: 'total_pinjaman', searchable: false },
                { data: 'total_nominal', name: 'total_nominal', searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'asc']]
        });

        $('#filterStartMonth, #filterEndMonth').on('change', function () {
            loanTable.ajax.reload();
        });

        $('#resetLoanFilter').on('click', function () {
            $('#filterStartMonth, #filterEndMonth').val('');
            loanTable.ajax.reload();
        });

        let borrowerTable;
        let borrowerMonth;

        window.showBorrowers = function (month) {
            borrowerMonth = month;
            $('#borrowerCompanyName').text(month);
            $('#modalBorrowers').modal('show');

            if ($.fn.DataTable.isDataTable('#tableBorrowers')) {
                borrowerTable.ajax.reload();
                return;
            }

            borrowerTable = $('#tableBorrowers').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('admin.tomi-dana.lender.pinjaman.detail.table') }}',
                    type: 'POST',
                    data: function (data) {
                        data._token = '{{ csrf_token() }}';
                        data.lender_id = @json(request('lender_id'));
                        data.start_month = borrowerMonth;
                        data.end_month = borrowerMonth;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_karyawan', name: 'data_karyawan.nama_karyawan' },
                    { data: 'perusahaan', name: 'perusahaan.nama' },
                    { data: 'nominal', name: 'nominal' },
                    { data: 'tenor_label', name: 'tenor' },
                    { data: 'status_label', name: 'status' },
                    { data: 'tanggal_pengajuan', name: 'created_at' }
                ],
                order: [[6, 'desc']]
            });
        };
    });
</script>
@endpush
