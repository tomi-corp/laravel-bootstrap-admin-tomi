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

                <div class="table-responsive">
                    <table id="tableKonfigurasiTomiDana" class="table table-hover table-bordered mb-0" style="width:100%">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Perusahaan</th>
                                <th>PIC Perusahaan</th>
                                <th>Lender</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($perusahaans as $perusahaan)
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
                                    <a href="{{ route('admin.tomi-dana.configurations.index', encryptSecure($perusahaan->id)) }}" class="btn btn-sm btn-primary">
                                        <i class="mdi mdi-cog-outline"></i> Konfigurasi
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada data perusahaan.</td>
                            </tr>
                            @endforelse
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
            responsive: true,
            pageLength: 10,
            order: [[0, 'asc']],
            language: {
                search: 'Cari:',
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
