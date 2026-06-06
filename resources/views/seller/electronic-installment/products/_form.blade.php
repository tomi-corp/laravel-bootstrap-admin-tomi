@csrf

@if (($method ?? 'POST') !== 'POST')
@method($method)
@endif

@php
$imageUrl = function (?string $path) {
    if (empty($path)) {
        return null;
    }

    if (preg_match('/^https?:\/\//', $path) || str_starts_with($path, '/')) {
        return $path;
    }

    $path = preg_replace('/^(public\/|storage\/)/', '', $path);

    return asset('storage/' . $path);
};
@endphp

<div class="form-group">
    <label for="name">Product Name</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
        name="name" value="{{ old('name', $product->name ?? '') }}"
        placeholder="Example: Samsung Galaxy A55" required>

    @error('name')
    <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="brand">Brand</label>
        <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand"
            value="{{ old('brand', $product->brand ?? '') }}" placeholder="Example: Samsung">

        @error('brand')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-6">
        <label for="model">Model</label>
        <input type="text" class="form-control @error('model') is-invalid @enderror" id="model"
            name="model" value="{{ old('model', $product->model ?? '') }}"
            placeholder="Example: SM-A556E">

        @error('model')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="is_active">Product Status</label>

        @php
        $selectedProductStatus = old('is_active', isset($product) ? (int) $product->is_active : 1);
        @endphp

        <select class="form-control @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
            <option value="1" @selected((string) $selectedProductStatus==='1' )>Active</option>
            <option value="0" @selected((string) $selectedProductStatus==='0' )>Inactive</option>
        </select>

        @error('is_active')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
        rows="3"
        placeholder="Product details, condition, or promo notes">{{ old('description', $product->description ?? '') }}</textarea>

    @error('description')
    <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-group">
    <label>Product Images</label>

    @php
    $existingImages = isset($product) && $product->relationLoaded('images')
    ? $product->images->take(3)->values()
    : collect();
    @endphp

    <div>
        <div class="d-flex flex-wrap" style="gap: 12px;">
            @for ($imageIndex = 0; $imageIndex < 3; $imageIndex++)
            @php
            $existingImage = $existingImages->get($imageIndex);
            $isVisible = $imageIndex === 0 || $existingImage || $existingImages->count() > $imageIndex;
            @endphp

            <div class="product-image-slot" data-image-slot="{{ $imageIndex }}" style="{{ $isVisible ? '' : 'display:none;' }}">
                <label for="product-image-{{ $imageIndex }}"
                    class="btn btn-outline-secondary rounded d-inline-flex align-items-center justify-content-center text-center p-0 overflow-hidden mb-0"
                    style="width: 150px; height: 150px;">
                    <div class="product-image-preview w-100 h-100 d-flex align-items-center justify-content-center">
                        @if ($existingImage)
                        <img src="{{ $imageUrl($existingImage->image_url) }}" alt="Product Image {{ $imageIndex + 1 }}"
                            class="w-100 h-100" style="object-fit: cover;">
                        @else
                        <div class="text-muted">
                            <div class="display-4 font-weight-light">+</div>
                            <div class="small font-weight-bold">Upload Image</div>
                        </div>
                        @endif
                    </div>
                </label>

                <input type="file" id="product-image-{{ $imageIndex }}" name="images[]"
                    class="d-none product-image-input @error('images.' . $imageIndex) is-invalid @enderror"
                    accept="image/*" data-image-index="{{ $imageIndex }}">

                @if ($existingImage)
                <input type="hidden" name="remove_image_ids[]" value="{{ $existingImage->id }}"
                    class="product-image-remove-input" disabled>
                @endif
            </div>
            @endfor
        </div>

        <small class="form-text text-muted">
            Klik kotak untuk upload gambar bertahap. Maksimal 3 gambar. Format: JPG, PNG, WEBP. Max 2MB.
        </small>

        @error('images')
        <small class="text-danger d-block">{{ $message }}</small>
        @enderror

        @error('images.*')
        <small class="text-danger d-block">{{ $message }}</small>
        @enderror
    </div>
</div>

<hr>

<h5 class="mb-3">Product Variants</h5>

@php
$defaultVariants = [
[
'sku' => '',
'color' => '',
'storage_gb' => '',
'ram_gb' => '',
'price' => '',
'stock' => '',
'image_url' => '',
'is_active' => 1,
],
];

