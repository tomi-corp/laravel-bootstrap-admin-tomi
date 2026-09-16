@extends('layout.app')
@section('title', 'TomiInvoice')

@push('css')
<style>
    #tomiInvoiceTable .btn {
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                    <li class="breadcrumb-item active">TomiInvoice</li>
                </ol>
            </div>
            <h4 class="page-title">TomiInvoice</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="media">
                    <div class="media-body">
                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Total Perusahaan</span>
                        <h3 class="mb-0 mt-1">{{ $totalPerusahaan ?? 0 }}</h3>
                    </div>
                    <div class="avatar-sm rounded-circle bg-primary align-self-center">
                        <span class="avatar-title rounded-circle bg-primary">
                            <i class="mdi mdi-office-building font-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="media">
                    <div class="media-body">
                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Total Invoice</span>
                        <h3 class="mb-0 mt-1">{{ number_format($totalInvoice ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="avatar-sm rounded-circle bg-info align-self-center">
                        <span class="avatar-title rounded-circle bg-info">
                            <i class="mdi mdi-file-document-outline font-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="media">
                    <div class="media-body">
                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Total Pinjaman</span>
                        <h3 class="mb-0 mt-1">Rp {{ number_format($totalPinjaman ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="avatar-sm rounded-circle bg-warning align-self-center">
                        <span class="avatar-title rounded-circle bg-warning">
                            <i class="mdi mdi-cash-multiple font-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="media">
                    <div class="media-body">
                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">Fee Tomi</span>
                        <h3 class="mb-0 mt-1">Rp {{ number_format($totalFee ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="avatar-sm rounded-circle bg-success align-self-center">
                        <span class="avatar-title rounded-circle bg-success">
                            <i class="mdi mdi-percent font-20"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Daftar TomiInvoice</h4>

                <table class="table table-bordered table-hover dt-responsive nowrap" id="tomiInvoiceTable"
                    style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Total Invoice</th>
                            <th>Total Pinjaman</th>
                            <th>Total Dibayarkan</th>
                            <th>Fee Tomi</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perusahaans ?? [] as $perusahaan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $perusahaan['nama'] }}</td>
                            <td>{{ number_format($perusahaan['total_invoice'] ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($perusahaan['total_pinjaman'] ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($perusahaan['total_dibayarkan'] ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($perusahaan['total_fee'] ?? 0, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.invoice-financing.show', $perusahaan['id']) }}"
                                    class="btn btn-sm btn-outline-info" title="Show">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data TomiInvoice.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $('#tomiInvoiceTable').DataTable({
        responsive: true,
        pageLength: 10,
        ordering: true,
        language: {
            emptyTable: 'Belum ada data TomiInvoice.',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 data',
            paginate: {
                previous: 'Sebelumnya',
                next: 'Berikutnya'
            }
        }
    });
</script>
@endpush
