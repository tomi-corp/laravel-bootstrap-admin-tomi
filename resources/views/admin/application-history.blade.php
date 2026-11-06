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
            <table class="table table-hover mb-0">
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
                <tbody>
                    @forelse ($pengajuan as $item)
                        <tr>
                            <td>{{ optional($item->created_at)->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>{{ $item->id_pinjaman ?? $item->id }}</td>
                            <td>{{ strtoupper($item->data_karyawan?->nama_karyawan ?? '-') }}</td>
                            <td>{{ strtoupper($item->perusahaan?->nama ?? '-') }}</td>
                            <td>Rp. {{ number_format((float) ($item->nominal ?? 0), 0, ',', '.') }}</td>
                            <td>{{ strtoupper($item->status_otp ?? '-') }}</td>
                            <td>{{ strtoupper($item->ttd ?? '-') }}</td>
                            <td>{{ strtoupper(str_replace('_', ' ', $item->status ?? '-')) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data pengajuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $pengajuan->links() }}
        </div>
    </div>
</div>
@endsection
