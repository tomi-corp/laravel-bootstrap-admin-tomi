@extends('layout.app')

@section('title', 'Seluruh Pinjaman TomiDana')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">TomiDana</li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tomi-dana.lender') }}">Lender</a></li>
                    <li class="breadcrumb-item active">Seluruh Pinjaman</li>
                </ol>
            </div>
            <h4 class="page-title">{{ $lender ? 'Pinjaman Lender ' . strtoupper($lender->nama) : 'Seluruh Pinjaman TomiDana' }}</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <a href="{{ route('admin.tomi-dana.lender') }}" class="btn btn-sm btn-secondary float-right mb-3">
            <i class="mdi mdi-arrow-left"></i> Kembali ke Lender
        </a>
        <h4 class="header-title">Daftar Seluruh Pinjaman</h4>
        <p class="text-muted">{{ $lender ? 'Menampilkan seluruh pinjaman lender yang dipilih.' : 'Menampilkan seluruh pinjaman yang tercatat pada TomiDana.' }}</p>
        <div class="table-responsive">
            <table id="tableLenderLoans" class="table table-hover table-striped text-center" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Pinjaman</th>
                        <th>Nama Karyawan</th>
                        <th>Perusahaan</th>
                        <th>Lender</th>
                        <th>Nominal</th>
                        <th>Tenor</th>
                        <th>Status</th>
                        <th>Tanggal Pengajuan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function () {
        $('#tableLenderLoans').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route('admin.tomi-dana.lender.pinjaman.table') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    lender_id: @json(request('lender_id'))
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'id_pinjaman', name: 'id' },
                { data: 'nama_karyawan', name: 'data_karyawan.nama_karyawan' },
                { data: 'perusahaan', name: 'perusahaan.nama' },
                { data: 'lender', name: 'lender.nama' },
                { data: 'nominal', name: 'nominal' },
                { data: 'tenor_label', name: 'tenor' },
                { data: 'status_label', name: 'status' },
                { data: 'tanggal_pengajuan', name: 'created_at' }
            ],
            order: [[8, 'desc']]
        });
    });
</script>
@endpush
