@extends('layout.app')
@section('title', 'Perusahaan TomiGaji')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Perusahaan TomiGaji</h4>
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
                <div class="table-responsive">
                <table class="table table-bordered table-hover nowrap" id="salaryCompanyTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>PIC</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Max Plafon</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perusahaans ?? [] as $perusahaan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $perusahaan['nama'] }}</td>
                            <td>{{ $perusahaan['pic_perusahaan'] }}</td>
                            <td>{{ $perusahaan['email_perusahaan'] }}</td>
                            <td>{{ $perusahaan['telp'] }}</td>
                            <td>Rp {{ number_format($perusahaan['max_plafon'] ?? 0, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $perusahaan['status'] === 'active' ? 'badge-success' : 'badge-warning' }}">
                                    {{ strtoupper($perusahaan['status']) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.salary-financing.show', $perusahaan['id']) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada perusahaan terdaftar.</td>
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
    $('#salaryCompanyTable').DataTable({
        scrollX: true,
        responsive: false,
        pageLength: 10
    });
</script>
@endpush
