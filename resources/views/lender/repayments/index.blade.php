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
                <p class="mt-1 text-sm text-white/90">Daftar repayment beserta pinjaman pada invoice.</p>
            </div>
        </div>

        <section class="rounded-lg border border-primary-100 bg-white p-5 text-primary-950 shadow-sm shadow-primary-950/10">
            <table id="repaymentsTable" class="display nowrap w-full min-w-[1200px]">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Periode</th>
                        <th>Total Loan</th>
                        <th>Loan</th>
                        <th>Admin</th>
                        <th>Total Repayment</th>
                        <th>Tanggal Pembayaran</th>
                        <th>File Bukti</th>
                        <th>Download Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($repayments as $repayment)
                        @php
                            $periodKey = $repayment->invoice?->period ? \Carbon\Carbon::parse($repayment->invoice->period)->format('Y-m') : null;
                            $downloadInvoice = $periodKey ? $invoiceDownloadIds->get($periodKey) : null;
                            $loans = ($repayment->invoice?->pinjaman ?? collect())->filter(function ($loan) use ($periodKey) {
                                return ! $periodKey
                                    || ! $loan->jatuh_tempoSesuaiInvoice
                                    || \Carbon\Carbon::parse($loan->jatuh_tempoSesuaiInvoice->jatuh_tempo)->format('Y-m') === $periodKey;
                            })->values();
                        @endphp
                        <tr>
                            <td>{{ $repayment->invoice?->no_invoice ?? '#'.$repayment->id_invoicing }}</td>
                            <td>{{ $repayment->invoice?->period ? \Carbon\Carbon::parse($repayment->invoice->period)->format('F Y') : '-' }}</td>
                            <td>{{ $loans->count() }}</td>
                            <td>
                                <button type="button" class="js-open-loans-modal inline-flex items-center gap-2 rounded-md bg-slate-100 px-3 py-1.5 text-sm font-medium text-primary-800 hover:bg-slate-200" data-target="#loansModal{{ $repayment->id }}">
                                    <i class="fa-solid fa-list"></i>
                                    Lihat Pinjaman
                                </button>
                            </td>
                            <td>{{ $repayment->admin?->nama ?? '-' }}</td>
                            <td>Rp {{ number_format($repayment->amount_total, 0, ',', '.') }}</td>
                            <td>{{ $repayment->tanggal_pembayaran ? \Carbon\Carbon::parse($repayment->tanggal_pembayaran)->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $repayment->payment_proof_file ?? '-' }}</td>
                            <td>
                                @if ($downloadInvoice)
                                    <a href="{{ route('lender.repayments.invoice.download', $downloadInvoice->id) }}" target="_blank" class="inline-flex items-center gap-2 rounded-md bg-[#0B8FE8] px-3 py-1.5 text-sm font-medium text-white hover:bg-[#0878C4]">
                                        <i class="fa-solid fa-download"></i>
                                        Download
                                    </a>
                                @else
                                    <span class="text-slate-400">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        @foreach ($repayments as $repayment)
            @php
                $periodKey = $repayment->invoice?->period ? \Carbon\Carbon::parse($repayment->invoice->period)->format('Y-m') : null;
                $loans = ($repayment->invoice?->pinjaman ?? collect())->filter(function ($loan) use ($periodKey) {
                    return ! $periodKey
                        || ! $loan->jatuh_tempoSesuaiInvoice
                        || \Carbon\Carbon::parse($loan->jatuh_tempoSesuaiInvoice->jatuh_tempo)->format('Y-m') === $periodKey;
                })->values();
            @endphp
            <div id="loansModal{{ $repayment->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
                <div class="flex max-h-[85vh] w-full max-w-4xl flex-col rounded-lg bg-white text-primary-950 shadow-xl">
                    <div class="flex items-start justify-between border-b border-primary-100 px-5 py-4">
                        <div>
                            <h2 class="text-lg font-semibold">Daftar Pinjaman</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $repayment->invoice?->no_invoice ?? '#'.$repayment->id_invoicing }}</p>
                        </div>
                        <button type="button" class="js-close-loans-modal rounded-md px-2 py-1 text-slate-500 hover:bg-slate-100" data-target="#loansModal{{ $repayment->id }}">Tutup</button>
                    </div>

                    <div class="overflow-y-auto px-5 py-4">
                        @if ($loans->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[760px] border-collapse text-sm">
                                    <thead>
                                        <tr class="border-b border-primary-100 bg-primary-50 text-left">
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
                                            <tr class="border-b border-primary-100">
                                                <td class="px-3 py-2 font-medium">{{ $loan->id_pinjaman ?? '#'.$loan->id }}</td>
                                                <td class="px-3 py-2">{{ $loan->data_karyawan?->nama_karyawan ?? $loan->user?->name ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $loan->perusahaan?->nama ?? '-' }}</td>
                                                <td class="px-3 py-2">Rp {{ number_format($loan->nominal ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-3 py-2">{{ $loan->tenor ?? '-' }} bulan</td>
                                                <td class="px-3 py-2">{{ $loan->jatuh_tempoSesuaiInvoice?->jatuh_tempo ? \Carbon\Carbon::parse($loan->jatuh_tempoSesuaiInvoice->jatuh_tempo)->format('d/m/Y') : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="rounded-md border border-primary-100 bg-primary-50/60 p-4 text-sm text-slate-500">
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

        $(document).on('click', '.js-close-loans-modal', function () {
            const target = $(this).data('target');

            $(target).addClass('hidden').removeClass('flex');
        });

        $(document).on('click', '[id^="loansModal"]', function (event) {
            if (event.target === this) {
                $(this).addClass('hidden').removeClass('flex');
            }
        });

        $(document).on('keydown', function (event) {
            if (event.key === 'Escape') {
                $('[id^="loansModal"].flex').addClass('hidden').removeClass('flex');
            }
        });
    </script>
@endpush
