@extends('layout.app')

@section('title', 'Detail Salary Financing')

@push('css')
<style>
    .salary-financing-table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    #detailTable {
        width: 100% !important;
        min-width: 1450px;
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

    .dataTables_wrapper {
        width: 100%;
    }

    .dataTables_scroll {
        width: 100%;
    }

    .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: hidden !important;
    }

    .dataTables_scrollHead {
        overflow: hidden !important;
    }

    .text-nowrap {
        white-space: nowrap !important;
    }
</style>
@endpush

@section('content')

@php
$namaPerusahaan = $perusahaan['nama'] ?? '-';

$maxPlafon = $perusahaan['max_plafon'] ?? 0;
$plafonTerpakai = $perusahaan['plafon_terpakai'] ?? 0;

$sisaPlafon = $perusahaan['sisa_plafon'] ?? ($maxPlafon - $plafonTerpakai);

if ($sisaPlafon < 0) {
    $sisaPlafon = 0;
}
@endphp

<div class="container-fluid">

    {{-- Title --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    Detail Salary Financing - {{ $namaPerusahaan }}
                </h4>
            </div>
        </div>
    </div>

    {{-- Card Plafon --}}
    <div class="row">

        {{-- Max Plafon --}}
        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-primary rounded-circle mr-3">
                        <i class="fas fa-bank avatar-title font-24 text-white"></i>
                    </div>

                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Max Plafon</h5>
                        <h3 class="mb-1">
                            Rp {{ number_format($maxPlafon, 0, ',', '.') }}
                        </h3>
                        <p class="text-muted mb-0">
                            Batas maksimal plafon
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Plafon Terpakai --}}
        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-warning rounded-circle mr-3">
                        <i class="fas fa-money-bill-wave avatar-title font-24 text-white"></i>
                    </div>

                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Plafon Terpakai</h5>
                        <h3 class="mb-1">
                            Rp {{ number_format($plafonTerpakai, 0, ',', '.') }}
                        </h3>
                        <p class="text-muted mb-0">
                            Total plafon yang sudah digunakan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sisa Plafon --}}
        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
            <div class="card-box h-100">
                <div class="media align-items-center">
                    <div class="avatar-md bg-success rounded-circle mr-3">
                        <i class="fas fa-wallet avatar-title font-24 text-white"></i>
                    </div>

                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Sisa Plafon</h5>
                        <h3 class="mb-1">
                            Rp {{ number_format($sisaPlafon, 0, ',', '.') }}
                        </h3>
                        <p class="text-muted mb-0">
                            Sisa plafon yang masih tersedia
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-0">Daftar Penggunaan Plafon</h4>
                    </div>

                    <div class="table-responsive salary-financing-table-wrapper">
                        <table id="detailTable" class="table table-bordered table-striped table-hover mb-0 nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th width="60">No</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Nomor Invoice</th>
                                    <th>Nama File</th>
                                    <th>Total Karyawan</th>
                                    <th>Total Pendapatan</th>
                                    <th>Biaya Admin</th>
                                    <th>Total Tagihan</th>
                                    <th>Tanggal Import</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($perusahaan['details'] ?? [] as $item)
                                @php
                                $detailNamaPerusahaan = $item['nama_perusahaan'] ?? ($perusahaan['nama'] ?? '-');

                                $nomorInvoice = $item['nomor_invoice']
                                ?? 'SF/' . now()->format('Ym') . '/' . ($item['id'] ?? $loop->iteration);

                                $namaFile = $item['filename']
                                ?? $item['file_name']
                                ?? '-';

                                $totalKaryawan = $item['jumlah_peminjam']
                                ?? $item['total_peminjam']
                                ?? 0;

                                $totalPendapatan = $item['total_pinjaman']
                                ?? $item['jumlah_pinjaman']
                                ?? $item['nominal']
                                ?? 0;

                                $biayaAdmin = $item['biaya_admin']
                                ?? $item['total_dibayarkan']
                                ?? ($totalPendapatan * 0.0225);

                                $totalTagihan = $item['total_tagihan']
                                ?? $item['total_fee']
                                ?? ($totalPendapatan + $biayaAdmin);

                                $tanggalImport = $item['created_at']
                                ?? $item['tanggal']
                                ?? null;

                                $status = $item['status'] ?? 'pending';

                                $statusLabel = [
                                'pending' => 'Pending',
                                'uploaded_transfer' => 'Bukti Transfer Uploaded',
                                'uploaded_bayar' => 'Bukti Bayar Uploaded',
                                'paid' => 'Paid',
                                'unpaid' => 'Unpaid',
                                'done' => 'Done',
                                'lunas' => 'Lunas',
                                'approved' => 'Approved',
                                ];

                                $statusClass = [
                                'pending' => 'badge-warning',
                                'uploaded_transfer' => 'badge-info',
                                'uploaded_bayar' => 'badge-primary',
                                'paid' => 'badge-success',
                                'unpaid' => 'badge-danger',
                                'done' => 'badge-success',
                                'lunas' => 'badge-success',
                                'approved' => 'badge-success',
                                ];
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        {{ $detailNamaPerusahaan }}
                                    </td>

                                    <td>
                                        {{ $nomorInvoice }}
                                    </td>

                                    <td title="{{ $namaFile }}">
                                        {{ $namaFile }}
                                    </td>

                                    <td>
                                        {{ number_format($totalKaryawan, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($biayaAdmin, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        @if(!empty($tanggalImport))
                                        {{ \Carbon\Carbon::parse($tanggalImport)->format('d-m-Y H:i') }}
                                        @else
                                        -
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge {{ $statusClass[$status] ?? 'badge-secondary' }}">
                                            {{ $statusLabel[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @if(!empty($item['id']))
                                        <a href="{{ route('admin.salary-financing.invoice.download', $item['id']) }}"
                                            target="_blank" class="btn btn-sm btn-danger" title="Download Invoice">
                                            <i class="mdi mdi-file-pdf-box"></i> Invoice
                                        </a>
                                        @else
                                        <button type="button" class="btn btn-sm btn-secondary" disabled>
                                            <i class="mdi mdi-file-pdf-box"></i> Invoice
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">
                                        Belum ada data salary financing.
                                    </td>
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
                    columnDefs: [
                        {
                            targets: '_all',
                            className: 'text-nowrap'
                        }
                    ],
                    language: {
                        emptyTable: "Belum ada data salary financing",
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        zeroRecords: "Data tidak ditemukan",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "›",
                            previous: "‹"
                        }
                    }
                });

            }

        });
    </script>
    @endpush
