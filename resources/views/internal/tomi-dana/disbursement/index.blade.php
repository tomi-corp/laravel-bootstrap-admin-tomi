@extends('internal.layouts.app')
@section('title', 'Disbursement')

@push('css')
    <style>
        #tableDisbursement th,
        #tableDisbursement td { white-space: nowrap; vertical-align: middle; }
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
                    <li class="breadcrumb-item active">Disbursement</li>
                </ol>
            </div>
            <h4 class="page-title">Disbursement</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card-box">
            <div class="d-flex justify-content-between">
                <h4 class="header-title">Disbursement Trend</h4>
            </div>

            <div class="mt-4">
                <div id="chart-with-area" class="ct-chart ct-golden-section"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <span>Total Disbursment</span>
                    <h4 id="totalDisbursementTotal">Rp 0</h4>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <span>Pending</span>
                    <h4 id="disbursementPending">0</h4>
                    <span>Disbursement</span>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <span>Hari ini</span>
                    <h4 id="sudahdicairkan">0</h4>
                    <span>Sudah Dicairkan</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-secondary alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>

    <h5>Keterangan :</h5>
    <span>🔴 <b>Sangat Mendesak</b> ≥ 24 jam Pengajuan dari hari sebelumnya, paling didahulukan</span><br>
    <span>🟠 <b>Mendesak</b> 4–24 jam Pengajuan semalam atau pagi hari</span><br>
    <span>🟡 <b>Normal</b> 1–4 jam Pengajuan hari ini, dalam rentang jam kerja</span><br>
    <span>🔵 <b>Baru Masuk</b> &lt; 1 jam Pengajuan baru (tidak langsung dicairkan)</span><br>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4>Disbursement List</h4>

                <table id="tableDisbursement" class="table table-hover nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>DateTime</th>
                            <th>Prioritas</th>
                            <th>Doc PK</th>
                            <th>KTP</th>
                            <th>Selfie</th>
                            <th>ID Pinjaman</th>
                            <th>Lender</th>
                            <th>Nama Perusahaan</th>
                            <th>Nama Karyawan</th>
                            <th>Total Pinjaman</th>
                            <th>Jumlah Disbursement</th>
                            <th>Bank</th>
                            <th>No.Rek</th>
                            <th>Atas Nama</th>
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
                <h4 class="modal-title" id="myCenterModalLabel">Ubah status disbursement</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>

            <form id="addFrom" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <span>ID Pinjaman </span></br>
                            <span>Bank </span></br>
                            <span>Atas Nama </span></br>
                            <span>No. Rekening </span></br>
                            <span>Jumlah Disbursment </span></br>
                        </div>

                        <div class="col-6">
                            <span>: </span><span id="id_pinjaman"></span></br>
                            <span>: </span><span id="bank"></span></br>
                            <span>: </span><span id="atas_nama"></span></br>
                            <span>: </span><span id="norek"></span></br>
                            <span>: </span><span id="disbursement"></span></br>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label for="transfer_date">Tanggal Transfer</label>
                        <input type="date" name="transfer_date" id="transfer_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="transfer_proof">Bukti Transfer</label>
                        <input type="file" name="transfer_proof" id="transfer_proof" class="form-control"
                            accept="image/*,.pdf" required>
                        <small class="text-muted">Format: JPG, JPEG, PNG, atau PDF.</small>
                    </div>
                </div>

                <div class="container">
                    <div class="checkbox mb-2 checkbox-info">
                        <input id="checkbox04" type="checkbox" onchange="changeButton()">
                        <label for="checkbox04">
                            Saya menyatakan bahwa dana pinjaman telah berhasil </br> dicairkan kepada peminjam
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>

                    <button class="btn btn-primary" type="submit" id="buttonSubmitEdit" disabled>
                        save <div id="loadingTextEdit"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- modal image --}}
<div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel"
    aria-hidden="true" style="display: none;" id="modalImage">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel">Detail Photo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>

            <div class="modal-body">
                <div class="text-center align-middle">
                    <img id="imagepreviewDetail" class="image-preview" src="" style="width: 100%">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/libs/chartist/chartist.min.js') }}"></script>
<script src="{{ asset('assets/libs/chartist/chartist-plugin-tooltip.min.js') }}"></script>

