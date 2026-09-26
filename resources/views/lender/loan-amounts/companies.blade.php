@extends('lender.layouts.app')

@section('title', 'Konfigurasi DSR')

@push('styles')
    <style>
        #loanAmountCompaniesTable th,
        #loanAmountCompaniesTable td {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-normal text-slate-950">Konfigurasi DSR</h1>
            <p class="mt-1 text-sm text-slate-600">Pilih perusahaan untuk mengatur Konfigurasi DSR.</p>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <div>
                <table id="loanAmountCompaniesTable" class="display nowrap w-full min-w-[720px]">
                    <thead>
                        <tr>
                            <th>Perusahaan</th>
                            <th>Status DSR</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                            <tr>
                                <td>
                                    <div class="font-medium text-slate-950">{{ $company->nama }}</div>
                                </td>
                                <td>
                                    {{ $company->loan_amount_exists ? 'Sudah diatur' : 'Belum diatur' }}
                                </td>
                                <td>
                                    <a href="{{ route('lender.loan-amounts.index', ['company_id' => $company->id]) }}" class="inline-flex items-center gap-2 rounded-md bg-primary px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-dark">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                        Konfigurasi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $('#loanAmountCompaniesTable').DataTable({
            responsive: false,
            scrollX: true,
            autoWidth: false,
            paging: true,
            info: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'Semua'],
            ],
            language: {
                lengthMenu: 'Tampilkan _MENU_ data',
                search: 'Cari:',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada perusahaan yang terdaftar untuk lender ini.',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya',
                },
            },
        });
    </script>
@endpush
