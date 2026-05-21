@extends('seller.layout.app')

@section('title', 'Edit Produk')

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-10">

            <div class="mb-3">
                <span class="badge badge-primary mb-2">Seller</span>
                <h3>Edit Produk</h3>
                <p class="text-muted">Ubah produk dan varian</p>
            </div>

            <div class="card">
                <div class="card-body">

                    <form action="{{ route('seller.electronic-installment.products.update', $product->id) }}"
                        method="POST" enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        {{-- PRODUCT INFO --}}
                        <div class="mb-3">
                            <label>Nama Produk</label>
                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label>Brand</label>
                            <input type="text" name="brand" class="form-control" value="{{ $product->brand }}">
                        </div>

                        <div class="mb-3">
                            <label>Model</label>
                            <input type="text" name="model" class="form-control" value="{{ $product->model }}">
                        </div>

                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control"
                                rows="3">{{ $product->description }}</textarea>
                        </div>

                        <hr>

                        {{-- VARIANT --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5>Varian Produk</h5>

                            <button type="button" class="btn btn-success btn-sm" id="addVariant">
                                + Tambah Varian
                            </button>
                        </div>

                        <div id="variantWrapper">

                            @foreach($product->variants as $i => $variant)

                            <div class="variant-item border rounded p-3 mb-3">

                                <div class="row">
                                    <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant->id }}">

                                    <div class="col-md-2">
                                        <label>RAM (GB)</label>
                                        <input type="number" name="variants[{{ $i }}][ram_gb]"
                                            value="{{ $variant->ram_gb }}" class="form-control" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Storage (GB)</label>
                                        <input type="number" name="variants[{{ $i }}][storage_gb]"
                                            value="{{ $variant->storage_gb }}" class="form-control" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Warna</label>
                                        <input type="text" name="variants[{{ $i }}][color]"
                                            value="{{ $variant->color }}" class="form-control">
                                    </div>

                                    <div class="col-md-2">
                                        <label>SKU</label>
                                        <input type="text" name="variants[{{ $i }}][sku]" value="{{ $variant->sku }}"
                                            class="form-control" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Harga</label>
                                        <input type="number" name="variants[{{ $i }}][price]"
                                            value="{{ $variant->price }}" class="form-control" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Stok</label>
                                        <input type="number" name="variants[{{ $i }}][stock]"
                                            value="{{ $variant->stock }}" class="form-control" required>
                                    </div>

                                    <div class="col-md-3 mt-2">
                                        <label>Gambar Varian</label>
                                        @if($variant->image_url)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $variant->image_url) }}"
                                                style="width:80px; border-radius:8px;">
                                        </div>
                                        @endif
                                        <input type="file" name="variants[{{ $i }}][image]" class="form-control">
                                    </div>

                                </div>

                                <button type="button" class="btn btn-danger btn-sm mt-2 removeVariant">
                                    Hapus
                                </button>

                            </div>

                            @endforeach

                        </div>
                        {{-- PRODUCT IMAGE --}}
                        <div class="mb-3">

                            <label>Gambar Produk (maksimal 3)</label>

                            @if($product->images->isNotEmpty())
                            <div class="row mb-2">
                                @foreach($product->images as $image)
                                <div class="col-md-3 mb-2">
                                    <img src="{{ asset('storage/' . $image->image_url) }}"
                                        style="width:120px; border-radius:8px;">
                                    <div class="form-check mt-1">
                                        <input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}"
                                            class="form-check-input" id="remove-image-{{ $image->id }}">
                                        <label class="form-check-label" for="remove-image-{{ $image->id }}">
                                            Hapus gambar
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <input type="file" name="images[]" class="form-control" multiple>

                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary">
                                Update Produk
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
    let index = {{ $product->variants->count() }};

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
