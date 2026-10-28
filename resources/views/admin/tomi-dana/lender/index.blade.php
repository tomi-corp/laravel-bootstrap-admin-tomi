@extends('layout.app')

@section('title', 'Lender TomiDana')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">TomiDana</li>
                    <li class="breadcrumb-item active">Lender</li>
                </ol>
            </div>
            <h4 class="page-title">Lender TomiDana</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <button type="button" class="btn btn-sm btn-primary float-right mb-3" data-toggle="modal" data-target="#modalLender">
            <i class="mdi mdi-plus-circle"></i> Tambah Lender
        </button>
        <h4 class="header-title">Daftar Lender TomiDana</h4>
        <p class="text-muted">Kelola data lender yang digunakan oleh produk TomiDana.</p>
        <div class="table-responsive">
            <table id="tableLenderTomiDana" class="table table-hover table-striped text-center" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Lender</th>
                        <th>Nama Lender</th>
                        <th>PIC Lender</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Bank</th>
                        <th>Nama Rekening</th>
                        <th>No. Rekening</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLender" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLenderTitle">Tambah Lender</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="lenderId">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nama Lender</label>
                        <input type="text" class="form-control" id="namaLender" required>
                        <small class="text-danger" id="errNamaLender"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>PIC Lender</label>
                        <input type="text" class="form-control" id="picLender" required>
                        <small class="text-danger" id="errPicLender"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>PIC Internal</label>
                        <input type="text" class="form-control" id="picInternal" required>
                        <small class="text-danger" id="errPicInternal"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="emailLender" required>
                        <small class="text-danger" id="errEmailLender"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Telepon</label>
                        <input type="text" class="form-control" id="telpLender" required>
                        <small class="text-danger" id="errTelpLender"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Status</label>
                        <select class="form-control" id="statusLender">
                            <option value="active">ACTIVE</option>
                            <option value="nonactive">INACTIVE</option>
                        </select>
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Alamat Lengkap</label>
                        <textarea class="form-control" id="alamatLender" rows="2" required></textarea>
                        <small class="text-danger" id="errAlamatLender"></small>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Bank</label>
                        <select class="form-control" id="bankLender">
                            <option value="">Pilih bank</option>
                            @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->code_bank }} - {{ $bank->nama_bank ?? $bank->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Nama Rekening</label>
                        <input type="text" class="form-control" id="namaRekening">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>No. Rekening</label>
                        <input type="text" class="form-control" id="noRekening">
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Nomor PKS</label>
                        <input type="text" class="form-control" id="nomorPks">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveLender">Simpan <span id="loadingLender"></span></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let lenderTable;

    $(function () {
        $('#modalLender input:not([type="email"]), #modalLender textarea').on('input', function () {
            this.value = this.value.toUpperCase();
        });

        lenderTable = $('#tableLenderTomiDana').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route('admin.tomi-dana.lender.table') }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'id_code_lender', name: 'id_code_lender', defaultContent: '-' },
                { data: 'nama', name: 'nama', defaultContent: '-' },
                { data: 'pic_lender', name: 'pic_lender', defaultContent: '-' },
                { data: 'email_lender', name: 'email_lender', defaultContent: '-' },
                { data: 'telp', name: 'telp', defaultContent: '-' },
                { data: 'bank', name: 'bank', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'nama_rekening', name: 'nama_rekening', defaultContent: '-' },
                { data: 'no_rekening', name: 'no_rekening', defaultContent: '-' },
                { data: 'status_label', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });

    function lenderPayload() {
        return {
            _token: '{{ csrf_token() }}',
            id: $('#lenderId').val(),
            nama: $('#namaLender').val(),
            pic_lender: $('#picLender').val(),
            pic_internal: $('#picInternal').val(),
            email_lender: $('#emailLender').val(),
            telp: $('#telpLender').val(),
            alamat_lengkap: $('#alamatLender').val(),
            id_bank: $('#bankLender').val(),
            nama_rekening: $('#namaRekening').val(),
            no_rekening: $('#noRekening').val(),
            nomor_pks: $('#nomorPks').val(),
            status: $('#statusLender').val()
        };
    }

    function resetLenderForm() {
        $('#lenderId').val('');
        $('#modalLender input, #modalLender textarea').val('');
        $('#bankLender').val('');
        $('#statusLender').val('active');
        $('[id^="err"]').text('');
    }

    $('#modalLender').on('hidden.bs.modal', resetLenderForm);

    $('#saveLender').on('click', function () {
        const button = $(this);
        const isEdit = Boolean($('#lenderId').val());
        button.prop('disabled', true);
        $('#loadingLender').addClass('spinner-border spinner-border-sm');

        $.ajax({
            url: isEdit ? '{{ route('admin.tomi-dana.lender.update') }}' : '{{ route('admin.tomi-dana.lender.store') }}',
            type: 'POST',
            data: lenderPayload(),
            success: function (response) {
                toastr.success(response.message);
                lenderTable.ajax.reload(null, false);
                $('#modalLender').modal('hide');
            },
            error: function (error) {
                const errors = error.responseJSON?.errors || {};
                $('#errNamaLender').text(errors.nama?.[0] || '');
                $('#errPicLender').text(errors.pic_lender?.[0] || '');
                $('#errPicInternal').text(errors.pic_internal?.[0] || '');
                $('#errEmailLender').text(errors.email_lender?.[0] || '');
                $('#errTelpLender').text(errors.telp?.[0] || '');
                $('#errAlamatLender').text(errors.alamat_lengkap?.[0] || '');
                toastr.error(error.responseJSON?.message || 'Data lender tidak dapat disimpan.');
            },
            complete: function () {
                button.prop('disabled', false);
                $('#loadingLender').removeClass('spinner-border spinner-border-sm');
            }
        });
    });

    function editLender(id) {
        $.post('{{ route('admin.tomi-dana.lender.detail') }}', { _token: '{{ csrf_token() }}', id: id }, function (response) {
            const data = response.data;
            $('#lenderId').val(data.id);
            $('#namaLender').val(data.nama);
            $('#picLender').val(data.pic_lender);
            $('#picInternal').val(data.pic_internal);
            $('#emailLender').val(data.email_lender);
            $('#telpLender').val(data.telp);
            $('#alamatLender').val(data.alamat_lengkap);
            $('#bankLender').val(data.id_bank);
            $('#namaRekening').val(data.nama_rekening);
            $('#noRekening').val(data.no_rekening);
            $('#nomorPks').val(data.nomor_pks);
            $('#statusLender').val(data.status);
            $('#modalLenderTitle').text('Ubah Lender');
            $('#modalLender').modal('show');
        });
    }

    function deleteLender(id) {
        if (!confirm('Hapus lender ini?')) return;

        $.post('{{ route('admin.tomi-dana.lender.delete') }}', { _token: '{{ csrf_token() }}', id: id }, function (response) {
            toastr.success(response.message);
            lenderTable.ajax.reload(null, false);
        });
    }
</script>
@endpush
