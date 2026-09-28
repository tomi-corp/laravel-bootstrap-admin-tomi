@extends('lender.layouts.app')

@section('title', 'Disbursement')

@push('styles')
    <style>
        #disbursementsTable th,
        #disbursementsTable td {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Disbursement</h1>
                <p class="mt-1 text-sm text-slate-600">Pinjaman yang belum dicairkan.</p>
            </div>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <table id="disbursementsTable" class="display nowrap w-full min-w-[1100px]">
                <thead>
                    <tr>
                        <th>ID Pinjaman</th>
                        <th>User</th>
                        <th>Perusahaan</th>
                        <th>Nominal</th>
                        <th>Diterima</th>
                        <th>Tenor</th>
                        <th>Status</th>
                        <th>Dokumen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loans as $loan)
                        @php
                            $pkUrl = $loan->document_name ? URL::temporarySignedRoute('preview.pk', now()->addMinutes(10), ['document' => $loan->document_name]) : null;
                            $ktpUrl = $loan->kyc?->ktp ? secureFile($loan->kyc->ktp) : null;
                            $selfieUrl = $loan->kyc?->selfie ? secureFile($loan->kyc->selfie) : null;
                        @endphp
                        <tr>
                            <td>{{ $loan->id_pinjaman }}</td>
                            <td>{{ $loan->user?->name ?? $loan->user?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $loan->company?->nama ?? '-' }}</td>
                            <td>Rp. {{ number_format($loan->nominal, 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($loan->jumlah_yang_diterima, 0, ',', '.') }}</td>
                            <td>{{ $loan->tenor }} bulan</td>
                            <td>{{ str_replace('_', ' ', $loan->status) }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @foreach ([['PK', $pkUrl], ['KTP', $ktpUrl], ['Selfie', $selfieUrl]] as [$label, $url])
                                        @if ($url)
                                            <a href="{{ $url }}" target="_blank" class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-900 hover:bg-slate-200">{{ $label }}</a>
                                        @else
                                            <span class="rounded-md bg-slate-50 px-2.5 py-1 text-xs text-slate-400">{{ $label }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button type="button" class="js-open-disbursement-modal lender-btn lender-btn-primary shadow-sm lender-btn-sm" data-target="#disbursementModal{{ $loan->id }}">Cairkan</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        @foreach ($loans as $loan)
            @php
                $bankName = trim(($loan->employee?->bank?->code_bank ?? '-').' '.($loan->employee?->bank?->description ?? ''));
                $accountName = $loan->employee?->nama_rekening ?? $loan->user?->name ?? '-';
                $accountNumber = $loan->employee?->nomor_bank ?? '-';
                $disbursementAmount = $loan->jumlah_yang_diterima ?? $loan->nominal;
            @endphp
            <div id="disbursementModal{{ $loan->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
                <div class="w-full max-w-2xl rounded-lg bg-white text-slate-950 shadow-xl">
                    <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="text-lg font-semibold">Cairkan Pinjaman</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $loan->id_pinjaman }}</p>
                        </div>
                        <button type="button" class="js-close-disbursement-modal rounded-md px-2 py-1 text-slate-500 hover:bg-slate-100" data-target="#disbursementModal{{ $loan->id }}">Tutup</button>
                    </div>
                    <form action="{{ route('lender.disbursements.store') }}" method="POST" enctype="multipart/form-data" class="px-5 py-4">
                        @csrf
                        <input type="hidden" name="pinjaman_id" value="{{ $loan->id }}">

                        <dl class="grid gap-3 md:grid-cols-2">
                            @foreach ([
                                'Bank' => $bankName,
                                'Atas Nama' => $accountName,
                                'Nomor Rekening' => $accountNumber,
                                'Dana Dicairkan' => 'Rp. '.number_format($disbursementAmount, 0, ',', '.'),
                            ] as $label => $value)
                                <div class="rounded-md border border-slate-200 bg-slate-50/50 p-3">
                                    <dt class="text-xs font-medium uppercase text-slate-700">{{ $label }}</dt>
                                    <dd class="mt-1 text-sm font-medium text-slate-950">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-900">Tanggal Transfer</label>
                                <input type="date" name="transfer_date" value="{{ now()->toDateString() }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-900">Bukti Transfer</label>
                                <input type="file" name="transfer_proof" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20" required>
                            </div>
                        </div>

                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" class="js-close-disbursement-modal rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300" data-target="#disbursementModal{{ $loan->id }}">Batal</button>
                            <button type="submit" class="lender-btn lender-btn-primary shadow-sm lender-btn-md">Simpan Pencairan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
    <script>
        $('#disbursementsTable').DataTable({
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
                emptyTable: 'Tidak ada pinjaman yang belum dicairkan',
                paginate: { next: 'Berikutnya', previous: 'Sebelumnya' },
            },
        });

        $(document).on('click', '.js-open-disbursement-modal', function () {
            const target = $(this).data('target');

            $(target).removeClass('hidden').addClass('flex');
        });

        $(document).on('click', '.js-close-disbursement-modal', function () {
            const target = $(this).data('target');

            $(target).addClass('hidden').removeClass('flex');
        });

        $(document).on('click', '[id^="disbursementModal"]', function (event) {
            if (event.target === this) {
                $(this).addClass('hidden').removeClass('flex');
            }
        });

        $(document).on('keydown', function (event) {
            if (event.key === 'Escape') {
                $('[id^="disbursementModal"].flex').addClass('hidden').removeClass('flex');
            }
        });
    </script>
@endpush
