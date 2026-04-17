@extends('layout.app')
@section('title', 'KSP Konfigurasi')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                    <li class="breadcrumb-item active">KSP Konfigurasi</li>
                </ol>
            </div>
            <h4 class="page-title">KSP Konfigurasi {{ $list_perusahaan->nama }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">

                <div class="form-group">
                    <label>Iuran Anggota</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp.</span>
                        </div>
                        <input type="number" class="form-control" id="iuran_anggota" required
                            placeholder="Iuran Anggota"
                            value="{{ $list_perusahaan->amount_pinjaman ? $list_perusahaan->amount_pinjaman->iuran_anggota : 0 }}">
                    </div>
                    <small class="text-danger" id="errIuran_anggota"></small>
                </div>

                <div class="form-group">
                    <label>Min Plafon</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp.</span>
                        </div>
                        <input type="number" class="form-control" id="minimal_pinjaman" required
                            placeholder="Min Plafon"
                            value="{{ $list_perusahaan->amount_pinjaman ? $list_perusahaan->amount_pinjaman->minimal : 0 }}">
                    </div>
                    <small class="text-danger" id="errMinimal_pinjaman"></small>
                </div>

                <div class="form-group">
                    <label>Max Plafon (Perusahaan)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp.</span>
                        </div>
                        <!-- <input type="number" class="form-control" id="maximal_pinjaman" required
                            placeholder="Max Plafon"
                            value="{{ $list_perusahaan->amount_pinjaman ? $list_perusahaan->amount_pinjaman->maximal : 0 }}"
                            disabled> -->
                        <input type="text" class="form-control" id="maximal_pinjaman_view" placeholder="Max Plafon"
                            value="{{ number_format($max_plafon ?? 0, 0, ',', '.') }}" disabled>

                        <input type="hidden" name="maximal_pinjaman" id="maximal_pinjaman">
                    </div>
                    <small class="text-danger" id="errMaximal_pinjaman"></small>
                </div>

                <div class="form-group">
                    <label>DSR Maksimal in %</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="maximal_persentase" required
                            placeholder="Maximal Persentase"
                            value="{{ $list_perusahaan->amount_pinjaman ? $list_perusahaan->amount_pinjaman->maximal_persentase : 0 }}">
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <small class="text-danger" id="errMaximal_persentase"></small>
                </div>

                <button type="button" class="btn btn-sm btn-primary float-right waves-effect waves-light"
                    onclick="amountPinjamanSave()">
                    Save
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-sm btn-primary float-right waves-effect waves-light"
                    data-toggle="modal" data-target="#modalAdd">
                    <i class="mdi mdi-plus-circle"></i> Tambah Konfigurasi
                </button>


                <table id="tableTenorPinjaman" class="table dt-responsive table-hover mt-4 text-center justify-center"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>Tenor</th>
                            <th>Rate</th>
                            <th>Asuransi</th>
                            <th>Provisi</th>
                            <th>Biaya Admin</th>
                            <th>Fee Tomi</th>
                            <th>Profesional Fee 1</th>
                            <th>Profesional Fee 2</th>
                            <th>KSP</th>
                            <th>Iuran Anggota</th>
                            <th>Biaya Layanan</th>
                            <th>Minimal Nominal</th>
                            <th>Maximal Nominal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>



{{-- modal tambah --}}
<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class=" modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Tambah Konfigurasi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Tenor</label>
                    <div class="input-group"> <input class="form-control" id="tenor" placeholder="Tenor" type="number">
                        <span class="input-group-text">bulan</span>
                    </div>
                    <small class="text-danger" id="errTenor"></small>
                </div>

                <div class="form-group">
                    <label>Rate</label>
                    <div class="input-group"> <input class="form-control" id="rate" placeholder="Rate" type="number">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-danger" id="errRate"></small>
                </div>

                <div class="form-group">
                    <label>Biaya asuransi</label>
                    <div class="input-group"> <input class="form-control" id="asuransi" placeholder="Biaya asuransi"
                            type="number">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-danger" id="errAsuransi"></small>
                </div>

                <div class="form-group">
                    <label>Biaya Provisi</label>
                    <div class="input-group"> <input class="form-control" id="provisi" placeholder="Biaya provisi"
                            type="number">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-danger" id="errProvisi"></small>
                </div>


                @if ($list_perusahaan->active_profesional_fee == 'active')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profesional Fee 1</label>
                            <input type="number" step="0.01" class="form-control hitung-admin" id="profesional_fee_1"
                                placeholder="0.00">
                        </div>
                        <small class="text-danger" id="errProfesional_fee_1"></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profesional Fee 2</label>
                            <input type="number" step="0.01" class="form-control hitung-admin" id="profesional_fee_2"
                                placeholder="0.00">
                        </div>
                        <small class="text-danger" id="errProfesional_fee_2"></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>KSP</label>
                            <input type="number" step="0.01" class="form-control hitung-admin" id="ksp"
                                placeholder="0.00">
                        </div>
                        <small class="text-danger" id="errKsp"></small>
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label>Biaya Admin</label>
                    <div class="input-group">
                        <input class="form-control" id="biayaadmin" placeholder="Biaya Admin" type="number" step="0.01"
                            {{ $list_perusahaan->active_profesional_fee == 'active' ? 'readonly' : '' }}>
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-danger" id="errBiayaAdmin"></small>
                </div>

                <div class="form-group">
                    <label>Fee Tomi</label>
                    <div class="input-group"> <input class="form-control" id="feeTomi" placeholder="Fee Tomi"
                            type="number">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-danger" id="errfeeTomi"></small>
                </div>


                <div class="form-group">
                    <label>Biaya Layanan</label>
                    <input class="form-control rupiah-input" id="biayalayanan" placeholder="Biaya Layanan" type="text"
                        data-original-value="0"> <small class="text-danger" id="errBiayalayanan"></small>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Minimal Nominal</label>
                            <input class="form-control rupiah-input" id="minimalNominalTenor"
                                placeholder="Minimal Nominal Tenor" type="text" data-original-value="0">
                            <small class="text-danger" id="errMinimalNominalTenor"></small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Maximal Nominal</label>
                            <input class="form-control rupiah-input" id="maximalNominalTenor"
                                placeholder="Maximal Nominal Tenor" type="text" data-original-value="0">
                            <small class="text-danger" id="errMaximalNominalTenor"></small>
                        </div>
                    </div>
                </div>




            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="button" id="buttonSubmit" onclick="buttonSubmit()">Save <div
                        id="loadingText">
                    </div></button>
            </div>
        </div>
    </div>
</div>


{{-- modal edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class=" modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Konfigurasi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            </div>
            <div class="modal-body">

                <input type="hidden" id="konfigurasiId">

                <div class="form-group">
                    <label>Tenor</label>
                    <div class="input-group"> <input class="form-control" id="tenorEdit" placeholder="Tenor"
                            type="number">
                        <span class="input-group-text">bulan</span>
                    </div>
                    <small class="text-danger" id="errTenorEdit"></small>
                </div>

                <div class="form-group">
                    <label>Rate</label>
                    <div class="input-group"> <input class="form-control" id="rateEdit" placeholder="Rate"
                            type="number">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-danger" id="errRateEdit"></small>
                </div>


                @if ($list_perusahaan->active_profesional_fee == 'active')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profesional Fee 1</label>
                            <input type="number" step="0.01" class="form-control hitung-adminEdit"
                                id="profesional_fee_1Edit" placeholder="0.00">
                        </div>
                        <small class="text-danger" id="errEditProfesional_fee_1Edit"></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profesional Fee 2</label>
                            <input type="number" step="0.01" class="form-control hitung-adminEdit"
                                id="profesional_fee_2Edit" placeholder="0.00">
                        </div>
                        <small class="text-danger" id="errEditProfesional_fee_2Edit"></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>KSP</label>
                            <input type="number" step="0.01" class="form-control hitung-adminEdit" id="kspEdit"
                                placeholder="0.00">
                        </div>
                        <small class="text-danger" id="errKspEdit"></small>
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label>Biaya Admin</label>
                    <div class="input-group">
                        <input class="form-control" id="biayaadminEdit" placeholder="Biaya Admin" type="number"
                            step="0.01" {{ $list_perusahaan->active_profesional_fee == 'active' ? 'readonly' : '' }}>
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-danger" id="errBiayaAdminEdit"></small>
                </div>



                <div class="form-group">
                    <label>Biaya Asuransi</label>
                    <div class="input-group"> <input class="form-control" id="asuransiEdit" placeholder="Biaya Asuransi"
                            type="number">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-danger" id="errAsuransiEdit"></small>
                </div>

                <div class="form-group">
                    <label>Biaya Provisi</label>
                    <div class="input-group"> <input class="form-control" id="provisiEdit" placeholder="Biaya Provisi"
                            type="number">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-danger" id="errProvisiEdit"></small>
                </div>

                <div class="form-group">
                    <label>Fee Tomi</label>
                    <div class="input-group"> <input class="form-control" id="feeTomiEdit" placeholder="Fee Tomi"
                            type="number">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-danger" id="errfeeTomiEdit"></small>
                </div>

                <div class="form-group">
                    <label>Biaya Layanan</label>
                    <input class="form-control rupiah-input-edit" id="biayalayananEdit" placeholder="Biaya Layanan"
                        type="text" data-original-value="0">
                    <small class="text-danger" id="errBiayaLayananEdit"></small>
                </div>


                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Minimal Nominal</label>
                            <input class="form-control rupiah-input-edit" id="minimalNominalTenorEdit"
                                placeholder="Minimal Nominal Tenor" type="text" data-original-value="0">
                            <small class="text-danger" id="errMinimalNominalTenorEdit"></small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Maximal Nominal</label>
                            <input class="form-control rupiah-input-edit" id="maximalNominalTenorEdit"
                                placeholder="Maximal Nominal Tenor" type="text" data-original-value="0">
                            <small class="text-danger" id="errMaximalNominalTenorEdit"></small>
                        </div>
                    </div>
                </div>



            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="button" id="buttonSubmitEdit" onclick="buttonSubmitEdit()">Save
                    <div id="loadingTextEdit">
                    </div>
                </button>
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
                <h5 class="modal-title" id="exampleModalCenterTitle">Delete Konfigurasi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            </div>
            <div class="modal-body">
                <div class="text-center align-middle">
                    <span>Apakah anda yakin menghapus konfigurasi ini?</span>

                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" type="button" id="buttonDelete" onclick="buttonDelete()">Delete <div
                        id="loadingTextDelete">
                    </div></button>
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
        "id": "{{ $list_perusahaan->id }}",
    };
    var table = $('#tableTenorPinjaman').DataTable({
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
            "url": '{!! route('admin.ksp.konfigurasi.table') !!}',
            "data": function (xx) {
                return $.extend(xx, dataTable);
            },
        },
        columns: [{
            data: 'tenor'
        }, {
            data: 'rate'
        }, {
            data: 'asuransi'
        }, {
            data: 'provisi'
        }, {
            data: 'biaya_admin'
        }, {
            data: 'fee_tomi'
        }, {
            data: 'profesional_fee_1'
        }, {
            data: 'profesional_fee_2'
        }, {
            data: 'ksp'
        }, {
            data: 'iuran_anggota'
        }, {
            data: 'biaya_layanan'
        }, {
            data: 'minimal_nominal'
        }, {
            data: 'maximal_nominal'
        }, {
            data: 'action'
        }]
    });

    deleteKonfigurasi = (id) => {
        deleteID = id;
        $('#modalDelete').modal('show');
    }
    edit = (id) => {
        editID = id; // Menyimpan ID ke variabel global
        // Bersihkan pesan error sebelum memuat data baru
        $('#errTenorEdit').text('');
        $('#errRateEdit').text('');
        $('#errAsuransiEdit').text('');
        $('#errProvisiEdit').text('');
        $('#errBiayaAdminEdit').text('');
        $('#errfeeTomiEdit').text('');
        $('#errBiayaLayananEdit').text('');
        $('#errMinimalNominalTenorEdit').text('');
        $('#errMaximalNominalTenorEdit').text('');

        // Pastikan hidden input ID ada di modal Edit: <input type="hidden" id="konfigurasiId">
        $('#konfigurasiId').val(id);

        $.ajax({
            url: '{!! route('admin.ksp.konfigurasi.detail') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': id
            },
            type: 'POST',
            dataType: 'json',
            success: function (detail) {
                const data = detail.data;
                console.log(data.provisi);
                // 1. Input Non-Rupiah (Tenor, Rate, Biaya Admin, Fee Tomi)
                $('#tenorEdit').val(data.tenor);
                $('#rateEdit').val(data.rate);
                $('#asuransiEdit').val(data.asuransi);
                $('#provisiEdit').val(data.provisi);
                $('#biayaadminEdit').val(data.biaya_admin);
                $('#feeTomiEdit').val(data.fee_tomi);

                $('#profesional_fee_1Edit').val(data.profesional_fee_1);
                $('#profesional_fee_2Edit').val(data.profesional_fee_2);
                $('#kspEdit').val(data.ksp);
                // $('#iurananggotaEdit').val(data.iuran_anggota); // Input ini tidak ada di HTML modal, jadi saya komentari.

                // 2. Input Rupiah (Biaya Layanan, Minimal, Maximal Nominal)

                // Biaya Layanan
                $('#biayalayananEdit')
                    .attr('data-original-value', data.biaya_layanan) // Simpan nilai integer mentah
                    .val(formatRupiah(data.biaya_layanan)); // Tampilkan nilai yang diformat

                // Minimal Nominal
                $('#minimalNominalTenorEdit')
                    .attr('data-original-value', data.minimal_nominal) // Simpan nilai integer mentah
                    .val(formatRupiah(data.minimal_nominal)); // Tampilkan nilai yang diformat

                // Maximal Nominal
                $('#maximalNominalTenorEdit')
                    .attr('data-original-value', data.maximal_nominal) // Simpan nilai integer mentah
                    .val(formatRupiah(data.maximal_nominal)); // Tampilkan nilai yang diformat

                $('#modalEdit').modal('show');
            }
        });
    }
    buttonDelete = () => {
        $.ajax({
            url: '{!! route('admin.ksp.konfigurasi.delete') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': deleteID
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#buttonDelete').attr("disabled", true);
                $('#loadingTextDelete').addClass("spinner-border");
            },
            success: function (saveConfig) {
                toastr.success(saveConfig.message)
                // listMenu();
                table.ajax.reload();
                $("#modalDelete").modal('hide');
                $('#buttonDelete').attr("disabled", false);
                $('#loadingTextDelete').removeClass("spinner-border");
            },
            error: function (saveConfig) {
                $('#buttonDelete').attr("disabled", false);
                $('#loadingTextDelete').removeClass("spinner-border");
            }
        });
    }

    buttonSubmit = () => {
        var tenor = $("#tenor").val();
        var rate = $("#rate").val();
        var asuransi = $("#asuransi").val();
        var provisi = $("#provisi").val();
        var biayaAdmin = $("#biayaadmin").val();
        var feeTomi = $("#feeTomi").val();
        // fee
        var profesional_fee_1 = $("#profesional_fee_1").val();
        var profesional_fee_2 = $("#profesional_fee_2").val();
        var ksp = $("#ksp").val();

        var biayaLayananRaw = $('#biayalayanan').attr('data-original-value');
        var minimalNominalRaw = $('#minimalNominalTenor').attr('data-original-value');
        var maximalNominalRaw = $('#maximalNominalTenor').attr('data-original-value');

        // Fallback/Safety Check: Pastikan nilai yang dikirim adalah string angka atau 0 jika kosong
        var biayaLayanan = biayaLayananRaw ? biayaLayananRaw : '0';
        var minimalNominal = minimalNominalRaw ? minimalNominalRaw : '0';
        var maximalNominal = maximalNominalRaw ? maximalNominalRaw : '0';


        $.ajax({
            url: '{!! route('admin.ksp.konfigurasi.add') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': "{{ $list_perusahaan->id }}",
                'tenor': tenor, // Nilai integer/angka
                'rate': rate, // Nilai integer/angka
                'asuransi': asuransi, // Nilai integer/angka
                'provisi': provisi, // Nilai integer/angka
                'biaya_admin': biayaAdmin, // Nilai integer/angka
                'fee_tomi': feeTomi, // Nilai integer/angka
                // 
                'profesional_fee_1': profesional_fee_1,
                'profesional_fee_2': profesional_fee_2,
                'ksp': ksp,

                // Kirim nilai Rupiah dalam format integer mentah (tanpa titik)
                'biaya_layanan': biayaLayanan,
                'minimal_nominal': minimalNominal,
                'maximal_nominal': maximalNominal,
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#buttonSubmit').attr("disabled", true);
                $('#loadingText').addClass("spinner-border");
                // Sembunyikan pesan error lama
                $('#errTenor').text('');
                $('#errRate').text('');
                $('#errAsuransi').text('');
                $('#errProvisi').text('');
                $('#errBiayaAdmin').text('');
                $('#errfeeTomi').text('');
                $('#errBiayaLayanan').text(''); // Perbaikan ID: errBiayaLayanan
                $('#errMinimalNominalTenor').text('');
                $('#errMaximalNominalTenor').text('');

                $('#errProfesional_fee_1').text('');
                $('#errProfesional_fee_2').text('');
                $('#errKsp').text('');
            },
            success: function (saveConfig) {
                toastr.success(saveConfig.message)
                // listMenu();
                table.ajax.reload();
                $("#modalAdd").modal('hide');

                // Clear input fields after success (optional but recommended)
                $('#tenor').val('');
                $('#rate').val('');
                $('#asuransi').val('');
                $('#provisi').val('');
                $('#biayaadmin').val('');
                $('#feeTomi').val('');
                $('#biayalayanan').val('').attr('data-original-value', '0');
                $('#minimalNominalTenor').val('').attr('data-original-value', '0');
                $('#maximalNominalTenor').val('').attr('data-original-value', '0');

                $('#buttonSubmit').attr("disabled", false);
                $('#loadingText').removeClass("spinner-border");
            },
            error: function (saveConfig) {
                // Pastikan ID error untuk Biaya Layanan sudah benar di sini: 'errBiayaLayanan'
                $('#errTenor').text(saveConfig.responseJSON.errors.tenor);
                $('#errRate').text(saveConfig.responseJSON.errors.rate);
                $('#errAsuransi').text(saveConfig.responseJSON.errors.asuransi);
                $('#errProvisi').text(saveConfig.responseJSON.errors.provisi);
                $('#errBiayaAdmin').text(saveConfig.responseJSON.errors.biaya_admin);
                $('#errfeeTomi').text(saveConfig.responseJSON.errors.fee_tomi);
                $('#errBiayaLayanan').text(saveConfig.responseJSON.errors
                    .biaya_layanan); // Perbaikan ID
                $('#errMinimalNominalTenor').text(saveConfig.responseJSON.errors.minimal_nominal);
                $('#errMaximalNominalTenor').text(saveConfig.responseJSON.errors.maximal_nominal);
                $('#buttonSubmit').attr("disabled", false);
                $('#loadingText').removeClass("spinner-border");
            }
        });
    }

    buttonSubmitEdit = () => {
        // 1. Ambil ID Konfigurasi
        var konfigurasiId = editID;

        // 2. Ambil nilai non-Rupiah
        var tenor = $("#tenorEdit").val();
        var rate = $("#rateEdit").val();
        var asuransi = $("#asuransiEdit").val();
        var provisi = $("#asuransiEdit").val();
        var biayaAdmin = $("#biayaadminEdit").val();
        var feeTomi = $("#feeTomiEdit").val();


        var profesional_fee_1 = $("#profesional_fee_1Edit").val();
        var profesional_fee_2 = $("#profesional_fee_2Edit").val();
        var ksp = $("#kspEdit").val();

        // 3. Ambil nilai Rupiah RAW INTEGER dari data-original-value
        var biayaLayananRaw = $('#biayalayananEdit').attr('data-original-value');
        var minimalNominalRaw = $('#minimalNominalTenorEdit').attr('data-original-value');
        var maximalNominalRaw = $('#maximalNominalTenorEdit').attr('data-original-value');

        // Fallback/Safety Check: Pastikan nilai yang dikirim adalah string angka atau 0 jika kosong
        var biayaLayanan = biayaLayananRaw ? biayaLayananRaw : '0';
        var minimalNominal = minimalNominalRaw ? minimalNominalRaw : '0';
        var maximalNominal = maximalNominalRaw ? maximalNominalRaw : '0';

        $.ajax({
            url: '{!! route('admin.ksp.konfigurasi.edit') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': konfigurasiId, // Menggunakan editID yang sudah diset di fungsi edit()
                'tenor': tenor,
                'rate': rate,
                'asuransi': asuransi,
                'provisi': provisi,
                'biaya_admin': biayaAdmin,
                'fee_tomi': feeTomi,

                // Kirim nilai Rupiah dalam format integer mentah
                'biaya_layanan': biayaLayanan,
                'minimal_nominal': minimalNominal,
                'maximal_nominal': maximalNominal,

                'profesional_fee_1': profesional_fee_1,
                'profesional_fee_2': profesional_fee_2,
                'ksp': ksp,

            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#buttonSubmitEdit').attr("disabled", true);
                $('#loadingTextEdit').addClass("spinner-border");
                // Bersihkan pesan error saat mulai proses
                $('#errTenorEdit').text('');
                $('#errRateEdit').text('');
                $('#errAsuransiEdit').text('');
                $('#errProvisiEdit').text('');
                $('#errBiayaAdminEdit').text('');
                $('#errfeeTomiEdit').text('');
                $('#errBiayaLayananEdit').text('');
                $('#errMinimalNominalTenorEdit').text('');
                $('#errMaximalNominalTenorEdit').text('');

                $('#errEditProfesional_fee_1').text('');
                $('#errEditProfesional_fee_2').text('');
                $('#errKsp').text('');


            },
            success: function (saveConfig) {
                toastr.success(saveConfig.message)
                table.ajax.reload();
                $("#modalEdit").modal('hide');

                // Clear errors on success (redundant but safe)
                $('#errTenorEdit').text('');
                $('#errRateEdit').text('');
                $('#errAsuransiEdit').text('');
                $('#errProvisiEdit').text('');
                $('#errBiayaAdminEdit').text('');
                $('#errfeeTomiEdit').text('');
                $('#errBiayaLayananEdit').text('');
                $('#errMinimalNominalTenorEdit').text('');
                $('#errMaximalNominalTenorEdit').text('');


                $('#errEditProfesional_fee_1').text('');
                $('#errEditProfesional_fee_2').text('');
                $('#errKsp').text('');


                $('#buttonSubmitEdit').attr("disabled", false);
                $('#loadingTextEdit').removeClass("spinner-border");
            },
            error: function (saveConfig) {
                // Tampilkan error dari backend
                $('#errTenorEdit').text(saveConfig.responseJSON.errors.tenor);
                $('#errRateEdit').text(saveConfig.responseJSON.errors.rate);
                $('#errAsuransiEdit').text(saveConfig.responseJSON.errors.asuransi);
                $('#errProvisiEdit').text(saveConfig.responseJSON.errors.provisi);
                $('#errBiayaAdminEdit').text(saveConfig.responseJSON.errors.biaya_admin);
                $('#errfeeTomiEdit').text(saveConfig.responseJSON.errors.fee_tomi);
                $('#errBiayaLayananEdit').text(saveConfig.responseJSON.errors.biaya_layanan);
                $('#errMinimalNominalTenorEdit').text(saveConfig.responseJSON.errors.minimal_nominal);
                $('#errMaximalNominalTenorEdit').text(saveConfig.responseJSON.errors.maximal_nominal);

                $('#errEditProfesional_fee_1').text(saveConfig.responseJSON.errors.profesional_fee_1);
                $('#errEditProfesional_fee_2').text(saveConfig.responseJSON.errors
                    .profesional_fee_2);
                $('#errKsp').text(saveConfig.responseJSON.errors.ksp);

                $('#buttonSubmitEdit').attr("disabled", false);
                $('#loadingTextEdit').removeClass("spinner-border");
            }
        });
    }

    amountPinjamanSave = () => {
        $.ajax({
            url: '{!! route('admin.ksp.konfigurasi.amount') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': "{{ $list_perusahaan->id }}",
                'iuran_anggota': $("#iuran_anggota").val(),
                'minimal': $("#minimal_pinjaman").val(),
                'maximal': $("#maximal_pinjaman").val(),
                'maximal_persentase': $("#maximal_persentase").val(),
            },
            type: 'POST',
            dataType: 'json',
            success: function (amountSubmit) {
                toastr.success(amountSubmit.message)
            },
            error: function (amountSubmitError) {
                $('#errIuran_anggota').text(amountSubmitError.responseJSON.errors.iuran_anggota);
                $('#errMinimal_pinjaman').text(amountSubmitError.responseJSON.errors.minimal);
                $('#errMaximal_pinjaman').text(amountSubmitError.responseJSON.errors.maximal);
                $('#errMaximal_persentase').text(amountSubmitError.responseJSON.errors
                    .maximal_persentase);
            }
        });
    }