if (old('variants')) {
$variants = old('variants');
} elseif (isset($product) && $product->relationLoaded('variants')) {
$variants = $product->variants->map(function ($variant) {
return [
'id' => $variant->id,
'sku' => $variant->sku,
'color' => $variant->color,
'storage_gb' => $variant->storage_gb,
'ram_gb' => $variant->ram_gb,
'price' => $variant->price,
'stock' => $variant->stock,
'image_url' => $variant->image_url,
'is_active' => (int) $variant->is_active,
];
})->toArray();

if (empty($variants)) {
$variants = $defaultVariants;
}
} elseif (isset($product) && method_exists($product, 'variants')) {
$variants = $product->variants->map(function ($variant) {
return [
'id' => $variant->id,
'sku' => $variant->sku,
'color' => $variant->color,
'storage_gb' => $variant->storage_gb,
'ram_gb' => $variant->ram_gb,
'price' => $variant->price,
'stock' => $variant->stock,
'image_url' => $variant->image_url,
'is_active' => (int) $variant->is_active,
];
})->toArray();

if (empty($variants)) {
$variants = $defaultVariants;
}
} else {
$variants = $defaultVariants;
}
@endphp

<div id="variant-wrapper">
    @foreach ($variants as $index => $variant)
    <div class="variant-item border rounded p-3 mb-3">
        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? '' }}">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>Variant {{ $index + 1 }}</strong>

            <button type="button" class="btn btn-sm btn-danger remove-variant"
                style="{{ $loop->first ? 'display:none;' : '' }}">
                Remove
            </button>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>SKU</label>
                <input type="text" name="variants[{{ $index }}][sku]"
                    class="form-control @error('variants.' . $index . '.sku') is-invalid @enderror"
                    value="{{ old('variants.' . $index . '.sku', $variant['sku'] ?? '') }}"
                    placeholder="Example: A55-BLK-128" required>

                @error('variants.' . $index . '.sku')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label>Color</label>
                <input type="text" name="variants[{{ $index }}][color]"
                    class="form-control @error('variants.' . $index . '.color') is-invalid @enderror"
                    value="{{ old('variants.' . $index . '.color', $variant['color'] ?? '') }}"
                    placeholder="Example: Black">

                @error('variants.' . $index . '.color')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label>Storage (GB)</label>
                <input type="number" name="variants[{{ $index }}][storage_gb]"
                    class="form-control @error('variants.' . $index . '.storage_gb') is-invalid @enderror"
                    value="{{ old('variants.' . $index . '.storage_gb', $variant['storage_gb'] ?? '') }}"
                    min="0" placeholder="Example: 128">

                @error('variants.' . $index . '.storage_gb')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>RAM (GB)</label>
                <input type="number" name="variants[{{ $index }}][ram_gb]"
                    class="form-control @error('variants.' . $index . '.ram_gb') is-invalid @enderror"
                    value="{{ old('variants.' . $index . '.ram_gb', $variant['ram_gb'] ?? '') }}" min="0"
                    placeholder="Example: 8">

                @error('variants.' . $index . '.ram_gb')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label>Price</label>
                <input type="number" name="variants[{{ $index }}][price]"
                    class="form-control @error('variants.' . $index . '.price') is-invalid @enderror"
                    value="{{ old('variants.' . $index . '.price', $variant['price'] ?? '') }}" min="0" placeholder="0"
                    required>

                @error('variants.' . $index . '.price')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label>Stock</label>
                <input type="number" name="variants[{{ $index }}][stock]"
                    class="form-control @error('variants.' . $index . '.stock') is-invalid @enderror"
                    value="{{ old('variants.' . $index . '.stock', $variant['stock'] ?? '') }}" min="0" placeholder="0"
                    required>

                @error('variants.' . $index . '.stock')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label>Status</label>

                @php
                $selectedVariantStatus = old('variants.' . $index . '.is_active', $variant['is_active'] ?? 1);
                @endphp

                <select name="variants[{{ $index }}][is_active]"
                    class="form-control @error('variants.' . $index . '.is_active') is-invalid @enderror" required>
                    <option value="1" @selected((string) $selectedVariantStatus==='1' )>Active</option>
                    <option value="0" @selected((string) $selectedVariantStatus==='0' )>Inactive</option>
                </select>

                @error('variants.' . $index . '.is_active')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-group mb-0">
            <label>Variant Image</label>

            <div>
                <label for="variant-image-{{ $index }}"
                    class="btn btn-outline-secondary rounded d-inline-flex align-items-center justify-content-center text-center p-0 overflow-hidden mb-0"
                    style="width: 120px; height: 120px;">
                    <div class="variant-image-preview w-100 h-100 d-flex align-items-center justify-content-center">
                        @if (!empty($variant['image_url']))
                        <img src="{{ $imageUrl($variant['image_url']) }}" alt="Variant Image {{ $index + 1 }}"
                            class="w-100 h-100" style="object-fit: cover;">
                        @else
                        <div class="text-muted">
                            <div class="h2 font-weight-light mb-0">+</div>
                            <div class="small font-weight-bold">Upload</div>
                        </div>
                        @endif
                    </div>
                </label>

                <input type="file" id="variant-image-{{ $index }}" name="variants[{{ $index }}][image]"
                    class="d-none variant-image-input @error('variants.' . $index . '.image') is-invalid @enderror"
                    accept="image/*">
            </div>

            @error('variants.' . $index . '.image')
            <small class="text-danger d-block">{{ $message }}</small>
            @enderror
        </div>
    </div>
    @endforeach
