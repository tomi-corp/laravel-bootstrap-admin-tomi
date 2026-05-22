@csrf

@if (($method ?? 'POST') !== 'POST')
    @method($method)
@endif

<div class="form-group">
    <label for="product_name">Product Name</label>
    <input type="text" class="form-control @error('product_name') is-invalid @enderror" id="product_name"
        name="product_name" value="{{ old('product_name', $product['product_name'] ?? '') }}"
        placeholder="Example: Samsung Galaxy A55" required>
    @error('product_name')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="category">Category</label>
        <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
            @php($selectedCategory = old('category', $product['category'] ?? ''))
            <option value="">Choose category</option>
            <option value="electronic" @selected($selectedCategory === 'electronic')>Electronic</option>
            <option value="gadget" @selected($selectedCategory === 'gadget')>Gadget</option>
        </select>
        @error('category')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="brand">Brand</label>
        <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand"
            value="{{ old('brand', $product['brand'] ?? '') }}" placeholder="Example: Samsung">
        @error('brand')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="product_model">Model</label>
        <input type="text" class="form-control @error('product_model') is-invalid @enderror" id="product_model"
            name="product_model" value="{{ old('product_model', $product['product_model'] ?? '') }}"
            placeholder="Example: SM-A556E">
        @error('product_model')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="sku">SKU</label>
        <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku"
            value="{{ old('sku', $product['sku'] ?? '') }}" placeholder="Example: GDG-A55-001">
        @error('sku')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="condition">Condition</label>
        @php($selectedCondition = old('condition', $product['condition'] ?? ''))
        <select class="form-control @error('condition') is-invalid @enderror" id="condition" name="condition" required>
            <option value="">Choose condition</option>
            <option value="new" @selected($selectedCondition === 'new')>New</option>
            <option value="used" @selected($selectedCondition === 'used')>Used</option>
            <option value="refurbished" @selected($selectedCondition === 'refurbished')>Refurbished</option>
        </select>
        @error('condition')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="warranty">Warranty</label>
        <input type="text" class="form-control @error('warranty') is-invalid @enderror" id="warranty"
            name="warranty" value="{{ old('warranty', $product['warranty'] ?? '') }}"
            placeholder="Example: Official 12 months">
        @error('warranty')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="price">Price</label>
        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price"
            value="{{ old('price', $product['price'] ?? '') }}" min="0" placeholder="0" required>
        @error('price')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="stock">Stock</label>
        <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock"
            value="{{ old('stock', $product['stock'] ?? '') }}" min="0" placeholder="0" required>
        @error('stock')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="color">Color</label>
        <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" name="color"
            value="{{ old('color', $product['color'] ?? '') }}" placeholder="Example: Black">
        @error('color')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-4">
        <label for="ram">RAM</label>
        <input type="text" class="form-control @error('ram') is-invalid @enderror" id="ram" name="ram"
            value="{{ old('ram', $product['ram'] ?? '') }}" placeholder="Example: 8 GB">
        @error('ram')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-4">
        <label for="storage">Storage</label>
        <input type="text" class="form-control @error('storage') is-invalid @enderror" id="storage"
            name="storage" value="{{ old('storage', $product['storage'] ?? '') }}" placeholder="Example: 256 GB">
        @error('storage')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="screen_size">Screen Size</label>
        <input type="text" class="form-control @error('screen_size') is-invalid @enderror" id="screen_size"
            name="screen_size" value="{{ old('screen_size', $product['screen_size'] ?? '') }}"
            placeholder="Example: 6.6 inch">
        @error('screen_size')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="battery">Battery</label>
        <input type="text" class="form-control @error('battery') is-invalid @enderror" id="battery"
            name="battery" value="{{ old('battery', $product['battery'] ?? '') }}" placeholder="Example: 5000 mAh">
        @error('battery')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="camera">Camera</label>
        <input type="text" class="form-control @error('camera') is-invalid @enderror" id="camera" name="camera"
            value="{{ old('camera', $product['camera'] ?? '') }}" placeholder="Example: 50 MP + 12 MP">
        @error('camera')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="operating_system">Operating System</label>
        <input type="text" class="form-control @error('operating_system') is-invalid @enderror"
            id="operating_system" name="operating_system"
            value="{{ old('operating_system', $product['operating_system'] ?? '') }}"
            placeholder="Example: Android 14">
        @error('operating_system')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="network">Network</label>
        <input type="text" class="form-control @error('network') is-invalid @enderror" id="network"
            name="network" value="{{ old('network', $product['network'] ?? '') }}" placeholder="Example: 4G/5G">
        @error('network')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="included_items">Included Items</label>
        <input type="text" class="form-control @error('included_items') is-invalid @enderror"
            id="included_items" name="included_items"
            value="{{ old('included_items', $product['included_items'] ?? '') }}"
            placeholder="Example: Box, charger, warranty card">
        @error('included_items')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
        rows="3" placeholder="Product details, condition, or promo notes">{{ old('description', $product['description'] ?? '') }}</textarea>
    @error('description')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('seller.electronic-installment.index') }}" class="btn btn-light">Cancel</a>
    <button class="btn btn-primary" type="submit">{{ $submitLabel ?? 'Save Product' }}</button>
</div>
