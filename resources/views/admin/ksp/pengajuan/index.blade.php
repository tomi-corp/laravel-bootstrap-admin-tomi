@extends('layout.app')
@section('title', 'Koperasi Simpan Pinjam')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item "><a href="{{ route('admin.ksp.index') }}">KSP
                                Perusahaan</a></li>
                        <li class="breadcrumb-item active">KSP Pengajuan</li>
                    </ol>
                </div>
                <h4 class="page-title">KSP Pengajuan</h4>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Detail Perushaan KSP</h5>
                    <div class="d-flex justify-content-between">
                        <span>Kode ID Perusahaan :</span>
                        <span>{{ $perusahaan->id_code_perusahaan }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Nama Perusahaan :</span>
                        <span>{{ $perusahaan->nama }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Nama PIC Perusahaan :</span>
                        <span>{{ $perusahaan->pic_perusahaan }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Nama PIC Internal :</span>
                        <span>{{ $perusahaan->pic_internal }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Alamat :</span>
                        <span>{{ $perusahaan->alamat }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Telepon :</span>
                        <span>{{ $perusahaan->telp }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Nomor PKS :</span>
                        <span>{{ $perusahaan->nomor_pks }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Last Updated :</span>
                        <span>{{ $perusahaan->updated_at }}</span>
                    </div>

                </div>
            </div>



            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Pengajuan Statistic</h4>
                    <div class="row text-center">
                        <div class="col-3 mb-3">
                            <h3 class="font-weight-light" id="belumValidasiWaCount"></h3>
                            <p class="text-muted text-overflow">Belum Validasi WA</p>
                        </div>
                        <div class="col-3 mb-3">
                            <h3 class="font-weight-light" id="pendingCount"></h3>
                            <p class="text-muted text-overflow">Pending</p>
                        </div>
                        <div class="col-3 mb-3">
                            <h3 class="font-weight-light" id="exportedCount"></h3>
                            <p class="text-muted text-overflow">Exported</p>
                        </div>
                        <div class="col-3 mb-3">
                            <h3 class="font-weight-light" id="approveCount"></h3>
                            <p class="text-muted text-overflow">Approve</p>
                        </div>
                        <div class="col-3 mb-3">
                            <h3 class="font-weight-light" id="rejectCount"></h3>
                            <p class="text-muted text-overflow">Reject</p>
                        </div>
                    </div>
                    <div class="chartjs-chart-example chartjs-chart">
                        <canvas id="doughnut"></canvas>
                    </div>
                </div>
            </div>


        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#pending" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                Pending
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#exported" data-toggle="tab" aria-expanded="true" class="nav-link">
                                Exported
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#approve" data-toggle="tab" aria-expanded="false" class="nav-link">
                                Approve
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#reject" data-toggle="tab" aria-expanded="false" class="nav-link">
                                Reject
                            </a>
                        </li>
                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane active" id="pending">
                            <div class="d-flex justify-content-between pb-4">
                                <a href="#" onclick="exportDataPending({{ $perusahaan->id }})"
                                    class="btn btn-sm btn-primary  waves-effect waves-light ">
                                    <span> <i class=" mdi mdi-file-document-box-outline"></i> Export Pengajuan</span>
                                </a>

                                <a href="#" onclick="prosesDataToExport({{ $perusahaan->id }})"
                                    class="btn btn-sm btn-primary waves-effect waves-light">
                                    <span> <i class="mdi mdi-arrow-right-bold"></i> Proses Exported</span>
                                </a>
                            </div>


                            <form id="pendingPinjamanExport" action="{{ route('admin.ksp.pengajuan.pending.export') }}"
                                method="POST" style="display: none;">
                                <input type="hidden" name="id" value="{{ $perusahaan->id }}" />
                                @csrf
                            </form>


                            <table id="tablePending" class="table dt-responsive table-hover text-center justify-center"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID Pinjaman</th>
                                        <th>Nama</th>
                                        <th>Nominal </th>
                                        <th>Tenor</th>
                                        <th>Rate</th>
                                        <th>Biaya Admin</th>
                                        <th>Iuran Anggota</th>
                                        <th>Total Biaya Admin</th>
                                        <th>Jumlah Yang Diterima</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                        <div class="tab-pane show" id="exported">
                            <div class="row">
                                <div class="col-4 pb-4">
                                    <div class="card-box" style="border:1px, solid">
                                        <form id="fileUploadDataKaryawan" method="POST"
                                            action="{{ route('admin.ksp.pengajuan.export.upload') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" class="form-control" accept=".xls,.xlsx"
                                                name="file" id="fileDataProcess" />
                                            <small>Hanya menerima format .xlsx</small><br>
                                            <small class="text-danger" id="errUploadData"></small>
                                            <div hidden id="progressBar">
                                                <div class="progress mb-3">
                                                    <div id="pgBar" class="progress-bar progress-bar-striped bg-info "
                                                        role="progressbar" style="width: 0%" aria-valuenow="0"
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary float-right pull-right" type="submit"
                                                id="buttonUploadDataExport">Upload Data <div id="loadingTextEdit">
                                                </div>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>


                            <table id="tableExported" class="table dt-responsive table-hover text-center justify-center"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID Pinjaman</th>
                                        <th>Nama</th>
                                        <th>Nominal </th>
                                        <th>Tenor</th>
                                        <th>Rate</th>
                                        <th>Biaya Admin</th>
                                        <th>Iuran Anggota</th>
                                        <th>Total Biaya Admin</th>
                                        <th>Jumlah Yang Diterima</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                        <div class="tab-pane" id="approve">
                            <table id="tableApprove" class="table dt-responsive table-hover text-center justify-center"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID Pinjaman</th>
                                        <th>Nama</th>
                                        <th>Nominal </th>
                                        <th>Tenor</th>
                                        <th>Rate</th>
                                        <th>Biaya Admin</th>
                                        <th>Iuran Anggota</th>
                                        <th>Total Biaya Admin</th>
                                        <th>Jumlah Yang Diterima</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane" id="reject">
                            <table id="tableReject" class="table dt-responsive table-hover text-center justify-center"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID Pinjaman</th>
                                        <th>Nama</th>
                                        <th>Nominal </th>
                                        <th>Tenor</th>
                                        <th>Rate</th>
                                        <th>Biaya Admin</th>
                                        <th>Iuran Anggota</th>
                                        <th>Total Biaya Admin</th>
                                        <th>Jumlah Yang Diterima</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    {{-- <h4 class="header-title mb-3">Tagihan</h4> --}}
                    <table id="tableInvoice" class="table dt-responsive table-hover text-center justify-center"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Tanggal/Tahun Transaksi</th>
                                <th>Total Transaksi Approve</th>
                                <th>Total Fee Base</th>
                                <th>Income KSP </th>
                                <th>Iuran Anggota</th>
                                <th>Download Invoice</th>
                                <th>Status Invoice</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                    <small>Note:<br> Tagihan kepada pihak perusahaan setiap tanggal 1</small>
                </div>
            </div>



        </div>


        {{-- modal Edit --}}
        <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog"
            aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;" id="modalEdit">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myCenterModalLabel">Update Status Invoice </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Status </label>
                            <select class="form-control" id="statusEdit" required>
                                <option value="lunas">Lunas</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">cancel</button>
                        <button class="btn btn-primary" type="submit" id="buttonSubmitEdit"
                            onclick="buttonSubmitEdit()">save <div id="loadingTextEdit">
                            </div></button>
                    </div>
                </div>
            </div>
        </div>


    @endsection

    @push('js')
        <script script src="{{ asset('assets/libs/chart-js/Chart.bundle.min.js') }}"></script>

        <script>
            let idEdit = '';
            getStatistic = () => {
                $.ajax({
                    url: '{!! route('admin.ksp.pengajuan.statistic') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': {{ $perusahaan->id }},
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(dataStatistic) {
                        $('#pendingCount').text(dataStatistic.pending);
                        $('#belumValidasiWaCount').text(dataStatistic.belum_validasi_wa);
                        $('#exportedCount').text(dataStatistic.exported);
                        $('#approveCount').text(dataStatistic.approve);
                        $('#rejectCount').text(dataStatistic.reject);
                        data = {
                            datasets: [{
                                    data: [dataStatistic.belum_validasi_wa, dataStatistic
                                        .pending, dataStatistic
                                        .exported, dataStatistic.approve, dataStatistic
                                        .reject
                                    ],
                                    backgroundColor: ["#fbcc5c", "#6c757d", "#02a8b5", "#6b5eae",
                                        "#fa5c7c"
                                    ],
                                    borderColor: "transparent",
                                    borderWidth: "3",

                                },
                                {
                                    maintainAspectRatio: !1,
                                    cutoutPercentage: 80,
                                    legend: {
                                        display: !1
                                    },
                                }
                            ],

                            // These labels appear in the legend and in the tooltips when hovering different arcs
                            labels: [
                                'Belum Validasi WA',
                                'Exported',
                                'Approve',
                                'Reject',
                            ]
                        };

                        var myDoughnutChart = new Chart($('#doughnut'), {
                            type: 'doughnut',
                            data: data,
                        });


                    }
                });
            }

            edit = (id) => {
                idEdit = id;
                $("#modalEdit").modal('show');
            }

            buttonSubmitEdit = () => {
                $.ajax({
                    url: '{!! route('admin.ksp.invoicing.update') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': idEdit,
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(prosesToExport) {
                        toastr.success(prosesToExport.message)
                        $("#modalEdit").modal('hide');
                        tableInvoice.ajax.reload();
                    }
                });
            }

            getStatistic();



            var dataTable = {
                "_token": "{{ csrf_token() }}",
                'id': {{ $perusahaan->id }}
            };
            var tablePending = $('#tablePending').DataTable({
                dom: 'B<"clear">lfrtip',
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
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('admin.ksp.pengajuan.pending.table') !!}',
                    "data": function(xx) {
                        return $.extend(xx, dataTable);
                    },
                },
                columns: [{
                    data: 'id_pinjaman'
                }, {
                    data: 'nama',
                    nama: 'data_karyawan.nama_karyawan',
                }, {
                    data: 'nominal'
                }, {
                    data: 'tenor'
                }, {
                    data: 'rate'
                }, {
                    data: 'biaya_admin'
                }, {
                    data: 'iuran_anggota'
                }, {
                    data: 'total_biaya_admin'
                }, {
                    data: 'jumlah_yang_diterima'
                }]
            });

            var tableExported = $('#tableExported').DataTable({
                dom: 'B<"clear">lfrtip',
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
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('admin.ksp.pengajuan.export.table') !!}',
                    "data": function(xx) {
                        return $.extend(xx, dataTable);
                    },
                },
                columns: [{
                    data: 'id_pinjaman'
                }, {
                    data: 'nama',
                    nama: 'data_karyawan.nama_karyawan',
                }, {
                    data: 'nominal'
                }, {
                    data: 'tenor'
                }, {
                    data: 'rate'
                }, {
                    data: 'biaya_admin'
                }, {
                    data: 'iuran_anggota'
                }, {
                    data: 'total_biaya_admin'
                }, {
                    data: 'jumlah_yang_diterima'
                }]
            });

            var tableReject = $('#tableReject').DataTable({
                dom: 'B<"clear">lfrtip',
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
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('admin.ksp.pengajuan.reject.table') !!}',
                    "data": function(xx) {
                        return $.extend(xx, dataTable);
                    },
                },
                columns: [{
                    data: 'id_pinjaman'
                }, {
                    data: 'nama',
                    nama: 'data_karyawan.nama_karyawan',
                }, {
                    data: 'nominal'
                }, {
                    data: 'tenor'
                }, {
                    data: 'rate'
                }, {
                    data: 'biaya_admin'
                }, {
                    data: 'iuran_anggota'
                }, {
                    data: 'total_biaya_admin'
                }, {
                    data: 'jumlah_yang_diterima'
                }]
            });

            var tableApprove = $('#tableApprove').DataTable({
                dom: 'B<"clear">lfrtip',
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
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('admin.ksp.pengajuan.approve.table') !!}',
                    "data": function(xx) {
                        return $.extend(xx, dataTable);
                    },
                },
                columns: [{
                    data: 'id_pinjaman'
                }, {
                    data: 'nama',
                    nama: 'data_karyawan.nama_karyawan',
                }, {
                    data: 'nominal'
                }, {
                    data: 'tenor'
                }, {
                    data: 'rate'
                }, {
                    data: 'biaya_admin'
                }, {
                    data: 'iuran_anggota'
                }, {
                    data: 'total_biaya_admin'
                }, {
                    data: 'jumlah_yang_diterima'
                }]
            });

            exportDataPending = (id) => {
                event.preventDefault();
                document.getElementById('pendingPinjamanExport').submit();

                tablePending.ajax.reload();
                tableExported.ajax.reload();
                tableApprove.ajax.reload();
                tableReject.ajax.reload();
            }

            prosesDataToExport = (id) => {
                $.ajax({
                    url: '{!! route('admin.ksp.pengajuan.proses.export') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': id,
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(prosesToExport) {
                        toastr.success(prosesToExport.message)
                        tablePending.ajax.reload();
                        tableExported.ajax.reload();
                        tableApprove.ajax.reload();
                        tableReject.ajax.reload();
                        getStatistic();
                    }
                });
            }


            $('#fileUploadDataKaryawan').ajaxForm({
                beforeSend: function() {
                    $('#errUploadData').text('');
                    $('#buttonUploadDataExport').attr("disabled", true);
                    $('#loadingTextEdit').addClass("spinner-border");
                    $('#progressBar').attr('hidden', false);

                    var percentage = '0';
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentage = percentComplete;
                    $('#pgBar').css("width", percentage + '%', function() {
                        return $(this).attr("aria-valuenow", percentage) + "%";
                    })
                },
                error: function(xhr, status, error) {
                    $('#errUploadData').text('');
                    $('#fileDataProcess').val('');
                    $('#progressBar').attr('hidden', true);
                    $('#buttonUploadDataExport').attr("disabled", false);
                    $('#loadingTextEdit').removeClass(
                        "spinner-border");
                    $('#errUploadData').text(xhr.responseJSON.message);
                },
                success: function(xhr) {
                    tablePending.ajax.reload();
                    tableExported.ajax.reload();
                    tableApprove.ajax.reload();
                    tableReject.ajax.reload();
                    getStatistic();
                    $('#errUploadData').text('');
                    $('#fileDataProcess').val('');
                    $('#progressBar').attr('hidden', true);
                    $('#buttonUploadDataExport').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                    toastr.success(xhr.message)
                }
            });



            var tableInvoice = $('#tableInvoice').DataTable({
                dom: 'B<"clear">lfrtip',
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
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('admin.ksp.invoicing.table') !!}',
                    "data": function(xx) {
                        return $.extend(xx, dataTable);
                    },
                },
                columns: [{
                        data: 'period'
                    }, {
                        data: 'total_transaksi_approve'
                    },
                    {
                        data: 'total_fee_base'
                    }, {
                        data: 'income_ksp',
                    }, {
                        data: 'iuran_anggota',
                    }, {
                        data: 'download_invoice',
                    }, {
                        data: 'status_invoicing',
                    }, {
                        data: 'action',
                    }
                ]
            });
        </script>
    @endpush
