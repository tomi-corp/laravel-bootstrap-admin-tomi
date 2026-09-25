@extends('lender.layouts.app')

@section('title', 'Repayment')

@push('styles')
    <style>
        #repaymentsTable th,
        #repaymentsTable td {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Repayment</h1>
                <p class="mt-1 text-sm text-slate-600">Daftar repayment beserta pinjaman pada invoice.</p>
            </div>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <table id="repaymentsTable" class="display nowrap w-full min-w-[1200px]">
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
                            $downloadInvoice = $periodKey ? $invoiceDownloadIds->get($periodKey) : null;
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
                                    <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">Lunas</span>
                                @else
                                    <span class="rounded-md bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700">{{ str_replace('_', ' ', $invoice->status ?? 'belum lunas') }}</span>
                                @endif
                            </td>
                            <td>{{ $loans->count() }}</td>
                            <td>
                                <button type="button" class="js-open-loans-modal inline-flex items-center gap-2 rounded-md bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-900 hover:bg-slate-200" data-target="#loansModal{{ $invoice->id }}">
                                    <i class="fa-solid fa-list"></i>
                                    Lihat Pinjaman
                                </button>
                            </td>
                            <td>{{ $repayment?->admin?->nama ?? '-' }}</td>
                            <td>{{ $repayment ? 'Rp '.number_format($repayment->amount_total, 0, ',', '.') : '-' }}</td>
                            <td>{{ $repayment?->tanggal_pembayaran ? \Carbon\Carbon::parse($repayment->tanggal_pembayaran)->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $repayment?->payment_proof_file ?? '-' }}</td>
                            <td>
                                @if ($isInDueWindow)
                                    <button type="button" class="js-open-proof-modal lender-btn lender-btn-primary shadow-sm lender-btn-sm" data-target="#proofModal{{ $invoice->id }}">
                                        <i class="fa-solid fa-upload"></i>
                                        Upload
                                    </button>
                                @else
                                    <button type="button" class="lender-btn lender-btn-sm cursor-not-allowed bg-slate-100 text-slate-400" disabled title="Upload hanya aktif pada periode H-jatuh tempo perusahaan">
                                        <i class="fa-solid fa-upload"></i>
                                        Belum H-jatuh tempo
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if ($downloadInvoice && $isInDueWindow)
                                    <a href="{{ route('lender.repayments.invoice.download', ['invoice' => $downloadInvoice->id, 'company_invoice' => $invoice->id]) }}" target="_blank" class="lender-btn lender-btn-primary shadow-sm lender-btn-sm">
                                        <i class="fa-solid fa-download"></i>
                                        Download
                                    </a>
                                @elseif ($downloadInvoice)
                                    <button type="button" class="lender-btn lender-btn-sm cursor-not-allowed bg-slate-100 text-slate-400" disabled title="Invoice hanya bisa didownload pada periode H-jatuh tempo perusahaan">
                                        <i class="fa-solid fa-download"></i>
                                        Belum H-jatuh tempo
                                    </button>
                                @else
                                    <span class="text-slate-400">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

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
            <div id="proofModal{{ $invoice->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
                <div class="w-full max-w-xl rounded-lg bg-white text-slate-950 shadow-xl">
                    <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="text-lg font-semibold">Upload Bukti Pembayaran</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $invoice->no_invoice ?? '#'.$invoice->id }}</p>
                        </div>
                        <button type="button" class="js-close-proof-modal rounded-md px-2 py-1 text-slate-500 hover:bg-slate-100" data-target="#proofModal{{ $invoice->id }}">Tutup</button>
                    </div>

                    <form action="{{ route('lender.repayments.invoice.proof', $invoice->id) }}" method="POST" enctype="multipart/form-data" class="px-5 py-4">
                        @csrf
                        <dl class="mb-4 grid gap-3 rounded-md border border-slate-200 bg-slate-50/60 p-3 text-sm">
                            <div>
                                <dt class="text-xs font-medium uppercase text-slate-700">Perusahaan</dt>
                                <dd class="mt-1 font-medium text-slate-950">{{ $invoice->perusahaan?->nama ?? $loans->first()?->perusahaan?->nama ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase text-slate-700">Periode</dt>
                                <dd class="mt-1 font-medium text-slate-950">{{ $invoice->period ? \Carbon\Carbon::parse($invoice->period)->format('F Y') : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase text-slate-700">Status</dt>
                                <dd class="mt-1 font-medium text-slate-950">{{ str_replace('_', ' ', $invoice->status ?? 'belum lunas') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase text-slate-700">Total Pinjaman</dt>
                                <dd class="mt-1 font-medium text-slate-950">{{ $loans->count() }}</dd>
                            </div>
                        </dl>

                        <div class="grid gap-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-900">Tanggal Pembayaran</label>
                                <input type="datetime-local" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $repayment?->tanggal_pembayaran ? \Carbon\Carbon::parse($repayment->tanggal_pembayaran)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-900">File Bukti</label>
                                <input type="file" name="payment_proof_file" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20" required>
                            </div>
                        </div>

                        @if ($repayment?->payment_proof_file)
                            <p class="mt-3 text-sm text-slate-500">File saat ini: {{ $repayment->payment_proof_file }}</p>
                        @endif

                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" class="js-close-proof-modal lender-btn bg-slate-50 text-slate-950 hover:bg-slate-100 lender-btn-md" data-target="#proofModal{{ $invoice->id }}">Batal</button>
                            <button type="submit" class="lender-btn lender-btn-primary shadow-sm lender-btn-md">Simpan Bukti</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="loansModal{{ $invoice->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
                <div class="flex max-h-[85vh] w-full max-w-4xl flex-col rounded-lg bg-white text-slate-950 shadow-xl">
                    <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="text-lg font-semibold">Daftar Pinjaman</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $invoice->no_invoice ?? '#'.$invoice->id }}</p>
                        </div>
                        <button type="button" class="js-close-loans-modal rounded-md px-2 py-1 text-slate-500 hover:bg-slate-100" data-target="#loansModal{{ $invoice->id }}">Tutup</button>
                    </div>

                    <div class="overflow-y-auto px-5 py-4">
                        @if ($loans->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[760px] border-collapse text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200 bg-slate-50 text-left">
                                            <th class="px-3 py-2">ID Pinjaman</th>
                                            <th class="px-3 py-2">Nama</th>
                                            <th class="px-3 py-2">Perusahaan</th>
                                            <th class="px-3 py-2">Nominal</th>
                                            <th class="px-3 py-2">Tenor</th>
                                            <th class="px-3 py-2">Jatuh Tempo</th>
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
                                            <tr class="border-b border-slate-200">
                                                <td class="px-3 py-2 font-medium">{{ $loan->id_pinjaman ?? '#'.$loan->id }}</td>
                                                <td class="px-3 py-2">{{ $loan->data_karyawan?->nama_karyawan ?? $loan->user?->name ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $loan->perusahaan?->nama ?? '-' }}</td>
                                                <td class="px-3 py-2">Rp {{ number_format($loan->nominal ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-3 py-2">{{ $loan->tenor ?? '-' }} bulan</td>
                                                <td class="px-3 py-2">{{ $periodAngsuran?->jatuh_tempo ? \Carbon\Carbon::parse($periodAngsuran->jatuh_tempo)->format('d/m/Y') : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="rounded-md border border-slate-200 bg-slate-50/60 p-4 text-sm text-slate-500">
                                Tidak ada pinjaman pada invoice ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
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
                lengthMenu: 'Tampilkan _MENU_ data',
                search: 'Cari:',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data repayment',
                paginate: { next: 'Berikutnya', previous: 'Sebelumnya' },
            },
        });

        $(document).on('click', '.js-open-loans-modal', function () {
            const target = $(this).data('target');

            $(target).removeClass('hidden').addClass('flex');
        });

        $(document).on('click', '.js-open-proof-modal', function () {
            const target = $(this).data('target');

            $(target).removeClass('hidden').addClass('flex');
        });

        $(document).on('click', '.js-close-loans-modal', function () {
            const target = $(this).data('target');

            $(target).addClass('hidden').removeClass('flex');
        });

        $(document).on('click', '.js-close-proof-modal', function () {
            const target = $(this).data('target');

            $(target).addClass('hidden').removeClass('flex');
        });

        $(document).on('click', '[id^="loansModal"]', function (event) {
            if (event.target === this) {
                $(this).addClass('hidden').removeClass('flex');
            }
        });

        $(document).on('click', '[id^="proofModal"]', function (event) {
            if (event.target === this) {
                $(this).addClass('hidden').removeClass('flex');
            }
        });

        $(document).on('keydown', function (event) {
            if (event.key === 'Escape') {
                $('[id^="loansModal"].flex').addClass('hidden').removeClass('flex');
                $('[id^="proofModal"].flex').addClass('hidden').removeClass('flex');
            }
        });
    </script>
@endpush
