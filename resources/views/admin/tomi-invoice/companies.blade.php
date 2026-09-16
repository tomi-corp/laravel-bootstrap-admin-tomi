@extends('layout.app')
@section('title', 'Perusahaan TomiInvoice')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Perusahaan TomiInvoice</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Perusahaan Terdaftar</h4>
                @if(!empty($selectedLender))
                    <p class="text-muted mb-3">
                        Lender: <strong>{{ $selectedLender['nama'] }}</strong>
                        @if($selectedLender['kode'] !== '-')
                            ({{ $selectedLender['kode'] }})
                        @endif
                    </p>
                @endif
                <table class="table table-bordered table-hover dt-responsive nowrap" id="invoiceCompanyTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>PIC</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Max Plafon</th>
                            <th>Rate</th>
                            <th>Admin Fee</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perusahaans ?? [] as $perusahaan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $perusahaan['nama'] }}</td>
                            <td>{{ $perusahaan['person_in_charge'] }}</td>
                            <td>{{ $perusahaan['email'] }}</td>
                            <td>{{ $perusahaan['phone'] }}</td>
                            <td>Rp {{ number_format($perusahaan['max_plafon'] ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($perusahaan['rate'] ?? 0, 2, ',', '.') }}%</td>
                            <td>Rp {{ number_format($perusahaan['admin_fee'] ?? 0, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $perusahaan['status'] === 'active' ? 'badge-success' : 'badge-warning' }}">
                                    {{ strtoupper($perusahaan['status']) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.invoice-financing.index', request()->filled('lender_id') ? ['lender_id' => request('lender_id')] : []) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada perusahaan terdaftar.</td>
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
    $('#invoiceCompanyTable').DataTable({
        responsive: true,
        pageLength: 10
    });
</script>
@endpush
