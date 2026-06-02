<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * List semua product
     */
    public function list(Request $request)
    {
        $query = Product::query()
            ->with($this->productRelationsForDisplay());

        // Filter berdasarkan seller
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Search product
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($item) use ($search) {
                $item->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter status jika kolom status ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data product berhasil diambil',
            'data' => $products,
        ]);
    }

    /**
     * Detail product
     */
    public function detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
        ], [], [
            'product_id' => 'product',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::with($this->productRelationsForDisplay())
            ->find($request->product_id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail product berhasil diambil',
            'data' => $product,
        ]);
    }

    /**
     * List variant berdasarkan product
     */
    public function variants(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
        ], [], [
            'product_id' => 'product',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $variants = ProductVariant::where('product_id', $request->product_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data variant berhasil diambil',
            'data' => $variants,
        ]);
    }

    /**
     * Detail variant
     */
    public function variantDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'variant_id' => ['required', 'exists:product_variants,id'],
        ], [], [
            'variant_id' => 'variant',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $variant = ProductVariant::with('product')
            ->find($request->variant_id);

        if (!$variant) {
            return response()->json([
                'status' => false,
                'message' => 'Variant tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail variant berhasil diambil',
            'data' => $variant,
        ]);
    }

    private function productRelationsForDisplay(): array
    {
        return [
            'seller',
            'variants' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('price', 'asc')
                    ->orderBy('id', 'asc');
            },
            'images' => function ($query) {
                $query->orderBy('id', 'asc');
            },
        ];
    }
}
