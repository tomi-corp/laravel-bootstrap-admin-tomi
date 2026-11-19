@extends('internal.layouts.app')
@section('title', 'Repayment')

@push('css')
    <style>
        #tableRepayment th,
        #tableRepayment td { white-space: nowrap; vertical-align: middle; }
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
                        <li class="breadcrumb-item active">Repayment</li>
                    </ol>
                </div>
                <h4 class="page-title">Repayment</h4>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <span>Total Repayment</span>
                    <h4 id="totalRepaymentRupiah">Rp 0</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <span>Outstanding</span>
                    <h4 id="totalOutstanding">Rp 0</h4>
                </div>
            </div>
        </div>

        {{-- <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <span>Late</span>
                    <h4 id="telat">Rp 0</h4>
                </div>
            </div>
        </div> --}}
    </div>


    <div class="alert alert-secondary alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h5>Keterangan :</h5>
        <span>Hari Ke Simbol Status Deskripsi</span></br>
        <span> H-5 ⚪️<b> Belum Jatuh Tempo </b> Tagihan akan jatuh tempo 5 hari lagi</span></br>
        <span>H-4 ⚪️ <b> Belum Jatuh Tempo </b> Tagihan akan jatuh tempo 4 hari lagi</span></br>
        <span> H-3 ⚪️<b> Belum Jatuh Tempo </b> Tagihan akan jatuh tempo 3 hari lagi</span></br>
        <span> H-2 ⚪️<b> Belum Jatuh Tempo </b> Tagihan akan jatuh tempo 2 hari lagi</span></br>
        <span> H-1 ⚪️ <b> Belum Jatuh Tempo </b> Tagihan akan jatuh tempo besok</span></br>
        <span> H0 🔵 <b> Baru Masuk Tagihan </b> jatuh tempo hari ini</span></br>
        <span> H+1 🟡<b> Normal Tagihan jatuh </b>tempo 1 hari yang lalu</span></br>
        <span>H+2 🟠<b> Mendesak Tagihan jatuh </b> tempo 2 hari yang lalu</span></br>
        <span>H+3 🔴 <b> Sangat Mendesak Tagihan </b>jatuh tempo ≥ 3 hari yang lalu (hari ke-3 dan seterusnya
            lewat)</span></br>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4>Repayment List</h4>

                    <table id="tableRepayment" class="table table-hover nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID kode Perusahaan</th>
                                <th>Nama Perusahaan</th>
                                <th>Lender</th>
                                <th>Status</th>
                                <th>Jatuh Tempo</th>
                                <th>Jumlah Pinjaman</th>
                                <th>Total Tagihan</th>
                                <th>Invoice</th>
                                <th>Status Invoice</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>


    {{-- modal disbursement --}}
    <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel"
        aria-hidden="true" style="display: none;" id="modalEdit">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel">Ubah status Repayment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form id="addFrom" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Waktu Pembayaran : </label>
                            <input class="form-control" type="datetime-local" id="tanggalPembayaran"></input>
                            <small class="text-danger" id="errtanggalPembayaran"></small>
                        </div>
                        <div class="form-group">
                            <label for="buktiTransfer">Bukti Transfer</label>
                            <input type="file" id="buktiTransfer" class="form-control" accept="image/*,.pdf">
                            <small class="text-muted d-block">Format: JPG, JPEG, PNG, atau PDF. Maksimal 5 MB.</small>
                            <small class="text-danger" id="errbuktiTransfer"></small>
                        </div>
                        <div class="container">
                            <div class="checkbox mb-2 checkbox-info">
                                <input id="checkbox04" type="checkbox" onchange="changeButton()">
                                <label for="checkbox04">
                                    Saya menyatakan bahwa invoicing terhadap perusahaan ini telah terbayarkan
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">cancel</button>
                        <button class="btn btn-primary" type="button" id="buttonSubmitEdit" disabled="true"
                            onClick="saveRepayment()">save <div id="loadingTextEdit">
                            </div></button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- modal disbursement --}}
    <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel"
        aria-hidden="true" style="display: none;" id="modalShowTagihan">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel">List Pinjaman</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <table id="tech-companies-1" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Pinjaman</th>
                                <th data-priority="1">Nama</th>
                                <th data-priority="2">Jumlah Angsuran</th>
                            </tr>
                        </thead>
                        <tbody id="bodyTablePendingKaryawan">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        let repaymentId;

        const changeButton = () => {
            if ($('#checkbox04').is(':checked')) {
                $('#buttonSubmitEdit').removeAttr('disabled');
            } else {
                $('#buttonSubmitEdit').attr('disabled', true);
            }
        };

        const resetRepaymentForm = () => {
            $('#checkbox04').prop('checked', false);
            $('#tanggalPembayaran').val('');
            $('#errtanggalPembayaran').text('');
            $('#buktiTransfer').val('');
            $('#errbuktiTransfer').text('');
            $('#buttonSubmitEdit').attr('disabled', true);
        };

        const saveRepayment = () => {
            $('#errtanggalPembayaran').text('');
            $('#errbuktiTransfer').text('');

            const formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('id', repaymentId);
            formData.append('tanggal', $('#tanggalPembayaran').val());

            const buktiTransfer = $('#buktiTransfer')[0].files[0];
            if (buktiTransfer) {
                formData.append('bukti_transfer', buktiTransfer);
            }

            $.ajax({
                url: '{!! route('internal.tomi-dana.repayment.action') !!}',
                data: formData,
                type: 'POST',
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#buttonSubmitEdit').attr("disabled", true);
                    $('#loadingTextEdit').addClass("spinner-border");
                },
                success: function(submitRes) {
                    repaymentId = '';
                    toastr.success(submitRes.message);
                    table.ajax.reload(null, false);
                    $("#modalEdit").modal('hide');
                    resetRepaymentForm();
                    $('#loadingTextEdit').removeClass("spinner-border");
                    perbaruiData();
                },
                error: function(err) {
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                    const res = err.responseJSON || {};
                    if (res.errors) {
                        $('#errtanggalPembayaran').text(res.errors.tanggal ? res.errors.tanggal[0] : '');
                        $('#errbuktiTransfer').text(res.errors.bukti_transfer ? res.errors.bukti_transfer[0] : '');
                    } else {
                        toastr.error(res.message || 'Gagal menyimpan pembayaran');
                    }
                }
            });
        };

        const edit = (id) => {
            repaymentId = id;
            resetRepaymentForm();
            $("#modalEdit").modal('show');
        };

        const showJatuhTempo = (id) => {
            $.ajax({
                url: '{!! route('internal.tomi-dana.repayment.list') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': id,
                },
                type: 'POST',
                dataType: 'json',
                error: function(err) {
                    toastr.error((err.responseJSON && err.responseJSON.message) || 'Gagal mengambil list tagihan');
                },
                success: function(res) {
                    let tableBody = $('#bodyTablePendingKaryawan');
                    tableBody.empty();
                    $.each(res.data, function(index, item) {
                        let row = $('<tr>');
                        row.append($('<td>').text(item
                            .id_pinjaman));
                        row.append($('<td>').text(item
                            .nama));
                        row.append($('<td>').text(item
                            .angsuran_perbulan));
                        row.appendTo(tableBody);
                    });
                    $('#modalShowTagihan').modal('show')
                }
            });
        }

        var dataTable = {
            "_token": "{{ csrf_token() }}",
        };
        var invoiceTable = "{{ (new \App\Models\invoicing_pinjaman())->getTable() }}";
        // Kolom action tidak ikut diekspor.
        const exportColumns = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        var table = $('#tableRepayment').DataTable({
            dom: 'B<"clear">lf<"table-scroll"t>rip',
            buttons: [{
                extend: 'excel',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                header: true,
                title: 'List Repayment',
                exportOptions: {
                    columns: exportColumns
                },
            }, {
                extend: 'copy',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                header: true,
                title: 'List Repayment',
                exportOptions: {
                    columns: exportColumns
                },
            }, {
                extend: 'print',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                header: true,
                title: 'List Repayment',
                exportOptions: {
                    columns: exportColumns
                },
            }],
            "lengthMenu": [10, 50, 100, 500, 1000],
            processing: true,
            serverSide: true,
            language: {
                processing: '<i class="dripicons-loading">autorenew</i>'
            },
            ajax: {
                "type": "POST",
                "url": '{!! route('internal.tomi-dana.repayment.table') !!}',
                "data": function(xx) {
                    return $.extend(xx, dataTable);
                },
            },
            columns: [{
                data: 'id_code_perusahaan',
                name: 'list_perusahaans.id_code_perusahaan'
            }, {
                data: 'nama_perusahaan',
                name: 'list_perusahaans.nama'
            }, {
                data: 'lender',
                name: 'lenders.nama'
            }, {
                data: 'prioritas',
                name: invoiceTable + '.period',
                searchable: false
            }, {
                data: 'tanggal_period',
                name: invoiceTable + '.period'
            }, {
                data: 'total_tagihan',
                searchable: false,
                orderable: false
            }, {
                data: 'total_pinjaman',
                searchable: false,
                orderable: false
            }, {
                data: 'download_invoice',
                searchable: false,
                orderable: false
            }, {
                data: 'status',
                name: invoiceTable + '.status'
            }, {
                data: 'action',
                searchable: false,
                orderable: false
            }]
        });

        const perbaruiData = () => {
            $.ajax({
                url: '{!! route('internal.tomi-dana.repayment.utils') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                },
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    $('#totalRepaymentRupiah').text(res.totalRepayment);
                    $('#totalOutstanding').text(res.outstanding);
                }
            });
        };
        perbaruiData();
    </script>
@endpush
