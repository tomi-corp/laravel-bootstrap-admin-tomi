@csrf

@if (($method ?? 'POST') !== 'POST')
@method($method)
@endif

<div class="form-group">
    <label for="product_name">Product Name</label>
    <input type="text" class="form-control @error('product_name') is-invalid @enderror" id="product_name"
        name="product_name" value="{{ old('product_name', $product->product_name ?? '') }}"
        placeholder="Example: Samsung Galaxy A55" required>

    @error('product_name')
    <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="category">Category</label>

        @php
        $selectedCategory = old('category', $product->category ?? '');
        @endphp

        <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
            <option value="">Choose category</option>
            <option value="electronic" @selected($selectedCategory==='electronic' )>Electronic</option>
            <option value="gadget" @selected($selectedCategory==='gadget' )>Gadget</option>
        </select>

        @error('category')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-6">
        <label for="brand">Brand</label>
        <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand"
            value="{{ old('brand', $product->brand ?? '') }}" placeholder="Example: Samsung">

        @error('brand')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="product_model">Model</label>
        <input type="text" class="form-control @error('product_model') is-invalid @enderror" id="product_model"
            name="product_model" value="{{ old('product_model', $product->product_model ?? '') }}"
            placeholder="Example: SM-A556E">

        @error('product_model')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-6">
        <label for="condition">Condition</label>

        @php
        $selectedCondition = old('condition', $product->condition ?? '');
        @endphp

        <select class="form-control @error('condition') is-invalid @enderror" id="condition" name="condition" required>
            <option value="">Choose condition</option>
            <option value="new" @selected($selectedCondition==='new' )>New</option>
            <option value="used" @selected($selectedCondition==='used' )>Used</option>
            <option value="refurbished" @selected($selectedCondition==='refurbished' )>Refurbished</option>
        </select>

        @error('condition')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="warranty">Warranty</label>
        <input type="text" class="form-control @error('warranty') is-invalid @enderror" id="warranty" name="warranty"
            value="{{ old('warranty', $product->warranty ?? '') }}" placeholder="Example: Official 12 months">

        @error('warranty')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-6">
        <label for="screen_size">Screen Size</label>
        <input type="text" class="form-control @error('screen_size') is-invalid @enderror" id="screen_size"
            name="screen_size" value="{{ old('screen_size', $product->screen_size ?? '') }}"
            placeholder="Example: 6.6 inch">

        @error('screen_size')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="battery">Battery</label>
        <input type="text" class="form-control @error('battery') is-invalid @enderror" id="battery" name="battery"
            value="{{ old('battery', $product->battery ?? '') }}" placeholder="Example: 5000 mAh">

        @error('battery')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-6">
        <label for="camera">Camera</label>
        <input type="text" class="form-control @error('camera') is-invalid @enderror" id="camera" name="camera"
            value="{{ old('camera', $product->camera ?? '') }}" placeholder="Example: 50 MP + 12 MP">

        @error('camera')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="operating_system">Operating System</label>
        <input type="text" class="form-control @error('operating_system') is-invalid @enderror" id="operating_system"
            name="operating_system" value="{{ old('operating_system', $product->operating_system ?? '') }}"
            placeholder="Example: Android 14">

        @error('operating_system')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group col-md-6">
        <label for="network">Network</label>
        <input type="text" class="form-control @error('network') is-invalid @enderror" id="network" name="network"
            value="{{ old('network', $product->network ?? '') }}" placeholder="Example: 4G/5G">

        @error('network')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-group">
    <label for="included_items">Included Items</label>
    <input type="text" class="form-control @error('included_items') is-invalid @enderror" id="included_items"
        name="included_items" value="{{ old('included_items', $product->included_items ?? '') }}"
        placeholder="Example: Box, charger, warranty card">

    @error('included_items')
    <small class="text-danger">{{ $message }}</small>
    @enderror
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

<hr>

<h5 class="mb-3">Product Variants</h5>

@php
$defaultVariants = [
[
'sku' => '',
'color' => '',
'internal' => '',
'ram' => '',
'price' => '',
'stock' => '',
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
'internal' => $variant->internal,
'ram' => $variant->ram,
'price' => $variant->price,
'stock' => $variant->stock,
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
'internal' => $variant->internal,
'ram' => $variant->ram,
'price' => $variant->price,
'stock' => $variant->stock,
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
                <input type="text" name="variants[{{ $index }}][sku]" class="form-control @error(" variants.$index.sku")
                    is-invalid @enderror" value="{{ old(" variants.$index.sku", $variant['sku'] ?? '' ) }}"
                    placeholder="Example: A55-BLK-128">

                @error("variants.$index.sku")
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label>Color</label>
                <input type="text" name="variants[{{ $index }}][color]" class="form-control @error("
                    variants.$index.color") is-invalid @enderror" value="{{ old(" variants.$index.color",
                    $variant['color'] ?? '' ) }}" placeholder="Example: Black">

                @error("variants.$index.color")
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label>Internal Storage</label>
                <input type="text" name="variants[{{ $index }}][internal]" class="form-control @error("
                    variants.$index.internal") is-invalid @enderror" value="{{ old(" variants.$index.internal",
                    $variant['internal'] ?? '' ) }}" placeholder="Example: 128 GB">

                @error("variants.$index.internal")
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>RAM</label>
                <input type="text" name="variants[{{ $index }}][ram]" class="form-control @error(" variants.$index.ram")
                    is-invalid @enderror" value="{{ old(" variants.$index.ram", $variant['ram'] ?? '' ) }}"
                    placeholder="Example: 8 GB">

                @error("variants.$index.ram")
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label>Price</label>
                <input type="number" name="variants[{{ $index }}][price]" class="form-control @error("
                    variants.$index.price") is-invalid @enderror" value="{{ old(" variants.$index.price",
                    $variant['price'] ?? '' ) }}" min="0" placeholder="0" required>

                @error("variants.$index.price")
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label>Stock</label>
                <input type="number" name="variants[{{ $index }}][stock]" class="form-control @error("
                    variants.$index.stock") is-invalid @enderror" value="{{ old(" variants.$index.stock",
                    $variant['stock'] ?? '' ) }}" min="0" placeholder="0" required>

                @error("variants.$index.stock")
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
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
                                placeholder="Example: A55-BLK-128">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Color</label>
                            <input type="text"
                                name="variants[${variantIndex}][color]"
                                class="form-control"
                                placeholder="Example: Black">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Internal Storage</label>
                            <input type="text"
                                name="variants[${variantIndex}][internal]"
                                class="form-control"
                                placeholder="Example: 128 GB">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>RAM</label>
                            <input type="text"
                                name="variants[${variantIndex}][ram]"
                                class="form-control"
                                placeholder="Example: 8 GB">
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
    });
</script>