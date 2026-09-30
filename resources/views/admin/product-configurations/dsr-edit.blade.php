@extends('layout.app')

@section('title', 'Konfigurasi DSR ' . $productLabel)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.product-configurations.dsr.companies', $product) }}">{{ $productLabel }}</a></li>
                    <li class="breadcrumb-item active">Konfigurasi DSR</li>
                </ol>
            </div>
            <h4 class="page-title">Konfigurasi DSR {{ strtoupper($company->nama) }}</h4>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.product-configurations.dsr.update', [$product, $company]) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Iuran Anggota</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                            </div>
                            <input class="form-control" name="iuran_anggota" type="number" min="0" value="{{ old('iuran_anggota', $company->amount_pinjaman?->iuran_anggota ?? 0) }}">
                        </div>
                        @error('iuran_anggota')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="form-group">
                        <label>Min Plafon</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                            </div>
                            <input class="form-control" name="minimal" type="number" min="0" value="{{ old('minimal', $company->amount_pinjaman?->minimal ?? 0) }}">
                        </div>
                        @error('minimal')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="form-group">
                        <label>Max Plafon</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                            </div>
                            <input class="form-control" name="maximal" type="number" min="0" value="{{ old('maximal', $company->amount_pinjaman?->maximal ?? $maxPlafon) }}">
                        </div>
                        <small class="form-text text-muted">Max plafon karyawan tertinggi: Rp. {{ number_format($maxPlafon, 0, ',', '.') }}</small>
                        @error('maximal')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="form-group">
                        <label>DSR Maksimal</label>
                        <div class="input-group">
                            <input class="form-control" name="maximal_persentase" type="number" min="0" step="0.01" value="{{ old('maximal_persentase', $company->amount_pinjaman?->maximal_persentase ?? 0) }}">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        @error('maximal_persentase')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a class="btn btn-light" href="{{ route('admin.product-configurations.dsr.companies', $product) }}">Kembali</a>
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