</script>

<script>
    // Function to format a number into Rupiah format (e.g., 100000 -> 100.000)
    function formatRupiah(angka) {
        if (angka == null || angka === '') return '';
        var number_string = angka.toString().replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    // Function to remove Rupiah formatting and return an integer (e.g., 100.000 -> 100000)
    function unformatRupiah(rupiah) {
        return parseInt(rupiah.replace(/[^0-9]/g, ''), 10);
    }

    $('.rupiah-input').on('keyup', function () {
        var input = $(this).val();
        // Remove all non-digit characters to get the raw number
        var rawValue = input.replace(/[^0-9]/g, '');

        // Store the raw integer value in a data attribute
        $(this).attr('data-original-value', rawValue);

        // Format and display the Rupiah value
        $(this).val(formatRupiah(rawValue));
    });

    // Optional: Re-format on focus loss (blur)
    $('.rupiah-input').on('blur', function () {
        var input = $(this).val();
        var rawValue = input.replace(/[^0-9]/g, '');
        $(this).val(formatRupiah(rawValue));
    });

    $('.rupiah-input-edit').on('keyup', function () {
        var input = $(this).val();
        // Hapus semua karakter non-digit kecuali titik koma
        var rawValue = input.replace(/[^0-9]/g, '');

        // Simpan nilai integer mentah di data attribute
        $(this).attr('data-original-value', rawValue);

        // Format dan tampilkan nilai Rupiah
        $(this).val(formatRupiah(rawValue));
    });

    $('.rupiah-input-edit').on('blur', function () {
        var input = $(this).val();
        var rawValue = input.replace(/[^0-9]/g, '');
        $(this).val(formatRupiah(rawValue));
    });

    $('.hitung-admin').on('input', function () {
        let fee1 = parseFloat($('#profesional_fee_1').val()) || 0;
        let fee2 = parseFloat($('#profesional_fee_2').val()) || 0;
        let ksp = parseFloat($('#ksp').val()) || 0;

        let total = fee1 + fee2 + ksp;

        $('#biayaadmin').val(total.toFixed(2));
    });


    $('.hitung-adminEdit').on('input', function () {
        let fee1 = parseFloat($('#profesional_fee_1Edit').val()) || 0;
        let fee2 = parseFloat($('#errEditProfesional_fee_2').val()) || 0;
        let ksp = parseFloat($('#kspEdit').val()) || 0;
        let total = fee1 + fee2 + ksp;

        $('#biayaadminEdit').val(total.toFixed(2));
    });

    const inputView = document.getElementById('maximal_pinjaman_view');
    const inputReal = document.getElementById('maximal_pinjaman');

    function formatRupiah(angka) {
        return angka.replace(/\D/g, '')
            .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    inputView.addEventListener('input', function (e) {
        let angka = e.target.value.replace(/\D/g, '');
        inputReal.value = angka; // nilai asli (tanpa titik)
        e.target.value = formatRupiah(angka); // tampilan pakai titik
    });

    // set awal
    inputReal.value = inputView.value.replace(/\D/g, '');
</script>
@endpush