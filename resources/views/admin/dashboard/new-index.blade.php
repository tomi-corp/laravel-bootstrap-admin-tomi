@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">

    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold mb-1">Dashboard</h4>
            <p class="text-muted mb-0">
                Ringkasan data perusahaan, karyawan, dan status pinjaman.
            </p>
        </div>
    </div>

    <div class="row g-4">

        {{-- Total Perusahaan --}}
        <div class="col-xl-3 col-md-6">
            <a href="{{ url('companies') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Perusahaan</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalPerusahaan) }}</h3>
                        </div>

                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fa-solid fa-building fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Total Karyawan --}}
        <div class="col-xl-3 col-md-6">
            <a href="{{ url('employees') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Karyawan</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalKaryawan) }}</h3>
                        </div>

                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="fa-solid fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Pinjaman Pending --}}
        <div class="col-xl-3 col-md-6">
            <a href="{{ url('pinjaman?status=pending') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Pinjaman Pending</p>
                            <h3 class="fw-bold mb-0">{{ number_format($karyawanPinjamanPending) }}</h3>
                        </div>

                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fa-solid fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Pinjaman Approved --}}
        <div class="col-xl-3 col-md-6">
            <a href="{{ url('pinjaman?status=approved') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Pinjaman Approved</p>
                            <h3 class="fw-bold mb-0">{{ number_format($karyawanPinjamanApproved) }}</h3>
                        </div>

                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fa-solid fa-circle-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Pinjaman Rejected --}}
        <div class="col-xl-3 col-md-6">
            <a href="{{ url('pinjaman?status=rejected') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Pinjaman Ditolak</p>
                            <h3 class="fw-bold mb-0">{{ number_format($karyawanPinjamanRejected) }}</h3>
                        </div>

                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="fa-solid fa-circle-xmark fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>
@endsection