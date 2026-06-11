<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GadgetInstallment;
use App\Models\GadgetTenor;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\tenor_pinjaman;
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
            ->map(fn(GadgetInstallment $installment) => $this->formatInstallmentForDisplay($installment));

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

    public function getTenor(GetTenorRequest $req)
    {
        $nip = explode("-", $req->nip);

        if (count($nip) < 2) {
            return response()->json([
                'status' => 400,
                'message' => 'Format NIP tidak valid',
            ], 400);
        }

        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with([
                'karyawanSingle' => function ($q) use ($nip) {
                    $q->where('nip', $nip[1]);
                },
            ])
            ->first();

        if (!$perusahaan) {
            return response()->json([
                'status' => 404,
                'message' => 'Perusahaan tidak ditemukan',
            ], 404);
        }

        if (!$perusahaan->karyawanSingle) {
            return response()->json([
                'status' => 404,
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        $tenor = GadgetTenor::where('company_id', $perusahaan->id)
            ->where('is_active', true)
            ->where(function ($q) use ($req) {
                $q->where(function ($query) use ($req) {
                    $query->whereNull('minimum_amount')
                        ->orWhere('minimum_amount', '<=', $req->nominal);
                })
                    ->where(function ($query) use ($req) {
                        $query->whereNull('maximum_amount')
                            ->orWhere('maximum_amount', '>=', $req->nominal);
                    });
            })
            ->orderBy('tenor_months')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'success get gadget tenor',
            'produk' => GadgetTenorResource::collection($tenor),
        ], 200);
    }

    private function normalizeGetTenorData(Request $request): void
    {
        if (!$request->filled('company_id') && $request->filled('id_perusahaan')) {
            $request->merge([
                'company_id' => $request->id_perusahaan,
            ]);
        }

        if (!$request->filled('variant_id') && $request->filled('product_variant_id')) {
            $request->merge([
                'variant_id' => $request->product_variant_id,
            ]);
        }

        if (!$request->filled('price') && $request->filled('harga')) {
            $request->merge([
                'price' => $request->harga,
            ]);
        }

        if (!$request->filled('nominal') && $request->filled('amount')) {
            $request->merge([
                'nominal' => $request->amount,
            ]);
        }
    }

    public function simulation(Request $request)
    {
        $this->normalizeSimulationData($request);

        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['required', 'exists:product_variants,id'],
            'gadget_tenor_id' => ['nullable', 'exists:gadget_tenors,id'],
            'id_tenor_pinjaman' => ['nullable', 'exists:tenor_pinjamen,id'],
            'installment_month' => ['required_without_all:id_tenor_pinjaman,gadget_tenor_id', 'integer', 'min:1'],
            'interest_rate' => ['nullable', 'numeric', 'min:0'],
        ], [], [
            'product_id' => 'product',
            'variant_id' => 'variant',
            'gadget_tenor_id' => 'tenor gadget',
            'id_tenor_pinjaman' => 'tenor pinjaman',
            'installment_month' => 'tenor cicilan',
            'interest_rate' => 'bunga',
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

        $variant = ProductVariant::where('id', $request->variant_id)
            ->where('product_id', $request->product_id)
            ->where('is_active', true)
            ->first();

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Variant tidak ditemukan atau tidak sesuai dengan product',
            ], 422);
        }

        $price = (float) $variant->price;
        $tenor = null;
        $gadgetTenor = null;
        $installmentMonth = (int) $request->installment_month;
        $interestRate = (float) ($request->interest_rate ?? 0);
        $totalFees = 0;
        $usesGadgetTenor = $request->filled('gadget_tenor_id');
        $usesLoanTenor = $request->filled('id_tenor_pinjaman');

        if ($usesGadgetTenor) {
            $gadgetTenor = GadgetTenor::where('id', $request->gadget_tenor_id)
                ->where('is_active', true)
                ->first();

            if (!$gadgetTenor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenor gadget tidak ditemukan atau tidak aktif',
                ], 404);
            }

            if (
                ($gadgetTenor->minimum_amount !== null && $price < (float) $gadgetTenor->minimum_amount)
                || ($gadgetTenor->maximum_amount !== null && $price > (float) $gadgetTenor->maximum_amount)
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harga gadget tidak sesuai dengan range nominal tenor gadget',
                    'data' => [
                        'price' => round($price, 2),
                        'minimum_amount' => $gadgetTenor->minimum_amount !== null ? round((float) $gadgetTenor->minimum_amount, 2) : null,
                        'maximum_amount' => $gadgetTenor->maximum_amount !== null ? round((float) $gadgetTenor->maximum_amount, 2) : null,
                    ],
                ], 422);
            }

            $installmentMonth = (int) $gadgetTenor->tenor_months;
            $interestRate = (float) $gadgetTenor->interest_rate;
            $totalFees = (float) $gadgetTenor->admin_fee
                + (float) $gadgetTenor->insurance_fee
                + (float) $gadgetTenor->provision_fee
                + (float) ($gadgetTenor->service_fee ?? 0);
        } elseif ($usesLoanTenor) {
            $tenor = tenor_pinjaman::find($request->id_tenor_pinjaman);

            if (!$tenor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenor pinjaman tidak ditemukan',
                ], 404);
            }

            if ($price < (float) $tenor->minimal_nominal || $price > (float) $tenor->maximal_nominal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harga gadget tidak sesuai dengan range nominal tenor pinjaman',
                    'data' => [
                        'price' => round($price, 2),
                        'minimal_nominal' => round((float) $tenor->minimal_nominal, 2),
                        'maximal_nominal' => round((float) $tenor->maximal_nominal, 2),
                        'formatted' => [
                            'price' => 'Rp  ' . number_format($price),
                            'minimal_nominal' => 'Rp  ' . number_format((float) $tenor->minimal_nominal),
                            'maximal_nominal' => 'Rp  ' . number_format((float) $tenor->maximal_nominal),
                        ],
                    ],
                ], 422);
            }

            $installmentMonth = (int) $tenor->tenor;
            $interestRate = (float) $tenor->rate;

            if ($installmentMonth < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenor pinjaman tidak valid',
                ], 422);
            }
        }

        $interestAmount = $usesLoanTenor
            ? $price * ($interestRate / 100) * $installmentMonth
            : $price * ($interestRate / 100);
        $totalAmount = $price + $interestAmount + $totalFees;
        $monthlyInstallment = $usesLoanTenor
            ? ceil(($totalAmount / $installmentMonth) / 1000) * 1000
            : $totalAmount / $installmentMonth;

        return response()->json([
            'success' => true,
            'message' => 'Simulasi cicilan gadget berhasil dihitung',
            'data' => [
                'product_id' => (int) $request->product_id,
                'variant_id' => (int) $request->variant_id,
                'gadget_tenor_id' => $gadgetTenor ? (int) $gadgetTenor->id : null,
                'id_tenor_pinjaman' => $tenor ? (int) $tenor->id : null,
                'price' => round($price, 2),
                'installment_month' => $installmentMonth,
                'interest_rate' => round($interestRate, 2),
                'interest_amount' => round($interestAmount, 2),
                'total_fees' => round($totalFees, 2),
                'total_amount' => round($totalAmount, 2),
                'monthly_installment' => round($monthlyInstallment, 2),
                'formatted' => [
                    'price' => 'Rp  ' . number_format($price),
                    'interest_rate' => round($interestRate, 2) . '%',
                    'interest_amount' => 'Rp  ' . number_format($interestAmount),
                    'total_fees' => 'Rp  ' . number_format($totalFees),
                    'total_amount' => 'Rp  ' . number_format($totalAmount),
                    'monthly_installment' => 'Rp  ' . number_format($monthlyInstallment),
                ],
                'gadget_tenor' => $gadgetTenor ? $this->formatGadgetTenor($gadgetTenor) : null,
                'tenor_pinjaman' => $tenor ? [
                    'id' => $tenor->id,
                    'tenor' => $tenor->tenor . ' bulan',
                    'rate' => $tenor->rate . '%',
                    'minimal_nominal' => 'Rp  ' . number_format((float) $tenor->minimal_nominal),
                    'maximal_nominal' => 'Rp  ' . number_format((float) $tenor->maximal_nominal),
                ] : null,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'brand' => $product->brand,
                    'model' => $product->model,
                ],
                'variant' => [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'color' => $variant->color,
                    'ram_gb' => $variant->ram_gb,
                    'storage_gb' => $variant->storage_gb,
                    'stock' => $variant->stock,
                ],
            ],
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

    private function normalizeSimulationData(Request $request): void
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

        if (!$request->filled('id_tenor_pinjaman') && $request->filled('tenor_pinjaman_id')) {
            $request->merge([
                'id_tenor_pinjaman' => $request->tenor_pinjaman_id,
            ]);
        }

        if (!$request->filled('gadget_tenor_id') && $request->filled('tenor_gadget_id')) {
            $request->merge([
                'gadget_tenor_id' => $request->tenor_gadget_id,
            ]);
        }
    }

    private function formatGadgetTenor(GadgetTenor $tenor): array
    {
        return [
            'id_gadget_tenor' => $tenor->id,
            'gadget_tenor_id' => $tenor->id,
            'company_id' => $tenor->company_id,
            'tenor_months' => $tenor->tenor_months,
            'tenor' => $tenor->tenor_months . ' bulan',
            'interest_rate' => round((float) $tenor->interest_rate, 2),
            'rate' => round((float) $tenor->interest_rate, 2) . ' %',
            'insurance_fee' => round((float) $tenor->insurance_fee, 2),
            'provision_fee' => round((float) $tenor->provision_fee, 2),
            'admin_fee' => round((float) $tenor->admin_fee, 2),
            'tomi_fee' => round((float) $tenor->tomi_fee, 2),
            'service_fee' => round((float) ($tenor->service_fee ?? 0), 2),
            'minimum_amount' => $tenor->minimum_amount !== null ? round((float) $tenor->minimum_amount, 2) : null,
            'maximum_amount' => $tenor->maximum_amount !== null ? round((float) $tenor->maximum_amount, 2) : null,
            'formatted' => [
                'insurance_fee' => 'Rp  ' . number_format((float) $tenor->insurance_fee),
                'provision_fee' => 'Rp  ' . number_format((float) $tenor->provision_fee),
                'admin_fee' => 'Rp  ' . number_format((float) $tenor->admin_fee),
                'tomi_fee' => 'Rp  ' . number_format((float) $tenor->tomi_fee),
                'service_fee' => 'Rp  ' . number_format((float) ($tenor->service_fee ?? 0)),
                'minimum_amount' => $tenor->minimum_amount !== null ? 'Rp  ' . number_format((float) $tenor->minimum_amount) : null,
                'maximum_amount' => $tenor->maximum_amount !== null ? 'Rp  ' . number_format((float) $tenor->maximum_amount) : null,
            ],
        ];
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
