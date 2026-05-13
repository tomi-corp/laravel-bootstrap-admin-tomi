<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seller Electronic & Gadget Installment</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
    <main class="container-fluid py-4">
        <div class="row align-items-center mb-4">
            <div class="col-lg-7">
                <span class="badge badge-primary mb-2">Seller</span>
                <h1 class="h3 mb-1">Electronic Installment Products</h1>
                <p class="text-muted mb-0">Manage electronic and gadget installment product offers.</p>
            </div>
            <div class="col-lg-5 text-lg-right mt-3 mt-lg-0">
                <a href="{{ route('seller.electronic-installment.products.import.page') }}" class="btn btn-outline-primary mr-2">
                    <i class="mdi mdi-file-import mr-1"></i> Import Products
                </a>
                <a href="{{ route('seller.electronic-installment.products.create') }}" class="btn btn-primary">
                    <i class="mdi mdi-plus mr-1"></i> Add Product
                </a>
            </div>
        </div>

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

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 mb-0">Product List</h2>
                            <span class="badge badge-light">{{ count($products) }} Products</span>
                        </div>

                        <div class="table-responsive">
                            <table id="sellerProductTable" class="table table-centered table-hover dt-responsive nowrap mb-0"
                                style="width: 100%;">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Specification</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Condition</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>
                                                <strong>{{ $product['product_name'] }}</strong>
                                                <div class="text-muted small">
                                                    {{ $product['brand'] ?: '-' }}
                                                    @if (!empty($product['product_model']))
                                                        / {{ $product['product_model'] }}
                                                    @endif
                                                    @if (!empty($product['sku']))
                                                        / {{ $product['sku'] }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ ucfirst($product['category']) }}</td>
                                            <td>
                                                <div>{{ $product['ram'] ?: '-' }} RAM / {{ $product['storage'] ?: '-' }}</div>
                                                <div class="text-muted small">
                                                    {{ $product['color'] ?: '-' }} | {{ $product['network'] ?: '-' }}
                                                </div>
                                            </td>
                                            <td>Rp {{ number_format((float) $product['price'], 0, ',', '.') }}</td>
                                            <td>{{ $product['stock'] }}</td>
                                            <td>
                                                <span class="badge badge-light">{{ ucfirst($product['condition']) }}</span>
                                                <div class="text-muted small">{{ $product['warranty'] ?: 'No warranty info' }}</div>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('seller.electronic-installment.products.edit', $product['id']) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <form
                                                    action="{{ route('seller.electronic-installment.products.delete', $product['id']) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit"
                                                        onclick="return confirm('Delete this product?')">
                                                        <i class="mdi mdi-trash-can"></i>
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
    </main>

    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#sellerProductTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                ordering: true,
                searching: true,
                paging: true,
                info: true,
                columnDefs: [{
                    targets: -1,
                    orderable: false,
                    searchable: false
                }],
                language: {
                    emptyTable: 'No products added yet.',
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ products',
                    info: 'Showing _START_ to _END_ of _TOTAL_ products',
                    infoEmpty: 'Showing 0 products',
                    infoFiltered: '(filtered from _MAX_ total products)',
                    zeroRecords: 'No matching products found.',
                    paginate: {
                        previous: 'Previous',
                        next: 'Next'
                    }
                }
            });
        });
    </script>
</body>

</html>
