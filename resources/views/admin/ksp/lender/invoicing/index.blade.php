@extends('layout.app')
@section('title', 'Koperasi Simpan Pinjam')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                    <li class="breadcrumb-item active">Invocing Lender</li>
                </ol>
            </div>
            <h4 class="page-title">Invoicing Tomi > Lender</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">

        <div class="card">
            <div class="card-body">
                <h4 class="pb-2">Pending Approval Invocing Lender</h4>

                <table id="tableApprovalInvoice" class="table dt-responsive table-hover mt-4 text-center justify-center"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>DateTime</th>
                            <th>Username Requester</th>
                            <th>Status Approval Layer 1</th>
                            <th>Status Approval Layer 2</th>
                            <th>First Approval</th>
                            <th>Second Approval</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4>Rekap Tagihan Sementara Bulan {{ $bulanIni }}</h4>
                <table id="tableListPerusahaan" class="table dt-responsive table-hover mt-4 text-center justify-center"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>ID Lender</th>
                            <th>Nama Lender</th>
                            <th>Total Pinjaman</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-home-tab" data-toggle="tab" data-target="#nav-home"
                            type="button" role="tab" aria-controls="nav-home" aria-selected="true">Pending</button>
                        <button class="nav-link" id="nav-profile-tab" data-toggle="tab" data-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Done</button>
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

                        <h4>Invocing Lender Pending</h4>
                        <table id="tableInvoicingPending"
                            class="table dt-responsive table-hover mt-4 text-center justify-center" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Pending Approval</th>
                                    <th>Period</th>
                                    <th>No Invoice</th>
                                    <th>ID Lender</th>
                                    <th>Nama Lender</th>
                                    <th>Total Pinjaman</th>
                                    <th>Revenue</th>
                                    <th>Download</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <h4>Invocing Lender Success</h4>
                        <table id="tableInvoicingDone"
                            class="table dt-responsive table-hover mt-4 text-center justify-center" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>No Invoice</th>
                                    <th>ID Lender</th>
                                    <th>Nama Lender</th>
                                    <th>Total Pinjaman</th>
                                    <th>Revenue</th>
                                    <th>Download</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>

            </div>
        </div>



    </div>
</div>
{{-- modal update invoicing --}}
<div class="modal fade" id="modalHistoryUpdate" tabindex="-1" role="dialog" aria-labelledby="modalHistoryTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistoryTitle">Ubah status Invoice Lender
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Waktu Pembayaran : </label>
                    <input class="form-control" type="datetime-local" id="tanggalPembayaran"></input>
                    <small class="text-danger" id="errtanggalPembayaran"></small>
                </div>
                <div class="form-group">
                    <div class="checkbox mb-2 checkbox-info">
                        <input id="checkbox04" type="checkbox" onchange="changeButton()">
                        <label for="checkbox04">
                            Saya menyatakan bahwa invoicing terhadap lender ini telah terbayarkan
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">cancel</button>
                <button class="btn btn-primary" type="button" id="buttonSubmitEdit" disabled="true"
                    onClick="saveInvoicing()">save <div id="loadingTextEdit">
                    </div></button>
            </div>
        </div>
    </div>
</div>

