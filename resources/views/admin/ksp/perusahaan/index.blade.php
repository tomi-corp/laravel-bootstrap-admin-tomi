@extends('layout.app')
@section('title', 'Koperasi Simpan Pinjam')
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

                    <button type="button" class="btn btn-sm btn-primary float-right waves-effect waves-light"
                        data-toggle="modal" data-target="#modalAdd">
                        <i class="mdi mdi-plus-circle"></i> Tambah Perusahaan
                    </button>



                    <table id="tableListPerusahaan" class="table dt-responsive table-hover mt-4 text-center justify-center"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>ID Perusahaan</th>
                                <th>Unique Code</th>
                                <th>PIC Perusahaan</th>
                                <th>PIC Internal</th>
                                <th>Nama Perusahaan</th>
                                <th>Telepon</th>
                                <th>Provinsi</th>
                                <th>Kabupaten / Kota</th>
                                <th>Kecamatan</th>
                                <th>Desa</th>
                                <th>Alamat Lengkap</th>
                                <th>Payment Date</th>
                                <th>Nomor PKS</th>
                                <th>List Karyawan</th>
                                <th>Proses Pengajuan</th>
                                <th>Konfigurasi Pinjaman</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>


            </div>
        </div>
    </div>




    {{-- modal tambah --}}
    <div class="modal fade" id="modalAdd" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class=" modal-dialog modal-lg modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Tambah Perusahaan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama PIC Perusahaan</label>
                                <input class="form-control" id="namaPicPerusahaan" placeholder="PIC Perushaan"></input>
                                <small class="text-danger" id="errNamaPicPerusahaan"></small>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama PIC Internal</label>
                                <input class="form-control" id="namaPicInternal" placeholder="PIC Internal"></input>
                                <small class="text-danger" id="errNamaPicInternal"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Perusahaan</label>
                                <input class="form-control" id="namaPerusahaan" placeholder="Nama Perusahaan"></input>
                                <small class="text-danger" id="errNamaPerusahaan"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Telepon</label>
                                <input class="form-control" id="telepon" placeholder="62535252" type="number"></input>
                                <small class="text-danger" id="errTelepon"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provinsi</label>
                                <select class="form-control" id="provinsiSelect" style="width: 100%">
                                    <option disabled>Select</option>
                                    @foreach ($provinsis as $provinsi)
                                        <option value="{{ $provinsi->provinsi }}">{{ $provinsi->provinsi }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="errProvinsi"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kabupaten / Kota</label>
                                <select class="form-control" id="kabupatenSelect" style="width: 100%">
                                    <option disabled>Select</option>
                                </select>
                                <small class="text-danger" id="errKabupaten_kota"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kecamatan</label>
                                <select class="form-control" id="kecamatanSelect" style="width: 100%">
                                    <option disabled>Select</option>
                                </select>
                                <small class="text-danger" id="errKecamatan"></small>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Desa</label>
                                <select class="form-control" id="desaSelect" style="width: 100%">
                                    <option disabled>Select</option>
                                </select>
                                <small class="text-danger" id="errDesa"></small>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Alamat Lengkap</label>
                                <textarea class="form-control" id="alamatLengkap" style="height: 70px;" placeholder="Alamat Lengkap"></textarea>
                                <small class="text-danger" id="errAlamatLengkap"></small>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Date</label>
                                <select class="form-control" id="paymentDate" placeholder="Alamat Lengkap"
                                    type="date">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select>
                                <small class="text-danger" id="errPaymentDate"></small>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nomor PKS</label>
                                <input class="form-control" id="nomor_pks" placeholder="42151231"
                                    type="number"></input>
                                <small class="text-danger" id="errNomor_pks"></small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Password</label>
                                <input class="form-control" id="password" placeholder="Password"
                                    type="password"></input>
                                <small class="text-danger" id="errPassword"></small>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="button" id="buttonSubmit">Save <div id="loadingText">
                        </div></button>
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
                    <h5 class="modal-title" id="exampleModalCenterTitle">Ubah Perusahaan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label>Nama PIC Perusahaan</label>
                                <input class="form-control" id="namaPicPerusahaanEdit"
                                    placeholder="PIC Perushaan"></input>
                                <small class="text-danger" id="errNamaPicPerusahaanEdit"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama PIC Internal</label>
                                <input class="form-control" id="namaPicInternalEdit" placeholder="PIC Internal"></input>
                                <small class="text-danger" id="errNamaPicInternalEdit"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Perusahaan</label>
                                <input class="form-control" id="namaPerusahaanEdit"
                                    placeholder="Nama Perusahaan"></input>
                                <small class="text-danger" id="errNamaPerusahaanEdit"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Telepon</label>
                                <input class="form-control" id="teleponEdit" placeholder="62535252"
                                    type="number"></input>
                                <small class="text-danger" id="errTeleponEdit"></small>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provinsi</label>
                                <select class="form-control" id="provinsiSelectEdit" style="width: 100%">
                                    <option disabled>Select</option>
                                    @foreach ($provinsis as $provinsi)
                                        <option value="{{ $provinsi->provinsi }}">{{ $provinsi->provinsi }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="errProvinsiEdit"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kabupaten / Kota</label>
                                <select class="form-control" id="kabupatenSelectEdit" style="width: 100%">
                                    <option disabled>Select</option>
                                    {{-- @foreach ($kotas as $kota)
                                        <option value="{{ $kota->kabupaten }}">{{ $kota->kabupaten }}</option>
                                    @endforeach --}}
                                </select>
                                <small class="text-danger" id="errKabupaten_kotaEdit"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kecamatan</label>
                                <select class="form-control" id="kecamatanSelectEdit" style="width: 100%">
                                    <option disabled>Select</option>
                                    {{-- @foreach ($kecamatans as $kecamatan)
                                        <option value="{{ $kecamatan->kecamatan }}-{{ $kecamatan->kodepos }}">
                                            {{ $kecamatan->kecamatan }} - {{ $kecamatan->kodepos }}</option>
                                    @endforeach --}}
                                </select>
                                <small class="text-danger" id="errKecamatanEdit"></small>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Desa</label>
                                <select class="form-control" id="desaSelectEdit" style="width: 100%">
                                    <option disabled>Select</option>
                                    {{-- @foreach ($kelurahans as $kelurahan)
                                        <option value="{{ $kelurahan->kecamatan }}">{{ $kelurahan->kecamatan }}</option>
                                    @endforeach --}}
                                </select>
                                <small class="text-danger" id="errDesaEdit"></small>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Alamat Lengkap</label>
                                <textarea class="form-control" id="alamatEdit" style="height: 70px;" placeholder="Alamat Lengkap"></textarea>
                                <small class="text-danger" id="errAlamatEdit"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Date</label>
                                <select class="form-control" id="paymentDateEdit" placeholder="Alamat Lengkap"
                                    type="date">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select>
                                <small class="text-danger" id="errPaymentDateEdit"></small>
                            </div>

                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nomor PKS</label>
                                <input class="form-control" id="nomor_pksEdit" placeholder="42151231"
                                    type="number"></input>
                                <small class="text-danger" id="errNomor_pksEdit"></small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Status </label>
                                <select class="form-control" id="statusEdit">
                                    <option value="active">ACTIVE</option>
                                    <option value="nonactive">NONACTIVE</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Password</label>
                                <input class="form-control" id="passwordEdit" placeholder="Password"
                                    type="password"></input>
                                <small class="text-danger" id="errPasswordEdit"></small>
                            </div>
                            <small>Note: Kosongkan jika tidak ingin mengubah password</small>
                        </div>


                    </div>



                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="button" id="buttonSubmitEdit">Save <div id="loadingTextEdit">
                        </div></button>
                </div>
            </div>
        </div>
    </div>


    {{-- modal delete --}}
    <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class=" modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Delete Perusahaan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="text-center align-middle">
                        <p>Apakah anda yakin menghapus perusahaan ini?, ini akan menghapus semua data karyawan dan
                            pinjaman
                            yang bersangkutan dengan perusahaan ini</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" type="button" id="buttonDelete" onclick="buttonDelete()">Delete
                        <div id="loadingTextDelete">
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        var mulai = "";
        var akhir = "";
        var editID = "";
        var deleteID = "";

        var dataTable = {
            "_token": "{{ csrf_token() }}",
        };

        $('#provinsiSelect').select2();
        $('#kabupatenSelect').select2();
        $('#kecamatanSelect').select2();
        $('#desaSelect').select2();


        $('#provinsiSelectEdit').select2();
        $('#kabupatenSelectEdit').select2();
        $('#kecamatanSelectEdit').select2();
        $('#desaSelectEdit').select2();

        $('#provinsiSelect').on('change', function() {
            $.ajax({
                url: '{!! route('admin.ksp.kabupaten') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'provinsi': this.value,
                },
                type: 'POST',
                dataType: 'json',
                success: function(result) {
                    $("#kabupatenSelect").empty();
                    result.map(item => {
                        var option = new Option(item.kabupaten, item.kabupaten, true, true);
                        $('#kabupatenSelect').append(option);
                    })
                }
            });
        });

        $('#kabupatenSelect').on('change', function() {
            $.ajax({
                url: '{!! route('admin.ksp.kecamatan') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'kabupaten': this.value,
                },
                type: 'POST',
                dataType: 'json',
                success: function(result) {
                    $("#kecamatanSelect").empty();
                    result.map(item => {
                        var option = new Option(item.kecamatan, item.kecamatan, true, true);
                        $('#kecamatanSelect').append(option);
                    })
                }
            });
        });

        $('#kecamatanSelect').on('change', function() {
            $.ajax({
                url: '{!! route('admin.ksp.desa') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'kecamatan': this.value,
                },
                type: 'POST',
                dataType: 'json',
                success: function(result) {
                    $("#desaSelect").empty();
                    result.map(item => {
                        var option = new Option(item.desa + ' - ' + item.kodepos, item.desa +
                            '-' + item.kodepos,
                            true,
                            true);
                        $('#desaSelect').append(option);
                    })
                }
            });
        });



        // $('#provinsiSelectEdit').on('change', function() {
        //     $.ajax({
        //         url: '{!! route('admin.ksp.kabupaten') !!}',
        //         data: {
        //             '_token': "{{ csrf_token() }}",
        //             'provinsi': this.value,
        //         },
        //         type: 'POST',
        //         dataType: 'json',
        //         success: function(result) {
        //             result.map(item => {
        //                 var option = new Option(item.kabupaten, item.kabupaten, true, true);
        //                 $('#kabupatenSelectEdit').append(option);
        //             })
        //         }
        //     });
        // });

        // $('#kabupatenSelectEdit').on('change', function() {
        //     $.ajax({
        //         url: '{!! route('admin.ksp.kecamatan') !!}',
        //         data: {
        //             '_token': "{{ csrf_token() }}",
        //             'kabupaten': this.value,
        //         },
        //         type: 'POST',
        //         dataType: 'json',
        //         success: function(result) {
        //             result.map(item => {
        //                 var option = new Option(item.kecamatan, item.kecamatan, true, true);
        //                 $('#kecamatanSelectEdit').append(option);
        //             })
        //         }
        //     });
        // });

        // $('#kecamatanSelectEdit').on('change', function() {
        //     $.ajax({
        //         url: '{!! route('admin.ksp.desa') !!}',
        //         data: {
        //             '_token': "{{ csrf_token() }}",
        //             'kecamatan': this.value,
        //         },
        //         type: 'POST',
        //         dataType: 'json',
        //         success: function(result) {
        //             result.map(item => {
        //                 var option = new Option(item.desa + ' - ' + item.kodepos, item.desa +
        //                     '-' + item.kodepos,
        //                     true,
        //                     true);
        //                 $('#desaSelectEdit').append(option);
        //             })
        //         }
        //     });
        // });




        var table = $('#tableListPerusahaan').DataTable({
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
                "url": '{!! route('admin.ksp.perusahaan.table') !!}',
                "data": function(xx) {
                    return $.extend(xx, dataTable);
                },
            },
            columns: [{
                data: 'id_code_perusahaan'
            }, {
                data: 'unique_code'
            }, {
                data: 'pic_perusahaan'
            }, {
                data: 'pic_internal'
            }, {
                data: 'nama'
            }, {
                data: 'telp'
            }, {
                data: 'provinsi'
            }, {
                data: 'kabupaten'
            }, {
                data: 'kecamatan'
            }, {
                data: 'desa'
            }, {
                data: 'alamat_lengkap'
            }, {
                data: 'payment_date'
            }, {
                data: 'nomor_pks'
            }, {
                data: 'list_karyawan'
            }, {
                data: 'proses_pengajuan'
            }, {
                data: 'konfigurasi_pinjaman'
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

        $('#buttonSubmit').on('click', function() {
            $.ajax({
                url: '{!! route('admin.ksp.add.perusahaan') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'pic_perusahaan': $("#namaPicPerusahaan").val(),
                    'pic_internal': $("#namaPicInternal").val(),
                    'nama_perusahaan': $("#namaPerusahaan").val(),
                    'nama_perusahaan': $("#namaPerusahaan").val(),
                    'telepon': $("#telepon").val(),
                    'provinsi': $("#provinsiSelect").val(),
                    'kabupaten': $("#kabupatenSelect").val(),
                    'kecamatan': $("#kecamatanSelect").val(),
                    'desa': $("#desaSelect").val(),
                    'alamat_lengkap': $("#alamatLengkap").val(),
                    'payment_date': $("#paymentDate").val(),
                    'nomor_pks': $("#nomor_pks").val(),
                    'password': $("#password").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmit').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                },
                success: function(add) {
                    toastr.success(add.message)
                    table.ajax.reload();
                    $("#modalAdd").modal('hide');
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                },
                error: function(err) {
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                    $('#errNamaPicPerusahaan').text(err.responseJSON.errors.nama_perusahaan);
                    $('#errNamaPicInternal').text(err.responseJSON.errors.nama_perusahaan);
                    $('#errNamaPerusahaan').text(err.responseJSON.errors.nama_perusahaan);
                    $('#errTelepon').text(err.responseJSON.errors.telepon);
                    $('#errAlamatLengkap').text(err.responseJSON.errors.alamat_lengkap);
                    $('#errNomor_pks').text(err.responseJSON.errors.nomor_pks);
                    $('#errProvinsi').text(err.responseJSON.errors.provinsi);
                    $('#errKabupaten').text(err.responseJSON.errors.kabupaten);
                    $('#errKecamatan').text(err.responseJSON.errors.kecamatan);
                    $('#errDesa').text(err.responseJSON.errors.desa);
                    $('#errPassword').text(err.responseJSON.errors.password);
                    $('#errPaymentDate').text(err.responseJSON.errors.payment_date);
                }
            });
        })


        edit = (id) => {
            editID = id;
            $.ajax({
                url: '{!! route('admin.ksp.detail.perusahaan') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': editID,
                },
                type: 'POST',
                dataType: 'json',
                success: function(detail) {
                    $("#namaPicPerusahaanEdit").val(detail.data.pic_perusahaan);
                    $("#namaPicInternalEdit").val(detail.data.pic_internal);
                    $("#namaPerusahaanEdit").val(detail.data.nama);
                    $("#teleponEdit").val(detail.data.telp);
                    $("#alamatEdit").val(detail.data.alamat_lengkap);
                    $("#nomor_pksEdit").val(detail.data.nomor_pks);
                    $("#statusEdit").val(detail.data.status);
                    $("#paymentDateEdit").val(detail.data.payment_date);
                    $("#provinsiSelectEdit").val(detail.data.provinsi).trigger('change');

                    $("#kabupatenSelectEdit").empty();

                    detail.data.list_kabupaten.map(item => {
                        var option = new Option(item.kabupaten, item.kabupaten, true, true);
                        $('#kabupatenSelectEdit').append(option);
                    })
                    $("#kabupatenSelectEdit").val(detail.data.kabupaten).trigger('change');


                    $("#kecamatanSelectEdit").empty();

                    detail.data.list_kecamatan.map(item => {
                        var option = new Option(item.kecamatan, item.kecamatan, true, true);
                        $('#kecamatanSelectEdit').append(option);
                    })
                    $("#kecamatanSelectEdit").val(detail.data.kecamatan).trigger('change');

                    $("#desaSelectEdit").empty();

                    detail.data.list_desa.map(item => {
                        var option = new Option(item.desa + ' - ' + item.kodepos, item.desa +
                            '-' + item.kodepos,
                            true,
                            true);
                        $('#desaSelectEdit').append(option);
                    })

                    $("#desaSelectEdit").val(detail.data.desa).trigger('change');
                    $('#modalEdit').modal('show');
                }
            });
        }


        $('#buttonSubmitEdit').on('click', function() {
            $.ajax({
                url: '{!! route('admin.ksp.edit.perusahaan') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': editID,
                    'nama_perusahaan': $("#namaPerusahaanEdit").val(),
                    'telepon': $("#teleponEdit").val(),
                    'provinsi': $("#provinsiSelectEdit").val(),
                    'kabupaten': $("#kabupatenSelectEdit").val(),
                    'kecamatan': $("#kecamatanSelectEdit").val(),
                    'desa': $("#desaSelectEdit").val(),
                    'alamat_lengkap': $("#alamatEdit").val(),
                    'payment_date': $("#paymentDateEdit").val(),
                    'nomor_pks': $("#nomor_pksEdit").val(),
                    'status': $("#statusEdit").val(),
                    'password': $("#passwordEdit").val(),

                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmitEdit').attr("disabled", true);
                    $('#loadingTextEdit').addClass("spinner-border");
                },
                success: function(add) {
                    toastr.success(add.message)
                    table.ajax.reload();
                    $("#modalEdit").modal('hide');
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                },
                error: function(err) {
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                    $('#errNamaPerusahaanEdit').text(err.responseJSON.errors.nama_perusahaan);
                    $('#errTeleponEdit').text(err.responseJSON.errors.telepon);
                    $('#errAlamatEdit').text(err.responseJSON.errors.alamat);
                    $('#errNomor_pksEdit').text(err.responseJSON.errors.nomor_pks);
                    $('#errPasswordEdit').text(err.responseJSON.errors.password);
                }
            });
        })

        deletePerusahaan = (id) => {
            deleteID = id;
            $('#modalDelete').modal('show');
        }

        buttonDelete = () => {
            $.ajax({
                url: '{!! route('admin.ksp.delete.perusahaan') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id': deleteID,
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonDelete').attr("disabled", true);
                    $('#loadingTextDelete').addClass("spinner-border");
                },
                success: function(deleteSave) {
                    toastr.success(deleteSave.message)
                    table.ajax.reload();
                    $("#modalDelete").modal('hide');
                    $('#buttonDelete').attr("disabled", false);
                    $('#loadingTextDelete').removeClass("spinner-border");
                }
            });
        }
    </script>
@endpush
