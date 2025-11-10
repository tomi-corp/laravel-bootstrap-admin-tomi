@extends('layout.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">List Invoicing Perusahan</li>
                    </ol>
                </div>
                <h4 class="page-title">List Invoicing Perusahan</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">List Invocing Perusahaan</div>


                    <table id="tableDisbursement" class="table dt-responsive table-hover mt-4 text-center justify-center"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>ID kode Perusahaan</th>
                                <th>Nama Perusahaan</th>
                                <th>Status</th>
                                <th>Jatuh Tempo</th>
                                <th>Total Tagihan</th>
                                <th>Jumlah Tagihan</th>
                                <th>Status Invoice</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>


        </div>
    </div>



    @push('js')
        <script>
            var dataTable = {
                "_token": "{{ csrf_token() }}",
            };
            var table = $('#tableDisbursement').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                    extend: 'excel',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                    header: true,
                    title: 'List Repayment',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    },
                }, {
                    extend: 'copy',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                    header: true,
                    title: 'List Repayment',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    },
                }, {
                    extend: 'print',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                    header: true,
                    title: 'List Repayment',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    },
                }],
                "lengthMenu": [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
                processing: true,
                responsive: true,
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('admin.ksp.perusahaan.invoicing.table') !!}',
                    "data": function(xx) {
                        return $.extend(xx, dataTable);
                    },
                },
                columns: [{
                    data: 'id_code_perusahaan'
                }, {
                    data: 'nama_perusahaan'
                }, {
                    data: 'prioritas'
                }, {
                    data: 'tanggal_period'
                }, {
                    data: 'total_tagihan'
                }, {
                    data: 'total_pinjaman'
                }, {
                    data: 'status'
                }]
            });
        </script>
    @endpush
@endsection
