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
            <div class="flex flex-wrap gap-2">
                <button type="button" class="js-open-import-modal lender-btn border border-slate-300 bg-white text-slate-700 shadow-sm hover:bg-slate-50 lender-btn-md" data-target="#employeeImportModal">
                    <i class="fa-solid fa-file-import"></i>
                    Import
                </button>
                <a href="{{ route('lender.employees.create') }}" class="lender-btn bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500 lender-btn-md shadow-sm">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Karyawan
                </a>
            </div>
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
                                    <a href="{{ route('lender.employees.show', $employee) }}" class="lender-btn bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500 lender-btn-sm">
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

        <div id="employeeImportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
            <div class="w-full max-w-xl rounded-lg bg-white text-slate-950 shadow-xl">
                <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-semibold">Import Karyawan</h2>
                        <p class="mt-1 text-sm text-slate-500">Upload file Excel sesuai template.</p>
                    </div>
                    <button type="button" class="js-close-import-modal rounded-md px-2 py-1 text-slate-500 hover:bg-slate-100" data-target="#employeeImportModal">Tutup</button>
                </div>

                <form action="{{ route('lender.employees.import') }}" method="POST" enctype="multipart/form-data" class="px-5 py-4">
                    @csrf

                    <div class="grid gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-900">Perusahaan</label>
                            <select name="id_perusahaan" class="js-import-company w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20">
                                <option value="">Ikuti code_perusahaan dari file</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" @selected(old('id_perusahaan') == $company->id)>
                                        {{ $company->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rounded-md border border-slate-200 bg-slate-50/70 p-3 text-sm text-slate-600">
                            Pilih perusahaan jika semua baris file ditujukan untuk satu perusahaan. Jika dikosongkan, sistem akan membaca kolom <span class="font-medium text-slate-900">code_perusahaan</span>.
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-900">Template</label>
                            <a href="{{ route('lender.employees.template.import') }}" data-base-url="{{ route('lender.employees.template.import') }}" class="js-template-download inline-flex items-center gap-2 rounded-md bg-slate-100 px-3 py-2 text-sm font-medium text-slate-900 hover:bg-slate-200">
                                <i class="fa-solid fa-download"></i>
                                Download Template
                            </a>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-900">File Excel</label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20" required>
                            @error('file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" class="js-close-import-modal lender-btn bg-slate-50 text-slate-950 hover:bg-slate-100 lender-btn-md" data-target="#employeeImportModal">Batal</button>
                        <button type="submit" class="lender-btn bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500 lender-btn-md">Upload</button>
                    </div>
                </form>
            </div>
        </div>
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

        $(document).on('click', '.js-open-import-modal', function () {
            const target = $(this).data('target');
            $(target).removeClass('hidden').addClass('flex');
        });

        $(document).on('click', '.js-close-import-modal', function () {
            const target = $(this).data('target');
            $(target).addClass('hidden').removeClass('flex');
        });

        $(document).on('click', '#employeeImportModal', function (event) {
            if (event.target === this) {
                $(this).addClass('hidden').removeClass('flex');
            }
        });

        $(document).on('keydown', function (event) {
            if (event.key === 'Escape') {
                $('#employeeImportModal.flex').addClass('hidden').removeClass('flex');
            }
        });

        function syncTemplateDownloadUrl() {
            const companyId = $('.js-import-company').val();
            const link = $('.js-template-download');
            const baseUrl = link.data('base-url');

            link.attr('href', companyId ? `${baseUrl}?id_perusahaan=${companyId}` : baseUrl);
        }

        $('.js-import-company').on('change', syncTemplateDownloadUrl);
        syncTemplateDownloadUrl();
    </script>
@endpush
