@extends('layout.app')

@section('title', 'Perusahaan')

@push('css')
    <style>
        #companyTable th,
        #companyTable td { white-space: nowrap; vertical-align: middle; }
        #companyModal .modal-content {
            max-height: calc(100vh - 3.5rem);
            overflow: hidden;
        }

        #companyModal .modal-header,
        #companyModal .modal-footer {
            flex-shrink: 0;
        }

        #companyForm {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
        }

        #companyModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box"><h4 class="page-title">Perusahaan</h4></div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-end mb-3" style="gap: .5rem;">
                        <button class="btn btn-success" type="button" data-toggle="modal" data-target="#importCompanyModal" onclick="resetImportCompany()">
                            <i class="mdi mdi-file-import mr-1"></i>Import
                        </button>
                        <a class="btn btn-info" href="{{ route('internal.companies.export') }}">
                            <i class="mdi mdi-file-export mr-1"></i>Export
                        </a>
                        <button class="btn btn-danger" type="button" id="deleteSelectedCompanyButton" onclick="deleteSelectedCompany()" disabled>
                            <i class="mdi mdi-trash-can mr-1"></i>Hapus Terpilih (<span id="selectedCompanyCount">0</span>)
                        </button>
                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#companyModal" onclick="openCreateCompany()">
                            <i class="mdi mdi-plus-circle mr-1"></i>Tambah Perusahaan
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="companyTable" class="table table-hover nowrap" style="width:100%">
                            <thead><tr><th class="text-center"><input type="checkbox" id="selectAllCompany" title="Pilih semua di halaman ini"></th><th>Kode Perusahaan</th><th>Nama Perusahaan</th><th>PIC Perusahaan</th><th>PIC Internal</th><th>Email</th><th>Telepon</th><th>Lender</th><th>Status</th><th>Tanggal Pembayaran</th><th>Aksi</th></tr></thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="companyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="companyModalTitle">Tambah Perusahaan</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button></div>
                <form id="companyForm">
                    <div class="modal-body">
                        <input type="hidden" id="companyId">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>PIC Perusahaan</label><input class="form-control" id="companyPic" name="pic_perusahaan"></div>
                            <div class="col-md-6 form-group"><label>PIC Internal</label><select class="form-control" id="internalAdmin" name="admin_id"><option value="">Pilih PIC internal</option>@foreach ($internalAdmins as $admin)<option value="{{ $admin->id }}">{{ $admin->username }} ({{ $admin->email }})</option>@endforeach</select></div>
                            <div class="col-md-6 form-group"><label>Nama Perusahaan</label><input class="form-control" id="companyName" name="nama_perusahaan"></div>
                            <div class="col-md-6 form-group"><label>Email</label><input class="form-control" id="companyEmail" name="email_perusahaan" type="email"></div>
                            <div class="col-md-6 form-group"><label>Telepon</label><input class="form-control" id="companyPhone" name="telepon" type="number"></div>
                            <div class="col-md-6 form-group"><label>Nomor Perjanjian</label><input class="form-control" id="agreementNumber" name="nomor_pks"></div>
                            <div class="col-md-6 form-group"><label>Provinsi</label><select class="form-control" id="province" name="provinsi"><option value="">Pilih provinsi</option>@foreach ($provinces as $province)<option value="{{ $province->provinsi }}">{{ $province->provinsi }}</option>@endforeach</select></div>
                            <div class="col-md-6 form-group"><label>Kabupaten / Kota</label><select class="form-control" id="city" name="kabupaten"><option value="">Pilih kabupaten / kota</option></select></div>
                            <div class="col-md-6 form-group"><label>Kecamatan</label><select class="form-control" id="district" name="kecamatan"><option value="">Pilih kecamatan</option></select></div>
                            <div class="col-md-6 form-group"><label>Desa</label><select class="form-control" id="village" name="desa"><option value="">Pilih desa</option></select></div>
                            <div class="col-md-12 form-group"><label>Alamat Lengkap</label><textarea class="form-control" id="address" name="alamat_lengkap" rows="3"></textarea></div>
                            <div class="col-md-6 form-group"><label>Tanggal Pembayaran</label><input class="form-control" id="paymentDate" name="payment_date" type="number"></div>
                            <div class="col-md-6 form-group d-none" id="statusGroup"><label>Status</label><select class="form-control" id="status" name="status"><option value="active">Aktif</option><option value="nonactive">Tidak Aktif</option></select></div>
                        </div>

                        <hr>
                        <h6 class="mt-0 mb-1">PKS</h6>
                        <p class="text-muted small mb-3" id="pksHelp">Wajib diisi saat menambah perusahaan.</p>
                        @if ($features->isEmpty())
                            <div class="alert alert-warning py-2">Belum ada fitur aktif. Tambahkan data fitur terlebih dahulu.</div>
                        @endif
                        <div class="row" id="pksFields">
                            <div class="col-md-6 form-group"><label>Fitur</label><select class="form-control" name="pks_feature_id"><option value="">Pilih fitur</option>@foreach ($features as $feature)<option value="{{ $feature->id }}">{{ strtoupper($feature->name) }}</option>@endforeach</select><small class="text-danger" data-error="pks_feature_id"></small></div>
                            <div class="col-md-6 form-group"><label>Nomor PKS</label><input class="form-control" name="pks_nomor" maxlength="255"><small class="text-danger" data-error="pks_nomor"></small></div>
                            <div class="col-md-4 form-group"><label>Tanggal PKS</label><input class="form-control" name="pks_tanggal" type="date"><small class="text-danger" data-error="pks_tanggal"></small></div>
                            <div class="col-md-4 form-group"><label>Mulai Berlaku</label><input class="form-control" name="pks_mulai_berlaku" type="date"><small class="text-danger" data-error="pks_mulai_berlaku"></small></div>
                            <div class="col-md-4 form-group"><label>Berakhir</label><input class="form-control" name="pks_berakhir" type="date"><small class="text-danger" data-error="pks_berakhir"></small></div>
                            <div class="col-md-12 form-group"><label>Upload Dokumen (PDF, maks 5 MB)</label><input class="form-control" name="pks_dokumen" type="file" accept="application/pdf"><small class="text-danger" data-error="pks_dokumen"></small></div>
                        </div>

                        <div class="d-none" id="pksListGroup">
                            <h6 class="mb-2">PKS Terdaftar</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead><tr><th>No</th><th>Fitur</th><th>Nomor PKS</th><th>Tanggal PKS</th><th>Mulai Berlaku</th><th>Berakhir</th><th>Status</th><th>Aksi</th></tr></thead>
                                    <tbody id="pksTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit" id="saveCompanyButton">Simpan Perusahaan</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importCompanyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Perusahaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                </div>
                <form id="importCompanyForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <p class="mb-2">Unggah file Excel sesuai template. Kolom <strong>pic_internal</strong> diisi username atau email admin internal.</p>
                        <a class="btn btn-sm btn-outline-secondary mb-3" href="{{ route('internal.companies.import.template') }}">
                            <i class="mdi mdi-download mr-1"></i>Unduh Template
                        </a>
                        <div class="form-group mb-0">
                            <label>File Import (xlsx, xls, csv)</label>
                            <input class="form-control" id="importCompanyFile" name="file" type="file" accept=".xlsx,.xls,.csv">
                        </div>
                        <div class="alert alert-danger mt-3 mb-0 d-none" id="importCompanyErrors" style="max-height: 200px; overflow-y: auto;"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-success" type="submit" id="importCompanyButton">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let companyTable;
        let editingCompany = false;
        const selectedCompanies = new Set();

        $(function () {
            companyTable = $('#companyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: '{{ route('internal.companies.table') }}', type: 'POST', data: { _token: '{{ csrf_token() }}' } },
                columns: [
                    { data: 'id', name: 'id', orderable: false, searchable: false, className: 'text-center', render: id => `<input type="checkbox" class="company-check" value="${id}" ${selectedCompanies.has(String(id)) ? 'checked' : ''}>` },
                    { data: 'id_code_perusahaan', name: 'id_code_perusahaan' }, { data: 'nama', name: 'nama' },
                    { data: 'pic_perusahaan', name: 'pic_perusahaan' }, { data: 'pic_internal', name: 'pic_internal' },
                    { data: 'email_perusahaan', name: 'email_perusahaan' }, { data: 'telp', name: 'telp' },
                    { data: 'lender', name: 'lender' }, { data: 'status', name: 'status' }, { data: 'payment_date', name: 'payment_date' },
                    { data: null, name: 'actions', orderable: false, searchable: false, render: row => `<button class="btn btn-sm btn-primary mr-1" onclick="editCompany(${row.id})"><i class="mdi mdi-pencil"></i></button><button class="btn btn-sm btn-danger" onclick="deleteCompany(${row.id})"><i class="mdi mdi-trash-can"></i></button>` }
                ],
                order: [[1, 'asc']]
            });
            $('#companyTable').on('change', '.company-check', function () {
                this.checked ? selectedCompanies.add(this.value) : selectedCompanies.delete(this.value);
                updateSelectedCompany();
            });
            // Tabel server-side: pilih semua berlaku untuk baris di halaman yang sedang tampil.
            $('#selectAllCompany').on('change', function () {
                $('#companyTable .company-check').prop('checked', this.checked).each((_, el) => this.checked ? selectedCompanies.add(el.value) : selectedCompanies.delete(el.value));
                updateSelectedCompany();
            });
            companyTable.on('draw.dt', updateSelectedCompany);
            $('#importCompanyForm').on('submit', submitImportCompany);
            $('#province').on('change', function () { loadOptions('{{ route('internal.companies.locations.cities') }}', { provinsi: this.value }, '#city', 'kabupaten', 'Pilih kabupaten / kota'); $('#district, #village').html('<option value="">Pilih pilihan</option>'); });
            $('#city').on('change', function () { loadOptions('{{ route('internal.companies.locations.districts') }}', { kabupaten: this.value }, '#district', 'kecamatan', 'Pilih kecamatan'); $('#village').html('<option value="">Pilih desa</option>'); });
            $('#district').on('change', function () { loadOptions('{{ route('internal.companies.locations.villages') }}', { kecamatan: this.value }, '#village', 'desa', 'Pilih desa', true); });
            $('#companyForm').on('submit', submitCompany);
        });

        function escapeHtml(value) {
            return $('<div>').text(value).html();
        }

        function updateSelectedCompany() {
            const visible = $('#companyTable .company-check');
            const visibleChecked = visible.filter(':checked').length;
            $('#selectedCompanyCount').text(selectedCompanies.size.toLocaleString('id-ID'));
            $('#deleteSelectedCompanyButton').prop('disabled', selectedCompanies.size === 0);
            $('#selectAllCompany').prop('checked', visible.length > 0 && visibleChecked === visible.length).prop('indeterminate', visibleChecked > 0 && visibleChecked < visible.length);
        }

        function deleteSelectedCompany() {
            const ids = [...selectedCompanies];
            if (!ids.length) { toastr.warning('Pilih minimal satu perusahaan.'); return; }
            if (!confirm(`Apakah Anda yakin ingin menghapus ${ids.length.toLocaleString('id-ID')} perusahaan terpilih beserta data karyawan dan pinjamannya?`)) return;
            const button = $('#deleteSelectedCompanyButton').prop('disabled', true);
            $.post('{{ route('internal.companies.delete-selected') }}', { ids, _token: '{{ csrf_token() }}' })
                .done(response => { selectedCompanies.clear(); companyTable.ajax.reload(null, false); toastr.success(response.message); })
                .fail(error => { toastr.error(error.responseJSON?.message || 'Gagal menghapus perusahaan terpilih.'); updateSelectedCompany(); })
                .always(() => button.prop('disabled', selectedCompanies.size === 0));
        }

        function resetPksSection(isEdit) {
            $('#companyForm [data-error]').text('');
            $('#pksHelp').text(isEdit ? 'Isi bagian ini hanya jika ingin menambah PKS baru.' : 'Wajib diisi saat menambah perusahaan.');
            $('#pksListGroup').toggleClass('d-none', !isEdit);
            $('#pksTableBody').empty();
        }

        function loadPks(id) {
            const body = $('#pksTableBody').html('<tr><td colspan="8" class="text-center text-muted">Memuat...</td></tr>');
            $.post('{{ route('internal.companies.pks.list') }}', { id, _token: '{{ csrf_token() }}' })
                .done(response => {
                    const { items } = response.data;
                    if (!items.length) { body.html('<tr><td colspan="8" class="text-center text-muted">Belum ada PKS</td></tr>'); return; }
                    body.html(items.map((item, index) => `<tr>
                        <td>${index + 1}</td>
                        <td>${escapeHtml(String(item.fitur).toUpperCase())}</td>
                        <td>${escapeHtml(String(item.nomor_pks).toUpperCase())}</td>
                        <td>${item.tanggal_pks}</td><td>${item.mulai_berlaku}</td><td>${item.berakhir}</td>
                        <td><span class="badge badge-${item.status === 'AKTIF' ? 'success' : 'secondary'}">${item.status}</span></td>
                        <td class="text-nowrap"><a class="btn btn-sm btn-info mr-1" href="${item.file_url}" target="_blank" rel="noopener" title="Lihat dokumen"><i class="mdi mdi-file-pdf-box"></i></a><button class="btn btn-sm btn-danger" type="button" title="Hapus" onclick="deletePks(${item.id})"><i class="mdi mdi-trash-can"></i></button></td>
                    </tr>`).join(''));
                })
                .fail(error => body.html(`<tr><td colspan="8" class="text-center text-danger">${escapeHtml(error.responseJSON?.message || 'Gagal memuat PKS.')}</td></tr>`));
        }

        function deletePks(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus PKS ini?')) return;
            $.post('{{ route('internal.companies.pks.delete') }}', { id, _token: '{{ csrf_token() }}' })
                .done(response => { loadPks($('#companyId').val()); toastr.success(response.message); })
                .fail(error => toastr.error(error.responseJSON?.message || 'Gagal menghapus PKS.'));
        }

        function resetImportCompany() {
            $('#importCompanyForm')[0].reset(); $('#importCompanyErrors').addClass('d-none').empty();
        }

        function submitImportCompany(event) {
            event.preventDefault();
            const button = $('#importCompanyButton');
            const data = new FormData(this); data.append('_token', '{{ csrf_token() }}');
            $('#importCompanyErrors').addClass('d-none').empty();
            button.prop('disabled', true).text('Mengimport...');
            $.ajax({ url: '{{ route('internal.companies.import') }}', method: 'POST', data, processData: false, contentType: false })
                .done(response => { $('#importCompanyModal').modal('hide'); companyTable.ajax.reload(null, false); toastr.success(response.message); })
                .fail(error => {
                    const json = error.responseJSON || {};
                    const messages = json.errors ? (Array.isArray(json.errors) ? json.errors : Object.values(json.errors).flat()) : [];
                    if (messages.length) {
                        $('#importCompanyErrors').removeClass('d-none').html(`<strong>${escapeHtml(json.message || 'Import gagal.')}</strong><ul class="mb-0 pl-3">${messages.map(item => `<li>${escapeHtml(item)}</li>`).join('')}</ul>`);
                    } else {
                        toastr.error(json.message || 'Gagal mengimport perusahaan.');
                    }
                })
                .always(() => button.prop('disabled', false).text('Import'));
        }

        function loadOptions(url, data, selector, label, placeholder, includePostalCode = false) {
            $.post(url, { ...data, _token: '{{ csrf_token() }}' }, function (response) {
                const options = [`<option value="">${placeholder}</option>`];
                (response.data || response).forEach(item => { const value = includePostalCode ? `${item.desa}-${item.kodepos}` : item[label]; const text = includePostalCode ? `${item.desa} - ${item.kodepos}` : item[label]; options.push(`<option value="${value}">${text}</option>`); });
                $(selector).html(options.join(''));
            });
        }

        function openCreateCompany() {
            editingCompany = false; $('#companyForm')[0].reset(); $('#companyId').val(''); $('#companyModalTitle').text('Tambah Perusahaan'); $('#saveCompanyButton').text('Simpan Perusahaan'); $('#statusGroup').addClass('d-none'); $('#city, #district, #village').html('<option value="">Pilih pilihan</option>'); resetPksSection(false);
        }

        function editCompany(id) {
            editingCompany = true;
            $('#companyForm')[0].reset(); resetPksSection(true); loadPks(id);
            $.post('{{ route('internal.companies.detail') }}', { id, _token: '{{ csrf_token() }}' }, function (response) {
                const item = response.data; $('#companyId').val(id); $('#companyPic').val(item.pic_perusahaan); $('#internalAdmin').val(item.admin_id); $('#companyName').val(item.nama); $('#companyPhone').val(item.telp); $('#agreementNumber').val(item.nomor_pks); $('#address').val(item.alamat_lengkap); $('#paymentDate').val(item.payment_date); $('#status').val(item.status); $('#statusGroup').removeClass('d-none'); $('#province').val(item.provinsi).trigger('change');
                setTimeout(() => { $('#city').html(`<option value="${item.kabupaten}">${item.kabupaten}</option>`).val(item.kabupaten).trigger('change'); setTimeout(() => { $('#district').html(`<option value="${item.kecamatan}">${item.kecamatan}</option>`).val(item.kecamatan).trigger('change'); setTimeout(() => $('#village').html(`<option value="${item.desa}">${item.desa}</option>`).val(item.desa), 500); }, 500); }, 500);
                $('#companyModalTitle').text('Edit Perusahaan'); $('#saveCompanyButton').text('Perbarui Perusahaan'); $('#companyModal').modal('show');
            });
        }

        function submitCompany(event) {
            event.preventDefault(); const data = new FormData(this); if (editingCompany) data.append('id', $('#companyId').val()); data.append('_token', '{{ csrf_token() }}');
            const url = editingCompany ? '{{ route('internal.companies.update') }}' : '{{ route('internal.companies.add') }}';
            $('#companyForm [data-error]').text('');
            $.ajax({ url, method: 'POST', data, processData: false, contentType: false })
                .done(() => { $('#companyModal').modal('hide'); companyTable.ajax.reload(null, false); toastr.success(editingCompany ? 'Perusahaan berhasil diperbarui.' : 'Perusahaan berhasil ditambahkan.'); })
                .fail(error => {
                    const errors = error.responseJSON?.errors || {};
                    let shownInline = false;
                    Object.entries(errors).forEach(([field, messages]) => { const target = $(`#companyForm [data-error="${field}"]`); if (target.length) { target.text(messages[0]); shownInline = true; } });
                    const otherErrors = Object.entries(errors).filter(([field]) => !$(`#companyForm [data-error="${field}"]`).length).map(([, messages]) => messages[0]);
                    if (otherErrors.length || !shownInline) toastr.error(otherErrors[0] || error.responseJSON?.message || 'Perusahaan gagal disimpan.');
                });
        }

        function deleteCompany(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus perusahaan dan data terkait?')) return;
            $.post('{{ route('internal.companies.delete') }}', { id, _token: '{{ csrf_token() }}' }).done(() => { companyTable.ajax.reload(null, false); toastr.success('Perusahaan berhasil dihapus.'); }).fail(error => toastr.error(error.responseJSON?.message || 'Perusahaan gagal dihapus.'));
        }
    </script>
@endpush
