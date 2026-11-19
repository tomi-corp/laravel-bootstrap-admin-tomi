@extends('layout.app')

@section('title', 'Lender')

@push('css')
    <style>
        #lenderTable th,
        #lenderTable td { white-space: nowrap; vertical-align: middle; }

        #lenderModal .modal-content {
            max-height: calc(100vh - 3.5rem);
            overflow: hidden;
        }

        #lenderModal .modal-header,
        #lenderModal .modal-footer {
            flex-shrink: 0;
        }

        #lenderForm {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
        }

        #lenderModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title mb-0">Daftar Lender</h4>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-end mb-3" style="gap: .5rem;">
                        <button class="btn btn-success" type="button" data-toggle="modal" data-target="#importLenderModal" onclick="resetImportLender()">
                            <i class="mdi mdi-file-import mr-1"></i>Import
                        </button>
                        <a class="btn btn-info" href="{{ route('internal.lenders.export') }}">
                            <i class="mdi mdi-file-export mr-1"></i>Export
                        </a>
                        <button class="btn btn-danger" type="button" id="deleteSelectedLenderButton" onclick="deleteSelectedLender()" disabled>
                            <i class="mdi mdi-trash-can mr-1"></i>Hapus Terpilih (<span id="selectedLenderCount">0</span>)
                        </button>
                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#lenderModal" onclick="openCreateLender()">
                            <i class="mdi mdi-plus-circle mr-1"></i>Tambah Lender
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="lenderTable" class="table table-hover nowrap" style="width:100%">
                            <thead><tr><th class="text-center"><input type="checkbox" id="selectAllLender" title="Pilih semua"></th><th>Kode Lender</th><th>Nama</th><th>PIC Lender</th><th>PIC Internal</th><th>Email</th><th>Telepon</th><th>Status</th><th>Aksi</th></tr></thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="lenderModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lenderModalTitle">Tambah Lender</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                </div>
                <form id="lenderForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="lenderId">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>PIC Lender</label><input class="form-control" id="picLender" name="pic_lender"><small class="text-danger" id="errorPicLender"></small></div>
                            <div class="col-md-6 form-group"><label>PIC Internal</label><input class="form-control" id="picInternal" name="pic_internal"><small class="text-danger" id="errorPicInternal"></small></div>
                            <div class="col-md-6 form-group"><label>Nama Lender</label><input class="form-control" id="lenderName" name="nama_lender"><small class="text-danger" id="errorLenderName"></small></div>
                            <div class="col-md-6 form-group"><label>Email</label><input class="form-control" id="lenderEmail" name="email_lender" type="email"><small class="text-danger" id="errorLenderEmail"></small></div>
                            <div class="col-md-6 form-group"><label>Telepon</label><input class="form-control" id="lenderPhone" name="telepon" type="number"><small class="text-danger" id="errorLenderPhone"></small></div>
                            <div class="col-md-6 form-group"><label>Nomor PKS</label><input class="form-control" id="agreementNumber" name="nomor_pks"><small class="text-danger" id="errorAgreementNumber"></small></div>
                            <div class="col-md-6 form-group"><label>Provinsi</label><select class="form-control" id="province" name="provinsi"><option value="">Pilih provinsi</option>@foreach ($provinces as $province)<option value="{{ $province->provinsi }}">{{ $province->provinsi }}</option>@endforeach</select></div>
                            <div class="col-md-6 form-group"><label>Kota / Kabupaten</label><select class="form-control" id="city" name="kabupaten"><option value="">Pilih kota / kabupaten</option></select></div>
                            <div class="col-md-6 form-group"><label>Kecamatan</label><select class="form-control" id="district" name="kecamatan"><option value="">Pilih kecamatan</option></select></div>
                            <div class="col-md-6 form-group"><label>Desa / Kelurahan</label><select class="form-control" id="village" name="desa"><option value="">Pilih desa / kelurahan</option></select></div>
                            <div class="col-md-12 form-group"><label>Alamat Lengkap</label><textarea class="form-control" id="address" name="alamat_lengkap" rows="3"></textarea></div>
                            <div class="col-md-6 form-group"><label>Bank</label><select class="form-control" id="bank" name="bank"><option value="">Pilih bank</option>@foreach ($banks as $item)<option value="{{ $item->id }}">{{ $item->code_bank }}</option>@endforeach</select></div>
                            <div class="col-md-6 form-group"><label>Nama Rekening</label><input class="form-control" id="accountName" name="an_rekening"></div>
                            <div class="col-md-6 form-group"><label>Nomor Rekening</label><input class="form-control" id="accountNumber" name="no_rekening" type="number"></div>
                            <div class="col-md-6 form-group"><label>Warna Brand</label><input class="form-control" id="brandColor" name="colorLender" type="color" value="#188ae2"></div>
                            <div class="col-md-6 form-group"><label>File PKS (PDF)</label><input class="form-control" id="pksFile" name="file_pks" type="file" accept="application/pdf"></div>
                            <div class="col-md-6 form-group"><label>Logo Lender</label><input class="form-control" id="lenderLogo" name="logoLender" type="file" accept="image/jpeg,image/png"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit" id="saveLenderButton">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importLenderModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Lender</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                </div>
                <form id="importLenderForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <p class="mb-2">Unggah file Excel sesuai template. Lender hasil import berstatus <strong>TIDAK AKTIF</strong>; file PKS dan logo dilengkapi lewat menu ubah.</p>
                        <a class="btn btn-sm btn-outline-secondary mb-3" href="{{ route('internal.lenders.import.template') }}">
                            <i class="mdi mdi-download mr-1"></i>Unduh Template
                        </a>
                        <div class="form-group mb-0">
                            <label>File Import (xlsx, xls, csv)</label>
                            <input class="form-control" id="importLenderFile" name="file" type="file" accept=".xlsx,.xls,.csv">
                        </div>
                        <div class="alert alert-danger mt-3 mb-0 d-none" id="importLenderErrors" style="max-height: 200px; overflow-y: auto;"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-success" type="submit" id="importLenderButton">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let lenderTable;
        let editingLender = false;

        const placeholders = {
            city: '<option value="">Pilih kota / kabupaten</option>',
            district: '<option value="">Pilih kecamatan</option>',
            village: '<option value="">Pilih desa / kelurahan</option>'
        };

        $(function () {
            lenderTable = $('#lenderTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ route('internal.lenders.table') }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' }
                },
                columns: [
                    { data: 'id', name: 'id', orderable: false, searchable: false, className: 'text-center', render: id => `<input type="checkbox" class="lender-check" value="${id}">` },
                    { data: 'id_code_lender', name: 'id_code_lender' },
                    { data: 'nama', name: 'nama' }, { data: 'pic_lender', name: 'pic_lender' }, { data: 'pic_internal', name: 'pic_internal' },
                    { data: 'email_lender', name: 'email_lender' }, { data: 'telp', name: 'telp' }, { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']],
                language: {
                    processing: 'Memuat...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 - 0 dari 0 data',
                    infoFiltered: '(disaring dari _MAX_ total data)',
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Belum ada data lender',
                    paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
                }
            });
            $('#province').on('change', function () { loadOptions('{{ route('internal.lenders.locations.cities') }}', { provinsi: this.value }, '#city', 'kabupaten', 'Pilih kota / kabupaten'); $('#district').html(placeholders.district); $('#village').html(placeholders.village); });
            $('#city').on('change', function () { loadOptions('{{ route('internal.lenders.locations.districts') }}', { kabupaten: this.value }, '#district', 'kecamatan', 'Pilih kecamatan'); $('#village').html(placeholders.village); });
            $('#district').on('change', function () { loadOptions('{{ route('internal.lenders.locations.villages') }}', { kecamatan: this.value }, '#village', 'desa', 'Pilih desa / kelurahan', true); });
            $('#lenderForm').on('submit', submitLender);
            $('#importLenderForm').on('submit', submitImportLender);

            // Pilih semua hanya mencentang baris yang lolos filter pencarian (semua halaman).
            $('#selectAllLender').on('change', function () {
                $(lenderTable.rows({ search: 'applied' }).nodes()).find('.lender-check').prop('checked', this.checked);
                updateSelectedLender();
            });
            $('#lenderTable').on('change', '.lender-check', updateSelectedLender);
            lenderTable.on('xhr.dt', () => setTimeout(updateSelectedLender));
            lenderTable.on('search.dt', () => setTimeout(updateSelectedLender));
        });

        function selectedLenderIds() {
            return lenderTable.$('.lender-check:checked').map((_, el) => el.value).get();
        }

        function updateSelectedLender() {
            const selected = selectedLenderIds().length;
            const visible = $(lenderTable.rows({ search: 'applied' }).nodes()).find('.lender-check');
            const visibleChecked = visible.filter(':checked').length;
            $('#selectedLenderCount').text(selected.toLocaleString('id-ID'));
            $('#deleteSelectedLenderButton').prop('disabled', selected === 0);
            $('#selectAllLender').prop('checked', visible.length > 0 && visibleChecked === visible.length).prop('indeterminate', visibleChecked > 0 && visibleChecked < visible.length);
        }

        function escapeHtml(value) {
            return $('<div>').text(value).html();
        }

        function resetImportLender() {
            $('#importLenderForm')[0].reset(); $('#importLenderErrors').addClass('d-none').empty();
        }

        function submitImportLender(event) {
            event.preventDefault();
            const button = $('#importLenderButton');
            const data = new FormData(this); data.append('_token', '{{ csrf_token() }}');
            $('#importLenderErrors').addClass('d-none').empty();
            button.prop('disabled', true).text('Mengimport...');
            $.ajax({ url: '{{ route('internal.lenders.import') }}', method: 'POST', data, processData: false, contentType: false })
                .done(response => { $('#importLenderModal').modal('hide'); lenderTable.ajax.reload(null, false); toastr.success(response.message); })
                .fail(error => {
                    const json = error.responseJSON || {};
                    const messages = json.errors ? (Array.isArray(json.errors) ? json.errors : Object.values(json.errors).flat()) : [];
                    if (messages.length) {
                        $('#importLenderErrors').removeClass('d-none').html(`<strong>${escapeHtml(json.message || 'Import gagal.')}</strong><ul class="mb-0 pl-3">${messages.map(item => `<li>${escapeHtml(item)}</li>`).join('')}</ul>`);
                    } else {
                        toastr.error(json.message || 'Gagal mengimport lender.');
                    }
                })
                .always(() => button.prop('disabled', false).text('Import'));
        }

        function deleteSelectedLender() {
            const ids = selectedLenderIds();
            if (!ids.length) { toastr.warning('Pilih minimal satu lender.'); return; }
            if (!confirm(`Apakah Anda yakin ingin mengajukan penghapusan ${ids.length.toLocaleString('id-ID')} lender terpilih? Penghapusan tetap menunggu approval.`)) return;
            const button = $('#deleteSelectedLenderButton').prop('disabled', true);
            $.post('{{ route('internal.lenders.delete-selected') }}', { ids, _token: '{{ csrf_token() }}' })
                .done(response => { $('#selectAllLender').prop('checked', false); lenderTable.ajax.reload(null, false); toastr.success(response.message); })
                .fail(error => { toastr.error(error.responseJSON?.message || 'Gagal menghapus lender terpilih.'); updateSelectedLender(); });
        }

        function loadOptions(url, data, selector, label, placeholder, includePostalCode = false) {
            $.post(url, { ...data, _token: '{{ csrf_token() }}' }, function (response) {
                const options = [`<option value="">${placeholder}</option>`];
                (response.data || response).forEach(item => { const value = includePostalCode ? `${item.desa}-${item.kodepos}` : item[label]; const text = includePostalCode ? `${item.desa} - ${item.kodepos}` : item[label]; options.push(`<option value="${value}">${text}</option>`); });
                $(selector).html(options.join(''));
            });
        }

        function openCreateLender() {
            editingLender = false; $('#lenderForm')[0].reset(); $('#lenderId').val(''); $('#lenderModalTitle').text('Tambah Lender'); $('#saveLenderButton').text('Simpan'); $('#city').html(placeholders.city); $('#district').html(placeholders.district); $('#village').html(placeholders.village);
        }

        function editLender(id) {
            editingLender = true;
            $.post('{{ route('internal.lenders.detail') }}', { id, _token: '{{ csrf_token() }}' }, function (response) {
                const item = response.data; $('#lenderId').val(id); $('#picLender').val(item.pic_lender); $('#picInternal').val(item.pic_internal); $('#lenderName').val(item.nama); $('#lenderEmail').val(item.email_lender); $('#lenderPhone').val(item.telp); $('#agreementNumber').val(item.nomor_pks); $('#address').val(item.alamat_lengkap); $('#brandColor').val(item.color || '#188ae2'); $('#bank').val(item.id_bank); $('#accountName').val(item.nama_rekening); $('#accountNumber').val(item.no_rekening); $('#province').val(item.provinsi).trigger('change');
                setTimeout(() => { $('#city').html(`<option value="${item.kabupaten}">${item.kabupaten}</option>`).val(item.kabupaten).trigger('change'); setTimeout(() => { $('#district').html(`<option value="${item.kecamatan}">${item.kecamatan}</option>`).val(item.kecamatan).trigger('change'); setTimeout(() => $('#village').html(`<option value="${item.desa}">${item.desa}</option>`).val(item.desa), 500); }, 500); }, 500);
                $('#lenderModalTitle').text('Ubah Lender'); $('#saveLenderButton').text('Perbarui'); $('#lenderModal').modal('show');
            });
        }

        function submitLender(event) {
            event.preventDefault(); const data = new FormData(this); if (editingLender) data.append('id', $('#lenderId').val()); data.append('_token', '{{ csrf_token() }}');
            const url = editingLender ? '{{ route('internal.lenders.update') }}' : '{{ route('internal.lenders.add') }}';
            $.ajax({ url, method: 'POST', data, processData: false, contentType: false }).done(() => { $('#lenderModal').modal('hide'); lenderTable.ajax.reload(null, false); toastr.success(editingLender ? 'Permintaan perubahan lender berhasil dikirim.' : 'Lender berhasil ditambahkan.'); }).fail(error => toastr.error(error.responseJSON?.message || 'Gagal menyimpan lender.'));
        }

        function deleteLender(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus lender ini?')) return;
            $.post('{{ route('internal.lenders.delete') }}', { id, _token: '{{ csrf_token() }}' }).done(() => { lenderTable.ajax.reload(null, false); toastr.success('Permintaan penghapusan lender berhasil dikirim.'); }).fail(error => toastr.error(error.responseJSON?.message || 'Gagal menghapus lender.'));
        }
    </script>
@endpush
