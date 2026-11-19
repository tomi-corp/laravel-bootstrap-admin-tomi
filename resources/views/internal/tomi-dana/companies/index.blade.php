@extends('layout.app')

@section('title', 'Perusahaan '.$feature->name)

@push('css')
    <style>
        #featureCompanyTable th,
        #featureCompanyTable td { white-space: nowrap; vertical-align: middle; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title mb-1">Perusahaan {{ $feature->name }}</h4>
                <p class="text-muted mb-0">Perusahaan yang memiliki PKS fitur {{ $feature->name }}. PKS dikelola dari menu Perusahaan.</p>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="featureCompanyTable" class="table table-hover nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Perusahaan</th>
                                    <th>Nama Perusahaan</th>
                                    <th>PIC Perusahaan</th>
                                    <th>PIC Internal</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Lender</th>
                                    <th>Nomor PKS</th>
                                    <th>Masa Berlaku PKS</th>
                                    <th>Status PKS</th>
                                    <th>Status Perusahaan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function () {
            $('#featureCompanyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: '{{ route('internal.tomi-dana.companies.table') }}', type: 'POST', data: { _token: '{{ csrf_token() }}' } },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'id_code_perusahaan', name: 'id_code_perusahaan' },
                    { data: 'nama', name: 'nama' },
                    { data: 'pic_perusahaan', name: 'pic_perusahaan' },
                    { data: 'pic_internal', name: 'pic_internal' },
                    { data: 'email_perusahaan', name: 'email_perusahaan' },
                    { data: 'telp', name: 'telp' },
                    { data: 'lender', name: 'lender', orderable: false },
                    { data: 'pks_number', name: 'pks_number', orderable: false },
                    { data: 'pks_period', name: 'pks_period', orderable: false, searchable: false },
                    { data: 'pks_status', name: 'pks_status', orderable: false, searchable: false },
                    { data: 'status', name: 'status' }
                ],
                order: [[2, 'asc']],
                language: {
                    processing: 'Memuat...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 - 0 dari 0 data',
                    infoFiltered: '(disaring dari _MAX_ total data)',
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Belum ada perusahaan dengan PKS {{ $feature->name }}',
                    paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
                }
            });
        });
    </script>
@endpush
