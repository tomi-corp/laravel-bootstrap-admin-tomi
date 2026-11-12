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
        white-space: nowrap;
    }

    .disbursement-status-card {
        border-left: 4px solid;
    }

    .disbursement-status-card .status-count {
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
    }

    .disbursement-status-card .status-label {
        color: #6c757d;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-card-pending { border-left-color: #f0ad4e; }
    .status-card-active { border-left-color: #28a745; }
    .status-card-paid { border-left-color: #007bff; }
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

@php($statusCounts = $statusCounts ?? [])

<div class="row mb-3">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card mb-0 disbursement-status-card status-card-pending">
            <div class="card-body">
                <div class="status-label">Belum Diproses</div>
                <div class="status-count text-warning">{{ number_format($statusCounts['belum_diproses'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card mb-0 disbursement-status-card status-card-active">
            <div class="card-body">
                <div class="status-label">Aktif</div>
                <div class="status-count text-success">{{ number_format($statusCounts['aktif'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-0 disbursement-status-card status-card-paid">
            <div class="card-body">
                <div class="status-label">Lunas</div>
                <div class="status-count text-primary">{{ number_format($statusCounts['lunas'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div>
                <h4 class="header-title mb-1">Daftar Pinjaman TomiDana</h4>
                <p class="text-muted mb-0">Pantau pinjaman berdasarkan status proses pencairan.</p>
            </div>
        </div>

        <ul class="nav nav-tabs mb-3" id="disbursementStatusTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-status-tab="belum_diproses">Belum Diproses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status-tab="aktif">Aktif</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status-tab="lunas">Lunas</a>
            </li>
        </ul>

        <div class="table-responsive">
            <table id="tableDisbursements" class="table table-hover disbursement-table mb-0"
                data-table-url="{{ route('admin.tomi-dana.disbursements.table') }}"
                data-approve-url="{{ route('admin.tomi-dana.disbursements.approve') }}"
                data-csrf-token="{{ csrf_token() }}">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Prioritas</th>
                        <th>Doc PK</th>
                        <th>KTP</th>
                        <th>Selfie</th>
                        <th>ID Pinjaman</th>
                        <th>Nama Perusahaan</th>
                        <th>Nama Karyawan</th>
                        <th>Total Pinjaman</th>
                        <th>Jumlah Disbursement</th>
                        <th>Bank</th>
                        <th>No Rek</th>
                        <th>Atas Nama</th>
                        <th>Status</th>
                        <th>Tanggal Transfer</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="disbursementStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="disbursementStatusForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Status Disbursement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="disbursementLoanId" name="id">
                    <div class="form-group">
                            <label for="disbursementLoan">Pinjaman</label>
                            <input type="text" class="form-control" id="disbursementLoan" readonly>
                    </div>
                    <div class="form-group">
                            <label for="disbursementBank">Bank</label>
                            <input type="text" class="form-control" id="disbursementBank" readonly>
                    </div>
                    <div class="form-group">
                            <label for="disbursementAccountName">Atas Nama</label>
                            <input type="text" class="form-control" id="disbursementAccountName" readonly>
                    </div>
                    <div class="form-group">
                            <label for="disbursementAccountNumber">No Rekening</label>
                            <input type="text" class="form-control" id="disbursementAccountNumber" readonly>
                    </div>
                    <div class="form-group">
                            <label for="disbursementAmount">Jumlah Disbursement</label>
                            <input type="text" class="form-control" id="disbursementAmount" readonly>
                    </div>
                    <div class="form-group">
                        <label for="transferDate">Tanggal Transfer <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="transferDate" name="transfer_date" required>
                        <small class="form-text text-muted">Tanggal dana ditransfer kepada peminjam.</small>
                    </div>
                    <div class="form-group">
                        <label for="transferProof">Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="transferProof" name="transfer_proof"
                            accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="form-text text-muted">Format JPG, PNG, atau PDF. Maksimal 5 MB.</small>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="disbursementDeclaration" name="declaration" value="1" required>
                        <label class="form-check-label" for="disbursementDeclaration">Saya menyatakan bahwa dana pinjaman telah berhasil dicairkan kepada peminjam</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitDisbursementStatus" disabled>
                        <i class="mdi mdi-content-save-outline mr-1"></i>Simpan
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
        var statusTab = 'belum_diproses';
        var declaration = $('#disbursementDeclaration');
        var submitDisbursementStatus = $('#submitDisbursementStatus');

        function toggleDisbursementSubmit() {
            submitDisbursementStatus.prop('disabled', !declaration.is(':checked'));
        }

        var table = disbursementTable.DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            scrollX: true,
            pageLength: 10,
            dom: '<"row"<"col-md-6"l><"col-md-6 text-right"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            language: {
                search: 'Cari:',
                emptyTable: 'Belum ada data pinjaman',
                processing: '<i class="dripicons-loading"></i> Memuat data...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Belum ada data pinjaman',
                zeroRecords: 'Pinjaman tidak ditemukan',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
            },
            ajax: {
                url: tableUrl,
                type: 'GET',
                data: function (data) {
                    data.status_tab = statusTab;
                }
            },
            columns: [
                { data: 'tanggal', name: 'created_at', defaultContent: '-' },
                { data: 'prioritas', name: 'prioritas', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'doc_pk', name: 'doc_pk', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'ktp', name: 'ktp', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'selfie', name: 'selfie', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'id_pinjaman', name: 'id_pinjaman', defaultContent: '-' },
                { data: 'nama_perusahaan', name: 'nama_perusahaan', orderable: false, defaultContent: '-' },
                { data: 'nama_karyawan', name: 'nama_karyawan', orderable: false, defaultContent: '-' },
                { data: 'total_pinjaman', name: 'total_pinjaman', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'jumlah_disbursement', name: 'jumlah_disbursement', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'bank', name: 'bank', orderable: false, defaultContent: '-' },
                { data: 'no_rekening', name: 'no_rekening', orderable: false, defaultContent: '-' },
                { data: 'atas_nama', name: 'atas_nama', orderable: false, defaultContent: '-' },
                { data: 'status_label', name: 'status', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'tanggal_transfer', name: 'transfer_date', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        $('#disbursementStatusTabs').on('click', '[data-status-tab]', function (event) {
            event.preventDefault();
            statusTab = $(this).data('status-tab');
            $('#disbursementStatusTabs .nav-link').removeClass('active');
            $(this).addClass('active');
            table.ajax.reload();
        });

        window.editDisbursement = function (id) {
            var row = table.rows().data().toArray().find(function (item) {
                return String(item.loan_id) === String(id);
            });

            if (!row) {
                toastr.error('Data pinjaman tidak ditemukan.');
                return;
            }

            $('#disbursementStatusForm')[0].reset();
            $('#disbursementLoanId').val(row.loan_id);
            $('#disbursementLoan').val(row.id_pinjaman);
            $('#disbursementBank').val(row.bank);
            $('#disbursementAccountName').val(row.atas_nama);
            $('#disbursementAccountNumber').val(row.no_rekening);
            $('#disbursementAmount').val(row.jumlah_disbursement);
            toggleDisbursementSubmit();
            $('#disbursementStatusModal').modal('show');
        };

        declaration.on('change', toggleDisbursementSubmit);

        $('#disbursementStatusForm').on('submit', function (event) {
            event.preventDefault();

            const button = $('#submitDisbursementStatus');
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
                    $('#disbursementStatusModal').modal('hide');
                    $('#tableDisbursements').DataTable().ajax.reload(null, false);
                },
                error: function (xhr) {
                    const response = xhr.responseJSON || {};
                    const errors = response.errors || {};
                    const message = (errors.transfer_date && errors.transfer_date[0]) || (errors.transfer_proof && errors.transfer_proof[0]) || (errors.declaration && errors.declaration[0]) || response.message || 'Status disbursement gagal disimpan.';
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