<script>
    let disbursementID;

    const changeButton = () => {
        if ($('#checkbox04').is(':checked')) {
            $('#buttonSubmitEdit').removeAttr('disabled');
        } else {
            $('#buttonSubmitEdit').attr('disabled', true);
        }
    };

    const resetDisbursementForm = () => {
        $('#checkbox04').prop('checked', false);
        $('#transfer_date').val('');
        $('#transfer_proof').val('');
        $('#buttonSubmitEdit').attr('disabled', true);
    };

    const saveDisbursement = () => {
        let formData = new FormData();

        formData.append('_token', "{{ csrf_token() }}");
        formData.append('id', disbursementID);
        formData.append('transfer_date', $('#transfer_date').val());

        let transferProof = $('#transfer_proof')[0].files[0];

        if (transferProof) {
            formData.append('transfer_proof', transferProof);
        }

        $.ajax({
            url: '{!! route('internal.tomi-dana.disbursement.action') !!}',
            data: formData,
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#buttonSubmitEdit').attr("disabled", true);
                $('#loadingTextEdit').addClass("spinner-border");
            },
            success: function (submitRes) {
                toastr.success(submitRes.message);

                table.ajax.reload(null, false);
                perbaruiData();
                $("#modalEdit").modal('hide');

                // reset form juga menonaktifkan tombol sampai checkbox dicentang lagi
                resetDisbursementForm();
                $('#loadingTextEdit').removeClass("spinner-border");
            },
            error: function (submitRes) {
                $('#buttonSubmitEdit').attr("disabled", false);
                $('#loadingTextEdit').removeClass("spinner-border");

                let message = 'Gagal menyimpan data transfer';

                if (submitRes.responseJSON && submitRes.responseJSON.message) {
                    message = submitRes.responseJSON.message;
                }

                toastr.error(message);
            }
        });
    };

    const edit = (id) => {
        disbursementID = id;
        resetDisbursementForm();

        $.ajax({
            url: '{!! route('internal.tomi-dana.disbursement.detail') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': id,
            },
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                $("#modalEdit").modal('show');
                $("#id_pinjaman").text(res.id_pinjaman);
                $("#bank").text(res.bank);
                $("#atas_nama").text(res.atas_nama);
                $("#norek").text(res.rekening);
                $("#disbursement").text(res.disbursement);
            },
            error: function (res) {
                let message = 'Gagal mengambil detail pinjaman';

                if (res.responseJSON && res.responseJSON.message) {
                    message = res.responseJSON.message;
                }

                toastr.error(message);
            }
        });
    };

    const documentPK = (id) => {
        $.ajax({
            url: '{!! route('internal.tomi-dana.disbursement.pk') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
                'id': id,
            },
            type: 'POST',
            xhrFields: {
                responseType: 'blob'
            },
            success: function (res, status, xhr) {
                const contentType = xhr.getResponseHeader("Content-Type");

                if (!contentType || !contentType.includes("application/pdf")) {
                    toastr.error('Dokumen PK tidak valid.');
                    return;
                }

                const url = window.URL.createObjectURL(new Blob([res], {
                    type: 'application/pdf'
                }));
                window.open(url, '_blank');

                setTimeout(() => {
                    window.URL.revokeObjectURL(url);
                }, 10000);
            },
            error: function (xhr) {
                toastr.error(xhr.status === 404 ? 'Dokumen PK tidak ditemukan.' : 'Gagal menampilkan dokumen PK.');
            }
        });
    };

    const showImage = (url, id, notFoundMessage) => {
        $.ajax({
            url: url,
            data: {
                '_token': "{{ csrf_token() }}",
                'id': id,
            },
            type: 'POST',
            xhrFields: {
                responseType: 'blob'
            },
            success: function (res) {
                const blob = new Blob([res], {
                    type: res.type || 'image/jpeg'
                });

                $('#imagepreviewDetail').attr('src', window.URL.createObjectURL(blob));
                $('#modalImage').modal('show');
            },
            error: function (xhr) {
                toastr.error(xhr.status === 404 ? notFoundMessage : 'Gagal menampilkan gambar. Silakan coba lagi.');
            }
        });
    };

    const ktp = (id) => showImage('{!! route('internal.tomi-dana.disbursement.ktp') !!}', id, 'Gambar KTP tidak ditemukan.');
    const selfie = (id) => showImage('{!! route('internal.tomi-dana.disbursement.selfie') !!}', id, 'Gambar Selfie tidak ditemukan.');

    $('#modalImage').on('hidden.bs.modal', function () {
        const src = $('#imagepreviewDetail').attr('src');
        if (src) {
            window.URL.revokeObjectURL(src);
        }
        $('#imagepreviewDetail').attr('src', '');
    });

    var dataTable = {
        "_token": "{{ csrf_token() }}",
    };

    // Kolom tombol dokumen (PK, KTP, Selfie) dan action tidak ikut diekspor.
    const exportColumns = [0, 1, 5, 6, 7, 8, 9, 10, 11, 12, 13];

    var table = $('#tableDisbursement').DataTable({
        dom: 'B<"clear">lf<"table-scroll"t>rip',
        buttons: [{
            extend: 'excel',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
            header: true,
            title: 'List Disbursment',
            exportOptions: {
                columns: exportColumns
            },
        },
        {
            extend: 'copy',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
            header: true,
            title: 'List Disbursment',
            exportOptions: {
                columns: exportColumns
            },
        },
        {
            extend: 'print',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
            header: true,
            title: 'List Disbursment',
            exportOptions: {
                columns: exportColumns
            },
        }
        ],
        "lengthMenu": [10, 50, 100, 500, 1000],
        processing: true,
        serverSide: true,
        order: [[0, 'asc']],
        language: {
            processing: '<i class="dripicons-loading">autorenew</i>'
        },
        ajax: {
            "type": "POST",
            "url": '{!! route('internal.tomi-dana.disbursement.table') !!}',
            "data": function (xx) {
                return $.extend(xx, dataTable);
            },
        },
        columns: [
            { data: 'datetime', name: 'created_at', searchable: false },
            { data: 'prioritas', name: 'created_at', searchable: false, orderable: false },
            { data: 'doc_pk', searchable: false, orderable: false },
            { data: 'ktp', searchable: false, orderable: false },
            { data: 'selfie', searchable: false, orderable: false },
            { data: 'id_pinjaman', name: 'id_pinjaman' },
            { data: 'lender', name: 'lender.nama', orderable: false },
            { data: 'nama_perusahaan', name: 'data_karyawan.perusahaan.nama', orderable: false },
            { data: 'nama_karyawan', name: 'data_karyawan.nama_karyawan', orderable: false },
            { data: 'total_pinjaman', name: 'nominal', searchable: false },
            { data: 'jumlah_disbursement', name: 'jumlah_yang_diterima', searchable: false },
            { data: 'bank', name: 'data_karyawan.bank.code_bank', orderable: false },
            { data: 'no_rek', name: 'data_karyawan.nomor_bank', orderable: false },
            { data: 'atas_nama', name: 'data_karyawan.nama_rekening', orderable: false },
            { data: 'action', searchable: false, orderable: false }
        ]
    });

    const perbaruiData = () => {
        $.ajax({
            url: '{!! route('internal.tomi-dana.disbursement.utils') !!}',
            data: {
                '_token': "{{ csrf_token() }}",
            },
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                const maxVal = Math.max(...res.series.flat(), 0);

                new Chartist.Line(
                    "#chart-with-area", {
                    labels: res.labels,
                    series: res.series,
                }, {
                    low: 0,
                    high: maxVal > 0 ? maxVal * 1.2 : 100,
                    showArea: true,
                    axisY: {
                        labelInterpolationFnc: function (value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        },
                        offset: 80
                    },
                    plugins: [
                        Chartist.plugins.tooltip({
                            transformTooltipTextFnc: function (value) {
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            }
                        })
                    ]
                }
                );

                $('#disbursementPending').text(res.totalDisbursementPending);
                $('#totalDisbursementTotal').text(res.total_disburement);
                $('#sudahdicairkan').text(res.disbursmentHariIni);
            }
        });
    };

    $(document).ready(function () {
        $('#addFrom').on('submit', function (e) {
            e.preventDefault();
            saveDisbursement();
        });

        $('#modalEdit').on('hidden.bs.modal', function () {
            resetDisbursementForm();
        });

        perbaruiData();
    });
</script>
@endpush
