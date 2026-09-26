@extends('lender.layouts.app')

@section('title', 'Konfigurasi Tenor')

@push('styles')
    <style>
        #configurationsTable th,
        #configurationsTable td {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Konfigurasi Tenor</h1>
                <p class="mt-1 text-sm text-slate-600">
                    @if ($selectedCompany)
                        {{ $selectedCompany->nama }} - kelola tenor, rate, biaya, dan nominal pinjaman.
                    @else
                        Kelola tenor, rate, biaya, dan nominal pinjaman.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('lender.configurations.index') }}" class="lender-btn border border-slate-300 bg-white text-slate-700 shadow-sm hover:bg-slate-50 lender-btn-md">
                    <i class="fa-solid fa-arrow-left"></i>
                    Perusahaan
                </a>
                <a href="{{ route('lender.configurations.create', $selectedCompany ? ['company_id' => $selectedCompany->id] : []) }}" class="lender-btn lender-btn-primary shadow-sm lender-btn-md">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Konfigurasi Tenor
                </a>
            </div>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <table id="configurationsTable" class="display nowrap w-full min-w-[1400px]">
                <thead>
                    <tr>
                        <th>Perusahaan</th>
                        <th>Tenor</th>
                        <th>Rate</th>
                        <th>Asuransi</th>
                        <th>Provisi</th>
                        <th>Biaya Admin</th>
                        <th>Fee TOMI</th>
                        <th>Biaya Layanan</th>
                        <th>Minimal</th>
                        <th>Maksimal</th>
                        <th>PF 1</th>
                        <th>PF 2</th>
                        <th>KSP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tenors as $tenor)
                        <tr>
                            <td>{{ $tenor->company?->nama ?? '-' }}</td>
                            <td>{{ $tenor->tenor }} bulan</td>
                            <td>{{ number_format($tenor->rate, 2, ',', '.') }}</td>
                            <td>{{ number_format($tenor->asuransi, 2, ',', '.') }}</td>
                            <td>{{ number_format($tenor->provisi, 2, ',', '.') }}</td>
                            <td>{{ number_format($tenor->biaya_admin, 2, ',', '.') }}</td>
                            <td>{{ number_format($tenor->fee_tomi, 2, ',', '.') }}</td>
                            <td>Rp {{ number_format($tenor->biaya_layanan ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($tenor->minimal_nominal ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($tenor->maximal_nominal ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($tenor->profesional_fee_1, 2, ',', '.') }}</td>
                            <td>{{ number_format($tenor->profesional_fee_2, 2, ',', '.') }}</td>
                            <td>{{ number_format($tenor->ksp, 2, ',', '.') }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('lender.configurations.edit', $tenor) }}" class="rounded-md bg-yellow-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-yellow-600">
                                        Edit
                                    </a>
                                    <form action="{{ route('lender.configurations.destroy', $tenor) }}" method="POST" class="js-delete-configuration">
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
        $('#configurationsTable').DataTable({
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
                emptyTable: 'Belum ada konfigurasi tenor',
                paginate: { next: 'Berikutnya', previous: 'Sebelumnya' },
            },
        });

        $('.js-delete-configuration').on('submit', function (event) {
            event.preventDefault();
            const form = this;

            AppAlert.fire({
                icon: 'warning',
                title: 'Hapus konfigurasi?',
                text: 'Data konfigurasi tenor akan dihapus.',
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
