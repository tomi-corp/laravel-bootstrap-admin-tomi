<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Electronic Installment Products</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="mb-4">
                    <span class="badge badge-primary mb-2">Seller</span>
                    <h1 class="h3 mb-1">Import Products</h1>
                    <p class="text-muted mb-0">Upload an Excel or CSV file to create or update products.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="mb-4">
                            <a href="{{ route('seller.electronic-installment.products.template') }}"
                                class="btn btn-light">
                                <i class="mdi mdi-download mr-1"></i> Download Template
                            </a>
                        </div>

                        <form action="{{ route('seller.electronic-installment.products.import') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="file">Excel or CSV File</label>
                                <input type="file" class="form-control-file @error('file') is-invalid @enderror"
                                    id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                @error('file')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="alert alert-light border">
                                <strong>Import rule:</strong> products with the same SKU will be updated. If SKU is empty,
                                product name, brand, and model will be used to prevent duplicate data.
                            </div>

                            <h2 class="h6">Supported headings</h2>
                            <p class="text-muted small">
                                product_name, category, brand, product_model, sku, condition, warranty, color, ram,
                                storage, screen_size, battery, camera, operating_system, network, included_items, price,
                                stock, description.
                            </p>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('seller.electronic-installment.index') }}" class="btn btn-light">
                                    Cancel
                                </a>
                                <button class="btn btn-primary" type="submit">
                                    <i class="mdi mdi-file-import mr-1"></i> Import Products
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
