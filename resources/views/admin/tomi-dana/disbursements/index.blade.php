@extends('layout.app')

@section('title', 'Pencairan TomiDana')

@push('css')
<style>
    .disbursement-table th {
        background: #f8f9fb;
        border-top: 0;
        color: #6c757d;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .disbursement-table td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.tomi-dana.companies') }}">TomiDana</a></li>
                    <li class="breadcrumb-item active">Pencairan</li>
                </ol>
            </div>
            <h4 class="page-title">Pencairan TomiDana</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div>
                <h4 class="header-title mb-1">Daftar Pinjaman Siap Dicairkan</h4>
                <p class="text-muted mb-0">OTP berhasil, TTE selesai, dan status pinjaman masih pending.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tableDisbursements" class="table table-hover disbursement-table mb-0"
                data-table-url="{{ route('admin.tomi-dana.disbursements.table') }}"
                data-approve-url="{{ route('admin.tomi-dana.disbursements.approve') }}"
                data-csrf-token="{{ csrf_token() }}">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Peminjam</th>
                        <th>Nomor Pinjaman</th>
                        <th>Lender</th>
                        <th>Perusahaan</th>
                        <th>Nominal</th>
                        <th>Tenor</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Bukti Dokumen</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="approveDisbursementModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="approveDisbursementForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Pencairan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="approveDisbursementId" name="id">
                    <div class="form-group">
                        <label for="transferDate">Tanggal Transfer <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="transferDate" name="transfer_date" required>
                        <small class="form-text text-muted">Tanggal dana ditransfer kepada peminjam.</small>
                    </div>
                    <div class="form-group mb-0">
                        <label for="transferProof">Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="transferProof" name="transfer_proof"
                            accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="form-text text-muted">Format JPG, PNG, atau PDF. Maksimal 5 MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="submitApproveDisbursement">
                        <i class="mdi mdi-check-circle-outline mr-1"></i>Approve & Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function () {
        var disbursementTable = $('#tableDisbursements');
        var tableUrl = disbursementTable.data('table-url');
        var approveUrl = disbursementTable.data('approve-url');
        var csrfToken = disbursementTable.data('csrf-token');

        disbursementTable.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 10,
            dom: '<"row"<"col-md-6"l><"col-md-6 text-right"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            language: {
                search: 'Cari:',
                emptyTable: 'Belum ada pinjaman yang siap dicairkan',
                processing: '<i class="dripicons-loading"></i> Memuat data...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Belum ada pinjaman yang siap dicairkan',
                zeroRecords: 'Pinjaman tidak ditemukan',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
            },
            ajax: {
                url: tableUrl,
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_peminjam', name: 'nama_peminjam', orderable: false, defaultContent: '-' },
                { data: 'nomor_pinjaman', name: 'id_pinjaman', defaultContent: '-' },
                { data: 'lender', name: 'lender', orderable: false, defaultContent: '-' },
                { data: 'perusahaan', name: 'perusahaan', orderable: false, defaultContent: '-' },
                { data: 'nominal', name: 'nominal', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'tenor_label', name: 'tenor', defaultContent: '-' },
                { data: 'tanggal_pengajuan', name: 'created_at', defaultContent: '-' },
                { data: 'evidence', name: 'evidence', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        window.approveDisbursement = function (id) {
            $('#approveDisbursementForm')[0].reset();
            $('#approveDisbursementId').val(id);
            $('#approveDisbursementModal').modal('show');
        };

        $('#approveDisbursementForm').on('submit', function (event) {
            event.preventDefault();

            const button = $('#submitApproveDisbursement');
            const formData = new FormData(this);
            formData.append('_token', csrfToken);
            button.prop('disabled', true).addClass('disabled');

            $.ajax({
                url: approveUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    toastr.success(response.message);
                    $('#approveDisbursementModal').modal('hide');
                    $('#tableDisbursements').DataTable().ajax.reload(null, false);
                },
                error: function (xhr) {
                    const response = xhr.responseJSON || {};
                    const errors = response.errors || {};
                    const message = (errors.transfer_date && errors.transfer_date[0]) || (errors.transfer_proof && errors.transfer_proof[0]) || response.message || 'Pinjaman gagal di-approve.';
                    toastr.error(message);
                },
                complete: function () {
                    button.prop('disabled', false).removeClass('disabled');
                }
            });
        });
    });
</script>
@endpush
