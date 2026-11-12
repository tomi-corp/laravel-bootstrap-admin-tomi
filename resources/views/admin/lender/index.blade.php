@extends('layout.app')

@section('title', 'Pemberi Pinjaman')

@push('css')
<style>
    #lenderTable th,
    #lenderTable td { white-space: nowrap; vertical-align: middle; }

    #lenderTable th:not(:nth-child(5)),
    #lenderTable td:not(:nth-child(5)),
    #lenderForm input:not([type="email"]),
    #lenderForm textarea,
    #lenderForm select {
        text-transform: uppercase;
    }

    #lenderForm input[type="email"] {
        text-transform: none;
    }

    #lenderModal .modal-content {
        max-height: calc(100vh - 3.5rem);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    #lenderModal #lenderForm {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
    }
    #lenderModal .modal-body {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0;
    }
    #lenderModal .modal-header,
    #lenderModal .modal-footer { flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Tomi</a></li>
                    <li class="breadcrumb-item active">Pemberi Pinjaman</li>
                </ol>
            </div>
            <h4 class="page-title">Pemberi Pinjaman</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <button type="button" class="btn btn-primary btn-sm float-right mb-3" id="addLenderButton">
            <i class="mdi mdi-plus-circle"></i> Tambah Pemberi Pinjaman
        </button>
        <h4 class="header-title">Daftar Pemberi Pinjaman</h4>
        <div class="table-responsive">
            <table id="lenderTable" class="table table-hover table-striped dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Lender</th>
                        <th>Nama Lender</th>
                        <th>PIC Lender</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Bank</th>
                        <th>Logo</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="lenderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lenderModalTitle">Tambah Pemberi Pinjaman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="lenderForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="lenderId" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group"><label>Nama Lender</label><input class="form-control" name="nama_lender" id="namaLender" required><small class="text-danger field-error" data-field="nama_lender"></small></div>
                            <div class="form-group"><label>PIC Lender</label><input class="form-control" name="pic_lender" id="picLender" required><small class="text-danger field-error" data-field="pic_lender"></small></div>
                            <div class="form-group"><label>PIC Internal</label><input class="form-control" name="pic_internal" id="picInternal" required><small class="text-danger field-error" data-field="pic_internal"></small></div>
                            <div class="form-group"><label>Email Lender</label><input type="email" class="form-control" name="email_lender" id="emailLender" required><small class="text-danger field-error" data-field="email_lender"></small></div>
                            <div class="form-group"><label>Telepon</label><input type="number" class="form-control" name="telepon" id="telepon" required><small class="text-danger field-error" data-field="telepon"></small></div>
                            <div class="form-group"><label>Nomor PKS</label><input class="form-control" name="nomor_pks" id="nomorPks" required><small class="text-danger field-error" data-field="nomor_pks"></small></div>
                            <div class="form-group"><label>Alamat Lengkap</label><textarea class="form-control" name="alamat_lengkap" id="alamatLengkap" required></textarea><small class="text-danger field-error" data-field="alamat_lengkap"></small></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label>Provinsi</label><select class="form-control location-select" name="provinsi" id="provinsi" required><option value="">Pilih provinsi</option>@foreach ($data['provinsis'] as $item)<option value="{{ $item->provinsi }}">{{ $item->provinsi }}</option>@endforeach</select><small class="text-danger field-error" data-field="provinsi"></small></div>
                            <div class="form-group"><label>Kabupaten / Kota</label><select class="form-control location-select" name="kabupaten" id="kabupaten" required><option value="">Pilih kabupaten / kota</option></select><small class="text-danger field-error" data-field="kabupaten"></small></div>
                            <div class="form-group"><label>Kecamatan</label><select class="form-control location-select" name="kecamatan" id="kecamatan" required><option value="">Pilih kecamatan</option></select><small class="text-danger field-error" data-field="kecamatan"></small></div>
                            <div class="form-group"><label>Desa</label><select class="form-control location-select" name="desa" id="desa" required><option value="">Pilih desa</option></select><small class="text-danger field-error" data-field="desa"></small></div>
                            <div class="form-group"><label>Bank</label><select class="form-control" name="bank" id="bank" required><option value="">Pilih bank</option>@foreach ($data['bank'] as $item)<option value="{{ $item->id }}">{{ $item->code_bank }}</option>@endforeach</select><small class="text-danger field-error" data-field="bank"></small></div>
                            <div class="form-group"><label>Nama Pemilik Rekening</label><input class="form-control" name="an_rekening" id="anRekening" required><small class="text-danger field-error" data-field="an_rekening"></small></div>
                            <div class="form-group"><label>Nomor Rekening</label><input type="number" class="form-control" name="no_rekening" id="noRekening" required><small class="text-danger field-error" data-field="no_rekening"></small></div>
                            <div class="form-group"><label>Warna Lender</label><input type="color" class="form-control" name="colorLender" id="colorLender" value="#188ae2" required><small class="text-danger field-error" data-field="colorLender"></small></div>
                            <div class="form-group"><label>File PKS <span class="text-muted" id="pksHint">(PDF)</span></label><input type="file" class="form-control" name="file_pks" id="filePks" accept="application/pdf"><button type="button" class="btn btn-outline-primary btn-sm mt-2 d-none" id="viewPksButton">Lihat Dokumen PKS</button><small class="text-danger field-error" data-field="file_pks"></small></div>
                            <div class="form-group"><label>Logo Lender</label><input type="file" class="form-control" name="logoLender" id="logoLender" accept="image/jpeg,image/png"><button type="button" class="btn btn-outline-primary btn-sm mt-2 d-none" id="viewLogoButton">Lihat Logo</button><small class="text-danger field-error" data-field="logoLender"></small></div>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="mb-0">Fitur Lender</label>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addFeatureButton"><i class="mdi mdi-plus"></i> Tambah Fitur</button>
                        </div>
                        <div id="lenderFeatures" class="row"></div>
                        <small class="text-muted">Nomor dan tanggal PKS dapat diisi untuk setiap fitur lender.</small>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary" id="saveLenderButton">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteLenderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Hapus Lender</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div><div class="modal-body">Yakin ingin menghapus lender ini?</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button></div></div></div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="previewModalTitle">Preview</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <div class="modal-body text-center p-0" id="previewModalBody"></div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let editingLenderId = null;
    let deletingLenderId = null;
    const csrf = '{{ csrf_token() }}';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf
        }
    });

    const lenderTable = $('#lenderTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: { url: '{{ route('admin.lender.table') }}', type: 'POST', data: { _token: csrf } },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id_code_lender', name: 'id_code_lender' },
            { data: 'nama', name: 'nama' },
            { data: 'pic_lender', name: 'pic_lender' },
            { data: 'email_lender', name: 'email_lender' },
            { data: 'telp', name: 'telp' },
            { data: 'bank', name: 'bank', orderable: false },
            { data: 'logo', name: 'logo', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    function resetLocation(selectId, placeholder) {
        $('#' + selectId).html('<option value="">' + placeholder + '</option>').trigger('change.select2');
    }

    function loadLocation(url, data, target, placeholder, selected, type) {
        return $.post(url, { ...data, _token: csrf }).done(function (items) {
            const $target = $('#' + target);
            $target.html('<option value="">' + placeholder + '</option>');
            items.forEach(function (item) {
                const value = type === 'desa' ? item.desa + '-' + item.kodepos : item[type];
                const label = type === 'desa' ? item.desa + ' - ' + item.kodepos : item[type];
                $target.append(new Option(label, value, false, value === selected));
            });
            $target.trigger('change.select2');
        });
    }

    $('#provinsi').on('change', function () {
        resetLocation('kabupaten', 'Pilih kabupaten / kota'); resetLocation('kecamatan', 'Pilih kecamatan'); resetLocation('desa', 'Pilih desa');
        if (this.value) loadLocation('{{ route('admin.ksp.kabupaten') }}', { provinsi: this.value }, 'kabupaten', 'Pilih kabupaten / kota', null, 'kabupaten');
    });
    $('#kabupaten').on('change', function () {
        resetLocation('kecamatan', 'Pilih kecamatan'); resetLocation('desa', 'Pilih desa');
        if (this.value) loadLocation('{{ route('admin.ksp.kecamatan') }}', { kabupaten: this.value }, 'kecamatan', 'Pilih kecamatan', null, 'kecamatan');
    });
    $('#kecamatan').on('change', function () {
        resetLocation('desa', 'Pilih desa');
        if (this.value) loadLocation('{{ route('admin.ksp.desa') }}', { kecamatan: this.value }, 'desa', 'Pilih desa', null, 'desa');
    });

    function clearErrors() { $('.field-error').text(''); }
    function showErrors(errors) { Object.keys(errors || {}).forEach(key => $('.field-error[data-field="' + key + '"]').text(errors[key][0])); }

    function addFeatureRow(feature = {}) {
        const index = $('#lenderFeatures .lender-feature-row').length;
        const options = `{!! collect($data['features'])->map(fn ($feature) => '<option value="' . $feature->id . '">' . e($feature->name) . '</option>')->implode('') !!}`;
        const row = $(
            '<div class="lender-feature-row col-12 mb-2">' +
                '<div class="border rounded p-2 position-relative">' +
                    '<button type="button" class="btn btn-outline-danger btn-sm remove-feature" title="Hapus fitur" style="position:absolute;top:8px;right:8px;"><i class="mdi mdi-delete"></i></button>' +
                '<div class="form-row align-items-end">' +
                    '<div class="col-12 pr-5"><label>Fitur</label><select class="form-control" name="lender_features[' + index + '][feature_id]" required><option value="">Pilih fitur</option>' + options + '</select></div>' +
                    '<div class="col-6"><label>Nomor PKS</label><input class="form-control" name="lender_features[' + index + '][pks_number]" value="' + (feature.pks_number || '') + '"></div>' +
                    '<div class="col-6"><label>Tanggal PKS</label><input type="date" class="form-control" name="lender_features[' + index + '][pks_date]" value="' + (feature.pks_date || '') + '"></div>' +
                    '<div class="col-6"><label>Berakhir</label><input type="date" class="form-control" name="lender_features[' + index + '][pks_expired_at]" value="' + (feature.pks_expired_at || '') + '"></div>' +
                    '<div class="col-6"><div class="custom-control custom-checkbox mt-4"><input type="checkbox" class="custom-control-input" id="featureActive' + index + '" name="lender_features[' + index + '][is_active]" value="1" ' + (feature.is_active === false || feature.is_active === 0 ? '' : 'checked') + '><label class="custom-control-label" for="featureActive' + index + '">Aktif</label></div></div>' +
                '</div>' +
                '</div>' +
            '</div>'
        );
        row.find('select').val(feature.feature_id || '');
        $('#lenderFeatures').append(row);
    }

    function resetFeatures(features = []) {
        $('#lenderFeatures').empty();
        features.length ? features.forEach(addFeatureRow) : addFeatureRow();
    }

    $('#addFeatureButton').on('click', function () { addFeatureRow(); });
    $(document).on('click', '.remove-feature', function () { $(this).closest('.lender-feature-row').remove(); });

    function resetForm() { editingLenderId = null; $('#lenderForm')[0].reset(); $('#lenderId').val(''); clearErrors(); resetLocation('kabupaten', 'Pilih kabupaten / kota'); resetLocation('kecamatan', 'Pilih kecamatan'); resetLocation('desa', 'Pilih desa'); resetFeatures(); $('#lenderModalTitle').text('Tambah Pemberi Pinjaman'); $('#pksHint').text('(PDF wajib)'); $('#viewPksButton, #viewLogoButton').addClass('d-none'); }

    $('#addLenderButton').on('click', function () { resetForm(); $('#lenderModal').modal('show'); });

    window.editLender = function (id) {
        resetForm(); editingLenderId = id; $('#lenderModalTitle').text('Ubah Pemberi Pinjaman'); $('#pksHint').text('(kosongkan jika tidak diubah)');
        $.post('{{ route('admin.lender.detail') }}', { _token: csrf, id: id }).done(function (response) {
            const item = response.data;
            $('#lenderId').val(id); $('#picLender').val(item.pic_lender); $('#picInternal').val(item.pic_internal); $('#namaLender').val(item.nama); $('#emailLender').val(item.email_lender); $('#telepon').val(item.telp); $('#nomorPks').val(item.nomor_pks); $('#alamatLengkap').val(item.alamat_lengkap); $('#bank').val(item.id_bank); $('#anRekening').val(item.nama_rekening); $('#noRekening').val(item.no_rekening); $('#colorLender').val(item.color || '#188ae2'); $('#provinsi').val(item.provinsi); resetFeatures(item.lender_features || []); $('#viewPksButton, #viewLogoButton').removeClass('d-none');
            loadLocation('{{ route('admin.ksp.kabupaten') }}', { provinsi: item.provinsi }, 'kabupaten', 'Pilih kabupaten / kota', item.kabupaten, 'kabupaten').then(function () { return loadLocation('{{ route('admin.ksp.kecamatan') }}', { kabupaten: item.kabupaten }, 'kecamatan', 'Pilih kecamatan', item.kecamatan, 'kecamatan'); }).then(function () { return loadLocation('{{ route('admin.ksp.desa') }}', { kecamatan: item.kecamatan }, 'desa', 'Pilih desa', item.desa, 'desa'); });
            $('#lenderModal').modal('show');
        }).fail(function () { toastr.error('Detail lender gagal dimuat.'); });
    };

    $('#viewPksButton').on('click', function () {
        $.ajax({ url: '{{ route('admin.lender.pks.show') }}', type: 'POST', data: { _token: csrf, id: editingLenderId }, xhrFields: { responseType: 'blob' } }).done(function (file) {
            const url = URL.createObjectURL(file);
            $('#previewModalTitle').text('Dokumen PKS');
            $('#previewModalBody').html('<iframe src="' + url + '" style="width:100%;height:75vh;border:0"></iframe>');
            $('#previewModal').modal('show');
        }).fail(function () { toastr.error('Dokumen PKS tidak dapat ditampilkan.'); });
    });

    $('#viewLogoButton').on('click', function () {
        showLenderLogo(editingLenderId);
    });

    window.imageLogo = function (id) {
        showLenderLogo(id);
    };

    function showLenderLogo(id) {
        $.post('{{ route('admin.lender.image.show') }}', { _token: csrf, id: id }).done(function (response) {
            $('#previewModalTitle').text('Logo Lender');
            $('#previewModalBody').html('<img src="' + response.url + '" alt="Logo lender" class="img-fluid p-3" style="max-height:75vh">');
            $('#previewModal').modal('show');
        }).fail(function () { toastr.error('Logo lender tidak dapat ditampilkan.'); });
    }

    window.deleteLender = function (id) { deletingLenderId = id; $('#deleteLenderModal').modal('show'); };

    $('#lenderForm').on('submit', function (event) {
        event.preventDefault(); clearErrors();
        const lenderId = $('#lenderId').val();
        const formData = new FormData(this);
        formData.set('id', lenderId);
        const url = lenderId ? '{{ route('admin.lender.update') }}' : '{{ route('admin.lender.add') }}';
        $.ajax({ url: url, type: 'POST', data: formData, contentType: false, processData: false, beforeSend: () => $('#saveLenderButton').prop('disabled', true) }).done(function (response) { toastr.success(response.message); $('#lenderModal').modal('hide'); lenderTable.ajax.reload(null, false); }).fail(function (xhr) { showErrors(xhr.responseJSON?.errors); toastr.error(xhr.responseJSON?.message || 'Data lender gagal disimpan.'); }).always(function () { $('#saveLenderButton').prop('disabled', false); });
    });

    $('#confirmDeleteButton').on('click', function () { $.post('{{ route('admin.lender.delete') }}', { _token: csrf, id: deletingLenderId }).done(function (response) { toastr.success(response.message); $('#deleteLenderModal').modal('hide'); lenderTable.ajax.reload(null, false); }).fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Lender gagal dihapus.'); }); });
</script>
@endpush
