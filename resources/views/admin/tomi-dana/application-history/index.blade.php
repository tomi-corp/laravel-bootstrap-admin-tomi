@extends('layout.app')

@section('title', 'Riwayat Pengajuan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Riwayat Pengajuan</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="header-title mb-3">Daftar Pengajuan Pinjaman</h4>

        <div class="table-responsive">
            <table id="applicationHistoryTable" class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>ID Pengajuan</th>
                        <th>Peminjam</th>
                        <th>Perusahaan</th>
                        <th>Nominal</th>
                        <th>OTP</th>
                        <th>TTD</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
    $('#applicationHistoryTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        pageLength: 10,
        dom: 'B<"row"<"col-md-6"l><"col-md-6 text-right"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-purple mb-2 mr-1',
                text: '<span class="mdi mdi-content-copy mr-1"></span>Copy',
                title: 'Riwayat Pengajuan'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-purple mb-2 mr-1',
                text: '<span class="mdi mdi-file-excel mr-1"></span>Excel',
                title: 'Riwayat Pengajuan'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-purple mb-2 mr-1',
                text: '<span class="mdi mdi-printer mr-1"></span>Print',
                title: 'Riwayat Pengajuan'
            }
        ],
        ajax: '{{ route('admin.application-history.table') }}',
        columns: [
            { data: 'tanggal', name: 'created_at' },
            { data: 'id_pengajuan', name: 'id_pinjaman' },
            { data: 'peminjam', name: 'data_karyawan.nama_karyawan', orderable: false },
            { data: 'perusahaan', name: 'perusahaan.nama', orderable: false },
            { data: 'nominal', name: 'nominal', orderable: false, searchable: false },
            { data: 'otp', name: 'status_otp' },
            { data: 'ttd', name: 'ttd' },
            { data: 'status_label', name: 'status' }
        ],
        language: {
            search: 'Cari:',
            processing: '<i class="dripicons-loading"></i> Memuat data...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Belum ada data pengajuan',
            emptyTable: 'Belum ada data pengajuan',
            zeroRecords: 'Pengajuan tidak ditemukan',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
        }
    });
</script>
@endpush
