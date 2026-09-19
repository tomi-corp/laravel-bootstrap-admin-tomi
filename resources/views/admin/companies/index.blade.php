@extends('layout.app')

@section('title', 'Admin Perusahaan')

@push('css')
<style>
    #companyModal .modal-content {
        max-height: calc(100vh - 3.5rem);
    }

    #companyModal .company-modal-body {
        max-height: calc(100vh - 12rem);
        overflow-y: auto;
    }

    #companyModal .modal-footer {
        flex-shrink: 0;
        border-top: 1px solid #dee2e6;
        background-color: #fff;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Perusahaan</li>
                </ol>
            </div>
            <h4 class="page-title">Perusahaan</h4>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Perusahaan Aktif</div>
                <div class="h4 mb-0">{{ $activeCompanies }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Perusahaan Nonaktif</div>
                <div class="h4 mb-0">{{ $inactiveCompanies }}</div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-sm btn-primary float-right mb-3" onclick="openCreateModal()">
                    <i class="fa-solid fa-circle-plus"></i> Tambah Perusahaan
                </button>

                <table id="companiesTable" class="table dt-responsive table-hover text-center" style="width:100%">
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
                <h5 class="modal-title" id="companyModalTitle">Tambah Perusahaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="companyForm">
                <div class="modal-body company-modal-body">
                    <input type="hidden" id="companyId">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Lender</label>
                            <select class="form-control" name="id_lender" id="id_lender">
                                <option value="">Pilih Lender</option>
                                @foreach ($lenders as $lender)
                                    <option value="{{ $lender->id }}">{{ $lender->nama }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-id_lender"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Perusahaan</label>
                            <input class="form-control text-uppercase" name="nama" id="nama">
                            <small class="text-danger error-nama"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Email Perusahaan</label>
                            <input class="form-control" name="email_perusahaan" id="email_perusahaan" type="email">
                            <small class="text-danger error-email_perusahaan"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>PIC Perusahaan</label>
                            <input class="form-control text-uppercase" name="pic_perusahaan" id="pic_perusahaan">
                            <small class="text-danger error-pic_perusahaan"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>PIC Internal</label>
                            <input class="form-control text-uppercase" name="pic_internal" id="pic_internal">
                            <small class="text-danger error-pic_internal"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Telepon</label>
                            <input class="form-control" name="telp" id="telp">
                            <small class="text-danger error-telp"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Provinsi</label>
                            <select class="form-control" name="id_provinsi" id="id_provinsi">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->provinsi }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-id_provinsi"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kabupaten/Kota</label>
                            <select class="form-control" name="id_kabupaten" id="id_kabupaten"></select>
                            <small class="text-danger error-id_kabupaten"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kecamatan</label>
                            <select class="form-control" name="id_kecamatan" id="id_kecamatan"></select>
                            <small class="text-danger error-id_kecamatan"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Desa</label>
                            <select class="form-control" name="id_desa" id="id_desa"></select>
                            <small class="text-danger error-id_desa"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Alamat Lengkap</label>
                            <textarea class="form-control text-uppercase" name="alamat_lengkap" id="alamat_lengkap" rows="2"></textarea>
                            <small class="text-danger error-alamat_lengkap"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Bank</label>
                            <input class="form-control" name="bank" id="bank">
                            <small class="text-danger error-bank"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>No Rekening</label>
                            <input class="form-control" name="no_rekening" id="no_rekening">
                            <small class="text-danger error-no_rekening"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Atas Nama Rekening</label>
                            <input class="form-control text-uppercase" name="atas_nama_rekening" id="atas_nama_rekening">
                            <small class="text-danger error-atas_nama_rekening"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Direktur Utama</label>
                            <input class="form-control text-uppercase" name="nama_direktur_utama" id="nama_direktur_utama">
                            <small class="text-danger error-nama_direktur_utama"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nomor PKS</label>
                            <input class="form-control text-uppercase" name="nomor_pks" id="nomor_pks">
                            <small class="text-danger error-nomor_pks"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Payment Date</label>
                            <input class="form-control" name="payment_date" id="payment_date" type="number" min="1" max="31">
                            <small class="text-danger error-payment_date"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>H-Min Jatuh Tempo</label>
                            <input class="form-control" name="h_min_jatuh_tempo" id="h_min_jatuh_tempo" type="number" min="0" max="31">
                            <small class="text-danger error-h_min_jatuh_tempo"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tanggal Aktif</label>
                            <input class="form-control" name="active_tanggal" id="active_tanggal" type="number" min="0" max="31">
                            <small class="text-danger error-active_tanggal"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="active">Active</option>
                                <option value="nonactive">Nonactive</option>
                            </select>
                            <small class="text-danger error-status"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Professional Fee</label>
                            <select class="form-control" name="active_profesional_fee" id="active_profesional_fee">
                                <option value="active">Active</option>
                                <option value="nonactive">Nonactive</option>
                            </select>
                            <small class="text-danger error-active_profesional_fee"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Set Pinjaman</label>
                            <select class="form-control" name="active_set_pinjaman" id="active_set_pinjaman">
                                <option value="active">Active</option>
                                <option value="nonactive">Nonactive</option>
                            </select>
                            <small class="text-danger error-active_set_pinjaman"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Max Plafon</label>
                            <input class="form-control" name="max_plafon" id="max_plafon" type="number" min="0">
                            <small class="text-danger error-max_plafon"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit" id="saveCompanyButton">
                        Simpan <span id="saveSpinner"></span>
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
            { extend: 'excel', className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1', text: '<i class="fa-solid fa-file-excel"></i><span class="pl-1">Excel</span>' },
            { extend: 'copy', className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1', text: '<i class="fa-solid fa-copy"></i><span class="pl-1">Copy</span>' },
            { extend: 'print', className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1', text: '<i class="fa-solid fa-print"></i><span class="pl-1">Print</span>' }
        ],
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            type: 'POST',
            url: '{{ route('admin.companies.table') }}',
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
        ]
    });

    $('#id_lender, #id_provinsi, #id_kabupaten, #id_kecamatan, #id_desa').select2({ width: '100%' });

    $(document).on('input', '.text-uppercase', function () {
        var cursorPosition = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(cursorPosition, cursorPosition);
    });

    function openCreateModal() {
        clearErrors();
        $('#companyForm')[0].reset();
        $('#companyId').val('');
        $('#id_lender, #id_provinsi').val('').trigger('change');
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
        $.get('{{ route('admin.companies.show', ':id') }}'.replace(':id', id), function (response) {
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
        if (!confirm('Hapus perusahaan ini?')) {
            return;
        }

        $.ajax({
            url: '{{ route('admin.companies.destroy', ':id') }}'.replace(':id', id),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                toastr.success(response.message);
                table.ajax.reload();
            },
            error: function () {
                toastr.error('Gagal menghapus data perusahaan');
            }
        });
    }

    $('#id_provinsi').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('admin.companies.cities') }}', { province_id: this.value }, '#id_kabupaten', 'kabupaten');
        $('#id_kecamatan, #id_desa').empty().trigger('change');
    });

    $('#id_kabupaten').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('admin.companies.districts') }}', { city_id: this.value }, '#id_kecamatan', 'kecamatan');
        $('#id_desa').empty().trigger('change');
    });

    $('#id_kecamatan').on('change', function () {
        if (isHydratingForm) {
            return;
        }

        loadOptions('{{ route('admin.companies.villages') }}', { district_id: this.value }, '#id_desa', 'desa', 'kodepos');
    });

    $('#companyForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#companyId').val();
        var method = id ? 'PUT' : 'POST';
        var url = id ? '{{ route('admin.companies.update', ':id') }}'.replace(':id', id) : '{{ route('admin.companies.store') }}';

        $('#saveCompanyButton').attr('disabled', true);
        $('#saveSpinner').addClass('spinner-border spinner-border-sm');

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

                toastr.error('Gagal menyimpan data perusahaan');
            },
            complete: function () {
                $('#saveCompanyButton').attr('disabled', false);
                $('#saveSpinner').removeClass('spinner-border spinner-border-sm');
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
