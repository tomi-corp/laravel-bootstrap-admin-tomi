@extends('seller.layout.app')

@section('title', 'Electronic Installment Products')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="row align-items-center my-2">

        <div class="col-lg-7">

            <h3 class="mb-1">Electronic Installment Products</h3>

            <p class="text-muted mb-0">
                Kelola produk cicilan elektronik dan gadget.
            </p>

        </div>

        <div class="col-lg-5 text-lg-right mt-3 mt-lg-0">

            <a href="{{ route('seller.electronic-installment.products.import.page') }}"
                class="btn btn-outline-primary mr-2">
                Import Produk
            </a>

            <a href="{{ route('seller.electronic-installment.products.create') }}" class="btn btn-primary">
                + Tambah Produk
            </a>

        </div>

    </div>

    {{-- ALERT --}}
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif

    {{-- TABLE --}}
    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h5 class="mb-0">Daftar Produk</h5>

                        <span class="badge badge-light">
                            {{ $products->count() }} Produk
                        </span>

                    </div>

                    <div class="table-responsive">

                        <table id="sellerProductTable" class="table table-hover table-centered w-100">

                            <thead class="thead-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Brand</th>
                                    <th>Varian</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($products as $product)

                                @php
                                $firstVariant = $product->variants->first();
                                @endphp

                                <tr>

                                    {{-- PRODUCT --}}
                                    <td>
                                        <strong>{{ $product->name }}</strong>

                                        <div class="text-muted small">
                                            {{ $product->model ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- BRAND --}}
                                    <td>
                                        {{ $product->brand ?? '-' }}
                                    </td>

                                    {{-- VARIANT INFO --}}
                                    <td>
                                        @if($firstVariant)
                                        {{ $firstVariant->ram_gb }}GB /
                                        {{ $firstVariant->storage_gb }}GB
                                        <br>
                                        <small class="text-muted">
                                            {{ $firstVariant->color ?? '-' }}
                                        </small>
                                        @else
                                        -
                                        @endif
                                    </td>

                                    {{-- PRICE --}}
                                    <td>
                                        @if($firstVariant)
                                        Rp {{ number_format($firstVariant->price, 0, ',', '.') }}
                                        @else
                                        -
                                        @endif
                                    </td>

                                    {{-- STOCK --}}
                                    <td>
                                        {{ $product->variants->sum('stock') }}
                                    </td>

                                    {{-- ACTION --}}
                                    <td class="text-right">

                                        <a href="{{ route('seller.electronic-installment.products.edit', $product->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('seller.electronic-installment.products.delete', $product->id) }}"
                                            method="POST" class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Hapus produk ini?')">

                                                Hapus

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

@push('js')
<script>
    $(document).ready(function () {
        $('#sellerProductTable').DataTable({
            responsive: true,
            pageLength: 10,
            ordering: true,
            searching: true,
            columnDefs: [
                { targets: -1, orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush