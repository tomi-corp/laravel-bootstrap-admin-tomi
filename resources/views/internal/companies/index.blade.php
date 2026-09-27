@extends('internal.layouts.app')

@section('title', 'Internal Perusahaan')

@section('content')
<div class="grid grid-cols-1 gap-4 md:grid-cols-12">
    <div class="md:col-span-12">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="sm:order-2">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                    <li class="text-blue-600"><a href="javascript: void(0);">Internal</a></li>
                    <li class="font-medium text-slate-600">Perusahaan</li>
                </ol>
            </div>
            <h4 class="text-2xl font-bold text-slate-950">Internal Perusahaan</h4>
        </div>
    </div>

    <div class="md:col-span-6">
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="p-5">
                <div class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-500">Perusahaan Aktif</div>
                <div class="text-2xl font-semibold text-slate-950">{{ $activeCompanies }}</div>
            </div>
        </div>
    </div>

    <div class="md:col-span-6">
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="p-5">
                <div class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-500">Perusahaan Nonaktif</div>
                <div class="text-2xl font-semibold text-slate-950">{{ $inactiveCompanies }}</div>
            </div>
        </div>
    </div>

    <div class="md:col-span-12">
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="p-5">
                <button type="button" class="mb-4 inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700" onclick="openCreateModal()">
                    <i class="fa-solid fa-circle-plus"></i> Tambah Perusahaan
                </button>

                <table id="companiesTable" class="w-full text-left text-sm text-slate-700">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>PIC Perusahaan</th>
                            <th>PIC Internal</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Lender</th>
                            <th class="none">Kode Unik</th>
                            <th class="none">ID Perusahaan</th>
                            <th class="none">Payment Date</th>
                            <th class="none">H-Min Jatuh Tempo</th>
                            <th class="none">Provinsi</th>
                            <th class="none">Kabupaten/Kota</th>
                            <th class="none">Kecamatan</th>
                            <th class="none">Desa</th>
                            <th class="none">Alamat</th>
                            <th class="none">Bank</th>
                            <th class="none">No Rekening</th>
                            <th class="none">Atas Nama Rekening</th>
                            <th class="none">Direktur Utama</th>
                            <th class="none">Nomor PKS</th>
                            <th class="none">Professional Fee</th>
                            <th class="none">Set Pinjaman</th>
                            <th class="none">Tanggal Aktif</th>
                            <th class="none">Max Plafon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="companyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-lg font-bold text-slate-950" id="companyModalTitle">Tambah Perusahaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="companyForm">
                <div class="modal-body">
                    <input type="hidden" id="companyId">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                        <div class="space-y-2 md:col-span-4">
                            <label class="block text-sm font-semibold text-slate-700">Lender</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="id_lender" id="id_lender">
                                <option value="">Pilih Lender</option>
                                @foreach ($lenders as $lender)
                                    <option value="{{ $lender->id }}">{{ $lender->nama }}</option>
                                @endforeach
                            </select>
                            <small class="text-xs text-red-600 error-id_lender"></small>
                        </div>
                        <div class="space-y-2 md:col-span-4">
                            <label class="block text-sm font-semibold text-slate-700">Nama Perusahaan</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="nama" id="nama">
                            <small class="text-xs text-red-600 error-nama"></small>
                        </div>
                        <div class="space-y-2 md:col-span-4">
                            <label class="block text-sm font-semibold text-slate-700">Email Perusahaan</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="email_perusahaan" id="email_perusahaan" type="email">
                            <small class="text-xs text-red-600 error-email_perusahaan"></small>
                        </div>
                        <div class="space-y-2 md:col-span-4">
                            <label class="block text-sm font-semibold text-slate-700">PIC Perusahaan</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="pic_perusahaan" id="pic_perusahaan">
                            <small class="text-xs text-red-600 error-pic_perusahaan"></small>
                        </div>
                        <div class="space-y-2 md:col-span-4">
                            <label class="block text-sm font-semibold text-slate-700">PIC Internal</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="pic_internal" id="pic_internal">
                            <small class="text-xs text-red-600 error-pic_internal"></small>
                        </div>
                        <div class="space-y-2 md:col-span-4">
                            <label class="block text-sm font-semibold text-slate-700">Telepon</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="telp" id="telp">
                            <small class="text-xs text-red-600 error-telp"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Provinsi</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="id_provinsi" id="id_provinsi">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->provinsi }}</option>
                                @endforeach
                            </select>
                            <small class="text-xs text-red-600 error-id_provinsi"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Kabupaten/Kota</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="id_kabupaten" id="id_kabupaten"></select>
                            <small class="text-xs text-red-600 error-id_kabupaten"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Kecamatan</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="id_kecamatan" id="id_kecamatan"></select>
                            <small class="text-xs text-red-600 error-id_kecamatan"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Desa</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="id_desa" id="id_desa"></select>
                            <small class="text-xs text-red-600 error-id_desa"></small>
                        </div>
                        <div class="space-y-2 md:col-span-12">
                            <label class="block text-sm font-semibold text-slate-700">Alamat Lengkap</label>
                            <textarea class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="alamat_lengkap" id="alamat_lengkap" rows="2"></textarea>
                            <small class="text-xs text-red-600 error-alamat_lengkap"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Bank</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="bank" id="bank">
                            <small class="text-xs text-red-600 error-bank"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">No Rekening</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="no_rekening" id="no_rekening">
                            <small class="text-xs text-red-600 error-no_rekening"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Atas Nama Rekening</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="atas_nama_rekening" id="atas_nama_rekening">
                            <small class="text-xs text-red-600 error-atas_nama_rekening"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Direktur Utama</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="nama_direktur_utama" id="nama_direktur_utama">
                            <small class="text-xs text-red-600 error-nama_direktur_utama"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Nomor PKS</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="nomor_pks" id="nomor_pks">
                            <small class="text-xs text-red-600 error-nomor_pks"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Payment Date</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="payment_date" id="payment_date" type="number" min="1" max="31">
                            <small class="text-xs text-red-600 error-payment_date"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">H-Min Jatuh Tempo</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="h_min_jatuh_tempo" id="h_min_jatuh_tempo" type="number" min="0" max="31">
                            <small class="text-xs text-red-600 error-h_min_jatuh_tempo"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Tanggal Aktif</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="active_tanggal" id="active_tanggal" type="number" min="0" max="31">
                            <small class="text-xs text-red-600 error-active_tanggal"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Status</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="status" id="status">
                                <option value="active">Active</option>
                                <option value="nonactive">Nonactive</option>
                            </select>
                            <small class="text-xs text-red-600 error-status"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Professional Fee</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="active_profesional_fee" id="active_profesional_fee">
                                <option value="active">Active</option>
                                <option value="nonactive">Nonactive</option>
                            </select>
                            <small class="text-xs text-red-600 error-active_profesional_fee"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Set Pinjaman</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="active_set_pinjaman" id="active_set_pinjaman">
                                <option value="active">Active</option>
                                <option value="nonactive">Nonactive</option>
                            </select>
                            <small class="text-xs text-red-600 error-active_set_pinjaman"></small>
                        </div>
                        <div class="space-y-2 md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700">Max Plafon</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="max_plafon" id="max_plafon" type="number" min="0">
                            <small class="text-xs text-red-600 error-max_plafon"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50" type="button" data-dismiss="modal">Cancel</button>
                    <button class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60" type="submit" id="saveCompanyButton">
                        Save <span id="saveSpinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var isHydratingForm = false;
    var table = $('#companiesTable').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [
            { extend: 'excel', className: 'inline-flex items-center gap-2 rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700', text: '<i class="fa-solid fa-file-excel"></i><span>Excel</span>' },
            { extend: 'copy', className: 'inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700', text: '<i class="fa-solid fa-copy"></i><span>Copy</span>' },
            { extend: 'print', className: 'inline-flex items-center gap-2 rounded-md bg-slate-700 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800', text: '<i class="fa-solid fa-print"></i><span>Print</span>' }
        ],
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            type: 'POST',
            url: '{{ route('internal.companies.table') }}',
            data: function (d) {
                d._token = '{{ csrf_token() }}';
                return d;
            }
        },
        columns: [
            { data: 'nama', name: 'nama', defaultContent: '-' },
            { data: 'pic_perusahaan', name: 'pic_perusahaan', defaultContent: '-' },
            { data: 'pic_internal', name: 'pic_internal', defaultContent: '-' },
            { data: 'email_perusahaan', name: 'email_perusahaan', defaultContent: '-' },
            { data: 'telp', name: 'telp', defaultContent: '-' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false, defaultContent: '-' },
            { data: 'lender_name', name: 'lender_name', orderable: false, defaultContent: '-' },
            { data: 'unique_code', name: 'unique_code', defaultContent: '-' },
            { data: 'id_code_perusahaan', name: 'id_code_perusahaan', defaultContent: '-' },
            { data: 'payment_date', name: 'payment_date', defaultContent: '-' },
            { data: 'h_min_jatuh_tempo', name: 'h_min_jatuh_tempo', defaultContent: '-' },
            { data: 'province_name', name: 'province_name', orderable: false, defaultContent: '-' },
            { data: 'city_name', name: 'city_name', orderable: false, defaultContent: '-' },
            { data: 'district_name', name: 'district_name', orderable: false, defaultContent: '-' },
            { data: 'village_name', name: 'village_name', orderable: false, defaultContent: '-' },
            { data: 'alamat_lengkap', name: 'alamat_lengkap', defaultContent: '-' },
            { data: 'bank', name: 'bank', defaultContent: '-' },
            { data: 'no_rekening', name: 'no_rekening', defaultContent: '-' },
            { data: 'atas_nama_rekening', name: 'atas_nama_rekening', defaultContent: '-' },
            { data: 'nama_direktur_utama', name: 'nama_direktur_utama', defaultContent: '-' },
            { data: 'nomor_pks', name: 'nomor_pks', defaultContent: '-' },
            { data: 'active_profesional_fee', name: 'active_profesional_fee', defaultContent: '-' },
            { data: 'active_set_pinjaman', name: 'active_set_pinjaman', defaultContent: '-' },
            { data: 'active_tanggal', name: 'active_tanggal', defaultContent: '-' },
            { data: 'max_plafon', name: 'max_plafon', defaultContent: '-' },
            { data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: '-' }
        ],
        initComplete: function () {
            styleDataTable();
        },
        drawCallback: function () {
            styleDataTable();
        }
    });

    $('#id_lender, #id_provinsi, #id_kabupaten, #id_kecamatan, #id_desa').select2({ width: '100%' });
    styleSelect2();

    function styleDataTable() {
        $('.dt-buttons').addClass('mb-4 flex flex-wrap gap-2');
        $('.dataTables_length, .dataTables_filter').addClass('mb-4 text-sm text-slate-600');
        $('.dataTables_length select, .dataTables_filter input').addClass('ml-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20');
        $('.dataTables_info').addClass('mt-4 text-sm text-slate-500');
        $('.dataTables_paginate').addClass('mt-4 text-sm');
        $('#companiesTable').addClass('border-separate border-spacing-0');
        $('#companiesTable thead th').addClass('border-b border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500');
        $('#companiesTable tbody td').addClass('border-b border-slate-100 px-4 py-3 align-middle text-sm text-slate-700');
        $('#companiesTable tbody tr').addClass('transition hover:bg-slate-50');
    }

    function styleSelect2() {
        $('.select2-container .select2-selection').addClass('min-h-[38px] rounded-md border border-slate-300');
        $('.select2-container .select2-selection__rendered').addClass('text-sm text-slate-900');
    }

    $(document).on('input', '.uppercase', function () {
        var cursorPosition = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(cursorPosition, cursorPosition);
    });

    function openCreateModal() {
        clearErrors();
        $('#companyForm')[0].reset();
        $('#companyId').val('');
        $('#id_kabupaten, #id_kecamatan, #id_desa').empty().trigger('change');
        $('#status, #active_profesional_fee, #active_set_pinjaman').val('active');
        $('#payment_date').val(25);
        $('#h_min_jatuh_tempo').val(9);
        $('#active_tanggal').val(1);
        $('#companyModalTitle').text('Tambah Perusahaan');
        $('#companyModal').modal('show');
    }

    function editCompany(id) {
        clearErrors();
        $.get('{{ route('internal.companies.show', ':id') }}'.replace(':id', id), function (response) {
            var company = response.data;
            isHydratingForm = true;
            $('#companyId').val(company.id);
            $('#companyModalTitle').text('Ubah Perusahaan');

            fillOptions('#id_kabupaten', response.cities, 'id', 'kabupaten');
            fillOptions('#id_kecamatan', response.districts, 'id', 'kecamatan');
            fillOptions('#id_desa', response.villages, 'id', 'desa', 'kodepos');

            Object.keys(company).forEach(function (key) {
                $('#' + key).val(company[key]).trigger('change');
            });

            isHydratingForm = false;
            $('#companyModal').modal('show');
        });
    }

    function deleteCompany(id) {
        Swal.fire({
            title: 'Hapus perusahaan?',
            text: 'Data perusahaan internal akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: '{{ route('internal.companies.destroy', ':id') }}'.replace(':id', id),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    toastr.success(response.message);
                    table.ajax.reload();
                },
                error: function () {
                    toastr.error('Gagal menghapus data perusahaan internal');
                }
            });
        });
    }

    $('#id_provinsi').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('internal.companies.cities') }}', { province_id: this.value }, '#id_kabupaten', 'kabupaten');
        $('#id_kecamatan, #id_desa').empty().trigger('change');
    });

    $('#id_kabupaten').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('internal.companies.districts') }}', { city_id: this.value }, '#id_kecamatan', 'kecamatan');
        $('#id_desa').empty().trigger('change');
    });

    $('#id_kecamatan').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('internal.companies.villages') }}', { district_id: this.value }, '#id_desa', 'desa', 'kodepos');
    });

    $('#companyForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#companyId').val();
        var method = id ? 'PUT' : 'POST';
        var url = id ? '{{ route('internal.companies.update', ':id') }}'.replace(':id', id) : '{{ route('internal.companies.store') }}';

        $('#saveCompanyButton').attr('disabled', true);
        $('#saveSpinner').addClass('fa-solid fa-spinner animate-spin');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function (response) {
                toastr.success(response.message);
                $('#companyModal').modal('hide');
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                        $('.error-' + key).text(xhr.responseJSON.errors[key][0]);
                    });
                    return;
                }

                toastr.error('Gagal menyimpan data perusahaan internal');
            },
            complete: function () {
                $('#saveCompanyButton').attr('disabled', false);
                $('#saveSpinner').removeClass('fa-solid fa-spinner animate-spin');
            }
        });
    });

    function loadOptions(url, data, selector, labelKey, secondaryLabelKey) {
        if (!Object.values(data)[0]) {
            $(selector).empty().trigger('change');
            return;
        }

        $.get(url, data, function (items) {
            fillOptions(selector, items, 'id', labelKey, secondaryLabelKey);
        });
    }

    function fillOptions(selector, items, valueKey, labelKey, secondaryLabelKey) {
        $(selector).empty().append(new Option('Pilih', '', false, false));
        items.forEach(function (item) {
            var label = item[labelKey];
            if (secondaryLabelKey && item[secondaryLabelKey]) {
                label += ' - ' + item[secondaryLabelKey];
            }
            $(selector).append(new Option(label, item[valueKey], false, false));
        });
        $(selector).trigger('change');
    }

    function clearErrors() {
        $('[class*="error-"]').text('');
    }
</script>
@endpush
