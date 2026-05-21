@extends('seller.layout.app')

@section('title', 'Dashboard')

@section('content')

{{-- WELCOME --}}
<div class="row mt-2">

    <div class="col-12">

        <div class="card mb-2">

            <div class="card-body py-3">

                <h4 class="mb-1">
                    👋 Welcome, {{ auth('seller')->user()->owner_name }}
                </h4>

                <p class="text-muted mb-0">
                    Selamat datang di dashboard seller TOMI
                </p>

            </div>

        </div>

    </div>

</div>

{{-- STAT CARDS --}}
<div class="row g-2">

    <div class="col-md-4">
        <div class="card mb-2">
            <div class="card-body py-3">
                <h6 class="mb-1 text-muted">Total Produk</h6>
                <h3 class="mb-0">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-2">
            <div class="card-body py-3">
                <h6 class="mb-1 text-muted">Total Variant</h6>
                <h3 class="mb-0">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-2">
            <div class="card-body py-3">
                <h6 class="mb-1 text-muted">Total Stok</h6>
                <h3 class="mb-0">0</h3>
            </div>
        </div>
    </div>

</div>

{{-- STORE INFO --}}
<div class="row mb-2">

    <div class="col-12">

        <div class="card mb-0">

            <div class="card-body py-3">

                <h5 class="mb-3">Informasi Toko</h5>

                <table class="table table-sm table-borderless mb-0">

                    <tr>
                        <th width="150">Nama Toko</th>
                        <td>{{ auth('seller')->user()->store_name }}</td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td>{{ auth('seller')->user()->email }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            @if(auth('seller')->user()->is_verified)
                            <span class="badge bg-success">Verified</span>
                            @else
                            <span class="badge bg-warning">Belum Verified</span>
                            @endif
                        </td>
                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection