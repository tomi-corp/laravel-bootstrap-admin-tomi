<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    /**
     * List variant berdasarkan product
     */
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
        ], [], [
            'product_id' => 'product',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $variants = ProductVariant::where('product_id', $request->product_id)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data product variant berhasil diambil',
            'data' => $variants,
        ]);
    }

    /**
     * Detail variant
     */
    public function detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'variant_id' => ['required', 'exists:product_variants,id'],
        ], [], [
            'variant_id' => 'product variant',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $variant = ProductVariant::with('product')
            ->find($request->variant_id);

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Product variant tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail product variant berhasil diambil',
            'data' => $variant,
        ]);
    }

    /**
     * Simpan variant baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:product_variants,sku'],
            'status' => ['nullable', 'in:active,inactive'],
        ], [], [
            'product_id' => 'product',
            'name' => 'nama variant',
            'price' => 'harga',
            'stock' => 'stok',
            'sku' => 'sku',
            'status' => 'status',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $variant = ProductVariant::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'sku' => $request->sku,
            'status' => $request->status ?? 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product variant berhasil dibuat',
            'data' => $variant,
        ], 201);
    }

    /**
     * Update variant
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'variant_id' => ['required', 'exists:product_variants,id'],
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:product_variants,sku,' . $request->variant_id],
            'status' => ['nullable', 'in:active,inactive'],
        ], [], [
            'variant_id' => 'product variant',
            'product_id' => 'product',
            'name' => 'nama variant',
            'price' => 'harga',
            'stock' => 'stok',
            'sku' => 'sku',
            'status' => 'status',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $variant = ProductVariant::find($request->variant_id);

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Product variant tidak ditemukan',
            ], 404);
        }

        $variant->update([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock ?? 0,
            'sku' => $request->sku,
            'status' => $request->status ?? $variant->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product variant berhasil diperbarui',
            'data' => $variant,
        ]);
    }

    /**
     * Hapus variant
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'variant_id' => ['required', 'exists:product_variants,id'],
        ], [], [
            'variant_id' => 'product variant',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $variant = ProductVariant::find($request->variant_id);

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Product variant tidak ditemukan',
            ], 404);
        }

        $variant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product variant berhasil dihapus',
        ]);
    }
}
