@extends('layout.app')
@section('title', 'Lender TomiGaji')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Lender TomiGaji</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Lender Terdaftar</h4>
                <table class="table table-bordered table-hover dt-responsive nowrap" id="salaryLenderTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Lender</th>
                            <th>PIC</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>No. PKS</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lenders ?? [] as $lender)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $lender['kode'] }}</td>
                            <td>{{ $lender['nama'] }}</td>
                            <td>{{ $lender['pic_lender'] }}</td>
                            <td>{{ $lender['email'] }}</td>
                            <td>{{ $lender['telp'] }}</td>
                            <td>{{ $lender['nomor_pks'] }}</td>
                            <td>
                                <span class="badge {{ $lender['status'] === 'active' ? 'badge-success' : 'badge-warning' }}">
                                    {{ strtoupper($lender['status']) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada lender terdaftar.</td>
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
    $('#salaryLenderTable').DataTable({
        responsive: true,
        pageLength: 10
    });
</script>
@endpush
