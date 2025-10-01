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
        <div class="col-md-4">
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
                        <label class="form-label">Versi Major</label>
                        <input type="text" name="versionMajor" class="form-control" placeholder="1.0.3" required
                            id="versionMajor">
                        <small class="text-danger" id="errVersionMajor"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Versi Minor</label>
                        <input type="text" name="versionMinor" class="form-control" placeholder="1.0.3" required
                            id="versionMinor">
                        <small class="text-danger" id="errVersionMinor"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Changelog</label>
                        <textarea name="changelog" class="form-control" rows="3" id="changelog"></textarea>
                        <small class="text-danger" id="errChangelog"></small>
                    </div>

                    <button class="btn btn-primary float-right" type="button" id="buttonSubmit">
                        Save
                        <div id="loadingText"></div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Daftar Update OTA -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4>Daftar Update OTA</h4>
                    <table id="tableOta" class="table dt-responsive table-hover mt-4 text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Platform</th>
                                <th>Version Major</th>
                                <th>Version Minor</th>
                                <th>Changelog</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
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
                    <h5 class="modal-title" id="exampleModalCenterTitle">Delete OTA
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="text-center align-middle">
                        <p>Apakah anda yakin menghapus OTA ini?</p>
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
        let table;
        let deleteID;

        $(document).ready(function() {
            // Init DataTable
            table = $('#tableOta').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        title: 'Daftar OTA Mobile',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        title: 'Daftar OTA Mobile',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        title: 'Daftar OTA Mobile',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
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
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columns: [{
                        data: 'created_at'
                    },
                    {
                        data: 'platform'
                    },
                    {
                        data: 'versionMajor'
                    },
                    {
                        data: 'versionMinor'
                    },
                    {
                        data: 'changelog'
                    }, {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        // Submit Form Upload
        $('#buttonSubmit').on('click', function() {
            let formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('platform', $("#plafrom").val());
            formData.append('versionMajor', $("#versionMajor").val());
            formData.append('versionMinor', $("#versionMinor").val());
            formData.append('changelog', $("#changelog").val());

            $.ajax({
                url: '{!! route('admin.ota.upload') !!}',
                data: formData,
                type: 'POST',
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#buttonSubmit').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                    $("#progressWrapper").show();
                    $("#progressBar").css("width", "0%").text("0%");
                },
                success: function(res) {
                    toastr.success(res.message);
                    // reload datatable
                    table.ajax.reload();
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");

                    // reset form
                    $('#plafrom').val('');
                    $('#versionMajor').val('');
                    $('#versionMinor').val('');
                    $('#otaUpdateID').val('');
                    $('#changelog').val('');
                    $('#errVersionMajor').text('');
                    $('#errVersionMinor').text('');
                },
                error: function(err) {
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");

                    let errors = err.responseJSON.errors;
                    $('#errPlatform').text(errors?.platform ?? '');
                    $('#errVersionMajor').text(errors?.versionMajor ?? '');
                    $('#errVersionMinor').text(errors?.versionMinor ?? '');
                    $('#errChangelog').text(errors?.changelog ?? '');
                }
            });
        });

        deleteOta = (id) => {
            deleteID = id;
            $('#modalDelete').modal('show');
        }

        buttonDelete = () => {
            $.ajax({
                url: '{!! route('admin.ota.delete') !!}',
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