</div>

<button type="button" class="btn btn-sm btn-outline-primary mb-4" id="add-variant">
    + Add Variant
</button>

<div class="d-flex justify-content-between">
    <a href="{{ route('seller.electronic-installment.index') }}" class="btn btn-light">
        Cancel
    </a>

    <button class="btn btn-primary" type="submit">
        {{ $submitLabel ?? 'Save Product' }}
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let variantIndex = document.querySelectorAll('.variant-item').length;

        const wrapper = document.getElementById('variant-wrapper');
        const addButton = document.getElementById('add-variant');

        const imageInputs = document.querySelectorAll('.product-image-input');
        const imageSlots = document.querySelectorAll('.product-image-slot');

        imageInputs.forEach(function (imageInput) {
            imageInput.addEventListener('change', function () {
                const file = this.files[0];

                if (!file) {
                    return;
                }

                const slot = this.closest('.product-image-slot');
                const preview = slot.querySelector('.product-image-preview');
                const removeInput = slot.querySelector('.product-image-remove-input');
                const currentIndex = Number(this.dataset.imageIndex);
                const nextSlot = imageSlots[currentIndex + 1];
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.innerHTML = `
                        <img
                            src="${e.target.result}"
                            alt="Product Image ${currentIndex + 1}"
                            class="w-100 h-100"
                            style="object-fit: cover;"
                        >
                    `;

                    if (nextSlot) {
                        nextSlot.style.display = '';
                    }

                    if (removeInput) {
                        removeInput.disabled = false;
                    }
                };

                reader.readAsDataURL(file);
            });
        });

        addButton.addEventListener('click', function () {
            const html = `
                <div class="variant-item border rounded p-3 mb-3">
                    <input type="hidden"
                        name="variants[${variantIndex}][id]"
                        value="">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Variant ${variantIndex + 1}</strong>

                        <button type="button" class="btn btn-sm btn-danger remove-variant">
                            Remove
                        </button>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>SKU</label>
                            <input type="text"
                                name="variants[${variantIndex}][sku]"
                                class="form-control"
                                placeholder="Example: A55-BLK-128"
                                required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Color</label>
                            <input type="text"
                                name="variants[${variantIndex}][color]"
                                class="form-control"
                                placeholder="Example: Black">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Storage (GB)</label>
                            <input type="number"
                                name="variants[${variantIndex}][storage_gb]"
                                class="form-control"
                                min="0"
                                placeholder="Example: 128">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>RAM (GB)</label>
                            <input type="number"
                                name="variants[${variantIndex}][ram_gb]"
                                class="form-control"
                                min="0"
                                placeholder="Example: 8">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Price</label>
                            <input type="number"
                                name="variants[${variantIndex}][price]"
                                class="form-control"
                                min="0"
                                placeholder="0"
                                required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Stock</label>
                            <input type="number"
                                name="variants[${variantIndex}][stock]"
                                class="form-control"
                                min="0"
                                placeholder="0"
                                required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Status</label>
                            <select
                                name="variants[${variantIndex}][is_active]"
                                class="form-control"
                                required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label>Variant Image</label>

                        <div>
                            <label for="variant-image-${variantIndex}"
                                class="btn btn-outline-secondary rounded d-inline-flex align-items-center justify-content-center text-center p-0 overflow-hidden mb-0"
                                style="width: 120px; height: 120px;">
                                <div class="variant-image-preview w-100 h-100 d-flex align-items-center justify-content-center">
                                    <div class="text-muted">
                                        <div class="h2 font-weight-light mb-0">+</div>
                                        <div class="small font-weight-bold">Upload</div>
                                    </div>
                                </div>
                            </label>

                            <input type="file"
                                id="variant-image-${variantIndex}"
                                name="variants[${variantIndex}][image]"
                                class="d-none variant-image-input"
                                accept="image/*">
                        </div>
                    </div>
                </div>
            `;

            wrapper.insertAdjacentHTML('beforeend', html);
            variantIndex++;
        });

        wrapper.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-variant')) {
                e.target.closest('.variant-item').remove();
            }
        });

        wrapper.addEventListener('change', function (e) {
            if (!e.target.classList.contains('variant-image-input')) {
                return;
            }

            const file = e.target.files[0];

            if (!file) {
                return;
            }

            const preview = e.target.closest('.form-group').querySelector('.variant-image-preview');
            const reader = new FileReader();

            reader.onload = function (event) {
                preview.innerHTML = `
                    <img
                        src="${event.target.result}"
                        alt="Variant Image"
                        class="w-100 h-100"
                        style="object-fit: cover;"
                    >
                `;
            };

            reader.readAsDataURL(file);
        });
    });
</script>
