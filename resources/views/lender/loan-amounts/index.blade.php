@extends('lender.layouts.app')

@section('title', 'Konfigurasi DSR')

@push('styles')
    <style>
        #loanAmountsTable th,
        #loanAmountsTable td {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Konfigurasi DSR</h1>
                <p class="mt-1 text-sm text-slate-600">
                    @if ($selectedCompany)
                        {{ $selectedCompany->nama }} - kelola minimal, maksimal, iuran, dan persentase pinjaman.
                    @else
                        Kelola minimal, maksimal, iuran, dan persentase pinjaman.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('lender.loan-amounts.index') }}" class="lender-btn border border-slate-300 bg-white text-slate-700 shadow-sm hover:bg-slate-50 lender-btn-md">
                    <i class="fa-solid fa-arrow-left"></i>
                    Perusahaan
                </a>
                @if ($amounts->isEmpty())
                    <a href="{{ route('lender.loan-amounts.create', $selectedCompany ? ['company_id' => $selectedCompany->id] : []) }}" class="lender-btn lender-btn-primary shadow-sm lender-btn-md">
                        <i class="fa-solid fa-plus"></i>
                        Tambah DSR
                    </a>
                @else
                    <a href="{{ route('lender.loan-amounts.edit', $amounts->first()) }}" class="rounded-md bg-yellow-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-600">
                        <i class="fa-solid fa-pen"></i>
                        Edit DSR
                    </a>
                @endif
            </div>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <table id="loanAmountsTable" class="display nowrap w-full min-w-[1000px]">
                <thead>
                    <tr>
                        <th>Perusahaan</th>
                        <th>Iuran Anggota</th>
                        <th>Minimal</th>
                        <th>Maksimal</th>
                        <th>Maksimal Persentase</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($amounts as $amount)
                        <tr>
                            <td>{{ $amount->company?->nama ?? '-' }}</td>
                            <td>Rp. {{ number_format($amount->iuran_anggota ?? 0, 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($amount->minimal, 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($amount->maximal, 0, ',', '.') }}</td>
                            <td>{{ number_format($amount->maximal_persentase ?? 0, 2, ',', '.') }}%</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('lender.loan-amounts.edit', $amount) }}" class="rounded-md bg-yellow-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-yellow-600">
                                        Edit
                                    </a>
                                    <form action="{{ route('lender.loan-amounts.destroy', $amount) }}" method="POST" class="js-delete-loan-amount">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $('#loanAmountsTable').DataTable({
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
                infoEmpty: 'Tidak ada data',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada Konfigurasi DSR',
                paginate: { next: 'Berikutnya', previous: 'Sebelumnya' },
            },
        });

        $('.js-delete-loan-amount').on('submit', function (event) {
            event.preventDefault();
            const form = this;

            AppAlert.fire({
                icon: 'warning',
                title: 'Hapus Konfigurasi DSR?',
                text: 'Data Konfigurasi DSR akan dihapus.',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endpush
