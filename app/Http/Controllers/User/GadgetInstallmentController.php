<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GadgetInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GadgetInstallmentController extends Controller
{
    public function index()
    {
        $installments = GadgetInstallment::with($this->installmentRelationsForDisplay())
            ->latest()
            ->get()
            ->map(fn (GadgetInstallment $installment) => $this->formatInstallmentForDisplay($installment));

        return response()->json([
            'success' => true,
            'message' => 'Gadget installment list retrieved successfully',
            'data' => $installments,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'seller_id' => ['required', 'exists:sellers,id'],
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['required', 'exists:product_variants,id'],

            'tenor_months' => ['required', 'integer', 'min:1'],
            'interest_rate' => ['required', 'numeric', 'min:0'],

            'start_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $variant = ProductVariant::where('id', $validated['product_variant_id'])
            ->where('product_id', $validated['product_id'])
            ->first();

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Product variant does not belong to selected product',
            ], 422);
        }

        $unitPrice = $variant->price;

        $principal = $unitPrice;

        $interestAmount = $principal * ($validated['interest_rate'] / 100);
        $totalAmount = $principal + $interestAmount;
        $monthlyInstallment = $totalAmount / $validated['tenor_months'];

        $installment = GadgetInstallment::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'user_id' => $validated['user_id'],
            'seller_id' => $validated['seller_id'],
            'product_id' => $validated['product_id'],
            'product_variant_id' => $validated['product_variant_id'],
            'unit_price' => $unitPrice,
            'tenor_months' => $validated['tenor_months'],
            'interest_rate' => $validated['interest_rate'],
            'start_date' => $validated['start_date'],
            'notes' => $validated['notes'] ?? null,
            'total_amount' => round($totalAmount, 2),
            'monthly_installment' => round($monthlyInstallment, 2),
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gadget installment created successfully',
            'data' => $this->formatInstallmentForDisplay(
                $installment->load($this->installmentRelationsForDisplay())
            ),
        ], 201);
    }

    public function show($id)
    {
        $installment = GadgetInstallment::with($this->installmentRelationsForDisplay())->find($id);

        if (!$installment) {
            return response()->json([
                'success' => false,
                'message' => 'Gadget installment not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Gadget installment detail retrieved successfully',
            'data' => $this->formatInstallmentForDisplay($installment),
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $installment = GadgetInstallment::find($id);

        if (!$installment) {
            return response()->json([
                'success' => false,
                'message' => 'Gadget installment not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => ['sometimes', 'exists:users,id'],
            'seller_id' => ['sometimes', 'exists:sellers,id'],
            'product_id' => ['sometimes', 'exists:products,id'],
            'product_variant_id' => ['sometimes', 'exists:product_variants,id'],

            'unit_price' => ['sometimes', 'numeric', 'min:0'],
            'tenor_months' => ['sometimes', 'integer', 'min:1'],
            'interest_rate' => ['sometimes', 'numeric', 'min:0'],

            'start_date' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:pending,approved,rejected,paid,cancelled'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $unitPrice = $validated['unit_price'] ?? $installment->unit_price;
        $tenorMonths = $validated['tenor_months'] ?? $installment->tenor_months;
        $interestRate = $validated['interest_rate'] ?? $installment->interest_rate;

        $principal = $unitPrice;

        $interestAmount = $principal * ($interestRate / 100);
        $totalAmount = $principal + $interestAmount;
        $monthlyInstallment = $totalAmount / $tenorMonths;

        $validated['total_amount'] = round($totalAmount, 2);
        $validated['monthly_installment'] = round($monthlyInstallment, 2);

        $installment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gadget installment updated successfully',
            'data' => $this->formatInstallmentForDisplay(
                $installment->fresh($this->installmentRelationsForDisplay())
            ),
        ], 200);
    }

    public function destroy($id)
    {
        $installment = GadgetInstallment::find($id);

        if (!$installment) {
            return response()->json([
                'success' => false,
                'message' => 'Gadget installment not found',
            ], 404);
        }

        $installment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gadget installment deleted successfully',
        ], 200);
    }

    private function generateInvoiceNumber(): string
    {
        do {
            $invoiceNumber = 'GI-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (GadgetInstallment::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    private function installmentRelationsForDisplay(): array
    {
        return [
            'user:id,name,email',
            'seller:id,store_name,owner_name,email,phone,logo_url,address,city,province,is_active,is_verified',
            'product' => function ($query) {
                $query->select('id', 'seller_id', 'name', 'slug', 'brand', 'model', 'description', 'is_active');
            },
            'product.images' => function ($query) {
                $query->select('id', 'product_id', 'image_url')
                    ->orderBy('id', 'asc');
            },
            'productVariant:id,product_id,sku,color,ram_gb,storage_gb,price,stock,image_url,is_active',
        ];
    }

    private function formatInstallmentForDisplay(GadgetInstallment $installment): GadgetInstallment
    {
        $installment->setAttribute('product_invoice', [
            'invoice_number' => $installment->invoice_number,
            'unit_price' => $installment->unit_price,
            'tenor_months' => $installment->tenor_months,
            'interest_rate' => $installment->interest_rate,
            'total_amount' => $installment->total_amount,
            'monthly_installment' => $installment->monthly_installment,
            'start_date' => $installment->start_date,
            'status' => $installment->status,
            'notes' => $installment->notes,
        ]);

        return $installment;
    }
}
