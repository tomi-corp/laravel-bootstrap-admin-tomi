@extends('layout.app')
@section('title', 'Daily Checkin')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">Daily Checkin</li>
                    </ol>
                </div>
                <h4 class="page-title">Daily Checkin</h4>
            </div>
        </div>


        <div class="col-12">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4>Konfigurasi Poin Harian
                            </h4>
                            <small class="text-muted">Kelola pengaturan poin harian untuk program reward. Tentukan
                                berapa banyak poin yang
                                bisa didapatkan pengguna setiap kali mereka melakukan aktivitas harian yang
                                ditentukan.</small>
                            <div class="pt-2">
                                @php
                                    $xxy = 1;
                                @endphp
                                @foreach ($daily as $harian)
                                    <div class="form-group">
                                        <label>Hari ke-
                                            {{ $xxy }}
                                        </label>
                                        <input class="form-control" placeholder="Hari ke- {{ $xxy }}"
                                            value="{{ $harian->reward }}" type="number" id="daily{{ $xxy }}"
                                            required />
                                        <small class="text-danger" id="errDaily{{ $xxy }}"></small>
                                    </div>
                                    @php
                                        $xxy++;
                                    @endphp
                                @endforeach

                            </div>
                            <button class="btn btn-primary float-right" type="button" id="buttonSubmit"
                                onclick="saveReward()">Save
                                <div id="loadingText">
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h4>Laporan Aktivitas Daily Check-in
                            </h4>
                            <small class="text-muted">Menampilkan daftar pengguna yang telah berhasil
                                melakukan daily check-in. Anda bisa melihat status check-in harian mereka, termasuk waktu
                                check-in dan informasi terkait lainnya, untuk memantau partisipasi dan memberikan reward
                                yang sesuai.</small>

                            <table id="tableDailyCheckin"
                                class="table dt-responsive table-hover mt-4 text-center justify-center" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>DateTime</th>
                                        <th>Name</th>
                                        <th>Ticket</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
@push('js')
    <script>
        var dataTable = {
            "_token": "{{ csrf_token() }}",
        };
        var table = $('#tableDailyCheckin').DataTable({
            dom: 'B<"clear">lfrtip',
            buttons: [{
                    extend: 'excel',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                    header: true,
                    title: 'List Daily Checkin',
                    exportOptions: {
                        columns: [0, 1, 2]
                    },
                },
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                    header: true,
                    title: 'List Daily Checkin',
                    exportOptions: {
                        columns: [0, 1, 2]
                    },
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                    header: true,
                    title: 'List Daily Checkin',
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
                "url": '{!! route('admin.daily.table') !!}',
                "data": function(xx) {
                    return $.extend(xx, dataTable);
                },
            },
            columns: [{
                data: 'datetime'
            }, {
                data: 'name'
            }, {
                data: 'ticket'
            }]
        });

        saveReward = () => {
            $.ajax({
                url: '{!! route('admin.daily.update') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'hari1': $('#daily1').val(),
                    'hari2': $('#daily2').val(),
                    'hari3': $('#daily3').val(),
                    'hari4': $('#daily4').val(),
                    'hari5': $('#daily5').val(),
                    'hari6': $('#daily6').val(),
                    'hari7': $('#daily7').val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmit').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                    $('#errDaily1').text("");
                    $('#errDaily2').text("");
                    $('#errDaily3').text("");
                    $('#errDaily4').text("");
                    $('#errDaily5').text("");
                    $('#errDaily6').text("");
                    $('#errDaily7').text("");

                },
                success: function(approve) {
                    toastr.success(approve.message)
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                },
                error: function(err) {
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                    $('#errDaily1').text(err.responseJSON.errors.hari1);
                    $('#errDaily2').text(err.responseJSON.errors.hari2);
                    $('#errDaily3').text(err.responseJSON.errors.hari3);
                    $('#errDaily4').text(err.responseJSON.errors.hari4);
                    $('#errDaily5').text(err.responseJSON.errors.hari5);
                    $('#errDaily6').text(err.responseJSON.errors.hari6);
                    $('#errDaily7').text(err.responseJSON.errors.hari7);
                }
            });
        }
    </script>
@endpush
