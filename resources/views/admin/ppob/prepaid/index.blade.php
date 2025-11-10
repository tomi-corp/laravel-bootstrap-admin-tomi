@extends('layout.app')
@section('title', 'PPOB')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">PPOB</li>
                        <li class="breadcrumb-item active">Prabayar</li>
                    </ol>
                </div>
                <h4 class="page-title">Transaksi PraBayar</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="row" id="contentMaster">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="text-xs font-weight-semibold text-uppercase mb-1">Total penjualan prabayar tanggal
                                <span class="float-right" id="tanggalPrabayar"></span>
                            </div>

                            <h2 id="today">Rp 0</h2>
                            <div class="d-flex justify-content-between">
                                <p>Total Sukses </br><span id="todaySukses"></span> </br>
                                    <span id="todaySuksesCount"></span>
                                </p>
                                <p>Total Gagal </br> <span id="todayGagal"></span> </br>
                                    <span id="todaySuksesGagal"></span>
                                </p>
                                <p>Total Proses </br><span id="todayProses"></span></br>
                                    <span id="todaySuksesProses"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-xs font-weight-semibold text-uppercase mb-1">Total penjualan prabayar
                                keseluruhan</div>
                            <h2 id="kesuluruhan">Rp 0</h2>
                            <div class="d-flex justify-content-between">
                                <p>Total Sukses </br><span id="keseluruhanSukses"></span> </br>
                                    <span id="keseluruhanSuksesCount"></span>
                                </p>
                                <p>Total Gagal </br> <span id="kesuluruhanGagal"></span> </br>
                                    <span id="keseluruhanSuksesGagal"></span>
                                </p>
                                <p>Total Proses </br><span id="kesulurhanProses"></span></br>
                                    <span id="keseluruhanSuksesProses"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" id="contentTable">
                <div class="card-body">
                    <h4>Laporan Transaksi Prabayar</h4>

                    <div class="d-flex justify-content-end">

                        <div class="pr-4">
                            <div class="dropdown" id="testtt">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                                <div class="dropdown-menu" id="dropdown-menu">
                                    <div class="card-body">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck1">
                                            <label class="custom-control-label" for="customCheck1">ID Order</label>
                                        </div>
                                        <input class="form-control" id="id_order"></input>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck2">
                                            <label class="custom-control-label" for="customCheck2">Nama Produk</label>
                                        </div>
                                        <input class="form-control" id="nama_produk"></input>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck3">
                                            <label class="custom-control-label" for="customCheck3">Nama Pelanggan</label>
                                        </div>
                                        <input class="form-control" id="nama_pelanggan"></input>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck4">
                                            <label class="custom-control-label" for="customCheck4">Metode Pembayaran</label>
                                        </div>
                                        <input class="form-control" id="metode_pembayaran"></input>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <input type="text" name="daterange" class="form-control" value=""
                                id="filterRangePicker" />
                        </div>
                    </div>

                    <table id="tablePrepaid" class="table dt-responsive table-hover mt-4 text-center justify-center"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>DateTime </th>
                                <th>Transaction ID </th>
                                <th>Customer Name</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Commision</th>
                                <th>Payment Method</th>
                                <th>Status Label</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="btn-group mb-2 pull-right" id="paginationCustomButton">

                    </div>
                </div>
            </div>


        </div>
    </div>



