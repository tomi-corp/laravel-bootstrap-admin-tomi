@extends('lender.layouts.app')

@section('title', 'Pinjaman')

@push('styles')
    <style>
        #loansTable th,
        #loansTable td {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Pinjaman</h1>
                <p class="mt-1 text-sm text-slate-600">Kelola data dari tabel pinjamen.</p>
            </div>
            <a href="{{ route('lender.loans.create') }}" class="lender-btn lender-btn-primary shadow-sm lender-btn-md">
                <i class="fa-solid fa-plus"></i>
                Tambah Pinjaman
            </a>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <table id="loansTable" class="display nowrap w-full min-w-[1200px]">
                <thead>
                    <tr>
                        <th>ID Pinjaman</th>
                        <th>User</th>
                        <th>Perusahaan</th>
                        <th>Lender</th>
                        <th>Nominal</th>
                        <th>Tenor</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loans as $loan)
                        <tr>
                            <td>{{ $loan->id_pinjaman }}</td>
                            <td>{{ $loan->user?->name ?? $loan->user?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $loan->company?->nama ?? '-' }}</td>
                            <td>{{ $loan->lender?->nama ?? '-' }}</td>
                            <td>Rp {{ number_format($loan->nominal, 0, ',', '.') }}</td>
                            <td>{{ $loan->tenor }} bulan</td>
                            <td><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ str_replace('_', ' ', $loan->status) }}</span></td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('lender.loans.show', $loan) }}" class="lender-btn lender-btn-primary shadow-sm lender-btn-sm">Detail</a>
                                    <a href="{{ route('lender.loans.edit', $loan) }}" class="rounded-md bg-yellow-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-yellow-600">Edit</a>
                                    <form action="{{ route('lender.loans.destroy', $loan) }}" method="POST" class="js-delete-row" data-title="Hapus pinjaman?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700">Hapus</button>
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
        $('#loansTable').DataTable({
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
                emptyTable: 'Belum ada data pinjaman',
                paginate: { next: 'Berikutnya', previous: 'Sebelumnya' },
            },
        });

        $('.js-delete-row').on('submit', function (event) {
            event.preventDefault();
            const form = this;
            AppAlert.fire({
                icon: 'warning',
                title: $(this).data('title'),
                text: 'Data akan dihapus dari daftar.',
                showCancelButton: true,
                                                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then((result) => result.isConfirmed && form.submit());
        });
    </script>
@endpush
