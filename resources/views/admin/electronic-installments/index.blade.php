@extends('layout.app')

@section('title', 'Cicilan Elektronik')

@section('content')


{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">
            Data Cicilan Elektronik
        </h2>

        <p class="text-muted mb-0">
            Daftar produk elektronik cicilan
        </p>
    </div>

    <a href="#" class="btn btn-primary">
        + Tambah Produk
    </a>
</div>

{{-- Alert Success --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Table --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Gambar</th>
                        <th class="py-3">Nama Produk</th>
                        <th class="py-3">Harga</th>
                        <th class="py-3">Tenor</th>
                        <th class="py-3">Bunga</th>
                        <th class="py-3">Cicilan / Bulan</th>
                        <th class="text-center py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($installments as $item)

                    @php
                    $total =
                    $item['price'] +
                    ($item['price'] * ($item['interest'] / 100) * $item['tenor']);

                    $monthly = $total / $item['tenor'];
                    @endphp

                    <tr>

                        <td class="px-4">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            <img src="https://placehold.co/80x80" width="80" height="80" class="rounded">
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $item['name'] }}
                            </div>

                            <small class="text-muted">
                                {{ $item['description'] }}
                            </small>
                        </td>

                        <td class="fw-bold text-primary">
                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                        </td>

                        <td>
                            {{ $item['tenor'] }} Bulan
                        </td>

                        <td>
                            {{ $item['interest'] }}%
                        </td>

                        <td class="fw-semibold text-success">
                            Rp {{ number_format($monthly, 0, ',', '.') }}
                        </td>

                        <td class="text-center">

                            <div class="d-flex gap-2 justify-content-center">

                                {{-- Detail --}}
                                <a href="#" class="btn btn-info btn-sm text-white">
                                    Detail
                                </a>

                                {{-- Edit --}}
                                <a href="#" class="btn btn-warning btn-sm text-white">
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <form action="#" method="POST">

                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus data ini?')">
                                        Hapus
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="8" class="text-center py-5">

                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486740.png" width="120" class="mb-3">

                            <h5 class="fw-bold">
                                Data Masih Kosong
                            </h5>

                            <p class="text-muted">
                                Belum ada produk cicilan elektronik.
                            </p>

                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>
</div>


@endsection