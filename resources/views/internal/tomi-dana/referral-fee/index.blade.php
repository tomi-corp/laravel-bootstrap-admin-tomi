@extends('internal.layouts.app')
@section('title', 'Biaya Referal')

@push('css')
    <style>
        #tableReferralCurrent th,
        #tableReferralCurrent td,
        #tableReferralHistory th,
        #tableReferralHistory td { white-space: nowrap; vertical-align: middle; }
        .table-scroll { clear: both; overflow-x: auto; margin-top: 1rem; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi Dana</a></li>
                        <li class="breadcrumb-item active">Biaya Referal</li>
                    </ol>
                </div>
                <h4 class="page-title">Biaya Referal</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-current-tab" data-toggle="tab" data-target="#nav-current"
                                type="button" role="tab" aria-controls="nav-current" aria-selected="true">BULAN BERJALAN</button>
                            <button class="nav-link" id="nav-history-tab" data-toggle="tab" data-target="#nav-history"
                                type="button" role="tab" aria-controls="nav-history" aria-selected="false">RIWAYAT</button>
                        </div>
                    </nav>

                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-current" role="tabpanel" aria-labelledby="nav-current-tab">
                            <h4 class="mt-3">FEE REFERAL {{ strtoupper($currentPeriod) }}</h4>
                            <small class="text-muted">Rekap sementara bulan berjalan; nilai bisa bertambah sampai akhir bulan.</small>
                            <table id="tableReferralCurrent" class="table table-hover nowrap text-center" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>PERIOD</th>
                                        <th>NO INVOICE</th>
                                        <th>PERUSAHAAN</th>
                                        <th>REFERAL 1</th>
                                        <th>REFERAL 2</th>
                                        <th>TOTAL PINJAMAN</th>
                                        <th>FEE REFERAL 1</th>
                                        <th>FEE REFERAL 2</th>
                                        <th>TOTAL FEE</th>
                                        <th>DOWNLOAD</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="nav-history" role="tabpanel" aria-labelledby="nav-history-tab">
                            <h4 class="mt-3">FEE REFERAL DITERBITKAN</h4>
                            <small class="text-muted">Invoice per perusahaan untuk bulan-bulan yang sudah lewat.</small>
                            <table id="tableReferralHistory" class="table table-hover nowrap text-center" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>PERIOD</th>
                                        <th>NO INVOICE</th>
                                        <th>PERUSAHAAN</th>
                                        <th>REFERAL 1</th>
                                        <th>REFERAL 2</th>
                                        <th>TOTAL PINJAMAN</th>
                                        <th>FEE REFERAL 1</th>
                                        <th>FEE REFERAL 2</th>
                                        <th>TOTAL FEE</th>
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
                        <small class="text-muted d-block mb-2">Tanggal, karyawan, nominal pinjaman, dan total selalu ditampilkan. Total = jumlah kolom referal yang dipilih.</small>
                        @foreach (\App\Http\Controllers\Internal\TomiDana\ReferralFeeController::PDF_COLUMNS as $value => $label)
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
        const dataTableToken = { "_token": "{{ csrf_token() }}" };

        const exportButtons = (title) => ['excel', 'copy', 'print'].map((type) => ({
            extend: type,
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: {
                excel: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                copy: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                print: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
            }[type],
            header: true,
            title: title,
            exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
        }));

        const tableOptions = (url, title) => ({
            dom: 'B<"clear">lf<"table-scroll"t>rip',
            buttons: exportButtons(title),
            lengthMenu: [10, 50, 100, 500, 1000],
            processing: true,
            serverSide: true,
            order: [[0, 'desc']],
            language: {
                processing: '<i class="dripicons-loading">autorenew</i>'
            },
            ajax: {
                type: 'POST',
                url: url,
                data: (d) => $.extend(d, dataTableToken),
            },
            columns: [
                { data: 'period_label', searchable: false },
                { data: 'invoice_number', orderable: false, searchable: false },
                { data: 'company' },
                { data: 'referral_1', orderable: false, searchable: false },
                { data: 'referral_2', orderable: false, searchable: false },
                { data: 'total_loans', orderable: false, searchable: false },
                { data: 'fee_1', orderable: false, searchable: false },
                { data: 'fee_2', orderable: false, searchable: false },
                { data: 'total_fee', orderable: false, searchable: false },
                { data: 'download', orderable: false, searchable: false },
            ],
        });

        $('#tableReferralCurrent').DataTable(tableOptions('{!! route('internal.tomi-dana.referral-fee.table', 'current') !!}', 'Fee Referal Bulan Berjalan'));

        let tableHistory = null;
        $('#nav-history-tab').on('shown.bs.tab', function () {
            if (!tableHistory) {
                tableHistory = $('#tableReferralHistory').DataTable(tableOptions('{!! route('internal.tomi-dana.referral-fee.table', 'history') !!}', 'Fee Referal Diterbitkan'));
            }
        });

        $(document).on('click', '.btn-download-invoice', function () {
            const url = "{{ route('internal.tomi-dana.referral-fee.download', [':company', ':period']) }}"
                .replace(':company', $(this).data('company'))
                .replace(':period', $(this).data('period'));

            $('#formDownloadInvoice').attr('action', url);
            $('#modalDownloadInvoice').modal('show');
        });
    </script>
@endpush
