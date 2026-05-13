<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Exports\SellerElectronicProductTemplateExport;
use App\Imports\SellerElectronicProductImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ElectronicInstallmentController extends Controller
{
    public function index()
    {
        $products = session('seller_electronic_installment_products', []);

        return view('seller.electronic-installment.index', compact('products'));
    }

    public function createProduct()
    {
        return view('seller.electronic-installment.create');
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new SellerElectronicProductTemplateExport(),
            'seller-electronic-product-template.xlsx'
        );
    }

    public function importProductPage()
    {
        return view('seller.electronic-installment.import');
    }

    public function storeProduct(Request $request)
    {
        $validated = $this->validateProduct($request);

        $products = session('seller_electronic_installment_products', []);
        $products[] = array_merge($validated, [
            'id' => $this->nextProductId($products),
        ]);

        session(['seller_electronic_installment_products' => $products]);

        return redirect()
            ->route('seller.electronic-installment.index')
            ->with('success', 'Product has been added.');
    }

    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new SellerElectronicProductImport();
        Excel::import($import, $request->file('file'));

        $importedProducts = $import->getProducts();
        $products = session('seller_electronic_installment_products', []);
        $createdCount = 0;
        $updatedCount = 0;

        foreach ($importedProducts as $product) {
            $existingIndex = $this->findProductIndex($products, $product);

            if ($existingIndex === null) {
                $products[] = array_merge($product, [
                    'id' => $this->nextProductId($products),
                ]);
                $createdCount++;

                continue;
            }

            $products[$existingIndex] = array_merge($products[$existingIndex], $product);
            $updatedCount++;
        }

        session(['seller_electronic_installment_products' => $products]);

        return redirect()
            ->route('seller.electronic-installment.index')
            ->with('success', "{$createdCount} products have been created and {$updatedCount} products have been updated.");
    }

    public function editProduct(int $id)
    {
        $product = collect(session('seller_electronic_installment_products', []))
            ->firstWhere('id', $id);

        if (!$product) {
            abort(404, 'Product not found');
        }

        return view('seller.electronic-installment.edit', compact('product'));
    }

    public function updateProduct(Request $request, int $id)
    {
        $validated = $this->validateProduct($request);

        $products = collect(session('seller_electronic_installment_products', []))
            ->map(function ($product) use ($id, $validated) {
                if ((int) $product['id'] !== $id) {
                    return $product;
                }

                return array_merge($product, $validated);
            })
            ->values()
            ->toArray();

        session(['seller_electronic_installment_products' => $products]);

        return redirect()
            ->route('seller.electronic-installment.index')
            ->with('success', 'Product has been updated.');
    }

    public function deleteProduct(int $id)
    {
        $products = collect(session('seller_electronic_installment_products', []))
            ->reject(fn($product) => (int) $product['id'] === $id)
            ->values()
            ->toArray();

        session(['seller_electronic_installment_products' => $products]);

        return redirect()
            ->route('seller.electronic-installment.index')
            ->with('success', 'Product has been deleted.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'product_name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'in:electronic,gadget'],
            'product_model' => ['nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'condition' => ['required', 'in:new,used,refurbished'],
            'warranty' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'ram' => ['nullable', 'string', 'max:50'],
            'storage' => ['nullable', 'string', 'max:50'],
            'screen_size' => ['nullable', 'string', 'max:50'],
            'battery' => ['nullable', 'string', 'max:50'],
            'camera' => ['nullable', 'string', 'max:100'],
            'operating_system' => ['nullable', 'string', 'max:100'],
            'network' => ['nullable', 'string', 'max:100'],
            'included_items' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }

    private function nextProductId(array $products): int
    {
        return ((int) collect($products)->max('id')) + 1;
    }

    private function findProductIndex(array $products, array $importedProduct): ?int
    {
        $importedSku = strtolower(trim((string) ($importedProduct['sku'] ?? '')));
        $importedKey = $this->productIdentityKey($importedProduct);

        foreach ($products as $index => $product) {
            $productSku = strtolower(trim((string) ($product['sku'] ?? '')));

            if ($importedSku !== '' && $productSku !== '' && $importedSku === $productSku) {
                return $index;
            }

            if ($importedSku === '' && $importedKey === $this->productIdentityKey($product)) {
                return $index;
            }
        }

        return null;
    }

    private function productIdentityKey(array $product): string
    {
        return strtolower(implode('|', [
            trim((string) ($product['product_name'] ?? '')),
            trim((string) ($product['brand'] ?? '')),
            trim((string) ($product['product_model'] ?? '')),
        ]));
    }
}
