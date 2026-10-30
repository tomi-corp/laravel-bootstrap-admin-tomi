@extends('layout.app')

@section('title', $company ? 'Pembayaran Angsuran - ' . strtoupper($company->nama) : 'Pembayaran Angsuran TomiDana')

@push('css')
<style>
    .repayment-summary-card {
        border: 1px solid #edf0f5;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(31, 45, 61, .04);
    }

    .repayment-summary-card .icon-box {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 21px;
    }

    .repayment-summary-card .summary-label {
        color: #98a6ad;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .repayment-summary-card .summary-value {
        color: #323a46;
        font-size: 21px;
        font-weight: 700;
    }

    .repayment-filter .form-control,
    .repayment-filter .custom-select {
        min-height: 38px;
    }

    .repayment-table th {
        background: #f8f9fb;
        border-top: 0;
        color: #6c757d;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .repayment-table td {
        color: #495057;
        vertical-align: middle;
    }

    .repayment-table .empty-state {
        color: #98a6ad;
        padding: 56px 20px;
    }

    .repayment-table .empty-state i {
        color: #c8d0d8;
        font-size: 40px;
    }

    .repayment-period-group td {
        background: #eef4fb;
        color: #3b6ea8;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.tomi-dana.companies') }}">TomiDana</a></li>
                    <li class="breadcrumb-item active">Pembayaran Angsuran</li>
                </ol>
            </div>
            <h4 class="page-title">{{ $company ? 'Pembayaran Angsuran - ' . strtoupper($company->nama) : 'Pembayaran Angsuran TomiDana' }}</h4>
        </div>
    </div>
</div>

@if (! $company)
<div class="card">
    <div class="card-body">
        <h4 class="header-title mb-3">Daftar Perusahaan</h4>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Perusahaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $companyItem)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ strtoupper($companyItem->nama ?? '-') }}</td>
                            <td>
                                <span class="badge badge-{{ $companyItem->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ strtoupper($companyItem->status ?? '-') }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info btn-view-repayments" data-company-id="{{ $companyItem->id }}" data-company-name="{{ $companyItem->nama }}">
                                    <i class="mdi mdi-receipt-text-outline mr-1"></i>Lihat Angsuran
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada data perusahaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="repaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repaymentModalTitle">Daftar Angsuran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableCompanyRepayments" class="table table-hover repayment-table mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Peminjam</th>
                                <th>Nomor Pinjaman</th>
                                <th>Lender</th>
                                <th>Perusahaan</th>
                                <th>Angsuran Ke-</th>
                                <th>Periode</th>
                                <th>Jatuh Tempo</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else
@php
    $repayments = collect($repayments ?? []);
    $totalRepayments = $totalRepayments ?? $repayments->count();
    $paidRepayments = $paidRepayments ?? $repayments->filter(fn ($item) => strtolower((string) data_get($item, 'status')) === 'lunas')->count();
    $pendingRepayments = $pendingRepayments ?? $repayments->filter(fn ($item) => in_array(strtolower((string) data_get($item, 'status')), ['menunggu', 'pending', 'belum lunas']))->count();
    $overdueRepayments = $overdueRepayments ?? $repayments->filter(fn ($item) => strtolower((string) data_get($item, 'status')) === 'terlambat')->count();
