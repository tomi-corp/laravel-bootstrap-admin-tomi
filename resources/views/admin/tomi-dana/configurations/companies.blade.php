@extends('layout.app')

@section('title', 'Konfigurasi TomiDana')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">TomiDana</li>
                    <li class="breadcrumb-item active">Konfigurasi</li>
                </ol>
            </div>
            <h4 class="page-title">Konfigurasi TomiDana</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Daftar Perusahaan</h5>
                <p class="text-muted">Pilih perusahaan untuk melihat dan mengatur konfigurasi pinjaman.</p>

                <div>
                    <table id="tableKonfigurasiTomiDana" class="table dt-responsive table-hover mt-4 text-center justify-center" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nama Perusahaan</th>
                                <th>PIC Perusahaan</th>
                                <th>Lender</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perusahaans as $perusahaan)
                            <tr>
                                <td>{{ strtoupper($perusahaan->nama ?? '-') }}</td>
                                <td>{{ strtoupper($perusahaan->pic_perusahaan ?? '-') }}</td>
                                <td>{{ strtoupper($perusahaan->lender->nama ?? '-') }}</td>
                                <td>
                                    @if ($perusahaan->status === 'active')
                                    <span class="badge badge-success">ACTIVE</span>
                                    @else
                                    <span class="badge badge-warning">NONACTIVE</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.tomi-dana.configurations.index', encryptSecure($perusahaan->id)) }}" class="btn btn-sm btn-primary mb-1">
                                        <i class="mdi mdi-tune-variant"></i> Konfigurasi
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
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
        $('#tableKonfigurasiTomiDana').DataTable({
            dom: 'B<"clear">lfrtip',
            buttons: [
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                    header: true,
                    title: 'Konfigurasi TomiDana',
                    exportOptions: { columns: [0, 1, 2, 3] }
                },
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                    header: true,
                    title: 'Konfigurasi TomiDana',
                    exportOptions: { columns: [0, 1, 2, 3] }
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
                    text: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
                    header: true,
                    title: 'Konfigurasi TomiDana',
                    exportOptions: { columns: [0, 1, 2, 3] }
                }
            ],
            lengthMenu: [10, 50, 100, 500, 1000, 2000, 5000, 10000, 50000, 100000],
            responsive: false,
            scrollX: true,
            order: [[0, 'asc']],
            language: {
                search: 'Cari:',
                emptyTable: 'Belum ada data perusahaan.',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ perusahaan',
                infoEmpty: 'Tidak ada perusahaan',
                zeroRecords: 'Perusahaan tidak ditemukan',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
            },
            columnDefs: [{ targets: 4, orderable: false, searchable: false }]
        });
    });
</script>
@endpush
