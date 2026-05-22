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
                        @csrf

                        {{-- PRODUCT INFO --}}
                        <div class="mb-3">
                            <label>Nama Produk</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Brand</label>
                            <input type="text" name="brand" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Model</label>
                            <input type="text" name="model" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <hr>

                        {{-- VARIANT SECTION --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5>Varian Produk</h5>

                            <button type="button" class="btn btn-success btn-sm" id="addVariant">
                                + Tambah Varian
                            </button>
                        </div>

                        <div id="variantWrapper">

                            {{-- TEMPLATE VARIANT --}}
                            <div class="variant-item border rounded p-3 mb-3">

                                <div class="row">

                                    <div class="col-md-2">
                                        <label>RAM (GB)</label>
                                        <input type="number" name="variants[0][ram_gb]" class="form-control" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Storage (GB)</label>
                                        <input type="number" name="variants[0][storage_gb]" class="form-control"
                                            required>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Warna</label>
                                        <input type="text" name="variants[0][color]" class="form-control">
                                    </div>

                                    <div class="col-md-2">
                                        <label>SKU</label>
                                        <input type="text" name="variants[0][sku]" class="form-control" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Harga</label>
                                        <input type="number" name="variants[0][price]" class="form-control" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Stok</label>
                                        <input type="number" name="variants[0][stock]" class="form-control" required>
                                    </div>

                                    <div class="col-md-3 mt-2">
                                        <label>Gambar Varian</label>
                                        <input type="file" name="variants[0][image]" class="form-control">
                                    </div>

                                </div>

                                <button type="button" class="btn btn-danger btn-sm mt-2 removeVariant">
                                    Hapus
                                </button>

                            </div>

                        </div>

                        <div class="mb-3">
                            <label>Gambar Produk (maksimal 3)</label>
                            <input type="file" name="images[]" class="form-control" multiple>
                        </div>

                        <div class="text-right">
                            <button class="btn btn-primary">
                                Simpan Produk
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>

    </div>

</div>

@endsection

@push('js')
<script>
    let index = 1;

    // ADD VARIANT
    document.getElementById('addVariant').addEventListener('click', function () {

        let wrapper = document.getElementById('variantWrapper');

        let html = `
    <div class="variant-item border rounded p-3 mb-3">

        <div class="row">

            <div class="col-md-2">
                <label>RAM (GB)</label>
                <input type="number" name="variants[${index}][ram_gb]" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label>Storage (GB)</label>
                <input type="number" name="variants[${index}][storage_gb]" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label>Warna</label>
                <input type="text" name="variants[${index}][color]" class="form-control">
            </div>

            <div class="col-md-2">
                <label>SKU</label>
                <input type="text" name="variants[${index}][sku]" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label>Harga</label>
                <input type="number" name="variants[${index}][price]" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label>Stok</label>
                <input type="number" name="variants[${index}][stock]" class="form-control" required>
            </div>

            <div class="col-md-3 mt-2">
                <label>Gambar Varian</label>
                <input type="file" name="variants[${index}][image]" class="form-control">
            </div>

        </div>

        <button type="button" class="btn btn-danger btn-sm mt-2 removeVariant">
            Hapus
        </button>

    </div>
    `;

        wrapper.insertAdjacentHTML('beforeend', html);

        index++;

    });

    // REMOVE VARIANT
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeVariant')) {
            e.target.closest('.variant-item').remove();
        }
    });
</script>
@endpush
