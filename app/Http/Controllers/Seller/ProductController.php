<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $sellerId = auth('seller')->id();

        $products = Product::with(['images', 'variants'])
            ->where('seller_id', $sellerId)
            ->latest()
            ->get();

        return view('seller.electronic-installment.products.index', compact('products'));
    }

    public function create()
    {
        return view('seller.electronic-installment.products.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        DB::transaction(function () use ($request, $validated) {
            $product = Product::create([
                'seller_id' => auth('seller')->id(),
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']) . '-' . time(),
                'brand' => $validated['brand'] ?? null,
                'model' => $validated['model'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            $this->storeProductImages($request, $product);

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
                ]);
            }
        });

        return redirect()
            ->route('seller.electronic-installment.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $product = Product::with(['images', 'variants'])
            ->where('seller_id', auth('seller')->id())
            ->findOrFail($id);

        return view('seller.electronic-installment.products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::with(['images', 'variants'])
            ->where('seller_id', auth('seller')->id())
            ->findOrFail($id);

        $validated = $this->validateProduct($request, $product);

        DB::transaction(function () use ($request, $validated, $product) {
            $product->update([
                'name' => $validated['name'],
                'brand' => $validated['brand'] ?? null,
                'model' => $validated['model'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            $this->deleteSelectedProductImages($request, $product);
            $this->storeProductImages($request, $product);

            $keptVariantIds = [];

            foreach ($request->variants as $index => $variant) {
                $variantImage = $this->storeVariantImage($request, $index);

                $payload = [
                    'product_id' => $product->id,
                    'sku' => $variant['sku'],
                    'ram_gb' => $variant['ram_gb'] ?? null,
                    'storage_gb' => $variant['storage_gb'] ?? null,
                    'color' => $variant['color'] ?? null,
                    'price' => $variant['price'],
                    'stock' => $variant['stock'] ?? 0,
                ];

                if ($variantImage) {
                    $payload['image_url'] = $variantImage;
                }

                $productVariant = null;

                if (!empty($variant['id'])) {
                    $productVariant = ProductVariant::where('product_id', $product->id)
                        ->where('id', $variant['id'])
                        ->first();
                }

                if ($productVariant) {
                    if ($variantImage && $productVariant->image_url) {
                        Storage::disk('public')->delete($productVariant->image_url);
                    }

                    $productVariant->update($payload);
                    $keptVariantIds[] = $productVariant->id;
                } else {
                    $newVariant = ProductVariant::create($payload);
                    $keptVariantIds[] = $newVariant->id;
                }
            }

            $product->variants()
                ->whereNotIn('id', $keptVariantIds)
                ->get()
                ->each(function ($variant) {
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

    public function destroy($id)
    {
        $product = Product::with(['images', 'variants'])
            ->where('seller_id', auth('seller')->id())
            ->findOrFail($id);

        $hasOrders = Order::where('product_id', $product->id)
            ->orWhereIn('variant_id', $product->variants->pluck('id'))
            ->exists();

        if ($hasOrders) {
            return back()->withErrors('Produk tidak bisa dihapus karena sudah memiliki order.');
        }

        DB::transaction(function () use ($product) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_url);
                $image->delete();
            }

            foreach ($product->variants as $variant) {
                if ($variant->image_url) {
                    Storage::disk('public')->delete($variant->image_url);
                }

                $variant->delete();
            }

            $product->delete();
        });

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],

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
        ]);

        $ownedVariantIds = $product ? $product->variants->pluck('id')->all() : [];

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
            ->each(function ($image) {
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
