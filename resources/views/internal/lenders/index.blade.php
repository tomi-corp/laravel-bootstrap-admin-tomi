@extends('internal.layouts.app')

@section('title', 'Internal Lender')

@section('content')
<div class="grid grid-cols-1 gap-4 md:grid-cols-12">
    <div class="md:col-span-12">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="sm:order-2">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                    <li class="text-blue-600"><a href="javascript: void(0);">Internal</a></li>
                    <li class="font-medium text-slate-600">Lender</li>
                </ol>
            </div>
            <h4 class="text-2xl font-bold text-slate-950">Internal Lender</h4>
        </div>
    </div>

    <div class="md:col-span-6">
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="p-5">
                <div class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-500">Lender Aktif</div>
                <div class="text-2xl font-semibold text-slate-950">{{ $activeLenders }}</div>
            </div>
        </div>
    </div>

    <div class="md:col-span-6">
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="p-5">
                <div class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-500">Lender Nonaktif</div>
                <div class="text-2xl font-semibold text-slate-950">{{ $inactiveLenders }}</div>
            </div>
        </div>
    </div>

    <div class="md:col-span-12">
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="p-5">
                <div class="mb-4 flex justify-end">
                    <button type="button" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700" onclick="openCreateModal()">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Lender
                    </button>
                </div>

                <table id="lendersTable" class="w-full text-left text-sm text-slate-700">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>PIC Lender</th>
                            <th>PIC Internal</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Bank</th>
                            <th>Status</th>
                            <th class="none">Kode Lender</th>
                            <th class="none">Provinsi</th>
                            <th class="none">Kabupaten/Kota</th>
                            <th class="none">Kecamatan</th>
                            <th class="none">Desa</th>
                            <th class="none">Alamat</th>
                            <th class="none">Nomor PKS</th>
                            <th class="none">Nama Rekening</th>
                            <th class="none">No Rekening</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="lenderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-lg font-bold text-slate-950" id="lenderModalTitle">Tambah Lender</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="lenderForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="lenderId">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Nama Lender</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="nama" id="nama">
                            <small class="text-xs text-red-600 error-nama"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Email Lender</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="email_lender" id="email_lender" type="email">
                            <small class="text-xs text-red-600 error-email_lender"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">PIC Lender</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="pic_lender" id="pic_lender">
                            <small class="text-xs text-red-600 error-pic_lender"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">PIC Internal</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="pic_internal" id="pic_internal">
                            <small class="text-xs text-red-600 error-pic_internal"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Telepon</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="telp" id="telp">
                            <small class="text-xs text-red-600 error-telp"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Nomor PKS</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="nomor_pks" id="nomor_pks">
                            <small class="text-xs text-red-600 error-nomor_pks"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Provinsi</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="id_provinsi" id="id_provinsi">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->provinsi }}</option>
                                @endforeach
                            </select>
                            <small class="text-xs text-red-600 error-id_provinsi"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Kabupaten/Kota</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="id_kabupaten" id="id_kabupaten"></select>
                            <small class="text-xs text-red-600 error-id_kabupaten"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Kecamatan</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="id_kecamatan" id="id_kecamatan"></select>
                            <small class="text-xs text-red-600 error-id_kecamatan"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Desa</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="id_desa" id="id_desa"></select>
                            <small class="text-xs text-red-600 error-id_desa"></small>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700">Alamat Lengkap</label>
                            <textarea class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="alamat_lengkap" id="alamat_lengkap" rows="2"></textarea>
                            <small class="text-xs text-red-600 error-alamat_lengkap"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Bank</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="id_bank" id="id_bank">
                                <option value="">Pilih Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->code_bank }}</option>
                                @endforeach
                            </select>
                            <small class="text-xs text-red-600 error-id_bank"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">No Rekening</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="no_rekening" id="no_rekening">
                            <small class="text-xs text-red-600 error-no_rekening"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Nama Rekening</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="nama_rekening" id="nama_rekening">
                            <small class="text-xs text-red-600 error-nama_rekening"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Status</label>
                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm uppercase text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="status" id="status">
                                <option value="active">Active</option>
                                <option value="nonactive">Nonactive</option>
                            </select>
                            <small class="text-xs text-red-600 error-status"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Logo</label>
                            <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" name="logo" id="logo" type="file" accept="image/jpeg,image/png,image/webp,image/svg+xml">
                            <div id="logoPreview" class="hidden items-center gap-3 rounded-md border border-slate-200 bg-slate-50 p-2">
                                <img id="logoPreviewImage" class="h-10 w-10 rounded object-contain" alt="Logo lender">
                                <a id="logoPreviewLink" class="text-sm font-semibold text-blue-600 hover:text-blue-700" href="#" target="_blank">Lihat logo saat ini</a>
                            </div>
                            <small class="text-xs text-red-600 error-logo"></small>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Color</label>
                            <div class="flex items-center gap-3">
                                <input class="h-10 w-16 cursor-pointer rounded-md border border-slate-300 bg-white p-1 shadow-sm" name="color" id="color" type="color" value="#2563eb">
                                <span id="colorValue" class="text-sm font-semibold uppercase text-slate-700">#2563EB</span>
                            </div>
                            <small class="text-xs text-red-600 error-color"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50" type="button" data-dismiss="modal">Cancel</button>
                    <button class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60" type="submit" id="saveLenderButton">
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
    var defaultCellLimit = 28;

    function escapeHtml(value) {
        return $('<div>').text(value ?? '-').html();
    }

    function truncateCell(limit) {
        return function (data, type) {
            var value = data ?? '-';
            var text = String(value);

            if (type !== 'display') {
                return text;
            }

            if (text.length <= limit) {
                return escapeHtml(text);
            }

            return '<span class="inline-block max-w-[18rem] truncate align-bottom" title="' + escapeHtml(text) + '">'
                + escapeHtml(text.slice(0, limit)) + '...'
                + '</span>';
        };
    }

    var table = $('#lendersTable').DataTable({
        dom: 'lfrtip',
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        ajax: {
            type: 'POST',
            url: '{{ route('internal.lenders.table') }}',
            data: function (d) {
                d._token = '{{ csrf_token() }}';
                return d;
            }
        },
        columns: [
            { data: 'nama', name: 'nama', defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'pic_lender', name: 'pic_lender', defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'pic_internal', name: 'pic_internal', defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'email_lender', name: 'email_lender', defaultContent: '-', render: truncateCell(32) },
            { data: 'telp', name: 'telp', defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'bank_name', name: 'bank_name', orderable: false, defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false, defaultContent: '-' },
            { data: 'id_code_lender', name: 'id_code_lender', defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'province_name', name: 'province_name', orderable: false, defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'city_name', name: 'city_name', orderable: false, defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'district_name', name: 'district_name', orderable: false, defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'village_name', name: 'village_name', orderable: false, defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'alamat_lengkap', name: 'alamat_lengkap', defaultContent: '-', render: truncateCell(36) },
            { data: 'nomor_pks', name: 'nomor_pks', defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'nama_rekening', name: 'nama_rekening', defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'no_rekening', name: 'no_rekening', defaultContent: '-', render: truncateCell(defaultCellLimit) },
            { data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: '-' }
        ],
        initComplete: function () {
            styleDataTable();
        },
        drawCallback: function () {
            styleDataTable();
        }
    });

    function styleDataTable() {
        $('.dataTables_length, .dataTables_filter').addClass('mb-4 text-sm text-slate-600');
        $('.dataTables_length select, .dataTables_filter input').addClass('ml-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20');
        $('.dataTables_info').addClass('mt-4 text-sm text-slate-500');
        $('.dataTables_paginate').addClass('mt-4 text-sm');
        $('.dataTables_scroll').addClass('overflow-hidden rounded-lg border border-slate-200');
        $('#lendersTable').addClass('border-separate border-spacing-0');
        $('#lendersTable thead th').addClass('border-b border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500');
        $('#lendersTable tbody td').addClass('whitespace-nowrap border-b border-slate-100 px-4 py-3 align-middle text-sm text-slate-700');
        $('#lendersTable tbody tr').addClass('transition hover:bg-slate-50');
    }

    $(document).on('input', 'input.uppercase, textarea.uppercase', function () {
        var cursorPosition = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(cursorPosition, cursorPosition);
    });

    function openCreateModal() {
        clearErrors();
        $('#lenderForm')[0].reset();
        $('#lenderId').val('');
        $('#logoPreview').addClass('hidden').removeClass('flex');
        $('#color').val('#2563eb');
        $('#colorValue').text('#2563EB');
        $('#id_kabupaten, #id_kecamatan, #id_desa').empty();
        $('#status').val('active');
        $('#lenderModalTitle').text('Tambah Lender');
        $('#lenderModal').modal('show');
    }

    function editLender(id) {
        clearErrors();
        $.get('{{ route('internal.lenders.show', ':id') }}'.replace(':id', id), function (response) {
            var lender = response.data;
            isHydratingForm = true;
            $('#lenderId').val(lender.id);
            $('#lenderModalTitle').text('Ubah Lender');

            fillOptions('#id_kabupaten', response.cities, 'id', 'kabupaten');
            fillOptions('#id_kecamatan', response.districts, 'id', 'kecamatan');
            fillOptions('#id_desa', response.villages, 'id', 'desa', 'kodepos');

            Object.keys(lender).forEach(function (key) {
                if (key !== 'logo') {
                    $('#' + key).val(lender[key]);
                }
            });

            if (lender.logo) {
                var logoUrl = '{{ asset('storage') }}/' + lender.logo;
                $('#logoPreviewImage').attr('src', logoUrl);
                $('#logoPreviewLink').attr('href', logoUrl);
                $('#logoPreview').removeClass('hidden').addClass('flex');
            } else {
                $('#logoPreview').addClass('hidden').removeClass('flex');
            }

            $('#color').val(lender.color || '#2563eb');
            $('#colorValue').text(($('#color').val() || '#2563eb').toUpperCase());

            isHydratingForm = false;
            $('#lenderModal').modal('show');
        });
    }

    function deleteLender(id) {
        Swal.fire({
            title: 'Hapus lender?',
            text: 'Data lender internal akan dihapus.',
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
                url: '{{ route('internal.lenders.destroy', ':id') }}'.replace(':id', id),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    toastr.success(response.message);
                    table.ajax.reload();
                },
                error: function () {
                    toastr.error('Gagal menghapus data lender internal');
                }
            });
        });
    }

    $('#id_provinsi').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('internal.lenders.cities') }}', { province_id: this.value }, '#id_kabupaten', 'kabupaten');
        $('#id_kecamatan, #id_desa').empty();
    });

    $('#id_kabupaten').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('internal.lenders.districts') }}', { city_id: this.value }, '#id_kecamatan', 'kecamatan');
        $('#id_desa').empty();
    });

    $('#id_kecamatan').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('internal.lenders.villages') }}', { district_id: this.value }, '#id_desa', 'desa', 'kodepos');
    });

    $('#lenderForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#lenderId').val();
        var method = 'POST';
        var url = id ? '{{ route('internal.lenders.update', ':id') }}'.replace(':id', id) : '{{ route('internal.lenders.store') }}';

        $('#lenderForm input.uppercase, #lenderForm textarea.uppercase').each(function () {
            this.value = this.value.toUpperCase();
        });

        var formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');

        if (id) {
            formData.append('_method', 'PUT');
        }

        $('#saveLenderButton').attr('disabled', true);
        $('#saveSpinner').addClass('fa-solid fa-spinner animate-spin');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.message);
                $('#lenderModal').modal('hide');
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                        $('.error-' + key).text(xhr.responseJSON.errors[key][0]);
                    });
                    return;
                }

                toastr.error('Gagal menyimpan data lender internal');
            },
            complete: function () {
                $('#saveLenderButton').attr('disabled', false);
                $('#saveSpinner').removeClass('fa-solid fa-spinner animate-spin');
            }
        });
    });

    function loadOptions(url, data, selector, labelKey, secondaryLabelKey) {
        if (!Object.values(data)[0]) {
            $(selector).empty();
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
    }

    function clearErrors() {
        $('[class*="error-"]').text('');
    }

    $('#color').on('input', function () {
        $('#colorValue').text((this.value || '#2563eb').toUpperCase());
    });
</script>
@endpush
