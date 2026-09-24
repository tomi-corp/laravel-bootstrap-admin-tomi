@extends('lender.layouts.app')

@section('title', 'Karyawan TomiDana')

@push('styles')
    <style>
        #employeesTable th,
        #employeesTable td {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Karyawan TomiDana</h1>
                <p class="mt-1 text-sm text-slate-600">Kelola data karyawan perusahaan untuk pengajuan pinjaman.</p>
            </div>
            <a href="{{ route('lender.employees.create') }}" class="lender-btn bg-black text-white shadow-sm hover:bg-slate-800 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-slate-300 lender-btn-md shadow-sm">
                <i class="fa-solid fa-plus"></i>
                Tambah Karyawan
            </a>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <table id="employeesTable" class="display nowrap w-full min-w-[1400px]">
                <thead>
                    <tr>
                        <th>Perusahaan</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>NIP</th>
                        <th>WhatsApp</th>
                        <th>Divisi</th>
                        <th>Departement</th>
                        <th>Jabatan</th>
                        <th>Max Plafon</th>
                        <th>Status</th>
                        <th>SP</th>
                        <th>Approve</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td>{{ $employee->company?->nama ?? '-' }}</td>
                            <td>{{ $employee->nama_karyawan }}</td>
                            <td>{{ $employee->nik }}</td>
                            <td>{{ $employee->nip }}</td>
                            <td>{{ $employee->whatsapp }}</td>
                            <td>{{ $employee->divisi ?? '-' }}</td>
                            <td>{{ $employee->departement }}</td>
                            <td>{{ $employee->jabatan }}</td>
                            <td>Rp {{ number_format((float) $employee->max_plafon, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($employee->status) }}</td>
                            <td>SP {{ $employee->status_sp }}</td>
                            <td>{{ ucfirst($employee->approve_satu) }} / {{ ucfirst($employee->approve_dua) }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('lender.employees.show', $employee) }}" class="lender-btn bg-black text-white shadow-sm hover:bg-slate-800 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-slate-300 lender-btn-sm">
                                        Detail
                                    </a>
                                    <a href="{{ route('lender.employees.edit', $employee) }}" class="rounded-md bg-yellow-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-yellow-600">
                                        Edit
                                    </a>
                                    <form action="{{ route('lender.employees.destroy', $employee) }}" method="POST" class="js-delete-employee">
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
        $('#employeesTable').DataTable({
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
                emptyTable: 'Belum ada data karyawan',
                paginate: { next: 'Berikutnya', previous: 'Sebelumnya' },
            },
        });

        $('.js-delete-employee').on('submit', function (event) {
            event.preventDefault();
            const form = this;

            AppAlert.fire({
                icon: 'warning',
                title: 'Hapus karyawan?',
                text: 'Data karyawan akan dihapus dari daftar.',
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
