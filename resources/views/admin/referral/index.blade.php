@extends('layout.app')
@section('title', 'Referral')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                    <li class="breadcrumb-item active">Referral</li>
                </ol>
            </div>
            <h4 class="page-title">Referral</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">List Referral</h4>
                    <button class="btn btn-primary" type="button" onclick="openAddModal()">
                        <i class="fa-solid fa-plus"></i> Tambah Referral
                    </button>
                </div>

                <div class="table-responsive">
                    <table id="tableReferral" class="table table-hover table-bordered nowrap text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Created At</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Email</th>
                                <th>Bank</th>
                                <th>No. Rekening</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formTitle">Tambah Referral</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="referral_id">

                <h5 class="mb-3">Identitas KTP</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>NIK</label>
                            <input class="form-control" id="national_id_number" type="text" maxlength="16">
                            <small id="errNationalIdNumber" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama</label>
                            <input class="form-control" id="name" type="text">
                            <small id="errName" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <small id="errIsActive" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tempat Lahir</label>
                            <input class="form-control" id="birth_place" type="text">
                            <small id="errBirthPlace" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input class="form-control" id="birth_date" type="date">
                            <small id="errBirthDate" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Gender</label>
                            <select class="form-control" id="gender">
                                <option value="">Pilih</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                            <small id="errGender" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Gol. Darah</label>
                            <select class="form-control" id="blood_type">
                                <option value="">Pilih</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                                <option value="-">-</option>
                            </select>
                            <small id="errBloodType" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" id="address" rows="2"></textarea>
                            <small id="errAddress" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>RT</label>
                            <input class="form-control" id="rt" type="text" maxlength="3">
                            <small id="errRt" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>RW</label>
                            <input class="form-control" id="rw" type="text" maxlength="3">
                            <small id="errRw" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kelurahan/Desa</label>
                            <select class="form-control" id="village_id">
                                <option value="">Pilih Kelurahan/Desa</option>
                            </select>
                            <small id="errVillageId" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kecamatan</label>
                            <select class="form-control" id="district_id">
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <small id="errDistrictId" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kota/Kabupaten</label>
                            <select class="form-control" id="city_id">
                                <option value="">Pilih Kota/Kabupaten</option>
                            </select>
                            <small id="errCityId" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Provinsi</label>
                            <select class="form-control" id="province_id">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->provinsi }}</option>
                                @endforeach
                            </select>
                            <small id="errProvinceId" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Pos</label>
                            <input class="form-control" id="postal_code" type="text">
                            <small id="errPostalCode" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Agama</label>
                            <select class="form-control" id="religion">
                                <option value="">Pilih Agama</option>
                                <option value="islam">Islam</option>
                                <option value="christian">Kristen</option>
                                <option value="catholic">Katolik</option>
                                <option value="hindu">Hindu</option>
                                <option value="buddhist">Buddha</option>
                                <option value="confucian">Konghucu</option>
                            </select>
                            <small id="errReligion" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Perkawinan</label>
                            <select class="form-control" id="marital_status">
                                <option value="">Pilih Status Perkawinan</option>
                                <option value="single">Belum Kawin</option>
                                <option value="married">Kawin</option>
                                <option value="divorced">Cerai Hidup</option>
                                <option value="widowed">Cerai Mati</option>
                            </select>
                            <small id="errMaritalStatus" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pekerjaan</label>
                            <input class="form-control" id="occupation" type="text">
                            <small id="errOccupation" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kewarganegaraan</label>
                            <input class="form-control" id="nationality" type="text" value="WNI">
                            <small id="errNationality" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>File KTP</label>
                            <input class="form-control" id="id_card_file" type="file" accept=".jpg,.jpeg,.png,.pdf">
                            <small id="idCardCurrent" class="form-text text-muted"></small>
                            <small id="errIdCardFile" class="text-danger"></small>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3 mt-3">Kontak dan Rekening</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Telepon/WhatsApp</label>
                            <input class="form-control" id="phone_number" type="text">
                            <small id="errPhoneNumber" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" id="email" type="email">
                            <small id="errEmail" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Bank</label>
                            <select class="form-control" id="bank_id">
                                <option value="">Pilih Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->code_bank ?? $bank->nama ?? 'Bank #'.$bank->id }}</option>
                                @endforeach
                            </select>
                            <small id="errBankId" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>No. Rekening</label>
                            <input class="form-control" id="bank_account_number" type="text">
                            <small id="errBankAccountNumber" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Pemilik Rekening</label>
                            <input class="form-control" id="bank_account_name" type="text">
                            <small id="errBankAccountName" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>File Buku Tabungan</label>
                            <input class="form-control" id="bank_book_file" type="file" accept=".jpg,.jpeg,.png,.pdf">
                            <small id="bankBookCurrent" class="form-text text-muted"></small>
                            <small id="errBankBookFile" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea class="form-control" id="notes" rows="2"></textarea>
                            <small id="errNotes" class="text-danger"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="button" id="buttonSubmit">
                    Save <div id="loadingText"></div>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Referral</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <div class="modal-body text-center">
                <p>Apakah anda yakin menghapus referral ini?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" type="button" id="buttonDelete" onclick="buttonDelete()">
                    Delete <div id="loadingTextDelete"></div>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let deleteID = '';
    const csrfToken = "{{ csrf_token() }}";

    const fields = [
        'national_id_number', 'name', 'birth_place', 'birth_date', 'gender', 'blood_type',
        'address', 'rt', 'rw', 'province_id', 'city_id', 'district_id', 'village_id', 'postal_code',
        'religion', 'marital_status', 'occupation', 'nationality', 'phone_number', 'email',
        'bank_id', 'bank_account_number', 'bank_account_name', 'is_active', 'notes'
    ];

    const errorMap = {
        national_id_number: '#errNationalIdNumber',
        name: '#errName',
        birth_place: '#errBirthPlace',
        birth_date: '#errBirthDate',
        gender: '#errGender',
        blood_type: '#errBloodType',
        address: '#errAddress',
        rt: '#errRt',
        rw: '#errRw',
        province_id: '#errProvinceId',
        city_id: '#errCityId',
        district_id: '#errDistrictId',
        village_id: '#errVillageId',
        postal_code: '#errPostalCode',
        religion: '#errReligion',
        marital_status: '#errMaritalStatus',
        occupation: '#errOccupation',
        nationality: '#errNationality',
        id_card_file: '#errIdCardFile',
        phone_number: '#errPhoneNumber',
        email: '#errEmail',
        bank_id: '#errBankId',
        bank_account_number: '#errBankAccountNumber',
        bank_account_name: '#errBankAccountName',
        bank_book_file: '#errBankBookFile',
        is_active: '#errIsActive',
        notes: '#errNotes',
    };

    const clearErrors = () => {
        Object.values(errorMap).forEach((selector) => $(selector).text(''));
    };

    const resetForm = () => {
        $('#referral_id').val('');
        fields.forEach((field) => $(`#${field}`).val(field === 'nationality' ? 'WNI' : ''));
        resetRegionSelect('#city_id', 'Pilih Kota/Kabupaten');
        resetRegionSelect('#district_id', 'Pilih Kecamatan');
        resetRegionSelect('#village_id', 'Pilih Kelurahan/Desa');
        $('#is_active').val('1');
        $('#id_card_file').val('');
        $('#bank_book_file').val('');
        $('#idCardCurrent').text('');
        $('#bankBookCurrent').text('');
        $('#formTitle').text('Tambah Referral');
        $('#buttonSubmit').html('Save <div id="loadingText"></div>');
        clearErrors();
    };

    const resetRegionSelect = (selector, placeholder) => {
        $(selector).html(`<option value="">${placeholder}</option>`);
    };

    const fillRegionSelect = (selector, rows, valueKey, textKey, placeholder, selectedValue = '') => {
        resetRegionSelect(selector, placeholder);

        rows.forEach((row) => {
            const suffix = row.kodepos ? ` ${row.kodepos}` : '';
            $(selector).append($('<option>', {
                value: row[valueKey],
                text: `${row[textKey]}${suffix}`,
            }));
        });

        if (selectedValue) {
            $(selector).val(String(selectedValue));
        }
    };

    const loadCities = (provinceId, selectedValue = '') => {
        resetRegionSelect('#city_id', 'Pilih Kota/Kabupaten');
        resetRegionSelect('#district_id', 'Pilih Kecamatan');
        resetRegionSelect('#village_id', 'Pilih Kelurahan/Desa');

        if (!provinceId) {
            return $.Deferred().resolve().promise();
        }

        return $.post('{!! route('admin.referral.cities') !!}', {
            _token: csrfToken,
            province_id: provinceId,
        }).done((rows) => fillRegionSelect('#city_id', rows, 'id', 'kabupaten', 'Pilih Kota/Kabupaten', selectedValue));
    };

    const loadDistricts = (cityId, selectedValue = '') => {
        resetRegionSelect('#district_id', 'Pilih Kecamatan');
        resetRegionSelect('#village_id', 'Pilih Kelurahan/Desa');

        if (!cityId) {
            return $.Deferred().resolve().promise();
        }

        return $.post('{!! route('admin.referral.districts') !!}', {
            _token: csrfToken,
            city_id: cityId,
        }).done((rows) => fillRegionSelect('#district_id', rows, 'id', 'kecamatan', 'Pilih Kecamatan', selectedValue));
    };

    const loadVillages = (districtId, selectedValue = '') => {
        resetRegionSelect('#village_id', 'Pilih Kelurahan/Desa');

        if (!districtId) {
            return $.Deferred().resolve().promise();
        }

        return $.post('{!! route('admin.referral.villages') !!}', {
            _token: csrfToken,
            district_id: districtId,
        }).done((rows) => fillRegionSelect('#village_id', rows, 'id', 'desa', 'Pilih Kelurahan/Desa', selectedValue));
    };

    window.openAddModal = () => {
        resetForm();
        $('#modalForm').modal('show');
    };

    const tableReferral = $('#tableReferral').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [{
            extend: 'excel',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<i class="fa-solid fa-file-excel"></i><span class="pl-1">Excel</span>',
            title: 'List Referral',
            exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
        }, {
            extend: 'copy',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<i class="fa-solid fa-copy"></i><span class="pl-1">Copy</span>',
            title: 'List Referral',
            exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
        }, {
            extend: 'print',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<i class="fa-solid fa-print"></i><span class="pl-1">Print</span>',
            title: 'List Referral',
            exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
        }],
        lengthMenu: [10, 50, 100, 500, 1000],
        processing: true,
        serverSide: true,
        scrollX: true,
        responsive: false,
        ajax: {
            type: 'POST',
            url: '{!! route('admin.referral.table') !!}',
            data: function (data) {
                data._token = csrfToken;
                return data;
            },
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'created_at_formatted' },
            { data: 'national_id_number' },
            { data: 'name' },
            { data: 'phone_number' },
            { data: 'email' },
            { data: 'bank_name' },
            { data: 'bank_account_number' },
            { data: 'status_badge', orderable: false },
            { data: 'action', orderable: false, searchable: false },
        ],
    });

    const buildFormData = () => {
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('id', $('#referral_id').val());
        fields.forEach((field) => formData.append(field, $(`#${field}`).val() ?? ''));

        if ($('#id_card_file')[0].files[0]) {
            formData.append('id_card_file', $('#id_card_file')[0].files[0]);
        }

        if ($('#bank_book_file')[0].files[0]) {
            formData.append('bank_book_file', $('#bank_book_file')[0].files[0]);
        }

        return formData;
    };

    $('#province_id').on('change', function () {
        loadCities(this.value);
    });

    $('#city_id').on('change', function () {
        loadDistricts(this.value);
    });

    $('#district_id').on('change', function () {
        loadVillages(this.value);
    });

    $('#buttonSubmit').on('click', function () {
        clearErrors();

        const id = $('#referral_id').val();
        const url = id ? '{!! route('admin.referral.update') !!}' : '{!! route('admin.referral.add') !!}';

        $.ajax({
            url: url,
            data: buildFormData(),
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#buttonSubmit').attr('disabled', true);
                $('#loadingText').addClass('spinner-border');
            },
            success: function (response) {
                toastr.success(response.message);
                tableReferral.ajax.reload();
                $('#buttonSubmit').attr('disabled', false);
                $('#loadingText').removeClass('spinner-border');
                $('#modalForm').modal('hide');
                resetForm();
            },
            error: function (err) {
                $('#buttonSubmit').attr('disabled', false);
                $('#loadingText').removeClass('spinner-border');

                if (err.responseJSON && err.responseJSON.errors) {
                    Object.entries(err.responseJSON.errors).forEach(([field, messages]) => {
                        if (errorMap[field]) {
                            $(errorMap[field]).text(messages);
                        }
                    });
                }
            },
        });
    });

    window.editReferral = (id) => {
        resetForm();

        $.ajax({
            url: '{!! route('admin.referral.detail') !!}',
            data: {
                _token: csrfToken,
                id: id,
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                const referral = response.data;
                $('#referral_id').val(referral.id);
                fields.forEach((field) => $(`#${field}`).val(referral[field] ?? ''));
                fillRegionSelect('#city_id', response.cities || [], 'id', 'kabupaten', 'Pilih Kota/Kabupaten', referral.city_id);
                fillRegionSelect('#district_id', response.districts || [], 'id', 'kecamatan', 'Pilih Kecamatan', referral.district_id);
                fillRegionSelect('#village_id', response.villages || [], 'id', 'desa', 'Pilih Kelurahan/Desa', referral.village_id);
                $('#is_active').val(referral.is_active ? '1' : '0');
                $('#formTitle').text('Edit Referral');
                $('#buttonSubmit').html('Update <div id="loadingText"></div>');

                if (response.id_card_url) {
                    $('#idCardCurrent').html(`<a href="${response.id_card_url}" target="_blank">Lihat file KTP saat ini</a>`);
                }

                if (response.bank_book_url) {
                    $('#bankBookCurrent').html(`<a href="${response.bank_book_url}" target="_blank">Lihat file buku tabungan saat ini</a>`);
                }

                $('#modalForm').modal('show');
            },
        });
    };

    window.deleteReferral = (id) => {
        deleteID = id;
        $('#modalDelete').modal('show');
    };

    window.buttonDelete = () => {
        $.ajax({
            url: '{!! route('admin.referral.delete') !!}',
            data: {
                _token: csrfToken,
                id: deleteID,
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#buttonDelete').attr('disabled', true);
                $('#loadingTextDelete').addClass('spinner-border');
            },
            success: function (response) {
                toastr.success(response.message);
                tableReferral.ajax.reload();
                $('#modalDelete').modal('hide');
                $('#buttonDelete').attr('disabled', false);
                $('#loadingTextDelete').removeClass('spinner-border');
            },
            error: function () {
                toastr.error('Referral gagal dihapus');
                $('#modalDelete').modal('hide');
                $('#buttonDelete').attr('disabled', false);
                $('#loadingTextDelete').removeClass('spinner-border');
            },
        });
    };
</script>
@endpush
