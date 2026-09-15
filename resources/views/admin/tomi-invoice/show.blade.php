@extends('layout.app')

@section('title', 'Detail TomiInvoice')

@push('css')
<style>
    .tomi-invoice-table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    #detailTable {
        width: 100% !important;
        min-width: 1700px;
        white-space: nowrap;
    }

    #detailTable th,
    #detailTable td {
        white-space: nowrap !important;
        vertical-align: middle;
    }

    #detailTable td:nth-child(4) {
        max-width: 280px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .text-nowrap {
        white-space: nowrap !important;
    }

    .term-cell {
        min-width: 220px;
        white-space: normal !important;
    }

    .term-cell .term-amount {
        font-weight: 700;
        line-height: 1.45;
        margin-bottom: 2px;
    }

    .term-cell .term-line {
        line-height: 1.45;
        margin-bottom: 2px;
    }

    .term-cell .btn {
        margin-top: 4px;
    }
</style>
@endpush

@section('content')
@php
$namaPerusahaan = $perusahaan['nama'] ?? '-';
$totalInvoice = $perusahaan['total_invoice'] ?? 0;
$totalPinjaman = $perusahaan['total_pinjaman'] ?? 0;
$totalDibayarkan = $perusahaan['total_dibayarkan'] ?? 0;
$totalFee = $perusahaan['total_fee'] ?? 0;
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    Detail TomiInvoice - {{ $namaPerusahaan }}
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-primary rounded-circle mr-3">
                        <i class="mdi mdi-file-document-outline avatar-title font-24 text-white"></i>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Total Invoice</h5>
                        <h3 class="mb-1">{{ number_format($totalInvoice, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Jumlah invoice perusahaan</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-warning rounded-circle mr-3">
                        <i class="mdi mdi-cash-multiple avatar-title font-24 text-white"></i>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Total Pinjaman</h5>
                        <h3 class="mb-1">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total nilai pembiayaan</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-info rounded-circle mr-3">
                        <i class="mdi mdi-wallet-outline avatar-title font-24 text-white"></i>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Total Dibayarkan</h5>
                        <h3 class="mb-1">Rp {{ number_format($totalDibayarkan, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total nominal termin</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-success rounded-circle mr-3">
                        <i class="mdi mdi-percent avatar-title font-24 text-white"></i>
                    </div>
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Fee Tomi</h5>
                        <h3 class="mb-1">Rp {{ number_format($totalFee, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total invoice fee</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-0">Daftar Invoice</h4>
                    </div>

                    <div class="table-responsive tomi-invoice-table-wrapper">
                        <table id="detailTable" class="table table-bordered table-striped table-hover mb-0 nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th width="60">No</th>
                                    <th>Mitra</th>
                                    <th>Pelanggan Mitra</th>
                                    <th>Nomor Invoice</th>
                                    <th>No. PO</th>
                                    <th>Nilai Pembiayaan</th>
                                    <th>Termin 1</th>
                                    <th>Termin 2</th>
                                    <th>Termin 3</th>
                                    <th>Total Termin</th>
                                    <th>Total Invoice Fee</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perusahaan['details'] ?? [] as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item['nama_perusahaan'] ?? $namaPerusahaan }}</td>
                                    <td>{{ $item['vendor_name'] ?? '-' }}</td>
                                    <td>{{ $item['nomor_invoice'] ?? '-' }}</td>
                                    <td>{{ $item['purchase_order_number'] ?? '-' }}</td>
                                    <td>Rp {{ number_format($item['approved_financing_amount'] ?? 0, 0, ',', '.') }}</td>
                                    @for($termNumber = 1; $termNumber <= 3; $termNumber++)
                                    @php
                                    $term = $item['terms'][$termNumber] ?? null;
                                    @endphp
                                    <td class="term-cell">
                                        @if($term)
                                        <div class="term-amount">
                                            Rp {{ number_format($term['term_amount'] ?? 0, 0, ',', '.') }}
                                        </div>
                                        <div class="term-line">
                                            Fee: Rp {{ number_format($term['total_fee'] ?? 0, 0, ',', '.') }}
                                        </div>
                                        <div class="term-line">
                                            Tanggal:
                                            @if(!empty($term['finance_invoice_term_date']))
                                            {{ \Carbon\Carbon::parse($term['finance_invoice_term_date'])->format('d-m-Y') }}
                                            @else
                                            -
                                            @endif
                                        </div>

                                        @if($term['can_download'] ?? false)
                                        <a href="javascript:void(0)"
                                            class="btn btn-sm btn-danger btn-download-invoice"
                                            data-url="{{ route('admin.invoice-financing.term.download', ['id' => $term['invoice_id'], 'term' => $term['term']]) }}"
                                            title="Download Invoice Termin {{ $termNumber }}">
                                            <i class="mdi mdi-file-pdf-box"></i> Invoice
                                        </a>
                                        @else
                                        <button type="button" class="btn btn-sm btn-secondary" disabled
                                            title="Termin sebelumnya belum selesai diproses">
                                            <i class="mdi mdi-lock"></i> Terkunci
                                        </button>
                                        @endif
                                        @else
                                        -
                                        @endif
                                    </td>
                                    @endfor
                                    <td>Rp {{ number_format($item['total_dibayarkan'] ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item['total_fee'] ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ strtoupper(str_replace('_', ' ', $item['status'] ?? '-')) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted">Belum ada data TomiInvoice.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDownloadInvoice" tabindex="-1" role="dialog"
    aria-labelledby="modalDownloadInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formDownloadInvoice" method="GET" target="_blank">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDownloadInvoiceLabel">Pilih Kolom Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="column_selection" value="1">

                    @php
                    $invoiceColumns = [
                    'vendor' => 'Pelanggan Mitra',
                    'purchase_order_number' => 'No. PO',
                    'approved_financing_amount' => 'Nilai Pembiayaan',
                    'term_amount' => 'Nominal Termin',
                    'fee_tomi' => 'Fee TomiInvoice',
                    'admin_fee' => 'Admin Fee Termin',
                    'total_fee' => 'Total Fee',
                    ];
                    @endphp

                    @foreach($invoiceColumns as $value => $label)
                    <div class="form-check mb-2">
                        <input class="form-check-input invoice-column-checkbox" type="checkbox" name="columns[]"
                            value="{{ $value }}" id="tomiInvoiceCol{{ \Illuminate\Support\Str::studly($value) }}"
                            checked>
                        <label class="form-check-label"
                            for="tomiInvoiceCol{{ \Illuminate\Support\Str::studly($value) }}">
                            {{ $label }}
                        </label>
                    </div>
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-download"></i> Download PDF
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        if ($('#detailTable').length) {
            if ($.fn.DataTable.isDataTable('#detailTable')) {
                $('#detailTable').DataTable().destroy();
            }

            $('#detailTable').DataTable({
                processing: true,
                pageLength: 10,
                scrollX: true,
                autoWidth: false,
                ordering: true,
                order: [[3, 'desc']],
                columnDefs: [{
                    targets: '_all',
                    className: 'text-nowrap'
                }],
                language: {
                    emptyTable: "Belum ada data TomiInvoice",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }

        $(document).on('click', '.btn-download-invoice', function () {
            $('#formDownloadInvoice').attr('action', $(this).data('url'));
            $('.invoice-column-checkbox').prop('checked', true);
            $('#modalDownloadInvoice').modal('show');
        });
    });
</script>
@endpush
