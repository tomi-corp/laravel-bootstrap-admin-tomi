@extends('layout.app')

@section('title', 'Invoice Fee Detail')

@section('content')
@php
$invoice = $result['invoice'] ?? [];
$rows = $result['data'] ?? [];
@endphp

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Invoice Fee Detail</h4>
                <p class="text-muted mb-0">Data dari API invoice-excel/{{ $id }}</p>
            </div>
            <div>
                <a href="{{ route('admin.invoice.fee.pdf', $id) }}" target="_blank" class="btn btn-danger">
                    <i class="mdi mdi-file-pdf-box"></i> Export PDF
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>ID:</strong> {{ $invoice['id'] ?? '-' }}</div>
            <div class="col-md-3"><strong>Perusahaan:</strong> {{ $invoice['nama_perusahaan'] ?? '-' }}</div>
            <div class="col-md-6"><strong>Filename:</strong> {{ $invoice['filename'] ?? '-' }}</div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Nama</th>
                    <th>No KTP</th>
                    <th>Alamat</th>
                    <th>No Telepon</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Jangka Waktu</th>
                    <th>Bank</th>
                    <th>No Rekening</th>
                    <th>Nama Pemilik Rekening</th>
                    <th>Tanggal Transfer</th>
                    <th>Total Dibayarkan</th>
                    <th>Total Fee</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['tanggal_pengajuan'] ?? '-' }}</td>
                    <td>{{ $row['nama'] ?? '-' }}</td>
                    <td>{{ $row['no_ktp'] ?? '-' }}</td>
                    <td>{{ $row['alamat'] ?? '-' }}</td>
                    <td>{{ $row['no_telepon'] ?? '-' }}</td>
                    <td>Rp. {{ number_format((float) ($row['jumlah_pinjaman'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ $row['jangka_waktu'] ?? '-' }}</td>
                    <td>{{ $row['nama_bank'] ?? '-' }}</td>
                    <td>{{ $row['no_rekening'] ?? '-' }}</td>
                    <td>{{ $row['nama_pemilik_rekening'] ?? '-' }}</td>
                    <td>{{ $row['tanggal_transfer'] ?? '-' }}</td>
                    <td>Rp. {{ number_format((float) ($row['total_dibayarkan'] ?? 0), 0, ',', '.') }}</td>
                    <td>Rp. {{ number_format((float) ($row['total_fee'] ?? 0), 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection