@extends('layout.app')

@section('title', 'Admin Pengembalian Dana')

@push('css')
<style>
    #repaymentsTable th,
    #repaymentsTable td {
        white-space: nowrap;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Pengembalian Dana</li>
                </ol>
            </div>
            <h4 class="page-title">Pengembalian Dana</h4>
            <p class="text-muted mb-0">Daftar repayment beserta pinjaman pada invoice.</p>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table id="repaymentsTable" class="table table-hover text-center nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Perusahaan</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Total Loan</th>
                            <th>Loan</th>
                            <th>Admin</th>
                            <th>Total Repayment</th>
                            <th>Tanggal Pembayaran</th>
                            <th>File Bukti</th>
                            <th>Upload Bukti</th>
                            <th>Download Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            @php
                                $repayment = $invoice->repayment;
                                $periodKey = $invoice->period ? \Carbon\Carbon::parse($invoice->period)->format('Y-m') : null;
                                $downloadInvoice = $invoiceDownloadIds->get($invoice->id);
                                $isInDueWindow = (bool) ($invoiceDownloadAvailability[$invoice->id] ?? false);
                                $loans = ($invoice->pinjaman ?? collect())->filter(function ($loan) use ($periodKey) {
                                    return $loan->status === 'approve'
                                        && (! $periodKey || $loan->angsuran->contains(function ($angsuran) use ($periodKey) {
                                            return $angsuran->status !== 'lunas'
                                                && \Carbon\Carbon::parse($angsuran->jatuh_tempo)->format('Y-m') === $periodKey;
                                        }));
                                })->values();
                            @endphp
                            <tr>
                                <td>{{ $invoice->no_invoice ?? '#'.$invoice->id }}</td>
                                <td>{{ $invoice->perusahaan?->nama ?? $loans->first()?->perusahaan?->nama ?? '-' }}</td>
                                <td>{{ $invoice->period ? \Carbon\Carbon::parse($invoice->period)->format('F Y') : '-' }}</td>
                                <td>
                                    @if ($invoice->status === 'lunas')
                                        <span class="badge badge-success">LUNAS</span>
                                    @else
                                        <span class="badge badge-warning">{{ strtoupper(str_replace('_', ' ', $invoice->status ?? 'belum lunas')) }}</span>
                                    @endif
                                </td>
                                <td>{{ $loans->count() }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#loansModal{{ $invoice->id }}">
                                        <i class="fa-solid fa-list"></i> Lihat Pinjaman
                                    </button>
                                </td>
                                <td>{{ $repayment?->admin?->nama ?? '-' }}</td>
                                <td>{{ $repayment ? 'Rp. '.number_format($repayment->amount_total, 0, ',', '.') : '-' }}</td>
                                <td>{{ $repayment?->tanggal_pembayaran ? \Carbon\Carbon::parse($repayment->tanggal_pembayaran)->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    @if ($repayment?->payment_proof_file)
                                        <a href="{{ asset('storage/'.$repayment->payment_proof_file) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fa-solid fa-file"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($isInDueWindow)
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#proofModal{{ $invoice->id }}">
                                            <i class="fa-solid fa-upload"></i> Upload
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-light" disabled title="Upload hanya aktif pada periode H-jatuh tempo perusahaan">
                                            Belum H-jatuh Tempo
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    @if ($downloadInvoice && $isInDueWindow)
                                        <a href="{{ route('admin.repayments.invoice.download', ['invoice' => $downloadInvoice->id, 'company_invoice' => $invoice->id]) }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-download"></i> Download
                                        </a>
                                    @elseif ($downloadInvoice)
                                        <button type="button" class="btn btn-sm btn-light" disabled title="Invoice hanya bisa didownload pada periode H-jatuh tempo perusahaan">
                                            Belum H-jatuh Tempo
                                        </button>
                                    @else
                                        <span class="text-muted">Belum tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach ($invoices as $invoice)
    @php
        $repayment = $invoice->repayment;
        $periodKey = $invoice->period ? \Carbon\Carbon::parse($invoice->period)->format('Y-m') : null;
        $loans = ($invoice->pinjaman ?? collect())->filter(function ($loan) use ($periodKey) {
            return $loan->status === 'approve'
                && (! $periodKey || $loan->angsuran->contains(function ($angsuran) use ($periodKey) {
                    return $angsuran->status !== 'lunas'
                        && \Carbon\Carbon::parse($angsuran->jatuh_tempo)->format('Y-m') === $periodKey;
                }));
        })->values();
    @endphp

    <div class="modal fade" id="proofModal{{ $invoice->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <form action="{{ route('admin.repayments.invoice.proof', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_invoicing" value="{{ $invoice->id }}">
                    <div class="modal-body">
                        <dl class="row">
                            <dt class="col-sm-5">Invoice</dt>
                            <dd class="col-sm-7">{{ $invoice->no_invoice ?? '#'.$invoice->id }}</dd>
                            <dt class="col-sm-5">Perusahaan</dt>
                            <dd class="col-sm-7">{{ $invoice->perusahaan?->nama ?? $loans->first()?->perusahaan?->nama ?? '-' }}</dd>
                            <dt class="col-sm-5">Periode</dt>
                            <dd class="col-sm-7">{{ $invoice->period ? \Carbon\Carbon::parse($invoice->period)->format('F Y') : '-' }}</dd>
                            <dt class="col-sm-5">Total Pinjaman</dt>
                            <dd class="col-sm-7">{{ $loans->count() }}</dd>
                        </dl>

                        <div class="form-group">
                            <label>Tanggal Pembayaran</label>
                            <input type="datetime-local" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $repayment?->tanggal_pembayaran ? \Carbon\Carbon::parse($repayment->tanggal_pembayaran)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" class="form-control">
                            @error('tanggal_pembayaran') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group">
                            <label>File Bukti</label>
                            <input type="file" name="payment_proof_file" accept=".jpg,.jpeg,.png,.pdf" class="form-control" required>
                            <small class="text-muted d-block">Format jpg, jpeg, png, atau pdf. Maksimal 5 MB.</small>
                            @error('payment_proof_file') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        @if ($repayment?->payment_proof_file)
                            <p class="text-muted mb-0">File saat ini: {{ $repayment->payment_proof_file }}</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Bukti</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loansModal{{ $invoice->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Pinjaman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    @if ($loans->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center mb-0">
                                <thead>
                                    <tr>
                                        <th>ID Pinjaman</th>
                                        <th>Nama</th>
                                        <th>Perusahaan</th>
                                        <th>Nominal</th>
                                        <th>Tenor</th>
                                        <th>Jatuh Tempo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loans as $loan)
                                        @php
                                            $periodAngsuran = $loan->angsuran->first(function ($angsuran) use ($periodKey) {
                                                return ! $periodKey
                                                    || \Carbon\Carbon::parse($angsuran->jatuh_tempo)->format('Y-m') === $periodKey;
                                            });
                                        @endphp
                                        <tr>
                                            <td>{{ $loan->id_pinjaman ?? '#'.$loan->id }}</td>
                                            <td>{{ $loan->data_karyawan?->nama_karyawan ?? $loan->user?->name ?? '-' }}</td>
                                            <td>{{ $loan->perusahaan?->nama ?? '-' }}</td>
                                            <td>Rp. {{ number_format($loan->nominal ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ $loan->tenor ?? '-' }} bulan</td>
                                            <td>{{ $periodAngsuran?->jatuh_tempo ? \Carbon\Carbon::parse($periodAngsuran->jatuh_tempo)->format('d/m/Y') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-light mb-0">Tidak ada pinjaman pada invoice ini.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('js')
<script>
    $('#repaymentsTable').DataTable({
        responsive: false,
        scrollX: true,
        scrollY: '420px',
        scrollCollapse: true,
        autoWidth: false,
        order: [],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
        language: {
            emptyTable: 'Belum ada data repayment.',
            zeroRecords: 'Repayment tidak ditemukan.',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
        }
    });
</script>
@endpush
