@extends('layout.app')
@section('title', 'OTA Mobile')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">Send Push Notification</li>
                    </ol>
                </div>
                <h4 class="page-title">Send Push Notification</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Send Notification</div>

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Title" required
                            id="title">
                        <small class="text-danger" id="errTitle"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Isi Pesan</label>
                        <textarea name="isiPesan" class="form-control" rows="3" id="isiPesan"></textarea>
                        <small class="text-danger" id="errisiPesan"></small>
                    </div>

                    <button class="btn btn-primary float-right" type="button" id="buttonSubmit">
                        Kirim
                        <div id="loadingText"></div>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">History Send Push Notification</div>
                    <table id="tableNotif" class="table dt-responsive table-hover mt-4 text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Title</th>
                                <th>Isi Pesan</th>
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
        let deleteID;

        // Submit Form Upload
        $('#buttonSubmit').on('click', function() {
            let formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('title', $("#title").val());
            formData.append('isiPesan', $("#isiPesan").val());

            $.ajax({
                url: '{!! route('admin.sendnotif.action') !!}',
                data: formData,
                type: 'POST',
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#buttonSubmit').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                },
                success: function(res) {
                    toastr.success(res.message);
                    // reload datatable
                    table.ajax.reload();
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");

                    // reset form
                    $('#title').val('');
                    $('#isiPesan').val('');
                },
                error: function(err) {
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");

                    let errors = err.responseJSON.errors;
                    $('#errTitle').text(errors?.title ?? '');
                    $('#errisiPesan').text(errors?.isiPesan ?? '');
                }
            });
        });



        $(document).ready(function() {
            // Init DataTable
            table = $('#tableNotif').DataTable({
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
                    url: '{!! route('admin.sendnotif.table') !!}', // route server-side
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columns: [{
                        data: 'created_at'
                    },
                    {
                        data: 'title'
                    },
                    {
                        data: 'isipesan'
                    }
                ]
            });
        });
    </script>
@endpush
