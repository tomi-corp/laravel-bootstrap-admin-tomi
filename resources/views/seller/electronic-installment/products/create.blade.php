@extends('seller.layout.app')

@section('title', 'Tambah Produk')

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-10">

            <div class="mb-3">
                <span class="badge badge-primary mb-2">Seller</span>
                <h3>Tambah Produk</h3>
                <p class="text-muted">Produk dengan varian (RAM, storage, warna, dll)</p>
            </div>

            <div class="card">
                <div class="card-body">

                    <form action="{{ route('seller.electronic-installment.products.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @include('seller.electronic-installment.products._form', [
                        'buttonText' => 'Simpan Produk'
                        ])
                    </form>

                </div>
            </div>

        </div>

    </div>

</div>

@endsection