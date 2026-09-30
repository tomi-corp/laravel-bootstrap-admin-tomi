@extends('layout.app')

@section('title', 'Konfigurasi Tenor ' . $productLabel)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item">{{ $productLabel }}</li>
                    <li class="breadcrumb-item active">Konfigurasi Tenor</li>
                </ol>
            </div>
            <h4 class="page-title">Konfigurasi Tenor {{ $productLabel }}</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="tenorCompaniesTable" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>Perusahaan</th>
                            <th>Kode</th>
                            <th>Jumlah Tenor</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                            <tr>
                                <td>{{ strtoupper($company->nama) }}</td>
                                <td>{{ $company->id_code_perusahaan ?? '-' }}</td>
                                <td>{{ number_format($company->tenor_pinjaman_count, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-{{ $company->tenor_pinjaman_count > 0 ? 'success' : 'warning' }}">
                                        {{ $company->tenor_pinjaman_count > 0 ? 'Sudah Diatur' : 'Belum Diatur' }}
                                    </span>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.product-configurations.tenor.edit', [$product, $company]) }}">
                                        <i class="fa-solid fa-gear"></i> Konfigurasi
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
@endsection

@push('js')
<script>
    $('#tenorCompaniesTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        language: {
            emptyTable: 'Belum ada perusahaan.',
            zeroRecords: 'Perusahaan tidak ditemukan.',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            paginate: {
                previous: 'Sebelumnya',
                next: 'Berikutnya'
            }
        }
    });
</script>
@endpush
