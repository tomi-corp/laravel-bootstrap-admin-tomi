@extends('layout.app')
@section('title', 'List Karyawan')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item "><a href="{{ route('admin.ksp.index') }}">List Perusahaan</a></li>
                        <li class="breadcrumb-item active">KSP Perusahaan</li>
                    </ol>
                </div>
                <h4 class="page-title">List Karyawan</h4>
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
                    <div class="row">
                        <div class="col-md-12">

                            <form id="fileUploadDataKaryawan" method="POST"
                                action="{{ route('admin.list.karyawan.upload') }}" enctype="multipart/form-data">
                                @csrf
                                <label>Upload Data Karyawan</label>
                                <input type="file" class="form-control" accept=".xls,.xlsx" name="file"
                                    id="fileDataKaryawan"></input>
                                <small>Hanya menerima format .xlsx</small><br>
                                <small class="text-danger" id="errUploadData"></small>
                                <div hidden id="progressBar">
                                    <div class="progress mb-3">
                                        <div id="pgBar" class=" progress-bar progress-bar-striped bg-info "
                                            role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm float-right pull-right " type="submit"
                        id="buttonUploadKaryawan">Upload
                        Data <div id="loadingTextEdit">
                        </div></button>
                    </form>
                </div>
            </div>


            <div class="card">
                <div class="card-body">
                    <h5>List Karyawan Penambahan</h5>
                    <span>Total Karyawan Pending Approve: </span>
                    <span class="badge badge-pink"><span id="karyawanPending"></span> Karyawan</span>
                    <i class="mdi mdi-eye text-info float-right" type="button"
                        onclick="showKaryawanPending({{ $perusahaan->id }})"></i>
                    <br>
                    <button class="btn btn-primary btn-sm float-right mt-4"
                        onclick="aprroveKaryawanPending({{ $perusahaan->id }})">Approve</button>
                </div>
            </div>

        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="pb-2">
                        <h5>List Karyawan</h5>

                    </div>

                    <table id="tableListKaryawan" class="table dt-responsive table-hover mt-4 text-center justify-center"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Tanggal Bergabung </th>
                                <th>Nama </th>
                                <th>NIK</th>
                                <th>NIP</th>
                                <th>Whatsapp</th>
                                <th>Alamat</th>
                                <th>Departement</th>
                                <th>Jabatan</th>
                                <th>Penghasilan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- modal karyawan pending --}}
    <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel"
        aria-hidden="true" style="display: none;" id="modalKaryawanPending">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel">Data Karyawan Pending</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <div class="responsive-table-plugin">
                        <div class="table-rep-plugin">
                            <div class="table-responsive" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nama Karyawan</th>
                                            <th data-priority="1">NIK</th>
                                            <th data-priority="3">NIP</th>
                                            <th data-priority="1">Whatsapp</th>
                                            <th data-priority="3">Alamat</th>
                                            <th data-priority="6">Jabatan</th>
                                            <th data-priority="6">Penghasilan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyTablePendingKaryawan">
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- table-rep-plugin-->
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">tutup</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var dataTable = {
                "_token": "{{ csrf_token() }}",
            };
            var table = $('#tableListKaryawan').DataTable({
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
                    "url": '{!! route('admin.list.karyawan.table', $perusahaan->id) !!}',
                    "data": function(xx) {
                        return $.extend(xx, dataTable);
                    },
                },

                columns: [{
                    data: 'tanggal_bergabung'
                }, {
                    data: 'nama_karyawan'
                }, {
                    data: 'nik'
                }, {
                    data: 'nip'
                }, {
                    data: 'whatsapp'
                }, {
                    data: 'alamat'
                }, {
                    data: 'departement'
                }, {
                    data: 'jabatan'
                }, {
                    data: 'penghasilan'
                }]
            });


            $('#fileUploadDataKaryawan').ajaxForm({
                beforeSend: function() {
                    $('#errUploadData').text('');
                    $('#buttonUploadKaryawan').attr("disabled", true);
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
                    $('#fileDataKaryawan').val('');
                    $('#progressBar').attr('hidden', true);
                    $('#buttonUploadKaryawan').attr(
                        "disabled", false);
                    $('#loadingTextEdit').removeClass(
                        "spinner-border");
                    $('#errUploadData').text(xhr.responseJSON.message);
                },
                success: function(xhr) {
                    karyawanPending();
                    $('#errUploadData').text('');
                    $('#fileDataKaryawan').val('');
                    $('#progressBar').attr('hidden', true);
                    $('#buttonUploadKaryawan').attr(
                        "disabled", false);
                    $('#loadingTextEdit').removeClass(
                        "spinner-border");
                    toastr.success(xhr.message)
                }
            });

            karyawanPending = () => {
                $.ajax({
                    url: '{!! route('admin.list.karyawan.pending.count') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': {{ $perusahaan->id }},
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(resultKaryawanPending) {
                        console.log("dsad")
                        $('#karyawanPending').text(resultKaryawanPending.pending)

                    }
                });
            };
            karyawanPending();

            showKaryawanPending = (id) => {
                $.ajax({
                    url: '{!! route('admin.list.karyawan.pending') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': id,
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(dataPendingKaryawan) {
                        let tableBody = $('#bodyTablePendingKaryawan');
                        tableBody.empty();

                        $.each(dataPendingKaryawan.data, function(index, item) {
                            let row = $('<tr>');
                            row.append($('<td>').text(item
                                .nama));
                            row.append($('<td>').text(item
                                .nik));
                            row.append($('<td>').text(item
                                .nip));
                            row.append($('<td>').text(item
                                .whatsapp));
                            row.append($('<td>').text(item
                                .alamat));
                            row.append($('<td>').text(item
                                .jabatan));
                            row.append($('<td>').text(item
                                .penghasilan));
                            row.appendTo(tableBody);
                        });
                        $('#modalKaryawanPending').modal('show')

                    }
                });

            }
            aprroveKaryawanPending = (id) => {
                $.ajax({
                    url: '{!! route('admin.list.karyawan.approve') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': id,
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(approve) {
                        karyawanPending();
                        toastr.success(approve.message);
                        table.ajax.reload();
                    }
                });
            }
        });
    </script>
@endpush
