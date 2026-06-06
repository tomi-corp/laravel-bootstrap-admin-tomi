<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Exports\SellerElectronicProductTemplateExport;
use App\Imports\SellerElectronicProductImport;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ElectronicInstallmentController extends Controller
{
    public function index()
    {
        $sellerId = auth('seller')->id();

        $products = Product::with(['images', 'variants'])
            ->where('seller_id', $sellerId)
            ->latest()
            ->get();

        return view('seller.electronic-installment.index', compact('products'));
    }

    public function createProduct()
    {
        return view('seller.electronic-installment.create');
    }

    public function storeProduct(Request $request)
    {
        $validated = $this->validateProduct($request);

        DB::transaction(function () use ($request, $validated) {

            $sellerId = auth('seller')->id();

            // 1. CREATE PRODUCT
            $product = Product::create([
                'seller_id' => $sellerId,
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']) . '-' . time(),
                'brand' => $validated['brand'] ?? null,
                'model' => $validated['model'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => (bool) ($validated['is_active'] ?? true),
            ]);

            $this->storeProductImages($request, $product);

            // 2. CREATE VARIANTS
            foreach ($request->variants as $index => $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variant['sku'],
                    'ram_gb' => $variant['ram_gb'] ?? null,
                    'storage_gb' => $variant['storage_gb'] ?? null,
                    'color' => $variant['color'] ?? null,
                    'price' => $variant['price'],
                    'stock' => $variant['stock'] ?? 0,
                    'image_url' => $this->storeVariantImage($request, $index),
                    'is_active' => (bool) ($variant['is_active'] ?? true),
                ]);
            }
        });

        return redirect()
            ->route('seller.electronic-installment.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function editProduct($id)
    {
        $product = Product::with(['images', 'variants'])
            ->where('seller_id', auth('seller')->id())
            ->findOrFail($id);

        return view('seller.electronic-installment.edit', compact('product'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::with(['images', 'variants'])
            ->where('seller_id', auth('seller')->id())
            ->findOrFail($id);

        $validated = $this->validateProduct($request, $product);

        DB::transaction(function () use ($request, $validated, $product) {

            // update product
            $product->update([
                'name' => $validated['name'],
                'brand' => $validated['brand'] ?? null,
                'model' => $validated['model'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => (bool) ($validated['is_active'] ?? true),
            ]);

            $this->deleteSelectedProductImages($request, $product);
            $this->storeProductImages($request, $product);

            $keptVariantIds = [];

            foreach ($request->variants as $index => $variant) {
                $variantImage = $this->storeVariantImage($request, $index);
                $variantId = $variant['id'] ?? null;

                $payload = [
                    'product_id' => $product->id,
                    'sku' => $variant['sku'],
                    'ram_gb' => $variant['ram_gb'] ?? null,
                    'storage_gb' => $variant['storage_gb'] ?? null,
                    'color' => $variant['color'] ?? null,
                    'price' => $variant['price'],
                    'stock' => $variant['stock'] ?? 0,
                    'is_active' => (bool) ($variant['is_active'] ?? true),
                ];

                if ($variantImage) {
                    $payload['image_url'] = $variantImage;
                }

                if ($variantId) {
                    $productVariant = ProductVariant::where('product_id', $product->id)
                        ->where('id', $variantId)
                        ->first();

                    if ($productVariant) {
                        if ($variantImage && $productVariant->image_url) {
                            Storage::disk('public')->delete($productVariant->image_url);
                        }

                        $productVariant->update($payload);
                        $keptVariantIds[] = $productVariant->id;

                        continue;
                    }
                }

                $newVariant = ProductVariant::create($payload);
                $keptVariantIds[] = $newVariant->id;
            }

            $product->variants()
                ->whereNotIn('id', $keptVariantIds)
                ->get()
                ->each(function (ProductVariant $variant) {
                    if ($variant->image_url) {
                        Storage::disk('public')->delete($variant->image_url);
                    }

                    $variant->delete();
                });
        });

        return redirect()
            ->route('seller.electronic-installment.index')
            ->with('success', 'Produk berhasil diupdate.');
    }

    public function deleteProduct($id)
    {
        $product = Product::with(['images', 'variants'])
            ->where('seller_id', auth('seller')->id())
            ->where('id', $id)
            ->firstOrFail();

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_url);
        }

        foreach ($product->variants as $variant) {
            if ($variant->image_url) {
                Storage::disk('public')->delete($variant->image_url);
            }
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    public function importProductPage()
    {
        return view('seller.electronic-installment.import');
    }

    public function downloadTemplate()
    {
        return Excel::download(new SellerElectronicProductTemplateExport(), 'template-produk-elektronik.xlsx');
    }

    public function importProducts(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new SellerElectronicProductImport();
        Excel::import($import, $request->file('file'));

        DB::transaction(function () use ($import) {
            foreach ($import->getProducts() as $row) {
                $variant = ProductVariant::with('product')
                    ->where('sku', $row['sku'])
                    ->first();

                if ($variant && (int) $variant->product->seller_id !== (int) auth('seller')->id()) {
                    throw ValidationException::withMessages([
                        'file' => "SKU {$row['sku']} sudah digunakan seller lain.",
                    ]);
                }

                $product = $variant?->product ?? Product::create([
                    'seller_id' => auth('seller')->id(),
                    'name' => $row['product_name'],
                    'slug' => Str::slug($row['product_name']) . '-' . Str::lower(Str::random(6)),
                    'brand' => $row['brand'] ?? null,
                    'model' => $row['product_model'] ?? null,
                    'description' => $row['description'] ?? null,
                ]);

                $product->update([
                    'name' => $row['product_name'],
                    'brand' => $row['brand'] ?? null,
                    'model' => $row['product_model'] ?? null,
                    'description' => $row['description'] ?? null,
                ]);

                $variantPayload = [
                    'product_id' => $product->id,
                    'ram_gb' => $row['ram_gb'] ?? null,
                    'storage_gb' => $row['storage_gb'] ?? null,
                    'color' => $row['color'] ?? null,
                    'price' => $row['price'],
                    'stock' => $row['stock'] ?? 0,
                ];

                if ($variant) {
                    $variant->update($variantPayload);
                } else {
                    ProductVariant::create($variantPayload + ['sku' => $row['sku']]);
                }
            }
        });

        return redirect()
            ->route('seller.electronic-installment.index')
            ->with('success', 'Produk berhasil diimport.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['image', 'max:2048'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer'],

            'variants' => ['required', 'array', 'min:1'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.sku' => ['required', 'string', 'max:100'],
            'variants.*.ram_gb' => ['nullable', 'integer', 'min:0'],
            'variants.*.storage_gb' => ['nullable', 'integer', 'min:0'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.image' => ['nullable', 'image', 'max:2048'],
            'variants.*.is_active' => ['required', 'boolean'],
        ]);

        $ownedVariantIds = $product
            ? $product->variants->pluck('id')->all()
            : [];

        foreach ($request->input('variants', []) as $index => $variant) {
            $variantId = in_array((int) ($variant['id'] ?? 0), $ownedVariantIds, true)
                ? (int) $variant['id']
                : null;

            $request->validate([
                "variants.$index.sku" => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('product_variants', 'sku')->ignore($variantId),
                ],
            ]);
        }

        return $validated;
    }

    private function storeProductImages(Request $request, Product $product): void
    {
        $existingCount = $product->images()->count();
        $newImages = $request->file('images', []);

        if ($existingCount + count($newImages) > 3) {
            throw ValidationException::withMessages([
                'images' => 'Gambar produk maksimal 3 file.',
            ]);
        }

        foreach ($newImages as $image) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $image->store('products', 'public'),
            ]);
        }
    }

    private function deleteSelectedProductImages(Request $request, Product $product): void
    {
        $imageIds = $request->input('remove_image_ids', []);

        if (empty($imageIds)) {
            return;
        }

        $product->images()
            ->whereIn('id', $imageIds)
            ->get()
            ->each(function (ProductImage $image) {
                Storage::disk('public')->delete($image->image_url);
                $image->delete();
            });
    }

    private function storeVariantImage(Request $request, int|string $index): ?string
    {
        $image = $request->file("variants.$index.image");

        if (!$image) {
            return null;
        }

        return $image->store('product-variants', 'public');
    }
}
