<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $this->normalizeFormData($request);

        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['required', 'exists:product_variants,id'],
            'installment_month' => ['required', 'integer', 'min:1'],
        ], [], [
            'product_id' => 'product',
            'variant_id' => 'variant',
            'installment_month' => 'tenor cicilan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::where('id', $request->product_id)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak ditemukan atau tidak aktif',
            ], 404);
        }

        $order = DB::transaction(function () use ($request) {
            $variant = ProductVariant::where('id', $request->variant_id)
                ->where('product_id', $request->product_id)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant tidak ditemukan atau tidak sesuai dengan product',
                ], 422);
            }

            if ($variant->stock <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok variant tidak tersedia',
                ], 422);
            }

            $price = $variant->price;
            $monthlyInstallment = round($price / $request->installment_month, 2);

            $order = Order::create([
                'user_id' => auth('api')->user()->id,
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'price' => $price,
                'installment_month' => $request->installment_month,
                'monthly_installment' => $monthlyInstallment,
                'status' => 'pending',
            ]);

            $variant->decrement('stock');

            return $order->load(['product', 'variant']);
        });

        if ($order instanceof \Illuminate\Http\JsonResponse) {
            return $order;
        }

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibuat',
            'data' => $order,
        ], 201);
    }

    private function normalizeFormData(Request $request): void
    {
        if (!$request->filled('variant_id') && $request->filled('product_variant_id')) {
            $request->merge([
                'variant_id' => $request->product_variant_id,
            ]);
        }

        if (!$request->filled('installment_month') && $request->filled('tenor_months')) {
            $request->merge([
                'installment_month' => $request->tenor_months,
            ]);
        }
    }
}
