@extends('layout.app')

@section('title', 'Invoice TomiDana')

@push('css')
<style>
    .invoice-card {
        border: 1px solid #e9ecef;
    }

    .invoice-meta-label {
        color: #98a6ad;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .invoice-meta-value {
        color: #343a40;
        font-weight: 600;
    }

    .invoice-table th {
        background: #f8f9fb;
        color: #6c757d;
        font-size: 11px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .invoice-table td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="{{ route('admin.tomi-dana.repayments') }}" class="btn btn-light">
                    <i class="mdi mdi-arrow-left mr-1"></i>Kembali
                </a>
            </div>
            <h4 class="page-title">Invoice TomiDana</h4>
        </div>
    </div>
</div>

<div class="card invoice-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h3 class="text-primary mb-1">INVOICE TOMIDANA</h3>
                <div class="text-muted">Rincian tagihan pembayaran angsuran</div>
            </div>
            <div class="text-right">
                <div class="invoice-meta-label">Nomor Invoice</div>
                <div class="invoice-meta-value">{{ $invoiceNumber }}</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="invoice-meta-label">Kode Perusahaan</div>
                <div class="invoice-meta-value">{{ strtoupper($company->id_code_perusahaan ?? '-') }}</div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="invoice-meta-label">Nama Perusahaan</div>
                <div class="invoice-meta-value">{{ strtoupper($company->nama ?? '-') }}</div>
            </div>
            <div class="col-md-4">
                <div class="invoice-meta-label">Periode / Jatuh Tempo</div>
                <div class="invoice-meta-value">{{ $invoicePeriod->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered invoice-table mb-4">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nomor Pinjaman</th>
                        <th>Nama Peminjam</th>
                        <th>Nominal Pinjaman</th>
                        <th>Total Tagihan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pinjaman as $loan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $loan->id_pinjaman ?? ('TD-' . $loan->id) }}</td>
                            <td>{{ strtoupper($loan->data_karyawan?->nama_karyawan ?? $loan->user?->name ?? '-') }}</td>
                            <td>Rp. {{ number_format((float) ($loan->nominal ?? 0), 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format((float) ($loan->total_angsuran_perbulan ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada pinjaman pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end">
            <div class="col-md-5">
                <table class="table table-sm mb-0">
                    <tr>
                        <th>Jumlah Pinjaman</th>
                        <td class="text-right">{{ number_format($jumlahPinjaman, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Tagihan</th>
                        <td class="text-right font-weight-bold">Rp. {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Status Invoice</th>
                        <td class="text-right">
                            <span class="badge badge-{{ strtolower((string) $invoice->status) === 'lunas' ? 'success' : 'warning' }}">
                                {{ strtolower((string) $invoice->status) === 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