{{-- modal Approve --}}
<div class="modal fade" id="modalApproveEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class=" modal-dialog modal-md modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Detail Perubahan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h6>Lender yang diubah</h6>
                <div class="row">
                    <div class="col-4">
                        <span>ID Lender :</span>
                    </div>
                    <div class="col-8">
                        <span id="id_lender_detail">Test</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <span>Nama Lender :</span>
                    </div>
                    <div class="col-8">
                        <span id="nama_lender_detail"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <span>Status Pertama :</span>
                    </div>
                    <div class="col-8">
                        <span id="status_pertama"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <span>Status Kedua :</span>
                    </div>
                    <div class="col-8">
                        <span id="status_kedua"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <span>Data yang diubah</span>
                        <div id="changedData"></div>
                    </div>
                </div>

                <div class="form-group pt-2">
                    <label>Action</label>
                    <select class="form-control" id="approveAction">
                        <option value="approve">APPROVE</option>
                        <option value="reject">REJECT</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="button" id="buttonSubmitApprove">Save <div
                        id="loadingTextApprove">
                    </div></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDownloadInvoice" tabindex="-1" role="dialog"
    aria-labelledby="modalDownloadInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formDownloadInvoice" method="GET" target="_blank">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Kolom Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="download_invoice_url" value="">

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="columns[]" value="tanggal_pinjaman"
                            id="colTanggal" checked>
                        <label class="form-check-label" for="colTanggal">Tanggal Pinjaman</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="columns[]" value="nip" id="colNip"
                            checked>
                        <label class="form-check-label" for="colNip">NIP / Nama</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="columns[]" value="nominal_pinjaman"
                            id="colNominal" checked>
                        <label class="form-check-label" for="colNominal">Nominal Pinjaman</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="columns[]" value="fee_tomi"
                            id="colFeeTomi" checked>
                        <label class="form-check-label" for="colFeeTomi">Fee TOMI</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="columns[]" value="biaya_layanan"
                            id="colBiayaLayanan" checked>
                        <label class="form-check-label" for="colBiayaLayanan">Biaya Layanan</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="columns[]" value="biaya_admin"
                            id="colBiayaAdmin" checked>
                        <label class="form-check-label" for="colBiayaAdmin">Biaya Admin</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="columns[]" value="total" id="colTotal"
                            checked>
                        <label class="form-check-label" for="colTotal">Total</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Download PDF</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    var idEdit = '';

    var dataTable = {
        "_token": "{{ csrf_token() }}",
    };
    var tableSementara = $('#tableListPerusahaan').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [{
            extend: 'excel',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
            header: true,
            title: 'List Lender',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
            },
        },
        {
            extend: 'copy',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
            header: true,
            title: 'List Perusahaan',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]

            },
        },
        {
            extend: 'print',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
            header: true,
            title: 'List Perusahaan',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
            },
        }
        ],
        "lengthMenu": [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
        processing: true,
        responsive: true,
        serverSide: true,
        language: {
            processing: '<i class="dripicons-loading">autorenew</i>'
        },
        ajax: {
            "type": "POST",
            "url": '{!! route('admin.lender.invoicing.sementara.table') !!}',
            "data": function (xx) {
                return $.extend(xx, dataTable);
            },
        },
        columns: [{
            data: 'id_code_lender'
        }, {
            data: 'nama'
        }, {
            data: 'total_pinjaman'
        }, {
            data: 'revenue'
        }]
    });

    var tableSementaraPending = $('#tableInvoicingPending').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [{
            extend: 'excel',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
            header: true,
            title: 'List Lender',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
            },
        },
        {
            extend: 'copy',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
            header: true,
            title: 'List Perusahaan',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]

            },
        },
        {
            extend: 'print',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
            header: true,
            title: 'List Perusahaan',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
            },
        }
        ],
        "lengthMenu": [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
        processing: true,
        responsive: true,
        serverSide: true,
        language: {
            processing: '<i class="dripicons-loading">autorenew</i>'
        },
        ajax: {
            "type": "POST",
            "url": '{!! route('admin.lender.invoicing.pending.table') !!}',
            "data": function (xx) {
                return $.extend(xx, dataTable);
            },
        },
        columns: [{
            data: 'pendingApproval'
        }, {
            data: 'period'
        }, {
            data: 'no_invoice'
        }, {
            data: 'id_code_lender'
        }, {
            data: 'nama_lender'
        }, {
            data: 'total_pinjaman'
        }, {
            data: 'revenue'
        }, {
            data: 'download'
        }, {
            data: 'action'
        }]
    });


    var tableSementaraDone = $('#tableInvoicingDone').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [{
            extend: 'excel',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
            header: true,
            title: 'List Lender',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
            },
        },
        {
            extend: 'copy',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
            header: true,
            title: 'List Perusahaan',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]

            },
        },
        {
            extend: 'print',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
            header: true,
            title: 'List Perusahaan',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
            },
        }
        ],
        "lengthMenu": [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
        processing: true,
        responsive: true,
        serverSide: true,
        language: {
            processing: '<i class="dripicons-loading">autorenew</i>'
        },
        ajax: {
            "type": "POST",
            "url": '{!! route('admin.lender.invoicing.done.table') !!}',
            "data": function (xx) {
                return $.extend(xx, dataTable);
            },
        },
        columns: [{
            data: 'period'
        }, {
            data: 'no_invoice'
        }, {
            data: 'id_code_lender'
        }, {
            data: 'nama_lender'
        }, {
            data: 'total_pinjaman'
        }, {
            data: 'revenue'
        }, {
            data: 'download'
        }]
    });

    var tableApproval = $('#tableApprovalInvoice').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [{
            extend: 'excel',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
            header: true,
            title: 'List Lender',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
            },
        },
        {
            extend: 'copy',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
            header: true,
            title: 'List Perusahaan',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]

            },
        },
        {
            extend: 'print',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
            header: true,
            title: 'List Perusahaan',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
            },
        }
        ],
        "lengthMenu": [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
        processing: true,
        responsive: true,
        serverSide: true,
        language: {
            processing: '<i class="dripicons-loading">autorenew</i>'
        },
        ajax: {
            "type": "POST",
            "url": '{!! route('admin.lender.approval.invoicing.lender') !!}',
            "data": function (xx) {
                return $.extend(xx, dataTable);
            },
        },
        columns: [{
            data: 'waktu'
        }, {
            data: 'requester'
        }, {
            data: 'status_pertama'
        }, {
            data: 'status_kedua'
        }, {
            data: 'first_approver_id'
        }, {
            data: 'second_approver_id'
        }, {
            data: 'action'
        }]
    });


    editInvocingLender = (id) => {
        idEdit = id;
        $('#modalHistoryUpdate').modal('show');
    }

    const changeButton = () => {
        if ($('#checkbox04').is(':checked')) {
            $('#buttonSubmitEdit').removeAttr('disabled');
        } else {
            $('#buttonSubmitEdit').attr('disabled', true);
        }
    };

    const saveInvoicing = () => {
        $('#checkbox04').prop('checked', false);
        $.ajax({
            url: '{!! route('admin.lender.invoicing.pending.update') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': idEdit,
                'tanggal': $('#tanggalPembayaran').val()
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#buttonSubmitEdit').attr("disabled", true);
                $('#loadingTextEdit').addClass("spinner-border");
            },
            success: function (submitRes) {
                idEdit = '';
                $('#tanggal').val('');
                toastr.success(submitRes.message);
                tableSementara.ajax.reload();
                tableApproval.ajax.reload();
                tableSementaraPending.ajax.reload();
                tableSementaraDone.ajax.reload();
                $("#modalHistoryUpdate").modal('hide');
                $('#buttonSubmitEdit').attr("disabled", false);
                $('#loadingTextEdit').removeClass("spinner-border");
            },
            error: function (err) {
                const errors = err.responseJSON.errors;
                if (errors) {
                    $('#errtanggalPembayaran').text(errors.tanggal ? errors.tanggal[0] : '');
                }
                $('#buttonSubmitEdit').attr("disabled", false);
                $('#loadingTextEdit').removeClass("spinner-border");
            }
        });
    };

    editApproval = (id) => {
        editApprovalID = id;
        $.ajax({
            url: '{!! route('admin.lender.approval.invoicing.detail') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': editApprovalID,
            },
            type: 'POST',
            dataType: 'json',
            success: function (detail) {
                $('#id_lender_detail').text(detail.data.id_code_lender);
                $('#nama_lender_detail').text(detail.data.nama);
                $('#status_pertama').text(detail.data.status_pertama);
                $('#status_kedua').text(detail.data.status_kedua);

                const $changedDataContainer = $('#changedData');
                $changedDataContainer.empty();

                // Ambil old_data dan new_data
                let data = JSON.parse(detail.data.new_data);
                let oldData = data.old_data;
                let newData = data.new_data;

                if (newData && Object.keys(newData).length > 0) {
                    let htmlContent = '<h6>Perubahan Data:</h6>';

                    $.each(newData, function (key, newValue) {
                        if (key === 'update_by') {
                            return true; // continue
                        }
                        const oldValue = oldData[key] ?? '-';
                        htmlContent += `
                        <span>
                            <strong>${key.replace(/_/g, ' ')}:</strong> 
                            <span class="text-danger">${oldValue}</span> 
                            &rarr; 
                            <span class="text-success">${newValue}</span>
                        </span></br>`;
                    });

                    $changedDataContainer.append(htmlContent);
                } else {
                    $changedDataContainer.html('<p>Tidak ada data yang ditampilkan.</p>');
                }

                $('#modalApproveEdit').modal('show');
            }
        });
    }

    $('#buttonSubmitApprove').on('click', function () {
        $.ajax({
            url: '{!! route('admin.lender.approval.invoicing.action') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': editApprovalID,
                'action': $('#approveAction').val(),
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#buttonSubmitApprove').attr("disabled", true);
                $('#loadingTextApprove').addClass("spinner-border");
            },
            success: function (deleteSave) {
                toastr.success(deleteSave.message)
                tableSementara.ajax.reload();
                tableApproval.ajax.reload();
                tableSementaraPending.ajax.reload();
                tableSementaraDone.ajax.reload();
                $("#modalApproveEdit").modal('hide');
                $('#buttonSubmitApprove').attr("disabled", false);
                $('#loadingTextApprove').removeClass("spinner-border");
            },
            error: function (err) {
                toastr.success(err.message)
                tableSementara.ajax.reload();
                tableApproval.ajax.reload();
                tableSementaraPending.ajax.reload();
                tableSementaraDone.ajax.reload();
                $("#modalApproveEdit").modal('hide');
                $('#buttonSubmitApprove').attr("disabled", false);
                $('#loadingTextApprove').removeClass("spinner-border");
            }
        });
    });

    $(document).on('click', '.btn-download-invoice', function () {
        let id = $(this).data('id');
        let url = "{{ route('admin.lender.invoicing.download', ':id') }}";
        url = url.replace(':id', id);

        $('#formDownloadInvoice').attr('action', url);
        $('#modalDownloadInvoice').modal('show');
    });
</script>
@endpush