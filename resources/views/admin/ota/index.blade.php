@extends('layout.app')
@section('title', 'OTA Mobile')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Tomi</a></li>
                    <li class="breadcrumb-item active">Update OTA Mobile</li>
                </ol>
            </div>
            <h4 class="page-title">OTA Mobile</h4>
        </div>
    </div>
</div>

<div class="row">
    <!-- Form Upload -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4>Upload Update OTA</h4>
                <div class="mb-3">
                    <label class="form-label">Platform</label>
                    <select name="platform" class="form-control" required id="plafrom">
                        <option value="android">Android</option>
                        <option value="ios">iOS</option>
                    </select>
                    <small class="text-danger" id="errPlatform"></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Versi</label>
                    <input type="text" name="version" class="form-control" placeholder="misal: 1.0.3" required id="version">
                    <small class="text-danger" id="errVersion"></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Changelog</label>
                    <textarea name="changelog" class="form-control" rows="3" id="changelog"></textarea>
                    <small class="text-danger" id="errChangelog"></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">File OTA (.zip/.bundle)</label>
                    <input type="file" name="file" class="form-control" accept=".zip,.bundle" required id="fileota">
                    <small class="text-danger" id="errFileOTA"></small>
                </div>

                <!-- Progress bar -->
                <div class="mb-3" id="progressWrapper" style="display:none;">
                    <label class="form-label">Upload Progress</label>
                    <div class="progress">
                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                            role="progressbar" style="width: 0%">0%</div>
                    </div>
                </div>

                <button class="btn btn-primary float-right" type="button" id="buttonSubmit">
                    Save 
                    <div id="loadingText"></div>
                </button>
            </div>
        </div>
    </div>

    <!-- Daftar Update OTA -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4>Daftar Update OTA</h4>
                <table id="tableOta" class="table dt-responsive table-hover mt-4 text-center"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Platform</th>
                            <th>Versi</th>
                            <th>Changelog</th>
                            <th>File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
let table; 

$(document).ready(function () {
    // Init DataTable
    table = $('#tableOta').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                title: 'Daftar OTA Mobile',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            },
            {
                extend: 'copy',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                title: 'Daftar OTA Mobile',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                title: 'Daftar OTA Mobile',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            }
        ],
        lengthMenu: [10, 50, 100, 500, 1000],
        processing: true,
        responsive: true,
        serverSide: true,
        language: {
            processing: '<i class="dripicons-loading">autorenew</i>'
        },
        ajax: {
            type: "POST",
            url: '{!! route('admin.ota.table') !!}', // route server-side
            data: function (d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'created_at' },
            { data: 'platform' },
            { data: 'version' },
            { data: 'changelog' },
            { data: 'file', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
});

// Submit Form Upload
$('#buttonSubmit').on('click', function () {
    let formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('platform', $("#plafrom").val());
    formData.append('version', $("#version").val());
    formData.append('changelog', $("#changelog").val());
    let file = $('#fileota')[0].files[0];
    formData.append('file', file);

    $.ajax({
        url: '{!! route('admin.ota.upload') !!}',
        data: formData,
        type: 'POST',
        dataType: 'json',
        processData: false, 
        contentType: false,
        xhr: function () {
            let xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    let percentComplete = Math.round((evt.loaded / evt.total) * 100);
                    $("#progressWrapper").show();
                    $("#progressBar").css("width", percentComplete + "%").text(percentComplete + "%");
                }
            }, false);
            return xhr;
        },
        beforeSend: function () {
            $('#buttonSubmit').attr("disabled", true);
            $('#loadingText').addClass("spinner-border");
            $("#progressWrapper").show();
            $("#progressBar").css("width", "0%").text("0%");
        },
        success: function (res) {
            toastr.success(res.message);

            // reload datatable
            table.ajax.reload();

            $('#buttonSubmit').attr("disabled", false);
            $('#loadingText').removeClass("spinner-border");

            // reset form
            $('#plafrom').val('');
            $('#version').val('');
            $('#changelog').val('');
            $('#fileota').val('');
            $("#progressWrapper").hide();
        },
        error: function (err) {
            $('#buttonSubmit').attr("disabled", false);
            $('#loadingText').removeClass("spinner-border");
            $("#progressWrapper").hide();

            let errors = err.responseJSON.errors;
            $('#errPlatform').text(errors?.platform ?? '');
            $('#errVersion').text(errors?.version ?? '');
            $('#errChangelog').text(errors?.changelog ?? '');
            $('#errFileOTA').text(errors?.file ?? '');
        }
    });
});
</script>
@endpush
