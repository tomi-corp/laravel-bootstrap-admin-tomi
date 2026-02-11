@extends('bta.layout.app')
@section('title', 'Nasabah')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">BTA</a></li>
                        <li class="breadcrumb-item active">Analyst</li>
                    </ol>
                </div>
                <h4 class="page-title">Review Analyst</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Analyst -->
                    <table id="tableAnalyst" class="table dt-responsive table-hover mt-4 text-center justify-center"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th data-priority="1">No. KTP</th>
                                <th data-priority="1">Nama Lengkap</th>
                                <th data-priority="2">Tanggal Lahir</th>
                                <th data-priority="3">Jenis Kelamin</th>
                                <th data-priority="1">No. Telepon</th>
                                <th data-priority="4">Status Perkawinan</th>
                                <th data-priority="2">Perusahaan</th>
                                <th data-priority="3">Status Pegawai</th>
                                <th data-priority="1">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>


        {{-- <div class="col-12">
            <div class="card-box">
                <h4 class="header-title mb-4">Tabs Vertical Left</h4>

                <div class="row">
                    <div class="col-sm-3">
                        <div class="nav flex-column nav-pills nav-pills-tab" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <a class="nav-link active show mb-2" id="v-pills-home-tab" data-toggle="pill"
                                href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                                Home</a>
                            <a class="nav-link mb-2" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile"
                                role="tab" aria-controls="v-pills-profile" aria-selected="false">
                                Profile</a>
                            <a class="nav-link mb-2" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages"
                                role="tab" aria-controls="v-pills-messages" aria-selected="false">
                                Messages</a>
                            <a class="nav-link mb-2" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings"
                                role="tab" aria-controls="v-pills-settings" aria-selected="false">
                                Settings</a>
                        </div>
                    </div> <!-- end col-->
                    <div class="col-sm-9">
                        <div class="tab-content pt-0">
                            <div class="tab-pane fade active show" id="v-pills-home" role="tabpanel"
                                aria-labelledby="v-pills-home-tab">
                                <p>Cillum ad ut irure tempor velit nostrud occaecat ullamco aliqua anim Lorem sint.
                                    Veniam sint duis incididunt
                                    do esse magna mollit excepteur laborum qui. Id id reprehenderit sit est eu
                                    aliqua occaecat quis et velit
                                    excepteur laborum mollit dolore eiusmod. Ipsum dolor in occaecat commodo et
                                    voluptate minim reprehenderit
                                    mollit pariatur. Deserunt non laborum enim et cillum eu deserunt excepteur ea
                                    incididunt minim occaecat.</p>
                            </div>
                            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                aria-labelledby="v-pills-profile-tab">
                                <p>Culpa dolor voluptate do laboris laboris irure reprehenderit id incididunt duis
                                    pariatur mollit aute magna
                                    pariatur consectetur. Eu veniam duis non ut dolor deserunt commodo et minim in
                                    quis laboris ipsum velit
                                    id veniam. Quis ut consectetur adipisicing officia excepteur non sit. Ut et elit
                                    aliquip labore Lorem
                                    enim eu. Ullamco mollit occaecat dolore ipsum id officia mollit qui esse anim
                                    eiusmod do sint minim consectetur
                                    qui.</p>
                            </div>
                            <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                                aria-labelledby="v-pills-messages-tab">
                                <p>Fugiat id quis dolor culpa eiusmod anim velit excepteur proident dolor aute qui
                                    magna. Ad proident laboris
                                    ullamco esse anim Lorem Lorem veniam quis Lorem irure occaecat velit nostrud
                                    magna nulla. Velit et et
                                    proident Lorem do ea tempor officia dolor. Reprehenderit Lorem aliquip labore
                                    est magna commodo est ea
                                    veniam consectetur.</p>
                            </div>
                            <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                                aria-labelledby="v-pills-settings-tab">
                                <p>Eu dolore ea ullamco dolore Lorem id cupidatat excepteur reprehenderit
                                    consectetur elit id dolor proident
                                    in cupidatat officia. Voluptate excepteur commodo labore nisi cillum duis aliqua
                                    do. Aliqua amet qui
                                    mollit consectetur nulla mollit velit aliqua veniam nisi id do Lorem deserunt
                                    amet. Culpa ullamco sit
                                    adipisicing labore officia magna elit nisi in aute tempor commodo eiusmod.</p>
                            </div>
                        </div>
                    </div> <!-- end col-->
                </div> <!-- end row-->



            </div>
        </div> --}}


    </div>



@endsection

@push('js')
    <script>
        $(document).ready(function() {

            var dataTable = {
                "_token": "{{ csrf_token() }}",
            };

            var table = $('#tableAnalyst').DataTable({
                dom: 'B<"clear">lfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7] // Hanya kolom data (tanpa Action)
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                        text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    }
                ],
                "lengthMenu": [10, 50, 100, 500, 1000],
                processing: true,
                responsive: true,
                serverSide: true,
                language: {
                    processing: '<i class="dripicons-loading">autorenew</i>'
                },
                ajax: {
                    "type": "POST",
                    "url": '{!! route('bta.analyst.table') !!}',
                    "data": function(d) {
                        return $.extend({}, d, dataTable);
                    },
                },
                columns: [{
                        data: 'no_ktp'
                    }, // No. KTP
                    {
                        data: 'nama_lengkap'
                    }, // Nama Lengkap
                    {
                        data: 'tanggal_lahir'
                    }, // Tanggal Lahir
                    {
                        data: 'jenis_kelamin'
                    }, // Jenis Kelamin
                    {
                        data: 'no_telp'
                    }, // No. Telepon
                    {
                        data: 'status_perkawinan'
                    }, // Status Perkawinan
                    {
                        data: 'nama_perusahaan'
                    }, // Perusahaan
                    {
                        data: 'status_pegawai'
                    }, // Status Pegawai
                    { // Action
                        data: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ]
            });
        });
    </script>
@endpush
