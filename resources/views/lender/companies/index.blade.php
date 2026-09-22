@extends('lender.layouts.app')

@section('title', 'List Perusahaan')
@section('page-title', 'List Perusahaan')
@section('page-subtitle', 'Kelola data perusahaan lender')

@push('styles')
    <style>
        #companiesTable th,
        #companiesTable td {
            white-space: nowrap;
        }

        .dt-scroll-body {
            border-bottom: 1px solid #DFF1FF;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal text-primary-950">List Perusahaan</h1>
                <p class="mt-1 text-sm text-primary-800">Kelola perusahaan yang terhubung dengan lender.</p>
            </div>
            <a href="{{ route('lender.companies.create') }}" class="inline-flex items-center gap-2 rounded-md bg-[#0B8FE8] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#0878C4]">
                <i class="fa-solid fa-plus"></i>
                Tambah Perusahaan
            </a>
        </div>

        <section class="rounded-lg border border-primary-100 bg-white p-5 text-primary-950 shadow-sm shadow-primary-950/10">
            <div>
                <table id="companiesTable" class="display nowrap w-full min-w-[1100px]">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>PIC</th>
                            <th>Email</th>
                            <th>Lender</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                            <tr>
                                <td>{{ $company->nama }}</td>
                                <td>{{ $company->pic_perusahaan }}</td>
                                <td>{{ $company->email_perusahaan ?? '-' }}</td>
                                <td>{{ optional($company->lender)->nama ?? '-' }}</td>
                                <td>
                                    <span class="rounded-full px-2 py-1 text-xs font-medium {{ $company->status === 'active' ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $company->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('lender.companies.show', $company) }}" class="rounded-md bg-[#0B8FE8] px-3 py-1.5 text-sm font-medium text-white hover:bg-[#0878C4]">
                                            Detail
                                        </a>
                                        <a href="{{ route('lender.companies.edit', $company) }}" class="rounded-md bg-yellow-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-yellow-600">
                                            Edit
                                        </a>
                                        <form action="{{ route('lender.companies.destroy', $company) }}" method="POST" class="js-delete-company">
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
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $('#companiesTable').DataTable({
            responsive: false,
            scrollX: true,
            scrollY: '420px',
            scrollCollapse: true,
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
                emptyTable: 'Belum ada data perusahaan',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya',
                },
            },
        });

        $('.js-delete-company').on('submit', function (event) {
            event.preventDefault();
            const form = this;

            AppAlert.fire({
                icon: 'warning',
                title: 'Hapus perusahaan?',
                text: 'Data perusahaan akan dihapus dari daftar.',
                showCancelButton: true,
                confirmButtonColor: '#0B8FE8',
                cancelButtonColor: '#64748b',
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
