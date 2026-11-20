@extends('internal.layouts.app')

@section('title', 'Referral')

@push('css')
    <style>
        #referralTable th, #referralTable td,
        #companyTable th, #companyTable td { white-space: nowrap; vertical-align: middle; }
        .form-section { font-size: 12px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: #6c757d; }

        /* Form membungkus body + footer, jadi scroll diatur manual: header & footer tetap, body bisa di-scroll. */
        #referralModal .modal-content { max-height: calc(100vh - 3.5rem); overflow: hidden; }
        #referralModal .modal-header,
        #referralModal .modal-footer { flex-shrink: 0; }
        #referralForm { display: flex; flex-direction: column; flex: 1 1 auto; min-height: 0; overflow: hidden; }
        #referralModal .modal-body { flex: 1 1 auto; min-height: 0; overflow-y: auto; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title mb-1">Referral</h4>
                <p class="text-muted mb-0">Akun referral beserta data KTP, rekening bank, dan perusahaan yang direferensikan.</p>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" type="button" onclick="openCreateReferral()">
                            <i class="mdi mdi-plus-circle mr-1"></i>Tambah Referral
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="referralTable" class="table table-hover nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>NAMA</th>
                                    <th>NIK</th>
                                    <th>TELEPON</th>
                                    <th>EMAIL</th>
                                    <th>REKENING</th>
                                    <th>KTP</th>
                                    <th>BUKU REKENING</th>
                                    <th>PERUSAHAAN</th>
                                    <th>AKUN LOGIN</th>
                                    <th>STATUS</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal tambah / ubah referral --}}
    <div class="modal fade" id="referralModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="referralModalTitle">TAMBAH REFERRAL</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                </div>
                <form id="referralForm" autocomplete="off" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="referralId" name="id">
                        <div class="row">
                            <div class="col-12 form-section mb-2">Data KTP</div>
                            <div class="col-md-6 form-group">
                                <label for="nik">NIK</label>
                                <input class="form-control" id="nik" name="national_id_number" maxlength="16" inputmode="numeric" placeholder="16 digit NIK">
                                <small class="text-danger" data-error="national_id_number"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="name">NAMA LENGKAP</label>
                                <input class="form-control" id="name" name="name" placeholder="Sesuai KTP">
                                <small class="text-danger" data-error="name"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="birthPlace">TEMPAT LAHIR</label>
                                <input class="form-control" id="birthPlace" name="birth_place">
                                <small class="text-danger" data-error="birth_place"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="birthDate">TANGGAL LAHIR</label>
                                <input class="form-control" id="birthDate" name="birth_date" type="date">
                                <small class="text-danger" data-error="birth_date"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="gender">JENIS KELAMIN</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="">Pilih</option>
                                    <option value="male">LAKI-LAKI</option>
                                    <option value="female">PEREMPUAN</option>
                                </select>
                                <small class="text-danger" data-error="gender"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="address">ALAMAT</label>
                                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                <small class="text-danger" data-error="address"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="idCardFile">FILE KTP</label>
                                <input class="form-control-file" id="idCardFile" name="id_card_file" type="file" accept=".jpg,.jpeg,.png,.pdf">
                                <small class="text-muted d-block" id="idCardHint">JPG, PNG, atau PDF. Maksimal 5 MB.</small>
                                <small class="text-danger" data-error="id_card_file"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="phone">TELEPON / WHATSAPP</label>
                                <input class="form-control" id="phone" name="phone_number">
                                <small class="text-danger" data-error="phone_number"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="email">EMAIL</label>
                                <input class="form-control" id="email" name="email" type="email">
                                <small class="text-danger" data-error="email"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="isActive">STATUS</label>
                                <select class="form-control" id="isActive" name="is_active">
                                    <option value="1">AKTIF</option>
                                    <option value="0">TIDAK AKTIF</option>
                                </select>
                            </div>

                            <div class="col-12"><hr class="mt-0"></div>
                            <div class="col-12 form-section mb-2">Rekening Bank</div>
                            <div class="col-md-6 form-group">
                                <label for="bankId">BANK</label>
                                <select class="form-control" id="bankId" name="bank_id">
                                    <option value="">Pilih bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ strtoupper($bank->code_bank) }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" data-error="bank_id"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="accountNumber">NOMOR REKENING</label>
                                <input class="form-control" id="accountNumber" name="bank_account_number" inputmode="numeric">
                                <small class="text-danger" data-error="bank_account_number"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="accountName">NAMA PEMILIK REKENING</label>
                                <input class="form-control" id="accountName" name="bank_account_name">
                                <small class="text-danger" data-error="bank_account_name"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="bankBookFile">FILE BUKU REKENING</label>
                                <input class="form-control-file" id="bankBookFile" name="bank_book_file" type="file" accept=".jpg,.jpeg,.png,.pdf">
                                <small class="text-muted d-block" id="bankBookHint">JPG, PNG, atau PDF. Maksimal 5 MB.</small>
                                <small class="text-danger" data-error="bank_book_file"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="notes">CATATAN</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>

                            <div class="col-12"><hr class="mt-0"></div>
                            <div class="col-12 form-section mb-1">Akun Login</div>
                            <div class="col-12"><p class="text-muted small mb-2" id="passwordHint">Password minimal 8 karakter, berisi huruf besar, huruf kecil, angka, dan simbol.</p></div>
                            <div class="col-md-6 form-group">
                                <label for="username">USERNAME</label>
                                <input class="form-control" id="username" name="username" placeholder="Username untuk login">
                                <small class="text-danger" data-error="username"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="accountEmail">EMAIL AKUN</label>
                                <input class="form-control" id="accountEmail" name="account_email" type="email" placeholder="email@contoh.com">
                                <small class="text-danger" data-error="account_email"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="password">PASSWORD</label>
                                <input class="form-control" id="password" name="password" type="password" autocomplete="new-password">
                                <small class="text-danger" data-error="password"></small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="passwordConfirmation">KONFIRMASI PASSWORD</label>
                                <input class="form-control" id="passwordConfirmation" name="password_confirmation" type="password" autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">BATAL</button>
                        <button class="btn btn-primary" type="submit" id="saveReferralButton">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal perusahaan yang direferensikan --}}
    <div class="modal fade" id="companyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">PERUSAHAAN · <span id="companyReferralName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="companyForm" class="row" autocomplete="off">
                        <input type="hidden" id="companyReferralId" name="referral_id">
                        <div class="col-md-5 form-group">
                            <label for="companyId">PERUSAHAAN</label>
                            <select class="form-control" id="companyId" name="company_id">
                                <option value="">Pilih perusahaan</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ strtoupper($company->nama) }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" data-error="company_id"></small>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="featureId">PRODUK</label>
                            <select class="form-control" id="featureId" name="feature_id">
                                <option value="">Pilih produk</option>
                                @foreach ($features as $feature)
                                    <option value="{{ $feature->id }}">{{ $feature->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" data-error="feature_id"></small>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="position">POSISI</label>
                            <select class="form-control" id="position" name="position">
                                @foreach ($positions as $value => $label)
                                    <option value="{{ $value }}">{{ strtoupper($label) }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger" data-error="position"></small>
                        </div>
                        <div class="col-md-2 form-group d-flex align-items-end">
                            <button class="btn btn-primary btn-block" type="submit" id="saveCompanyButton">TAUTKAN</button>
                        </div>
                    </form>
                    <small class="text-muted d-block mb-2">Perusahaan harus memiliki PKS untuk produk yang dipilih. Tiap perusahaan + produk hanya punya satu Referal 1 dan satu Referal 2.</small>
                    <div class="table-responsive">
                        <table id="companyTable" class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>PERUSAHAAN</th>
                                    <th>PRODUK</th>
                                    <th>POSISI</th>
                                    <th class="text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const csrfToken = '{{ csrf_token() }}';
        const dtLanguage = {
            processing: 'Memuat...',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 - 0 dari 0 data',
            infoFiltered: '(disaring dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada referral',
            paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
        };
        let referralTable;
        let editingReferral = false;

        $(function () {
            referralTable = $('#referralTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: '{{ route('internal.referral.table') }}', type: 'POST', data: { _token: csrfToken } },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'national_id_number', name: 'national_id_number' },
                    { data: 'phone_number', name: 'phone_number' },
                    { data: 'email', name: 'email' },
                    { data: 'bank_account', name: 'bank_account_number' },
                    { data: 'ktp', name: 'ktp', orderable: false, searchable: false },
                    { data: 'bank_book', name: 'bank_book', orderable: false, searchable: false },
                    { data: 'companies', name: 'companies', orderable: false, searchable: false },
                    { data: 'account', name: 'account', orderable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']],
                language: dtLanguage
            });

            $('#referralForm').on('submit', submitReferral);
            $('#companyForm').on('submit', submitCompany);
        });

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function clearErrors(form) {
            $(`${form} [data-error]`).text('');
        }

        function showErrors(form, error, fallback) {
            const errors = error.responseJSON?.errors;
            if (errors) {
                Object.entries(errors).forEach(([field, messages]) => $(`${form} [data-error="${field}"]`).text(messages[0]));
            } else {
                toastr.error(error.responseJSON?.message || fallback);
            }
        }

        function openCreateReferral() {
            editingReferral = false;
            $('#referralForm')[0].reset();
            $('#referralId').val('');
            clearErrors('#referralForm');
            $('#idCardHint, #bankBookHint').text('JPG, PNG, atau PDF. Maksimal 5 MB.');
            $('#passwordHint').text('Password minimal 8 karakter, berisi huruf besar, huruf kecil, angka, dan simbol.');
            $('#referralModalTitle').text('TAMBAH REFERRAL');
            $('#saveReferralButton').text('SIMPAN');
            $('#referralModal').modal('show');
        }

        function editReferral(id) {
            $.post('{{ route('internal.referral.detail') }}', { id, _token: csrfToken })
                .done(response => {
                    const item = response.data;
                    editingReferral = true;
                    $('#referralForm')[0].reset();
                    clearErrors('#referralForm');
                    $('#referralId').val(item.id);
                    $('#nik').val(item.national_id_number);
                    $('#name').val(item.name);
                    $('#birthPlace').val(item.birth_place);
                    $('#birthDate').val(item.birth_date);
                    $('#gender').val(item.gender ?? '');
                    $('#address').val(item.address);
                    $('#phone').val(item.phone_number);
                    $('#email').val(item.email);
                    $('#isActive').val(item.is_active ? '1' : '0');
                    $('#bankId').val(item.bank_id ?? '');
                    $('#accountNumber').val(item.bank_account_number);
                    $('#accountName').val(item.bank_account_name);
                    $('#notes').val(item.notes);
                    $('#username').val(item.username);
                    $('#accountEmail').val(item.account_email);
                    const keepHint = 'Kosongkan jika tidak ingin mengganti file.';
                    $('#idCardHint').text(item.has_ktp ? keepHint : 'JPG, PNG, atau PDF. Maksimal 5 MB.');
                    $('#bankBookHint').text(item.has_bank_book ? keepHint : 'JPG, PNG, atau PDF. Maksimal 5 MB.');
                    $('#passwordHint').text('Kosongkan password jika tidak ingin menggantinya.');
                    $('#referralModalTitle').text('UBAH REFERRAL');
                    $('#saveReferralButton').text('PERBARUI');
                    $('#referralModal').modal('show');
                })
                .fail(error => toastr.error(error.responseJSON?.message || 'Gagal memuat data referral.'));
        }

        function submitReferral(event) {
            event.preventDefault();
            clearErrors('#referralForm');
            const button = $('#saveReferralButton');
            const label = button.text();
            const formData = new FormData(this);
            formData.append('_token', csrfToken);

            button.prop('disabled', true).text('MENYIMPAN...');
            $.ajax({
                url: editingReferral ? '{{ route('internal.referral.update') }}' : '{{ route('internal.referral.add') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            })
                .done(response => {
                    $('#referralModal').modal('hide');
                    referralTable.ajax.reload(null, false);
                    toastr.success(response.message);
                })
                .fail(error => showErrors('#referralForm', error, 'Gagal menyimpan referral.'))
                .always(() => button.prop('disabled', false).text(label));
        }

        function deleteReferral(id) {
            if (!confirm('Hapus referral ini beserta akun login-nya?')) return;
            $.post('{{ route('internal.referral.delete') }}', { id, _token: csrfToken })
                .done(response => { referralTable.ajax.reload(null, false); toastr.success(response.message); })
                .fail(error => toastr.error(error.responseJSON?.message || 'Gagal menghapus referral.'));
        }

        function openCompanies(id) {
            $('#companyForm')[0].reset();
            clearErrors('#companyForm');
            $('#companyReferralId').val(id);
            loadCompanies(id).done(() => $('#companyModal').modal('show'));
        }

        function loadCompanies(id) {
            return $.post('{{ route('internal.referral.companies') }}', { id, _token: csrfToken })
                .done(response => {
                    $('#companyReferralName').text(response.data.referral);
                    const rows = response.data.items.map(item => `
                        <tr>
                            <td>${escapeHtml(item.company)}</td>
                            <td>${escapeHtml(item.feature)}</td>
                            <td><span class="badge badge-info">${escapeHtml(item.position)}</span></td>
                            <td class="text-right">
                                <button class="btn btn-sm btn-danger" type="button" title="Hapus" onclick="detachCompany(${item.id})"><i class="mdi mdi-trash-can"></i></button>
                            </td>
                        </tr>`);
                    $('#companyTable tbody').html(rows.length ? rows.join('') : '<tr><td colspan="4" class="text-center text-muted">Belum ada perusahaan yang ditautkan.</td></tr>');
                })
                .fail(error => toastr.error(error.responseJSON?.message || 'Gagal memuat perusahaan.'));
        }

        function submitCompany(event) {
            event.preventDefault();
            clearErrors('#companyForm');
            const button = $('#saveCompanyButton');
            const referralId = $('#companyReferralId').val();

            button.prop('disabled', true);
            $.post('{{ route('internal.referral.companies.add') }}', $(this).serialize() + '&_token=' + csrfToken)
                .done(response => {
                    toastr.success(response.message);
                    $('#companyId, #featureId').val('');
                    loadCompanies(referralId);
                    referralTable.ajax.reload(null, false);
                })
                .fail(error => showErrors('#companyForm', error, 'Gagal menautkan perusahaan.'))
                .always(() => button.prop('disabled', false));
        }

        function detachCompany(id) {
            if (!confirm('Hapus tautan perusahaan ini?')) return;
            $.post('{{ route('internal.referral.companies.delete') }}', { id, _token: csrfToken })
                .done(response => {
                    toastr.success(response.message);
                    loadCompanies($('#companyReferralId').val());
                    referralTable.ajax.reload(null, false);
                })
                .fail(error => toastr.error(error.responseJSON?.message || 'Gagal menghapus tautan.'));
        }
    </script>
@endpush
