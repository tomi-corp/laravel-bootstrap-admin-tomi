@extends('layout.app')

@section('title', 'Admin Pencairan Dana')

@push('css')
<style>
    #disbursementsTable th,
    #disbursementsTable td {
        white-space: nowrap;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Pencairan Dana</li>
                </ol>
            </div>
            <h4 class="page-title">Pencairan Dana</h4>
            <p class="text-muted mb-0">Pinjaman yang belum dicairkan.</p>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table id="disbursementsTable" class="table table-hover text-center nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID Pinjaman</th>
                            <th>User</th>
                            <th>Perusahaan</th>
                            <th>Nominal</th>
                            <th>Diterima</th>
                            <th>Tenor</th>
                            <th>Status</th>
                            <th>Rekening</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            @php
                                $bankName = trim(($loan->data_karyawan?->bank?->code_bank ?? '-').' '.($loan->data_karyawan?->bank?->description ?? ''));
                                $accountName = $loan->data_karyawan?->nama_rekening ?? $loan->user?->name ?? '-';
                                $accountNumber = $loan->data_karyawan?->nomor_bank ?? '-';
                                $disbursementAmount = $loan->jumlah_yang_diterima ?? $loan->nominal;
                            @endphp
                            <tr>
                                <td>{{ $loan->id_pinjaman ?? '#'.$loan->id }}</td>
                                <td>{{ $loan->user?->name ?? $loan->user?->nama_lengkap ?? '-' }}</td>
                                <td>{{ $loan->perusahaan?->nama ?? '-' }}</td>
                                <td>Rp. {{ number_format($loan->nominal ?? 0, 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($disbursementAmount ?? 0, 0, ',', '.') }}</td>
                                <td>{{ $loan->tenor ?? '-' }} bulan</td>
                                <td>{{ strtoupper(str_replace('_', ' ', $loan->status ?? '-')) }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#accountModal{{ $loan->id }}">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#disbursementModal{{ $loan->id }}">
                                        Cairkan
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach ($loans as $loan)
    @php
        $bankName = trim(($loan->data_karyawan?->bank?->code_bank ?? '-').' '.($loan->data_karyawan?->bank?->description ?? ''));
        $accountName = $loan->data_karyawan?->nama_rekening ?? $loan->user?->name ?? '-';
        $accountNumber = $loan->data_karyawan?->nomor_bank ?? '-';
        $disbursementAmount = $loan->jumlah_yang_diterima ?? $loan->nominal;
    @endphp

    <div class="modal fade" id="accountModal{{ $loan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rekening Pencairan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Bank</dt>
                        <dd class="col-sm-8">{{ $bankName ?: '-' }}</dd>
                        <dt class="col-sm-4">Atas Nama</dt>
                        <dd class="col-sm-8">{{ $accountName }}</dd>
                        <dt class="col-sm-4">Nomor Rekening</dt>
                        <dd class="col-sm-8">{{ $accountNumber }}</dd>
                        <dt class="col-sm-4">Dana Dicairkan</dt>
                        <dd class="col-sm-8">Rp. {{ number_format($disbursementAmount ?? 0, 0, ',', '.') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="disbursementModal{{ $loan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cairkan Pinjaman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <form action="{{ route('admin.disbursements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pinjaman_id" value="{{ $loan->id }}">
                    <div class="modal-body">
                        <dl class="row">
                            <dt class="col-sm-4">ID Pinjaman</dt>
                            <dd class="col-sm-8">{{ $loan->id_pinjaman ?? '#'.$loan->id }}</dd>
                            <dt class="col-sm-4">Bank</dt>
                            <dd class="col-sm-8">{{ $bankName ?: '-' }}</dd>
                            <dt class="col-sm-4">Atas Nama</dt>
                            <dd class="col-sm-8">{{ $accountName }}</dd>
                            <dt class="col-sm-4">Nomor Rekening</dt>
                            <dd class="col-sm-8">{{ $accountNumber }}</dd>
                            <dt class="col-sm-4">Dana Dicairkan</dt>
                            <dd class="col-sm-8">Rp. {{ number_format($disbursementAmount ?? 0, 0, ',', '.') }}</dd>
                        </dl>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Tanggal Transfer</label>
                                <input type="date" name="transfer_date" value="{{ now()->toDateString() }}" class="form-control">
                                @error('transfer_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Bukti Transfer</label>
                                <input type="file" name="transfer_proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control" required>
                                <small class="text-muted d-block">Format jpg, jpeg, png, atau pdf. Maksimal 5 MB.</small>
                                @error('transfer_proof') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Pencairan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('js')
<script>
    $('#disbursementsTable').DataTable({
        responsive: false,
        scrollX: true,
        scrollY: '420px',
        scrollCollapse: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
        language: {
            emptyTable: 'Tidak ada pinjaman yang belum dicairkan.',
            zeroRecords: 'Pinjaman tidak ditemukan.',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
        }
    });
</script>
@endpush