@endphp

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card repayment-summary-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-soft-primary text-primary mr-3"><i class="mdi mdi-calendar-check"></i></div>
                    <div>
                        <div class="summary-label">Total Angsuran</div>
                        <div class="summary-value">{{ number_format($totalRepayments, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card repayment-summary-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-soft-success text-success mr-3"><i class="mdi mdi-check-circle-outline"></i></div>
                    <div>
                        <div class="summary-label">Sudah Lunas</div>
                        <div class="summary-value">{{ number_format($paidRepayments, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card repayment-summary-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-soft-warning text-warning mr-3"><i class="mdi mdi-clock-outline"></i></div>
                    <div>
                        <div class="summary-label">Menunggu Pembayaran</div>
                        <div class="summary-value">{{ number_format($pendingRepayments, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card repayment-summary-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-soft-danger text-danger mr-3"><i class="mdi mdi-alert-circle-outline"></i></div>
                    <div>
                        <div class="summary-label">Terlambat</div>
                        <div class="summary-value">{{ number_format($overdueRepayments, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div>
                <h4 class="header-title mb-1">Daftar Pembayaran Angsuran</h4>
                <p class="text-muted mb-0">Pantau status pembayaran angsuran pinjaman TomiDana.</p>
            </div>
        </div>

        <form id="repaymentFilter" class="repayment-filter row align-items-end mb-3" method="GET">
            <div class="col-lg-6 col-md-6 mb-2">
                <label for="status" class="mb-1">Status</label>
                <select class="custom-select" id="status" name="status">
                    <option value="">Semua status</option>
                    @foreach (['lunas' => 'Lunas', 'pending' => 'Menunggu Pembayaran', 'terlambat' => 'Terlambat'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-6 col-md-6 mb-2">
                <button type="submit" class="btn btn-primary btn-block"><i class="mdi mdi-filter-variant mr-1"></i>Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table id="tableRepayments" class="table table-hover repayment-table mb-0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Peminjam</th>
                        <th>Nomor Pinjaman</th>
                        <th>Lender</th>
                        <th>Perusahaan</th>
                        <th>Angsuran Ke-</th>
                        <th>Periode</th>
                        <th>Jatuh Tempo</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@push('js')
@if (! $company)
<script>
    $(function () {
        let companyRepaymentTable;
        let selectedCompanyId;

        $('.btn-view-repayments').on('click', function () {
            selectedCompanyId = $(this).data('company-id');
            const companyName = String($(this).data('company-name') || '-').toUpperCase();

            $('#repaymentModalTitle').text('Daftar Angsuran - ' + companyName);
            $('#repaymentModal').modal('show');

            if (!companyRepaymentTable) {
                companyRepaymentTable = $('#tableCompanyRepayments').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    pageLength: 10,
                    order: [[6, 'asc'], [7, 'asc']],
                    drawCallback: function () {
                        const api = this.api();
                        let lastPeriod = null;

                        api.rows({ page: 'current' }).every(function () {
                            const row = this.data();
                            const period = row.periode || '-';

                            if (period !== lastPeriod) {
                                const safePeriod = $('<div>').text(period).html();
                                $(this.node()).before('<tr class="repayment-period-group"><td colspan="11">Periode: ' + safePeriod + '</td></tr>');
                                lastPeriod = period;
                            }
                        });
                    },
                    language: {
                        search: 'Cari:',
                        emptyTable: 'Belum ada data angsuran',
                        zeroRecords: 'Data angsuran tidak ditemukan',
                        processing: 'Memuat data...',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                        infoEmpty: 'Belum ada data angsuran',
                        paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
                    },
                    ajax: {
                        url: '{{ route('admin.tomi-dana.repayments.table') }}',
                        type: 'POST',
                        data: function (data) {
                            data._token = '{{ csrf_token() }}';
                            data.company_id = selectedCompanyId;
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'nama_peminjam', name: 'nama_peminjam', defaultContent: '-' },
                        { data: 'nomor_pinjaman', name: 'nomor_pinjaman', defaultContent: '-' },
                        { data: 'lender', name: 'lender', defaultContent: '-' },
                        { data: 'perusahaan', name: 'perusahaan', defaultContent: '-' },
                        { data: 'angsuran_ke', name: 'pembayaran', defaultContent: '-' },
                        { data: 'periode', name: 'jatuh_tempo', orderable: false, searchable: false, defaultContent: '-' },
                        { data: 'tanggal_jatuh_tempo', name: 'jatuh_tempo', defaultContent: '-' },
                        { data: 'nominal', name: 'nominal', orderable: false, searchable: false, defaultContent: '-' },
                        { data: 'status_label', name: 'status', orderable: false, searchable: false, defaultContent: '-' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ]
                });
            } else {
                companyRepaymentTable.ajax.reload();
            }

            $('#repaymentModal').one('shown.bs.modal', function () {
                companyRepaymentTable.columns.adjust().responsive.recalc();
            });
        });
    });
</script>
@else
<script>
    $(function () {
        const repaymentTable = $('#tableRepayments').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 10,
            order: [[6, 'asc'], [7, 'asc']],
            drawCallback: function () {
                const api = this.api();
                let lastPeriod = null;

                api.rows({ page: 'current' }).every(function () {
                    const row = this.data();
                    const period = row.periode || '-';

                    if (period !== lastPeriod) {
                        const safePeriod = $('<div>').text(period).html();
                        $(this.node()).before('<tr class="repayment-period-group"><td colspan="11">Periode: ' + safePeriod + '</td></tr>');
                        lastPeriod = period;
                    }
                });
            },
            dom: '<"row"<"col-md-6"l><"col-md-6 text-right"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            language: {
                search: 'Cari:',
                emptyTable: '<div class="empty-state text-center"><i class="mdi mdi-receipt-text-outline d-block mb-2"></i><div class="font-weight-bold text-dark">Belum ada data pembayaran</div><div class="small">Data pembayaran angsuran TomiDana akan tampil di sini.</div></div>',
                processing: '<i class="dripicons-loading"></i> Memuat data...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Belum ada data pembayaran',
                zeroRecords: 'Data pembayaran tidak ditemukan',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
            },
            ajax: {
                url: '{{ route('admin.tomi-dana.repayments.table') }}',
                type: 'POST',
                data: function (data) {
                    data._token = '{{ csrf_token() }}';
                    data.status = $('#status').val();
                    data.company_id = @json($companyId);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_peminjam', name: 'nama_peminjam', defaultContent: '-' },
                { data: 'nomor_pinjaman', name: 'nomor_pinjaman', defaultContent: '-' },
                { data: 'lender', name: 'lender', defaultContent: '-' },
                { data: 'perusahaan', name: 'perusahaan', defaultContent: '-' },
                { data: 'angsuran_ke', name: 'pembayaran', defaultContent: '-' },
                { data: 'periode', name: 'jatuh_tempo', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'tanggal_jatuh_tempo', name: 'jatuh_tempo', defaultContent: '-' },
                { data: 'nominal', name: 'nominal', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'status_label', name: 'status', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        $('#repaymentFilter').on('submit', function (event) {
            event.preventDefault();
            repaymentTable.ajax.reload();
        });

        $('#status').on('change', function () {
            repaymentTable.ajax.reload();
        });

    });
</script>
@endif
@endpush
