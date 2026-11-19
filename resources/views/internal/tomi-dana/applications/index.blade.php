@extends('layout.app')
@section('title', 'Koperasi Simpan Pinjam')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">DDT</a></li>
                        <li class="breadcrumb-item "><a href="{{ route('internal.tomi-dana.companies') }}">KSP
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
                        <span>Payment Date :</span>
                        <span>{{ $perusahaan->payment_date }}</span>
                    </div>


                    <div class="d-flex justify-content-between">
                        <span>Last Updated :</span>
                        <span>{{ $perusahaan->updated_at }}</span>
                    </div>

                </div>
            </div>




            <style>
                .pengajuan-stat-card .stat-grid {
                    display: grid;
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                    gap: 0;
                    text-align: center;
                }

                .pengajuan-stat-card .stat-item {
                    padding: 12px 6px;
                }

                .pengajuan-stat-card .stat-number {
                    display: block;
                    font-size: 32px;
                    line-height: 1.2;
                    font-weight: 300;
                    color: #344054;
                    margin-bottom: 8px;
                }

                .pengajuan-stat-card .stat-label {
                    display: block;
                    font-size: 15px;
                    line-height: 1.35;
                    color: #98a6ad;
                    white-space: normal;
                    word-break: normal;
                    overflow-wrap: normal;
                }

                .pengajuan-stat-card .stat-done {
                    grid-column: span 2;
                    text-align: left;
                    padding-left: 18px;
                    margin-top: 10px;
                }

                .pengajuan-stat-card .chart-wrap {
                    min-height: 220px;
                    margin-top: 20px;
                }

                @media (max-width: 576px) {
                    .pengajuan-stat-card .stat-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                    }


                    .pengajuan-stat-card .stat-done {
                        grid-column: span 2;
                    }
                }
            </style>

            <div class="card pengajuan-stat-card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Pengajuan Statistic</h4>

                    <div class="stat-grid">
                        <div class="stat-item">
                            <span class="stat-number" id="belumValidasiWaCount">0</span>
                            <span class="stat-label">Belum Validasi WA</span>
                        </div>

                        <div class="stat-item">
                            <span class="stat-number" id="onprosesCount">0</span>
                            <span class="stat-label">On Proses</span>
                        </div>

                        <div class="stat-item">
                            <span class="stat-number" id="pendingCount">0</span>
                            <span class="stat-label">Pending</span>
                        </div>

                        <div class="stat-item">
                            <span class="stat-number" id="approveCount">0</span>
                            <span class="stat-label">Approve</span>
                        </div>

                        <div class="stat-item stat-done">
                            <span class="stat-number" id="doneCount">0</span>
                            <span class="stat-label">Done</span>
                        </div>
                    </div>

                    <div class="chart-wrap">
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

                            <div class="form-group float-right">
                                <div class="d-flex">
                                    Tanggal:
                                    <input type="text" name="daterangeProses" id="daterangeProses"
                                        class="form-control form-control-sm" value="" readonly />
                                </div>
                            </div>


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

                                        <th>Pendapatan Bunga</th>
                                        <th>DPP Bunga</th>
                                        <th>PPN Bunga</th>

                                        <th>Iuran Anggota</th>
                                        <th>Total Biaya Admin</th>
                                        <th>Jumlah Yang Diterima</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                        <div class="tab-pane show" id="exported">
                            <div class="form-group float-right">
                                <div class="d-flex">
                                    Tanggal:
                                    <input type="text" name="daterangePending" id="daterangePending"
                                        class="form-control form-control-sm" value="" readonly />
                                </div>
                            </div>
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
                                        <th class="none">Biaya ADM KSP DTT</th>
                                        <th class="none">DPP KSP</th>
                                        <th class="none">PPN KSP</th>


                                        <th class="none">Pendapatan Bunga</th>
                                        <th class="none">DPP Bunga</th>
                                        <th class="none">PPN Bunga</th>

                                        <th class="none">Iuran Anggota</th>
                                        <th class="none">Total Biaya Admin</th>
                                        <th>Jumlah Yang Diterima</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                        <div class="tab-pane" id="approve">
                            <div class="form-group float-right">
                                <div class="d-flex">
                                    Tanggal:
                                    <input type="text" name="daterangeApprove" id="daterangeApprove"
                                        class="form-control form-control-sm" value="" readonly />
                                </div>
                            </div>

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
                                        <th class="none">Biaya ADM KSP DTT</th>
                                        <th class="none">DPP KSP</th>
                                        <th class="none">PPN KSP</th>


                                        <th class="none">Pendapatan Bunga</th>
                                        <th class="none">DPP Bunga</th>
                                        <th class="none">PPN Bunga</th>

                                        <th class="none">Iuran Anggota</th>
                                        <th class="none">Total Biaya Admin</th>
                                        <th>Jumlah Yang Diterima</th>
                                        <th class="none">Biaya</th>
                                        <th>Detail Angsuran</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <div class="tab-pane" id="done">


                            <div class="form-group float-right">
                                <div class="d-flex">
                                    Tanggal:
                                    <input type="text" name="daterangeDone" id="daterangeDone"
                                        class="form-control form-control-sm" value="" readonly />
                                </div>
                            </div>

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
                                        <th class="none">Biaya ADM KSP DTT</th>
                                        <th class="none">DPP KSP</th>
                                        <th class="none">PPN KSP</th>

                                        <th class="none">Pendapatan Bunga</th>
                                        <th class="none">DPP Bunga</th>
                                        <th class="none">PPN Bunga</th>

                                        <th class="none">Iuran Anggota</th>
                                        <th class="none"> Total Biaya Admin</th>
                                        <th>Jumlah Yang Diterima</th>
                                        <th class="none">Biaya</th>
                                        <th>Detail Angsura</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


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
                            </tr>
                        </thead>
                    </table>
                    <small>Note:<br> Tagihan kepada pihak perusahaan dapat didownload H+1 Payment Date</small>
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


    @endsection

    @push('js')
        <script script src="{{ asset('assets/libs/chart-js/Chart.bundle.min.js') }}"></script>

        <script>
            let invoicePeriod = {};
            let rangeOnproses = '';
            let rangePending = '';
            let rangeApprove = '';
            let rangeDone = '';

            // date range picker => proses
            $('#daterangeProses').on('cancel.daterangepicker', function(ev, picker) {
                $('#daterangeProses').val('');
                rangeOnproses = '';
                tableOnproses.draw();
            });

            $('input[name="daterangeProses"]').daterangepicker({
                "minDate": "01/01/2025",
                opens: 'left',
                autoUpdateInput: false
            }, function(start, end, label) {
                rangeOnproses = start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY');
                $('#daterangeProses').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
                tableOnproses.draw();
            });


            // date range picker => pending
            $('#daterangePending').on('cancel.daterangepicker', function(ev, picker) {
                $('#daterangePending').val('');
                rangePending = '';
                tablePending.draw();
            });

            $('input[name="daterangePending"]').daterangepicker({
                "minDate": "01/01/2025",
                opens: 'left',
                autoUpdateInput: false
            }, function(start, end, label) {
                rangePending = start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY');
                $('#daterangePending').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
                tablePending.draw();
            });

            // date range picker => approve
            $('#daterangeApprove').on('cancel.daterangepicker', function(ev, picker) {
                rangeApprove = '';

                $('#daterangeApprove').val('');
                tableApprove.draw();
            });

            $('input[name="daterangeApprove"]').daterangepicker({
                "minDate": "01/01/2025",
                opens: 'left',
                autoUpdateInput: false
            }, function(start, end, label) {
                rangeApprove = start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY');
                $('#daterangeApprove').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
                tableApprove.draw();
            });

            // date range picker => Done
            $('#daterangeDone').on('cancel.daterangepicker', function(ev, picker) {
                rangeDone = '';
                $('#daterangeDone').val('');
                tableDone.draw();
            });

            $('input[name="daterangeDone"]').daterangepicker({
                "minDate": "01/01/2025",
                opens: 'left',
                autoUpdateInput: false
            }, function(start, end, label) {
                rangeDone = start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY');
                $('#daterangeDone').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
                tableDone.draw();
            });

            // end Date range picker

            let pengajuanDoughnutChart = null;

            getStatistic = () => {
                $.ajax({
                    url: '{!! route('internal.tomi-dana.applications.statistic') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': {{ $perusahaan->id }},
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(dataStatistic) {
                        const belumValidasiWa = parseInt(dataStatistic.belum_validasi_wa ?? 0) || 0;
                        const onProses = parseInt(dataStatistic.onproses ?? 0) || 0;
                        const pending = parseInt(dataStatistic.pending ?? 0) || 0;
                        const approve = parseInt(dataStatistic.approve ?? 0) || 0;
                        const done = parseInt(dataStatistic.done ?? 0) || 0;

                        $('#belumValidasiWaCount').text(belumValidasiWa);
                        $('#onprosesCount').text(onProses);
                        $('#pendingCount').text(pending);
                        $('#approveCount').text(approve);
                        $('#doneCount').text(done);

                        const chartData = {
                            labels: [
                                'Belum Validasi WA',
                                'On Proses',
                                'Pending',
                                'Approve',
                                'Done'
                            ],
                            datasets: [{
                                data: [belumValidasiWa, onProses, pending, approve, done],
                                backgroundColor: [
                                    "#fbcc5c",
                                    "#6c757d",
                                    "#02a8b5",
                                    "#6b5eae",
                                    "#31ce77"
                                ],
                                borderColor: "#ffffff",
                                borderWidth: 3,
                            }]
                        };

                        if (pengajuanDoughnutChart) {
                            pengajuanDoughnutChart.destroy();
                        }

                        const doughnutCanvas = document.getElementById('doughnut');
                        pengajuanDoughnutChart = new Chart(doughnutCanvas, {
                            type: 'doughnut',
                            data: chartData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutoutPercentage: 80,
                                legend: {
                                    display: true,
                                    position: 'bottom'
                                }
                            }
                        });
                    }
                });
            }

            // Tombol edit di tabel tagihan mengirim periode (tahun, bulan) yang akan ditandai lunas.
            editInvoicing = (tahun, bulan) => {
                invoicePeriod = { tahun, bulan };
                $("#modalEdit").modal('show');
            }

            buttonSubmitEdit = () => {
                $.ajax({
                    url: '{!! route('internal.tomi-dana.applications.invoices.update') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': {{ $perusahaan->id }},
                        'tahun': invoicePeriod.tahun,
                        'bulan': invoicePeriod.bulan,
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

            function getTableData(rangeValue) {
                return {
                    "_token": "{{ csrf_token() }}",
                    "id": {{ $perusahaan->id }},
                    "range": rangeValue
                };
            }

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
                        title: 'List On Proses',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]

                        },
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        header: true,
                        title: 'List On Proses',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                        },
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        header: true,
                        title: 'List On Proses',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
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
                    "url": '{!! route('internal.tomi-dana.applications.table', 'on_proses') !!}',
                    "data": function(xx) {
                        return $.extend(xx, getTableData(rangeOnproses));
                    },
                },
                columns: [{
                    data: 'id_pinjaman'
                }, {
                    data: 'nama',
                    nama: 'data_karyawan.nama_karyawan',
                }, {
                    data: 'angsuran_perbulan'
                }, {
                    data: 'nominal'
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
                    data: 'pendapatan_bunga'
                }, {
                    data: 'dpp_bunga'
                }, {
                    data: 'ppn_bunga'
                }, {
                    data: 'iuran_anggota'
                }, {
                    data: 'total_biaya_admin'
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
                    "url": '{!! route('internal.tomi-dana.applications.table', 'pending') !!}',
                    "data": function(xx) {
                        return $.extend(xx, getTableData(rangePending));
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
                    data: 'pendapatan_bunga'
                }, {
                    data: 'dpp_bunga'
                }, {
                    data: 'ppn_bunga'
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
                    "url": '{!! route('internal.tomi-dana.applications.table', 'approve') !!}',
                    "data": function(xx) {
                        return $.extend(xx, getTableData(rangeApprove));
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
                    data: 'pendapatan_bunga'
                }, {
                    data: 'dpp_bunga'
                }, {
                    data: 'ppn_bunga'
                }, {
                    data: 'iuran_anggota'
                }, {
                    data: 'total_biaya_admin'
                }, {
                    data: 'jumlah_yang_diterima'
                }, {
                    data: 'biaya'
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
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                        },
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        header: true,
                        title: 'List Done',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                        },
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        header: true,
                        title: 'List Done',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
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
                    "url": '{!! route('internal.tomi-dana.applications.table', 'done') !!}',
                    "data": function(xx) {
                        return $.extend(xx, getTableData(rangeDone));
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
                    data: 'pendapatan_bunga'
                }, {
                    data: 'dpp_bunga'
                }, {
                    data: 'ppn_bunga'
                }, {
                    data: 'iuran_anggota'
                }, {
                    data: 'total_biaya_admin'
                }, {
                    data: 'jumlah_yang_diterima'
                }, {
                    data: 'biaya'
                }, {
                    data: 'detail_angsuran'
                }]
            });




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
                    tableOnproses.ajax.reload();
                    tablePending.ajax.reload();
                    tableApprove.ajax.reload();
                    tableDone.ajax.reload();
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
                    "url": '{!! route('internal.tomi-dana.applications.invoices.table') !!}',
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
                    url: '{!! route('internal.tomi-dana.applications.installments') !!}',
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
        </script>
    @endpush
