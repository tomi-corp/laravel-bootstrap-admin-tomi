@extends('bta.layout.app')

@section('title', $pageTitle ?? 'Finance')

@section('content')
<style>
    .finance-nav {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 18px;
    }

    .finance-nav .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #64748b;
        font-weight: 600;
        padding: 0 0 12px;
        margin-right: 24px;
    }

    .finance-nav .nav-link.active {
        background: transparent;
        border-bottom-color: #2563eb;
        color: #2563eb;
    }

    .proof-preview {
        width: 100%;
        max-height: 72vh;
        object-fit: contain;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #f8fafc;
    }

</style>

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">BTA</a></li>
                    <li class="breadcrumb-item active">{{ $pageTitle ?? 'Finance' }}</li>
                </ol>
            </div>
            <h4 class="page-title">{{ $pageTitle ?? 'Finance' }}</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="nav finance-nav">
                    <a href="{{ route('bta.finance.index') }}"
                        class="nav-link {{ ($mode ?? 'pending') === 'pending' ? 'active' : '' }}">
                        <i class="mdi mdi-upload"></i> Belum Upload Bukti
                    </a>
                    <a href="{{ route('bta.finance.uploaded.index') }}"
                        class="nav-link {{ ($mode ?? 'pending') === 'uploaded' ? 'active' : '' }}">
                        <i class="mdi mdi-check-circle-outline"></i> Sudah Upload Bukti Bayar
                    </a>
                </div>

                <table id="tableFinance" class="table dt-responsive table-hover mt-4 text-center justify-center"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th data-priority="1">No. KTP</th>
                            <th data-priority="1">Nama Lengkap</th>
                            <th data-priority="2">Tanggal Lahir</th>
                            <th data-priority="3">Jenis Kelamin</th>
                            <th data-priority="1">No. Telepon</th>
                            <th data-priority="4">Status Perkawinan</th>
                            <th data-priority="2">Perusahaan</th>
                            <th data-priority="3">Status Pegawai</th>
                            <th data-priority="1">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modalProof" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalProofLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProofLabel">Bukti Bayar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Nasabah</label>
                    <input type="text" class="form-control" id="proofNama" disabled>
                </div>

                <div class="form-group">
                    <label>Tanggal Disbursement</label>
                    <input type="date" class="form-control" id="proofTanggalDisbursement" disabled>
                </div>

                <div class="form-group">
                    <label>Mulai Pinjaman</label>
                    <input type="date" class="form-control" id="proofMulaiPinjaman" disabled>
                </div>

                <div class="form-group">
                    <label>Akhir Pinjaman</label>
                    <input type="date" class="form-control" id="proofAkhirPinjaman" disabled>
                </div>

                <label>Bukti Disbursement</label>
                <div class="text-center">
                    <img src="" alt="Bukti Bayar" class="proof-preview" id="proofPreview">
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-outline-primary" id="proofOpenNewTab" target="_blank">
                    <i class="mdi mdi-open-in-new"></i> Buka
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div id="modalDisbursement" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalDisbursementLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formDisbursement" class="modal-content" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="finance_id">
            <input type="hidden" id="finance_jangka_waktu">

            <div class="modal-header">
                <h5 class="modal-title" id="modalDisbursementLabel">Disbursement Finance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Nasabah</label>
                    <input type="text" class="form-control" id="finance_nama" readonly>
                </div>

                <div class="form-group">
                    <label for="tanggal_disbursement">Tanggal Disbursement <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="tanggal_disbursement"
                        id="tanggal_disbursement" required>
                    <small class="text-danger" id="errTanggalDisbursement"></small>
                </div>

                <div class="form-group">
                    <label for="bukti_disbursement">Upload Bukti Disbursement <span
                            class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="bukti_disbursement" id="bukti_disbursement"
                        accept=".jpg,.jpeg,.png,.pdf" required>
                    <small class="text-muted d-block" id="buktiInfo">Format: JPG, PNG, atau PDF. Maksimal 5 MB.</small>
                    <small class="text-danger" id="errBuktiDisbursement"></small>
                </div>

                <div class="form-group">
                    <label for="mulai_pinjaman">Mulai Pinjaman <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="mulai_pinjaman" id="mulai_pinjaman" required>
                    <small class="text-danger" id="errMulaiPinjaman"></small>
                </div>

                <div class="form-group">
                    <label for="akhir_pinjaman">Akhir Pinjaman <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="akhir_pinjaman" id="akhir_pinjaman" readonly
                        required>
                    <small class="text-danger" id="errAkhirPinjaman"></small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitDisbursement">
                    <i class="mdi mdi-content-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        var dataTable = {
            "_token": "{{ csrf_token() }}",
        };

        var tableFinance = $('#tableFinance').DataTable({
            dom: 'B<"clear">lfrtip',
            buttons: [{
                    extend: 'excel',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                }
            ],
            "lengthMenu": [10, 50, 100, 500, 1000],
            processing: true,
            responsive: true,
            serverSide: true,
            language: {
                processing: '<i class="dripicons-loading">autorenew</i>'
            },
            ajax: {
                "type": "POST",
                "url": '{!! route($tableRoute ?? 'bta.finance.table') !!}',
                "data": function(d) {
                    return $.extend({}, d, dataTable);
                },
            },
            columns: [{
                    data: 'no_ktp'
                },
                {
                    data: 'nama_lengkap'
                },
                {
                    data: 'tanggal_lahir'
                },
                {
                    data: 'jenis_kelamin'
                },
                {
                    data: 'no_telp'
                },
                {
                    data: 'status_perkawinan'
                },
                {
                    data: 'nama_perusahaan'
                },
                {
                    data: 'status_pegawai'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false,
                }
            ]
        });

        function clearDisbursementErrors() {
            $('#errTanggalDisbursement').text('');
            $('#errBuktiDisbursement').text('');
            $('#errMulaiPinjaman').text('');
            $('#errAkhirPinjaman').text('');
        }

        function calculateAkhirPinjaman() {
            var mulaiPinjaman = $('#mulai_pinjaman').val();
            var jangkaWaktu = parseInt($('#finance_jangka_waktu').val()) || 0;

            if (!mulaiPinjaman || jangkaWaktu <= 0) {
                $('#akhir_pinjaman').val('');
                return;
            }

            var startDate = new Date(mulaiPinjaman);
            startDate.setMonth(startDate.getMonth() + jangkaWaktu);

            var year = startDate.getFullYear();
            var month = String(startDate.getMonth() + 1).padStart(2, '0');
            var day = String(startDate.getDate()).padStart(2, '0');

            $('#akhir_pinjaman').val(year + '-' + month + '-' + day);
        }

        $(document).on('click', '.btn-disbursement', function() {
            clearDisbursementErrors();

            var today = new Date().toISOString().slice(0, 10);
            var tanggalDisbursement = $(this).data('tanggal-disbursement') || today;

            $('#formDisbursement')[0].reset();
            $('#finance_id').val($(this).data('id'));
            $('#finance_nama').val($(this).data('nama'));
            $('#finance_jangka_waktu').val($(this).data('jangka-waktu'));
            $('#tanggal_disbursement').val(tanggalDisbursement);
            $('#mulai_pinjaman').val($(this).data('mulai-pinjaman'));
            $('#akhir_pinjaman').val($(this).data('akhir-pinjaman'));

            if ($('#mulai_pinjaman').val() && !$('#akhir_pinjaman').val()) {
                calculateAkhirPinjaman();
            }

            if ($(this).data('has-bukti') == 1) {
                $('#buktiInfo').text('Bukti sebelumnya sudah ada. Upload file baru untuk mengganti.');
            } else {
                $('#buktiInfo').text('Format: JPG, PNG, atau PDF. Maksimal 5 MB.');
            }

            $('#modalDisbursement').modal('show');
        });

        $('#mulai_pinjaman').on('change input', function() {
            calculateAkhirPinjaman();
        });

        $(document).on('click', '.btn-view-proof', function() {
            var url = $(this).data('url');
            var nama = $(this).data('nama') || 'Nasabah';

            $('#modalProofLabel').text('Bukti Bayar - ' + nama);
            $('#proofNama').val(nama);
            $('#proofTanggalDisbursement').val($(this).data('tanggal-disbursement') || '');
            $('#proofMulaiPinjaman').val($(this).data('mulai-pinjaman') || '');
            $('#proofAkhirPinjaman').val($(this).data('akhir-pinjaman') || '');
            $('#proofPreview').attr('src', url);
            $('#proofOpenNewTab').attr('href', url);
            $('#modalProof').modal('show');
        });

        $('#modalProof').on('hidden.bs.modal', function() {
            $('#proofNama').val('');
            $('#proofTanggalDisbursement').val('');
            $('#proofMulaiPinjaman').val('');
            $('#proofAkhirPinjaman').val('');
            $('#proofPreview').attr('src', '');
            $('#proofOpenNewTab').attr('href', '#');
        });

        $('#formDisbursement').on('submit', function(e) {
            e.preventDefault();
            clearDisbursementErrors();

            var $btn = $('#btnSubmitDisbursement');
            var originalHtml = $btn.html();
            var formData = new FormData(this);

            $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

            $.ajax({
                url: '{!! route('bta.finance.approve') !!}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    toastr.success(response.message || 'Data disbursement berhasil disimpan.');
                    $('#modalDisbursement').modal('hide');
                    tableFinance.ajax.reload(null, false);
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        $('#errTanggalDisbursement').text(errors.tanggal_disbursement ? errors
                            .tanggal_disbursement[0] : '');
                        $('#errBuktiDisbursement').text(errors.bukti_disbursement ? errors
                            .bukti_disbursement[0] : '');
                        $('#errMulaiPinjaman').text(errors.mulai_pinjaman ? errors
                            .mulai_pinjaman[0] : '');
                        $('#errAkhirPinjaman').text(errors.akhir_pinjaman ? errors
                            .akhir_pinjaman[0] : '');
                    } else {
                        toastr.error(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON
                            .message : 'Terjadi kesalahan saat menyimpan data.');
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    });
</script>
@endpush
