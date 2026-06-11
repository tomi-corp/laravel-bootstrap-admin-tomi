<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GadgetInstallment;
use App\Models\GadgetTenor;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;

class GadgetInstallmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $installments = GadgetInstallment::with([
            'user',
            'seller',
            'product',
            'variant',
            'tenor',
        ])
            ->latest()
            ->get();

        $tableAvailable = true;

        return view(
            'admin.gadget-installments.index',
            compact('installments', 'tableAvailable')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('id', 'desc')->get();

        $products = Product::with('variants')
            ->where('is_active', true)
            ->get();

        $variants = ProductVariant::where('is_active', true)
            ->get();

        $tenors = GadgetTenor::where('is_active', true)
            ->orderBy('tenor_months')
            ->get();

        return view(
            'admin.gadget-installments.create',
            compact('users', 'products', 'variants', 'tenors')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'gadget_tenor_id' => 'required|exists:gadget_tenors,id',

            'down_payment' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $variant = ProductVariant::findOrFail($validated['variant_id']);
        $tenor = GadgetTenor::findOrFail($validated['gadget_tenor_id']);

        $quantity = $validated['quantity'] ?? 1;
        $unitPrice = $variant->price;
        $productPrice = $unitPrice * $quantity;
        $downPayment = $validated['down_payment'] ?? 0;

        $remainingAmount = $productPrice - $downPayment;

        if ($remainingAmount < 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'down_payment' => 'DP tidak boleh lebih besar dari harga produk.',
                ]);
        }

        $totalInterest = $remainingAmount * ($tenor->interest_rate / 100);

        $totalAmount = $remainingAmount
            + $totalInterest
            + $tenor->admin_fee
            + $tenor->insurance_fee
            + $tenor->provision_fee
            + ($tenor->service_fee ?? 0);

        $monthlyInstallment = $totalAmount / $tenor->tenor_months;

        GadgetInstallment::create([
            'invoice_number' => $this->generateInvoiceNumber(),

            'user_id' => $validated['user_id'],
            'product_id' => $validated['product_id'],
            'variant_id' => $validated['variant_id'],
            'gadget_tenor_id' => $validated['gadget_tenor_id'],

            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'product_price' => $productPrice,
            'down_payment' => $downPayment,

            'tenor_months' => $tenor->tenor_months,
            'interest_rate' => $tenor->interest_rate,
            'admin_fee' => $tenor->admin_fee,

            'total_interest' => $totalInterest,
            'total_amount' => $totalAmount,
            'monthly_installment' => $monthlyInstallment,

            'start_date' => $validated['start_date'] ?? null,
            'notes' => $validated['notes'] ?? null,

            'status' => 'pending',
        ]);

        return redirect()
            ->route('admin.gadget-installments.index')
            ->with('success', 'Data cicilan gadget berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $installment = GadgetInstallment::with([
            'user',
            'seller',
            'product',
            'variant',
            'tenor',
        ])
            ->findOrFail($id);

        return view(
            'admin.gadget-installments.show',
            compact('installment')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $installment = GadgetInstallment::with([
            'user',
            'seller',
            'product',
            'variant',
            'tenor',
        ])
            ->findOrFail($id);

        $users = User::orderBy('id', 'desc')->get();

        $products = Product::with('variants')
            ->where('is_active', true)
            ->get();

        $variants = ProductVariant::where('is_active', true)
            ->get();

        $tenors = GadgetTenor::where('is_active', true)
            ->orderBy('tenor_months')
            ->get();

        return view(
            'admin.gadget-installments.edit',
            compact('installment', 'users', 'products', 'variants', 'tenors')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $installment = GadgetInstallment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|string',
            'serial_number' => 'nullable|string|max:255',
            'imei' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $installment->update($validated);

        return redirect()
            ->route('admin.gadget-installments.index')
            ->with('success', 'Data cicilan gadget berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $installment = GadgetInstallment::findOrFail($id);
        $installment->delete();

        return redirect()
            ->route('admin.gadget-installments.index')
            ->with('success', 'Data cicilan gadget berhasil dihapus');
    }

    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');

        $lastId = GadgetInstallment::max('id') ?? 0;
        $nextNumber = str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        return 'GI-' . $date . '-' . $nextNumber;
    }
}
