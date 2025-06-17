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
                                <th>Divisi</th>
                                <th>Departement</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Status SP</th>
                                <th>BANK</th>
                                <th>Nama Rekening</th>
                                <th>No Bank</th>
                                <th>Penghasilan</th>
                                <th>Action</th>
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


    {{-- modal edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class=" modal-dialog modal-lg modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Ubah Data Karyawan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Karyawan</label>
                                <input class="form-control" name="nama_karyawan" id="nama_karyawan" required></input>
                                <small id="errNama_karyawan"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Bergabung</label>
                                <input class="form-control" name="tanggal_bergabung" id="tanggal_bergabung" required
                                    type="date"></input>
                                <small id="errTanggal_bergabung"></small>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>NIK</label>
                                <input class="form-control" name="nik" id="nik" required></input>
                                <small id="errNik"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label>NIP</label>
                                <input class="form-control" name="nip" id="nip" required></input>
                                <small class="text-danger" id="errNip"></small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">

                                <label>Whatsapp</label>
                                <input class="form-control" name="whatsapp" id="whatsapp" required></input>
                                <small class="text-danger" id="errWhatsapp"></small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">

                                <label>Alamat Lengkap</label>
                                <textarea id="alamat_lengkap" class="form-control"></textarea>
                                <small class="text-danger" id="errAlamat_lengkap"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label>Divisi</label>
                                <input class="form-control" name="divisi" id="divisi" required></input>
                                <small class="text-danger" id="errDivisi"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label>Departement</label>
                                <input class="form-control" name="departement" id="departement" required></input>
                                <small class="text-danger" id="errDepartement"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label>Jabatan</label>
                                <input class="form-control" name="jabatan" id="jabatan" required></input>
                                <small class="text-danger" id="errJabatan"></small>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">

                                <label>Status</label>
                                <select class="form-control" id="status">
                                    <option value="tetap">Tetap</option>
                                    <option value="percobaan">Percobaan</option>
                                    <option value="phk">PHK</option>
                                    <option value="resign">Resign</option>
                                </select>
                                <small class="text-danger" id="errStatus"></small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">

                                <label>Status SP</label>
                                <select class="form-control" id="statusSp">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                                <small class="text-danger" id="errStatusSp"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label>BANK</label>
                                <select class="form-control" id="bank">
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}"> {{ $bank->code_bank }} -
                                            {{ $bank->description }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="errStatusSp"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label>Nomor Bank</label>
                                <input class="form-control" name="nomor_bank" id="nomor_bank" required
                                    type="number"></input>
                                <small class="text-danger" id="errNomorBank"></small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">

                                <label>Penghasilan</label>
                                <input class="form-control" name="penghasilan" id="penghasilan" required
                                    type="number"></input>
                                <small class="text-danger" id="errPenghasilan"></small>
                            </div>
                        </div>


                    </div>


                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="button" id="buttonSubmitEdit"
                        onclick="buttonSubmitUpdateKaryawan()">Save <div id="loadingTextEditUpdateKaryawan">
                        </div></button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
    <script>
        $(document).ready(function() {
            let editID = '';

            var dataTable = {
                "_token": "{{ csrf_token() }}",
            };
            const table = $('#tableListKaryawan').DataTable({
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
                    data: 'divisi'
                }, {
                    data: 'departement'
                }, {
                    data: 'jabatan'
                }, {
                    data: 'status'
                }, {
                    data: 'status_sp'
                }, {
                    data: 'bank'
                }, {
                    data: 'nama_rekening'
                }, {
                    data: 'nomor_bank'
                }, {
                    data: 'penghasilan'
                }, {
                    data: 'action'
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




            edit = (id) => {
                editID = id;
                $.ajax({
                    url: '{!! route('admin.list.karyawan.detail') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': editID,
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(detail) {
                        $('#nama_karyawan').val(detail.data.nama);
                        $('#tanggal_bergabung').val(detail.data.tanggal_bergabung);
                        $('#nik').val(detail.data.nik);
                        $('#nip').val(detail.data.nip);
                        $('#whatsapp').val(detail.data.whatsapp);
                        $('#alamat_lengkap').text(detail.data.alamat);
                        $('#divisi').val(detail.data.divisi);
                        $('#departement').val(detail.data.departement);
                        $('#jabatan').val(detail.data.jabatan);
                        $('#status').val(detail.data.status);
                        $('#statusSp').val(detail.data.status_sp);
                        $('#bank').val(detail.data.bank);
                        $('#nomor_bank').val(detail.data.nomor_bank);
                        $('#penghasilan').val(detail.data.penghasilan);
                        $('#modalEdit').modal('show');
                    }
                });
            }


            buttonSubmitUpdateKaryawan = () => {
                $.ajax({
                    url: '{!! route('admin.list.karyawan.updateKaryawan') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': editID,
                        'nama_karyawan': $("#nama_karyawan").val(),
                        'tanggal_bergabung': $("#tanggal_bergabung").val(),
                        'nik': $("#nik").val(),
                        'nip': $("#nip").val(),
                        'whatsapp': $("#whatsapp").val(),
                        'alamat_lengkap': $("#alamat_lengkap").val(),
                        'divisi': $("#divisi").val(),
                        'departement': $("#departement").val(),
                        'jabatan': $("#jabatan").val(),
                        'status': $("#status").val(),
                        'statusSp': $("#statusSp").val(),
                        'bank': $("#bank").val(),
                        'nomor_bank': $("#nomor_bank").val(),
                        'penghasilan': $("#penghasilan").val(),
                    },
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#buttonSubmitEdit').attr("disabled", true);
                        $('#loadingTextEditUpdateKaryawan').addClass("spinner-border");
                    },
                    success: function(add) {
                        toastr.success(add.message);
                        table.ajax.reload();

                        $("#modalEdit").modal('hide');
                        $('#buttonSubmitEdit').attr("disabled", false);
                        $('#loadingTextEditUpdateKaryawan').removeClass("spinner-border");
                    },
                    error: function(err) {
                        $('#buttonSubmitEdit').attr("disabled", false);
                        $('#loadingTextEditUpdateKaryawan').removeClass("spinner-border");

                        $('#errNama_karyawan').text(err.responseJSON.errors.nama_karyawan);
                        $('#errTanggal_bergabung').text(err.responseJSON.errors.tanggal_bergabung);
                        $('#errNik').text(err.responseJSON.errors.nik);
                        $('#errNip').text(err.responseJSON.errors.nip);
                        $('#errWhatsapp').text(err.responseJSON.errors.whatsapp);
                        $('#errAlamat_lengkap').text(err.responseJSON.errors.alamat);
                        $('#errDivisi').text(err.responseJSON.errors.divisi);
                        $('#errDepartement').text(err.responseJSON.errors.departement);
                        $('#errJabatan').text(err.responseJSON.errors.jabatan);
                        $('#errStatus').text(err.responseJSON.errors.status);
                        $('#errStatusSp').text(err.responseJSON.errors.status_sp);
                        $('#errBank').text(err.responseJSON.errors.bank);
                        $('#errNomor_bank').text(err.responseJSON.errors.nomor_bank);
                        $('#errPenghasilan').text(err.responseJSON.errors.penghasilan);
                        $('#modalEdit').modal('show');
                    }
                });
            }


        });
    </script>
@endpush
