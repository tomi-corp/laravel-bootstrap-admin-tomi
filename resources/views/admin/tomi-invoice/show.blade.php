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
        min-width: 1200px;
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
                        <p class="text-muted mb-0">Total nilai pinjaman</p>
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
                        <p class="text-muted mb-0">Total yang sudah dibayarkan</p>
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
                        <p class="text-muted mb-0">Total fee terakumulasi</p>
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
                                    <th>Nama Perusahaan</th>
                                    <th>Nomor Invoice</th>
                                    <th>Nama File</th>
                                    <th>Total Peminjam</th>
                                    <th>Total Pinjaman</th>
                                    <th>Total Dibayarkan</th>
                                    <th>Fee Tomi</th>
                                    <th>Tanggal Import</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perusahaan['details'] ?? [] as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item['nama_perusahaan'] ?? $namaPerusahaan }}</td>
                                    <td>{{ $item['nomor_invoice'] ?? '-' }}</td>
                                    <td title="{{ $item['filename'] ?? '-' }}">{{ $item['filename'] ?? '-' }}</td>
                                    <td>{{ number_format($item['jumlah_peminjam'] ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item['total_pinjaman'] ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item['total_dibayarkan'] ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item['total_fee'] ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        @if(!empty($item['created_at']))
                                        {{ \Carbon\Carbon::parse($item['created_at'])->format('d-m-Y H:i') }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center" style="gap: 4px;">
                                            <a href="{{ route('admin.invoice.fee.pdf', $item['id']) }}"
                                                target="_blank" class="btn btn-sm btn-danger" title="Invoice Fee TOMI">
                                                <i class="mdi mdi-file-pdf-box"></i> Invoice
                                            </a>
                                            <a href="{{ route('admin.generate.invoice.download.excel', $item['id']) }}"
                                                class="btn btn-sm btn-success" title="Download Excel">
                                                <i class="mdi mdi-file-excel"></i> Excel
                                            </a>
                                            <a href="{{ route('admin.transfer.request.pdf', $item['id']) }}"
                                                target="_blank" class="btn btn-sm btn-warning" title="Permintaan Transfer">
                                                <i class="mdi mdi-cash-multiple"></i> Transfer
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">Belum ada data TomiInvoice.</td>
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
                order: [[8, 'desc']],
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
    });
</script>
@endpush
