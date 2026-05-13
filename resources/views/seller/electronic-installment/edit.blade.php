<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Electronic Installment Product</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="mb-4">
                    <span class="badge badge-primary mb-2">Seller</span>
                    <h1 class="h3 mb-1">Edit Product</h1>
                    <p class="text-muted mb-0">Update product details for electronic or gadget installment offers.</p>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('seller.electronic-installment.products.update', $product['id']) }}"
                            method="POST">
                            @include('seller.electronic-installment._form', [
                                'method' => 'PUT',
                                'product' => $product,
                                'submitLabel' => 'Update Product',
                            ])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
