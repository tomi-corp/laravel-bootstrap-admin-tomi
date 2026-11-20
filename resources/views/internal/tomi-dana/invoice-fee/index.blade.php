@extends('internal.layouts.app')
@section('title', 'Biaya Aplikasi')

@push('css')
    <style>
        #tableInvoicingPending th,
        #tableInvoicingPending td,
        #tableInvoicingDone th,
        #tableInvoicingDone td { white-space: nowrap; vertical-align: middle; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi Dana</a></li>
                        <li class="breadcrumb-item active">Biaya Aplikasi</li>
                    </ol>
                </div>
                <h4 class="page-title">Biaya Aplikasi</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-pending-tab" data-toggle="tab" data-target="#nav-pending"
                                type="button" role="tab" aria-controls="nav-pending" aria-selected="true">PENDING</button>
                            <button class="nav-link" id="nav-done-tab" data-toggle="tab" data-target="#nav-done"
                                type="button" role="tab" aria-controls="nav-done" aria-selected="false">DONE</button>
                        </div>
                    </nav>

                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-pending" role="tabpanel" aria-labelledby="nav-pending-tab">
                            <h4 class="mt-3">INVOICING LENDER PENDING</h4>
                            <table id="tableInvoicingPending" class="table dt-responsive table-hover mt-4 text-center" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>PENDING APPROVAL</th>
                                        <th>PERIOD</th>
                                        <th>NO INVOICE</th>
                                        <th>ID LENDER</th>
                                        <th>NAMA LENDER</th>
                                        <th>TOTAL PINJAMAN</th>
                                        <th>REVENUE</th>
                                        <th>DOWNLOAD</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="nav-done" role="tabpanel" aria-labelledby="nav-done-tab">
                            <h4 class="mt-3">INVOICING LENDER SUCCESS</h4>
                            <table id="tableInvoicingDone" class="table dt-responsive table-hover mt-4 text-center" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>PERIOD</th>
                                        <th>NO INVOICE</th>
                                        <th>ID LENDER</th>
                                        <th>NAMA LENDER</th>
                                        <th>TOTAL PINJAMAN</th>
                                        <th>REVENUE</th>
                                        <th>DOWNLOAD</th>
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
    <div class="modal fade" id="modalInvoiceUpdate" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceUpdateTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInvoiceUpdateTitle">UBAH STATUS INVOICE LENDER</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>WAKTU PEMBAYARAN :</label>
                        <input class="form-control" type="datetime-local" id="tanggalPembayaran">
                        <small class="text-danger" id="errtanggalPembayaran"></small>
                    </div>
                    <div class="form-group">
                        <div class="checkbox mb-2 checkbox-info">
                            <input id="checkboxConfirm" type="checkbox" onchange="changeButton()">
                            <label for="checkboxConfirm">
                                SAYA MENYATAKAN BAHWA INVOICING TERHADAP LENDER INI TELAH TERBAYARKAN
                            </label>
                        </div>
                    </div>
                    <small class="text-muted">Perubahan status menunggu approval 2 layer di halaman KSP &gt; Lender &gt; Invoicing.</small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
                    <button class="btn btn-primary" type="button" id="buttonSubmitEdit" disabled onclick="saveInvoiceFee()">
                        SAVE <div id="loadingTextEdit"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal download invoice --}}
    <div class="modal fade" id="modalDownloadInvoice" tabindex="-1" role="dialog" aria-labelledby="modalDownloadInvoiceLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formDownloadInvoice" method="GET" target="_blank">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDownloadInvoiceLabel">PILIH KOLOM INVOICE</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @foreach ([
                            'tanggal_pinjaman' => 'TANGGAL PINJAMAN',
                            'nip' => 'NIP / NAMA',
                            'nominal_pinjaman' => 'NOMINAL PINJAMAN',
                            'rate' => 'RATE',
                            'fee_tomi' => 'FEE TOMI',
                            'biaya_layanan' => 'BIAYA LAYANAN',
                            'biaya_admin' => 'BIAYA ADMIN',
                            'total' => 'TOTAL',
                        ] as $value => $label)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $value }}" id="col_{{ $value }}" checked>
                                <label class="form-check-label" for="col_{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">DOWNLOAD PDF</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let invoiceFeeId = '';

        const dataTableToken = {
            "_token": "{{ csrf_token() }}",
        };

        const exportButtons = (title, columns) => [{
            extend: 'excel',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
            header: true,
            title: title,
            exportOptions: { columns: columns },
        }, {
            extend: 'copy',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
            header: true,
            title: title,
            exportOptions: { columns: columns },
        }, {
            extend: 'print',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
            header: true,
            title: title,
            exportOptions: { columns: columns },
        }];

        const tablePending = $('#tableInvoicingPending').DataTable({
            dom: 'B<"clear">lfrtip',
            buttons: exportButtons('Invoicing Lender Pending', [0, 1, 2, 3, 4, 5, 6]),
            lengthMenu: [10, 50, 100, 500, 1000],
            processing: true,
            responsive: true,
            serverSide: true,
            language: {
                processing: '<i class="dripicons-loading">autorenew</i>'
            },
            ajax: {
                type: 'POST',
                url: '{!! route('internal.tomi-dana.invoice-fee.pending.table') !!}',
                data: (d) => $.extend(d, dataTableToken),
            },
            columns: [
                { data: 'pendingApproval', orderable: false, searchable: false },
                { data: 'period' },
                { data: 'no_invoice' },
                { data: 'id_code_lender' },
                { data: 'nama_lender' },
                { data: 'total_pinjaman', searchable: false },
                { data: 'revenue', searchable: false },
                { data: 'download', orderable: false, searchable: false },
                { data: 'action', orderable: false, searchable: false },
            ],
        });

        let tableDone = null;
        const initTableDone = () => {
            if (tableDone) {
                return tableDone;
            }

            tableDone = $('#tableInvoicingDone').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: exportButtons('Invoicing Lender Done', [0, 1, 2, 3, 4, 5]),
                lengthMenu: [10, 50, 100, 500, 1000],
                processing: true,
                responsive: true,
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    type: 'POST',
                    url: '{!! route('internal.tomi-dana.invoice-fee.done.table') !!}',
                    data: (d) => $.extend(d, dataTableToken),
                },
                columns: [
                    { data: 'period' },
                    { data: 'no_invoice' },
                    { data: 'id_code_lender' },
                    { data: 'nama_lender' },
                    { data: 'total_pinjaman', searchable: false },
                    { data: 'revenue', searchable: false },
                    { data: 'download', orderable: false, searchable: false },
                ],
            });

            return tableDone;
        };

        $('#nav-done-tab').on('shown.bs.tab', function () {
            initTableDone();
        });

        const reloadTables = () => {
            tablePending.ajax.reload();
            if (tableDone) {
                tableDone.ajax.reload();
            }
        };

        const resetInvoiceFeeForm = () => {
            $('#checkboxConfirm').prop('checked', false);
            $('#tanggalPembayaran').val('');
            $('#errtanggalPembayaran').text('');
            $('#buttonSubmitEdit').attr('disabled', true);
        };

        window.editInvoiceFee = (id) => {
            invoiceFeeId = id;
            resetInvoiceFeeForm();
            $('#modalInvoiceUpdate').modal('show');
        };

        const changeButton = () => {
            if ($('#checkboxConfirm').is(':checked')) {
                $('#buttonSubmitEdit').removeAttr('disabled');
            } else {
                $('#buttonSubmitEdit').attr('disabled', true);
            }
        };

        const saveInvoiceFee = () => {
            $('#errtanggalPembayaran').text('');
            $.ajax({
                url: '{!! route('internal.tomi-dana.invoice-fee.update') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': invoiceFeeId,
                    'tanggal': $('#tanggalPembayaran').val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $('#buttonSubmitEdit').attr('disabled', true);
                    $('#loadingTextEdit').addClass('spinner-border');
                },
                success: function (res) {
                    invoiceFeeId = '';
                    toastr.success(res.message);
                    reloadTables();
                    $('#modalInvoiceUpdate').modal('hide');
                    resetInvoiceFeeForm();
                },
                error: function (err) {
                    const errors = err.responseJSON ? err.responseJSON.errors : null;
                    if (errors) {
                        $('#errtanggalPembayaran').text(errors.tanggal ? errors.tanggal[0] : '');
                    } else {
                        toastr.error('Gagal menyimpan perubahan invoice.');
                    }
                    $('#buttonSubmitEdit').attr('disabled', !$('#checkboxConfirm').is(':checked'));
                },
                complete: function () {
                    $('#loadingTextEdit').removeClass('spinner-border');
                },
            });
        };

        $(document).on('click', '.btn-download-invoice', function () {
            const id = $(this).data('id');
            const url = "{{ route('internal.tomi-dana.invoice-fee.download', ':id') }}".replace(':id', id);

            $('#formDownloadInvoice').attr('action', url);
            $('#modalDownloadInvoice').modal('show');
        });
    </script>
@endpush
