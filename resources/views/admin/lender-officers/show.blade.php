@extends('layout.app')
@section('title', 'Detail Pengurus Lender')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.lender-officers.index') }}">Pengurus Lender</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
            <h4 class="page-title">Detail Pengurus Lender</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="text-right mb-3">
                    <a href="{{ route('admin.lender-officers.edit', $officer) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('admin.lender-officers.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr><th>Lender</th><td>{{ $officer->lender?->nama ?? '-' }}</td></tr>
                            <tr><th>Jabatan</th><td>{{ $positions[$officer->position] ?? $officer->position }}</td></tr>
                            <tr><th>Nama</th><td>{{ trim($officer->first_name.' '.$officer->last_name) }}</td></tr>
                            <tr><th>NIK</th><td>{{ $officer->nik }}</td></tr>
                            <tr><th>Tempat/Tanggal Lahir</th><td>{{ $officer->place_of_birth ?? '-' }} / {{ optional($officer->date_of_birth)->format('Y-m-d') ?? '-' }}</td></tr>
                            <tr><th>Gender</th><td>{{ $officer->gender === 'male' ? 'Laki-laki' : ($officer->gender === 'female' ? 'Perempuan' : '-') }}</td></tr>
                            <tr><th>Telepon</th><td>{{ $officer->phone_number ?? '-' }}</td></tr>
                            <tr><th>Email</th><td>{{ $officer->email ?? '-' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr><th>Alamat KTP</th><td>{{ $officer->ktp_address ?? '-' }}</td></tr>
                            <tr><th>RT/RW</th><td>{{ $officer->rt ?? '-' }} / {{ $officer->rw ?? '-' }}</td></tr>
                            <tr><th>Provinsi</th><td>{{ $officer->province?->provinsi ?? '-' }}</td></tr>
                            <tr><th>Kota/Kabupaten</th><td>{{ $officer->city?->kabupaten ?? '-' }}</td></tr>
                            <tr><th>Kecamatan</th><td>{{ $officer->district?->kecamatan ?? '-' }}</td></tr>
                            <tr><th>Kelurahan/Desa</th><td>{{ $officer->village?->desa ?? '-' }}</td></tr>
                            <tr><th>Kode Pos</th><td>{{ $officer->postal_code ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>

                <table class="table table-bordered">
                    <tr><th style="width: 25%">Mulai Menjabat</th><td>{{ optional($officer->started_at)->format('Y-m-d') ?? '-' }}</td></tr>
                    <tr><th>Akhir Menjabat</th><td>{{ optional($officer->ended_at)->format('Y-m-d') ?? '-' }}</td></tr>
                    <tr><th>No. SK/Dokumen Pengangkatan</th><td>{{ $officer->appointment_reference_number ?? '-' }}</td></tr>
                    <tr>
                        <th>File KTP</th>
                        <td>
                            @if ($officer->ktp_file_path)
                                <a href="{{ Storage::disk('public')->url($officer->ktp_file_path) }}" target="_blank">Lihat file</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr><th>Catatan</th><td>{{ $officer->notes ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