@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $(document).on('click', '#dropdown-menu', function(e) {
                e.stopPropagation();
            });
            // daterangepicker
            $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end
                    .format('YYYY-MM-DD'));
            });

            let id_order;
            let nama_produk;
            let nama_pelanggan;
            let metode_pembayaran;
            let filterRangePicker;
            let dataUntukTable;
            let meta;
            let pageTable = 1;

            let table = $('#tablePrepaid').DataTable({
                // dom: 'B<"clear">lfrtip',
                dom: 'rtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        header: false,
                        exportOptions: {
                            columns: [0, 1, 2]
                        },
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        header: false,
                        exportOptions: {
                            columns: [0, 1, 2]
                        },
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        header: false,
                        exportOptions: {
                            columns: [0, 1, 2]
                        },
                    }
                ],
                "lengthMenu": [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
                processing: true,
                responsive: true,
                serverSide: false,
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                // ajax: {
                //     "type": "GET",
                //     "url": '{!! route('admin.ppob.prepaid.table') !!}',
                //     // "data": function(xx) {
                //     //     return $.extend(xx, dataTable);
                //     // },
                // },
                "data": dataUntukTable,

                columns: [{
                    data: 'transaction_at'
                }, {
                    data: 'transaction_id'
                }, {
                    data: 'customer_name'
                }, {
                    data: 'product_name'
                }, {
                    data: 'price'
                }, {
                    data: 'commission'
                }, {
                    data: 'payment_method'
                }, {
                    data: 'status_label'
                }]
            });

            perbaruiData = (
                id_order,
                nama_produk,
                nama_pelanggan,
                metode_pembayaran,
                filterRangePicker,
                pageTable
            ) => {
                $.ajax({
                    url: '{!! route('admin.ppob.prepaid.summary') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id_order': id_order,
                        'nama_produk': nama_produk,
                        'nama_pelanggan': nama_pelanggan,
                        'metode_pembayaran': metode_pembayaran,
                        'filterRangePicker': filterRangePicker,
                        'pageTable': pageTable
                    },
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function() {
                        $("#contentMaster").addClass('loading-skeleton');
                    },
                    success: function(summaryPrepaid) {
                        $("#contentMaster").removeClass('loading-skeleton');
                        // keseluruhan
                        $('#tanggalPrabayar').text(summaryPrepaid.data.date);
                        keseluruhan = summaryPrepaid.data.all.success.sales +
                            summaryPrepaid.data.all.failed.sales +
                            summaryPrepaid.data.all.pending.sales;
                        $('#kesuluruhan').text("Rp  " + keseluruhan.toLocaleString());
                        $('#keseluruhanSukses').text("Rp  " + summaryPrepaid.data.all.success.sales
                            .toLocaleString());
                        $('#kesuluruhanGagal').text("Rp  " + summaryPrepaid.data.all.failed.sales
                            .toLocaleString());
                        $('#kesulurhanProses').text("Rp  " + summaryPrepaid.data.all.pending.sales
                            .toLocaleString());

                        $('#keseluruhanSuksesCount').text(summaryPrepaid.data.all.success.count +
                            " Transaksi");
                        $('#keseluruhanSuksesGagal').text(summaryPrepaid.data.all.failed.count +
                            " Transaksi");
                        $('#keseluruhanSuksesProses').text(summaryPrepaid.data.all.pending.count +
                            " Transaksi");
                        // today
                        keseluruhanToday = summaryPrepaid.data.today.success.sales +
                            summaryPrepaid.data.today.failed.sales +
                            summaryPrepaid.data.today.pending.sales;
                        $('#today').text("Rp  " + keseluruhanToday.toLocaleString());
                        $('#todaySukses').text("Rp  " + summaryPrepaid.data.today.success.sales
                            .toLocaleString());
                        $('#todayGagal').text("Rp  " + summaryPrepaid.data.today.failed.sales
                            .toLocaleString());
                        $('#todayProses').text("Rp  " + summaryPrepaid.data.today.pending.sales
                            .toLocaleString());

                        $('#todaySuksesCount').text(summaryPrepaid.data.today.success.count +
                            " Transaksi");
                        $('#todaySuksesGagal').text(summaryPrepaid.data.today.failed.count +
                            " Transaksi");
                        $('#todaySuksesProses').text(summaryPrepaid.data.today.pending.count +
                            " Transaksi");
                    }
                });

                $.ajax({
                    url: '{!! route('admin.ppob.prepaid.table') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id_order': id_order,
                        'nama_produk': nama_produk,
                        'nama_pelanggan': nama_pelanggan,
                        'metode_pembayaran': metode_pembayaran,
                        'filterRangePicker': filterRangePicker,
                        'pageTable': pageTable,
                    },
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function() {
                        $("#contentTable").addClass('loading-skeleton');
                    },
                    success: function(tablePrepaid) {
                        $("#contentTable").removeClass('loading-skeleton');

                        dataUntukTable = tablePrepaid.data;

                        table.clear();
                        table.rows.add(dataUntukTable).draw();
                        meta = tablePrepaid.meta;
                        console.log(tablePrepaid.meta);
                        var $container = $("#paginationCustomButton");
                        $('#paginationCustomButton').empty()
                        $.each(tablePrepaid.meta.links, function(index, value) {
                            console.log(index)
                            var $button = $("<button></button>");

                            if (index == 0) {
                                $button.text('⮜');
                            } else if (index == 2) {
                                $button.text('⮞');
                            } else {
                                $button.text(value.label);
                            }

                            $button.attr('id', "paginationButton" + index);
                            $button.attr('linkto', value.url);

                            $button.attr("onClick", "navigation(" + index + ")");

                            if (value.active == false) {
                                $button.addClass("btn btn-light disabled");
                            } else {
                                $button.addClass("btn btn-primary");
                            }
                            $button.attr("data-index",
                                index);
                            $container.append($button);
                        });


                        // for (i = 1; i <= 10; i++) {
                        //     $('<div class = "dynmic-buttons">')
                        //         .append($('<button/>', {
                        //             text: i,
                        //             id: 'dyn-btn' + i,
                        //             click: function() {
                        //                 DYNFUNC.dynBtnClicked($(this).text());
                        //             }
                        //         }))
                        //         .appendTo($('#dynamic-btn-container'));
                        // }
                    }
                });
            }
            perbaruiData(
                id_order,
                nama_produk,
                nama_pelanggan,
                metode_pembayaran,
                filterRangePicker,
                pageTable
            );

            $('#id_order').on('input', function() {
                if ($('#customCheck1:checked').val() == "on") {
                    id_order = $("#id_order").val();
                } else {
                    id_order = '';
                }
                perbaruiData(
                    id_order,
                    nama_produk,
                    nama_pelanggan,
                    metode_pembayaran,
                    filterRangePicker,
                    pageTable
                );
            });

            $('#nama_produk').on('input', function() {
                if ($('#customCheck2:checked').val() == "on") {
                    nama_produk = $("#nama_produk").val();
                } else {
                    nama_produk = '';
                }
                perbaruiData(
                    id_order,
                    nama_produk,
                    nama_pelanggan,
                    metode_pembayaran,
                    filterRangePicker,
                    pageTable
                );
            });

            $('#nama_pelanggan').on('input', function() {
                if ($('#customCheck3:checked').val() == "on") {
                    nama_pelanggan = $("#nama_pelanggan").val();
                } else {
                    nama_pelanggan = '';
                }
                perbaruiData(
                    id_order,
                    nama_produk,
                    nama_pelanggan,
                    metode_pembayaran,
                    filterRangePicker,
                    pageTable
                );
            });

            $('#metode_pembayaran').on('input', function() {
                if ($('#customCheck4:checked').val() == "on") {
                    metode_pembayaran = $("#metode_pembayaran").val();
                } else {
                    metode_pembayaran = '';
                }
                perbaruiData(
                    id_order,
                    nama_produk,
                    nama_pelanggan,
                    metode_pembayaran,
                    filterRangePicker,
                    pageTable
                );
            });

            $(document).on('click', '#customCheck1:checked', function(e) {
                if ($('#customCheck1:checked').val() == "on") {
                    id_order = $('#id_order').val();
                } else {
                    id_order = ''
                }
                perbaruiData(
                    id_order,
                    nama_produk,
                    nama_pelanggan,
                    metode_pembayaran,
                    filterRangePicker,
                    pageTable
                );
            });
            $(document).on('click', '#customCheck2:checked', function(e) {
                if ($('#customCheck2:checked').val() == "on") {
                    nama_produk = $('#nama_produk').val();
                } else {
                    nama_produk = ''
                }
                perbaruiData(
                    id_order,
                    nama_produk,
                    nama_pelanggan,
                    metode_pembayaran,
                    filterRangePicker,
                    pageTable
                );
            });
            $(document).on('click', '#customCheck3:checked', function(e) {
                if ($('#customCheck3:checked').val() == "on") {
                    nama_pelanggan = $('#nama_pelanggan').val();
                } else {
                    nama_pelanggan = '';
                }
                perbaruiData(
                    id_order,
                    nama_produk,
                    nama_pelanggan,
                    metode_pembayaran,
                    filterRangePicker,
                    pageTable
                );
            });
            $(document).on('click', '#customCheck4:checked', function(e) {
                if ($('#customCheck4:checked').val() == "on") {
                    metode_pembayaran = $('#metode_pembayaran').val();
                } else {
                    metode_pembayaran = '';
                }
                perbaruiData(
                    id_order,
                    nama_produk,
                    nama_pelanggan,
                    metode_pembayaran,
                    filterRangePicker,
                    pageTable
                );
            });
            $(document).on('change', '#filterRangePicker', function(e) {
                filterRangePicker = $('#filterRangePicker').val();
                pageTable = 1;
                perbaruiData(
                    id_order,
                    nama_produk,
                    nama_pelanggan,
                    metode_pembayaran,
                    filterRangePicker,
                    pageTable
                );
            });

            var dataTable = {
                "_token": "{{ csrf_token() }}",
            };


            navigation = (id) => {
                const linkkkk = $('#paginationButton' + id).attr('linkto')
                if (linkkkk != undefined) {
                    var queryString = linkkkk.split('?page=')[1];
                    pageTable = queryString;
                    perbaruiData(
                        id_order,
                        nama_produk,
                        nama_pelanggan,
                        metode_pembayaran,
                        filterRangePicker,
                        pageTable
                    );
                }
            }
        });
    </script>
@endpush
