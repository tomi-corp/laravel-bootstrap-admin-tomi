@extends('layout.app')
@section('title', 'KSP')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">KSP Perusahaan</li>
                    </ol>
                </div>
                <h4 class="page-title">KSP Perusahaan</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a href="#home" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                Pending Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#profile" data-toggle="tab" aria-expanded="true" class="nav-link">
                                Reject Pengajuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#messages" data-toggle="tab" aria-expanded="false" class="nav-link">
                                Approve Pengajuan
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="home">
                            <table id="tableAllAbsensi"
                                class="table dt-responsive table-hover mt-4 text-center justify-center" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>DateTime</th>
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>Plafon</th>
                                        <th>Tenor / Admin</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane show" id="profile">
                            <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus
                                ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer
                                tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus.
                                Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                            <p class="mb-0">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo
                                ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient
                                montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium
                                quis, sem. Nulla consequat massa quis enim.</p>
                        </div>
                        <div class="tab-pane" id="messages">
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.
                                Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur
                                ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla
                                consequat massa quis enim.</p>
                            <p class="mb-0">Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim
                                justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis
                                pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate
                                eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>



        {{-- modal detail --}}
        <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel"
            aria-hidden="true" style="display: none;" id="modalShow">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myCenterModalLabel">Detail Pengajuan Pinjaman</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">

                                <small>Informasi Peminjam</small>
                                <div class="card shadow">
                                    <div class="card-body">
                                        <p id="namePeminjam">Amroji</p>
                                        <div class="row">
                                            <div class="col-6">
                                                <span>NIK</span><br>
                                                <span id="nik"></span>

                                            </div>
                                            <div class="col-6">
                                                <span>Tanggal Bergabung</span><br>
                                                <span id="tanggalGabung"></span>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <small>Alasan Pengajuan</small>
                                <div class="card shadow">
                                    <div class="card-body">
                                        <p id="alasanPengajuan"></p>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <small>Detail Pinjaman</small>
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 ">
                                                <span>ID Pengajuan</span><br>
                                                <span id="idPengajuan"></span>
                                            </div>
                                            <div class="col-6">
                                                <span>Tanggal Pengajuan</span><br>
                                                <span id="tanggalPengajuan"></span>
                                            </div>
                                            <div class="col-6 pt-2">
                                                <span>Status</span><br>
                                                <span id="statusPengajuan"> </span>
                                            </div>
                                        </div>
                                        <hr style="border: 1px solid #6c757d;">
                                        <div class="row">
                                            <div class="col-6">
                                                <span>Plafon Pinjaman</span>
                                            </div>
                                            <div class="col-6">
                                                <span id="plafonPinjaman"> </span>
                                            </div>

                                            <div class="col-6">
                                                <span>Tenor</span>
                                            </div>
                                            <div class="col-6">
                                                <span id="tenor"> </span>
                                            </div>

                                            <div class="col-6">
                                                <span> Biaya Admin</span>
                                            </div>
                                            <div class="col-6">
                                                <span id="biayaAdmin"> </span>
                                            </div>

                                            <div class="col-6">
                                                <span> Angsuran Per Bulan</span>
                                            </div>
                                            <div class="col-6">
                                                <span id="angsuranPerbulan">Rp 25.000.000 </span>
                                            </div>

                                            <div class="col-6">
                                                <span> Dana yang diterima</span>
                                            </div>
                                            <div class="col-6">
                                                <span id="jumlahDiterima">Rp 25.000.000 </span>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">cancel</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal verifikasi --}}
        <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog"
            aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;" id="modalApprove">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myCenterModalLabel">Verifikasi Pengajuan</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Ubah Status Pengajuan</label>
                            <select class="form-control" id="statusVerifikasi">
                                <option value="approve">Approve</option>
                                <option value="reject">Reject</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pesan</label>
                            <textarea class="form-control" style="min-height:30px;" id="pesan"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" id="saveVerif">Save <div id="loadingText">
                            </div></button>
                    </div>
                </div>
            </div>
        </div>


    </div>


    </div>



@endsection

@push('js')
    <script>
        var mulai = "";
        var akhir = "";
        var edit = "";

        var dataTable = {
            "_token": "{{ csrf_token() }}",
            "mulai": mulai,
            "akhir": akhir
        };
        var table = $('#tableAllAbsensi').DataTable({
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
                "url": '{!! route('admin.ksp.pending.table') !!}',
                "data": function(xx) {
                    return $.extend(xx, dataTable);
                },
            },
            columns: [{
                data: 'datetime',
            }, {
                data: 'nama'
            }, {
                data: 'nik'
            }, {
                data: 'plafon'
            }, {
                data: 'tenor'
            }, {
                data: 'status'
            }, {
                data: 'action'
            }]
        });

        detail = (id) => {
            $.ajax({
                url: '{!! route('admin.ksp.detail') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': id,
                },
                type: 'POST',
                dataType: 'json',
                success: function(detail) {
                    $("#modalShow").modal('show');
                    $("#namePeminjam").text(detail.pengajuan.nama);
                    $("#nik").text(detail.pengajuan.nik);
                    $("#tanggalGabung").text(detail.pengajuan.tanggal_terdaftar);
                    $("#alasanPengajuan").text(detail.pengajuan.tujuan);
                    $("#idPengajuan").text(detail.pengajuan.id_pinjaman);
                    $("#tanggalPengajuan").text(detail.pengajuan.tanggal_pengajuan);
                    $("#statusPengajuan").text(detail.pengajuan.status);
                    $("#plafonPinjaman").text(detail.pengajuan.plafon);
                    $("#tenor").text(detail.pengajuan.tenor);
                    $("#biayaAdmin").text(detail.pengajuan.percent);
                    $("#angsuranPerbulan").text(detail.pengajuan.angsuran_perbulan);
                    $("#jumlahDiterima").text(detail.pengajuan.jumlah_diterima);

                }
            });
        }

        approve = (id) => {
            edit = id;
            $("#modalApprove").modal('show');
        }

        $('#saveVerif').on('click', function() {
            $.ajax({
                url: '{!! route('admin.ksp.verifikasi') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': edit,
                    'status': $("#statusVerifikasi").val(),
                    'note': $("#pesan").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#saveVerif').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                },
                success: function(approve) {
                    toastr.success(approve.message)
                    table.ajax.reload();
                    $("#modalApprove").modal('hide');
                    $('#saveVerif').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                }
            });
        })
    </script>
@endpush
