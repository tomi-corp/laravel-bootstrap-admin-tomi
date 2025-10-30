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
        <div class="col-12">
            <p>
                <button class="btn btn-warning" type="button" id="showDetailPerusahaan" onclick="showDetailPerusahaan()">
                    <span id="textDetailPerusahaan">Hide</span>
                    <i id="iconDetailPerusahaan" class ="mdi mdi-arrow-left-bold-outline"></i>
                </button>
            </p>
        </div>
        <div class="col-md-4" id="hideDetailPerusahaan">

            <div class="card">
                <div class="card-body">
                    <h5>Detail Perusahaan KSP</h5>
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
                        <span>Provinsi :</span>
                        <span>{{ $perusahaan->provinsi->provinsi }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Kabupaten/Kota :</span>
                        <span>{{ $perusahaan->kabupaten->kabupaten }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Kecamatan :</span>
                        <span>{{ $perusahaan->kabupaten->kabupaten }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Desa :</span>
                        <span>{{ $perusahaan->desa->desa . ' - ' . $perusahaan->desa->kodepos }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Alamat Lengkap :</span>
                        <span>{{ $perusahaan->alamat_lengkap }}</span>
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
                            <p class="text-muted text-overflow">On Proses</p>
                        </div>
                        <div class="col-3 mb-3">
                            <h3 class="font-weight-light" id="exportedCount"></h3>
                            <p class="text-muted text-overflow">Pending</p>
                        </div>
                        <div class="col-3 mb-3">
                            <h3 class="font-weight-light" id="approveCount"></h3>
                            <p class="text-muted text-overflow">Approve</p>
                        </div>

                        <div class="col-3 mb-3">
                            <h3 class="font-weight-light" id="doneCount"></h3>
                            <p class="text-muted text-overflow">Done</p>
                        </div>
                    </div>
                    <div class="chartjs-chart-example chartjs-chart">
                        <canvas id="doughnut"></canvas>
                    </div>
                </div>
            </div>


        </div>


        <div class="col-md-8" id="hideDetailPerusahaanDua">
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5>Keterangan :</h5>
                <ul>
                    <li>Tab <b>on proses</b> ketika peminjam melakukan peminjaman dan belum melakukan aksi OTP
                    </li>
                    <li>Tab <b>pending</b> ketika end user melakukan peminjaman dan sudah sampai tahap tanda tangan privy,
                        menunggu untuk dilakukan disbursement oleh admin</li>
                    <li>Tab <b>approve</b> ketika peminjam berhasil mendapatkan dana dan memiliki cicilan yang berjalan</li>
                    <li>Tab <b>done</b> ketika peminjam sudah melunasi semua cicilan yang berjalan</li>
                </ul>
            </div>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#pending" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                On Proses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#exported" data-toggle="tab" aria-expanded="true" class="nav-link">
                                Pending
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#approve" data-toggle="tab" aria-expanded="false" class="nav-link">
                                Approve
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#done" data-toggle="tab" aria-expanded="false" class="nav-link">
                                Done
                            </a>
                        </li>
                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane active" id="pending">



                            <table id="tableOnproses" class="table dt-responsive table-hover text-center justify-center"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID Pinjaman</th>
                                        <th>Nama</th>
                                        <th>Nominal </th>
                                        <th>Angsuran Perbulan</th>
                                        <th>Tenor</th>

                                        <th>Rate</th>
                                        <th>Biaya ADM KSP DDT</th>
                                        <th>DPP KSP</th>
                                        <th>PPN KSP</th>
                                        <th>Biaya ADM PANDU</th>

                                        <th>DPP PANDU</th>
                                        <th>PPN PANDU</th>
                                        {{-- <th>Biaya KYC</th>
                                        <th>Pendapatan Pandu</th> --}}
                                        <th>Jumlah Yang Diterima</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                        <div class="tab-pane show" id="exported">

                            <table id="tablePending" class="table dt-responsive table-hover text-center justify-center"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID Pinjaman</th>
                                        <th>Nama</th>
                                        <th>Nominal </th>
                                        <th>Angsuran Perbulan</th>
                                        <th>Tenor</th>

                                        <th>Rate</th>
                                        <th>Biaya ADM KSP DDT</th>
                                        <th>DPP KSP</th>
                                        <th>PPN KSP</th>
                                        <th>Biaya ADM PANDU</th>

                                        <th>DPP PANDU</th>
                                        <th>PPN PANDU</th>
                                        {{-- <th>Biaya KYC</th>
                                        <th>Pendapatan Pandu</th> --}}
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
                                        <th>Angsuran Perbulan</th>
                                        <th>Tenor</th>

                                        <th>Rate</th>
                                        <th>Biaya ADM KSP DDT</th>
                                        <th>DPP KSP</th>
                                        <th>PPN KSP</th>
                                        <th>Biaya ADM PANDU</th>

                                        <th>DPP PANDU</th>
                                        <th>PPN PANDU</th>
                                        <th>Biaya E-KYC</th>
                                        <th>Pendapatan Pandu</th>
                                        <th>Jumlah Yang Diterima</th>

                                        <th>Detail Angsuran</th>

                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <div class="tab-pane" id="done">
                            <table id="tableDone" class="table dt-responsive table-hover text-center justify-center"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID Pinjaman</th>
                                        <th>Nama</th>
                                        <th>Nominal </th>
                                        <th>Angsuran Perbulan</th>
                                        <th>Tenor</th>

                                        <th>Rate</th>
                                        <th>Biaya ADM KSP DDT</th>
                                        <th>DPP KSP</th>
                                        <th>PPN KSP</th>
                                        <th>Biaya ADM PANDU</th>

                                        <th>DPP PANDU</th>
                                        <th>PPN PANDU</th>
                                        <th>Biaya E-KYC</th>
                                        <th>Pendapatan Pandu</th>
                                        <th>Jumlah Yang Diterima</th>

                                        <th>Detail Angsuran</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="card">
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Tagihan</h4>
                            <table id="tableInvoice" class="table dt-responsive table-hover text-center justify-center"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Bulan / Tahun Transaksi</th>
                                        <th>Total Tagihan</th>
                                        <th>Download Invoice</th>
                                        <th>Status Invoice</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                            <small>Note:<br> Tagihan kepada KSP dapat didownload tiap tanggal 1</small>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

        {{-- modal detail Angsuran --}}
        <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog"
            aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;" id="modalDetailAngsuran">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myCenterModalLabel">Detail Angsuran </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <table class="table text-center align-middle">
                            <thead>
                                <tr>
                                    <th>Angsuran ke</th>
                                    <th>Jatuh Tempo</th>
                                    <th>status</th>
                                </tr>
                            </thead>
                            <tbody id="checkAngsuranTable">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal update invoicing --}}
        <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog"
            aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;" id="modalUpdateInvoicing">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myCenterModalLabel">Konfirmasi Pembayaran</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body text-center align-middle">
                        <span>Apakah anda yakin untuk melunasi invoicing ini terhadap Lender ?</span>
                    </div>
                    </hr>
                    <div class="container">
                        <div class="checkbox mb-2 checkbox-info">
                            <input id="checkbox04" type="checkbox" onchange="changeButton()">
                            <label for="checkbox04">
                                Saya menyatakan bahwa invoicing ini telah dibayarkan
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">cancel</button>
                        <button class="btn btn-primary" type="submit" id="buttonSubmitInvoicing" disabled="true"
                            onClick="saveInvoicing()">save <div id="loadingTextInvoicing">
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
            let tahunInvoicing = '';
            let bulanInvoicing = '';
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
                        $('#pendingCount').text(dataStatistic.on_proses);
                        $('#belumValidasiWaCount').text(dataStatistic.belum_validasi_wa);
                        $('#exportedCount').text(dataStatistic.pending);
                        $('#approveCount').text(dataStatistic.approve);
                        $('#doneCount').text(dataStatistic.done);
                        data = {
                            datasets: [{
                                    data: [dataStatistic.belum_validasi_wa,
                                        dataStatistic.on_proses,
                                        dataStatistic.pending,
                                        dataStatistic.approve,
                                        dataStatistic.done
                                    ],
                                    backgroundColor: ["#fbcc5c",
                                        "#6c757d",
                                        "#02a8b5",
                                        "#6b5eae",
                                        "#31ce77",
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
                                'On Proses',
                                'Pending',
                                'Approve',
                                'Done'
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


            getStatistic();



            var dataTable = {
                "_token": "{{ csrf_token() }}",
                'id': {{ $perusahaan->id }}
            };
            var tableOnproses = $('#tableOnproses').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        header: true,
                        title: 'List On Process',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
                        },
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        header: true,
                        title: 'List On Process',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
                        },
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        header: true,
                        title: 'List On Process',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
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
                    "url": '{!! route('admin.ksp.pengajuan.onproses.table') !!}',
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
                    data: 'angsuran_perbulan'
                }, {
                    data: 'tenor'
                }, {
                    data: 'rate'
                }, {
                    data: 'biaya_admin_ksp'
                }, {
                    data: 'dpp_adm_ksp'
                }, {
                    data: 'ppn_adm_ksp'
                }, {
                    data: 'biaya_admin_pandu'
                }, {
                    data: 'dpp_adm_pandu'
                }, {
                    data: 'ppn_adm_pandu'
                }, {
                    data: 'jumlah_yang_diterima'
                }]
            });

            var tablePending = $('#tablePending').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        header: true,
                        title: 'List Pending',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
                        },
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        header: true,
                        title: 'List Pending',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
                        },
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        header: true,
                        title: 'List Pending',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
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
                    data: 'angsuran_perbulan'
                }, {
                    data: 'tenor'
                }, {
                    data: 'rate'
                }, {
                    data: 'biaya_admin_ksp'
                }, {
                    data: 'dpp_adm_ksp'
                }, {
                    data: 'ppn_adm_ksp'
                }, {
                    data: 'biaya_admin_pandu'
                }, {
                    data: 'dpp_adm_pandu'
                }, {
                    data: 'ppn_adm_pandu'
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
                        header: true,
                        title: 'List Approve',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                        },
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        header: true,
                        title: 'List Approve',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                        },
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        header: true,
                        title: 'List Approve',
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
                    data: 'angsuran_perbulan'
                }, {
                    data: 'tenor'
                }, {
                    data: 'rate'
                }, {
                    data: 'biaya_admin_ksp'
                }, {
                    data: 'dpp_adm_ksp'
                }, {
                    data: 'ppn_adm_ksp'
                }, {
                    data: 'biaya_admin_pandu'
                }, {
                    data: 'dpp_adm_pandu'
                }, {
                    data: 'ppn_adm_pandu'
                }, {
                    data: 'cost_tomi'
                }, {
                    data: 'pendapatan_pandu'
                }, {
                    data: 'jumlah_yang_diterima'
                }, {
                    data: 'detail_angsuran'
                }]
            });



            var tableDone = $('#tableDone').DataTable({

                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        header: true,
                        title: 'List Done',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                        },
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        header: true,
                        title: 'List Done',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
                        },
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        header: true,
                        title: 'List Done',
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
                    "url": '{!! route('admin.ksp.pengajuan.done.table') !!}',
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
                    data: 'angsuran_perbulan'
                }, {
                    data: 'tenor'
                }, {
                    data: 'rate'
                }, {
                    data: 'biaya_admin_ksp'
                }, {
                    data: 'dpp_adm_ksp'
                }, {
                    data: 'ppn_adm_ksp'
                }, {
                    data: 'biaya_admin_pandu'
                }, {
                    data: 'dpp_adm_pandu'
                }, {
                    data: 'ppn_adm_pandu'
                }, {
                    data: 'cost_tomi'
                }, {
                    data: 'pendapatan_pandu'
                }, {
                    data: 'jumlah_yang_diterima'
                }, {
                    data: 'detail_angsuran'
                }]
            });




            let defaultetail = true;

            showDetailPerusahaan = () => {
                if (defaultetail) {
                    $('#showDetailPerusahaan').removeClass('btn-warning');
                    $('#showDetailPerusahaan').addClass('btn-primary');
                    $('#textDetailPerusahaan').text('Show');
                    $('#iconDetailPerusahaan').addClass('mdi-arrow-right-bold-outline');
                    $('#iconDetailPerusahaan').removeClass('mdi-arrow-left-bold-outline');
                    $('#hideDetailPerusahaan').removeClass('col-md-4');
                    $('#hideDetailPerusahaan').hide();


                    $('#hideDetailPerusahaanDua').removeClass('col-md-8');
                    $('#hideDetailPerusahaanDua').addClass('col-md-12');
                    defaultetail = false;
                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
                } else {
                    $('#showDetailPerusahaan').addClass('btn-warning');
                    $('#showDetailPerusahaan').removeClass('btn-primary');
                    $('#textDetailPerusahaan').text('Hide');
                    $('#iconDetailPerusahaan').addClass('mdi-arrow-left-bold-outline');
                    $('#iconDetailPerusahaan').removeClass('mdi-arrow-right-bold-outline');
                    $('#hideDetailPerusahaan').addClass('col-md-4');
                    $('#hideDetailPerusahaan').show();
                    $('#hideDetailPerusahaanDua').removeClass('col-md-12');
                    $('#hideDetailPerusahaanDua').addClass('col-md-8');
                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
                    defaultetail = true;
                }
            }

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
            });

            detailAngsuran = (id) => {
                $.ajax({
                    url: '{!! route('admin.ksp.check.angsuran') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': id,
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(checkAngsuran) {
                        $("#checkAngsuranTable").empty();
                        $.each(checkAngsuran.message, function(i, item) {
                            $('<tr>').html("<td>" + item.angsuran_ke + "</td><td>" + item.jatuh_tempo +
                                "</td><td>" + item.status + "</td>").appendTo(
                                '#checkAngsuranTable');
                        });
                        $('#modalDetailAngsuran').modal('show');
                    }
                })
            }

            editInvoicing = (tahun, bulan) => {
                tahunInvoicing = tahun;
                bulanInvoicing = bulan;
                $('#modalUpdateInvoicing').modal('show');
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
                    url: '{!! route('admin.ksp.invoicing.update') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'tahun': tahunInvoicing,
                        'bulan': bulanInvoicing,
                        'id_list_perusahaan': {{ $perusahaan->id }}
                    },
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#buttonSubmitInvoicing').attr("disabled", true);
                        $('#loadingTextInvoicing').addClass("spinner-border");
                    },
                    success: function(submitRes) {
                        toastr.success(submitRes.message)
                        table.ajax.reload();
                        $("#modalUpdateInvoicing").modal('hide');
                        $('#buttonSubmitInvoicing').attr("disabled", false);
                        $('#loadingTextInvoicing').removeClass("spinner-border");
                    },
                    error: function(submitRes) {
                        $('#buttonSubmitInvoicing').attr("disabled", false);
                        $('#loadingTextInvoicing').removeClass("spinner-border");
                    }
                });
            };

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
                    "url": '{!! route('admin.ksp.invoicing.lender.table') !!}',
                    "data": function(xx) {
                        return $.extend(xx, dataTable);
                    },
                },
                columns: [{
                    data: 'period'
                }, {
                    data: 'total_tagihan',
                }, {
                    data: 'download_invoice',
                }, {
                    data: 'status_invoicing',
                }, {
                    data: 'action',
                }]
            });
        </script>
    @endpush
