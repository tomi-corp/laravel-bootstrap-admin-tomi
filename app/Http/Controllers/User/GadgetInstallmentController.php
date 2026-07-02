<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Pinjaman\DataKaryawanResource;
use App\Models\GadgetInstallment;
use App\Models\GadgetTenor;
use App\Models\ListPerusahaan;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\tenor_pinjaman;
use App\Models\User;
use App\Models\verif_otp_pengajuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GadgetInstallmentController extends Controller
{
    private array $columnCache = [];

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
            'seller_id' => ['nullable', 'exists:sellers,id'],
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['required_without:variant_id', 'exists:product_variants,id'],
            'variant_id' => ['required_without:product_variant_id', 'exists:product_variants,id'],
            'gadget_tenor_id' => ['nullable', 'exists:gadget_tenors,id'],
            'tenor_months' => ['required_without:gadget_tenor_id', 'integer', 'min:1'],
            'interest_rate' => ['nullable', 'numeric', 'min:0'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
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
        $variantId = $validated['variant_id'] ?? $validated['product_variant_id'];

        $variant = ProductVariant::where('id', $variantId)
            ->where('product_id', $validated['product_id'])
            ->first();

        if (! $variant) {
            return response()->json([
                'success' => false,
                'message' => 'Product variant does not belong to selected product',
            ], 422);
        }

        $tenor = isset($validated['gadget_tenor_id'])
            ? GadgetTenor::find($validated['gadget_tenor_id'])
            : null;

        $calculation = $this->calculateInstallment(
            (float) $variant->price,
            (int) ($validated['quantity'] ?? 1),
            (float) ($validated['down_payment'] ?? 0),
            $tenor?->tenor_months ?? (int) $validated['tenor_months'],
            (float) ($tenor?->interest_rate ?? ($validated['interest_rate'] ?? 0)),
            $tenor
        );

        if ($calculation['remaining_amount'] < 0) {
            return response()->json([
                'success' => false,
                'message' => 'DP tidak boleh lebih besar dari harga produk.',
            ], 422);
        }

        $installment = GadgetInstallment::create($this->filterInstallmentPayload([
            'invoice_number' => $this->generateInvoiceNumber(),
            'user_id' => $validated['user_id'],
            'seller_id' => $validated['seller_id'] ?? Product::find($validated['product_id'])?->seller_id,
            'product_id' => $validated['product_id'],
            'variant_id' => $variant->id,
            'product_variant_id' => $variant->id,
            'gadget_tenor_id' => $tenor?->id,
            'quantity' => $calculation['quantity'],
            'unit_price' => $calculation['unit_price'],
            'product_price' => $calculation['product_price'],
            'down_payment' => $calculation['down_payment'],
            'tenor_months' => $calculation['tenor_months'],
            'interest_rate' => $calculation['interest_rate'],
            'admin_fee' => $calculation['admin_fee'],
            'total_interest' => $calculation['total_interest'],
            'total_amount' => $calculation['total_amount'],
            'monthly_installment' => $calculation['monthly_installment'],
            'start_date' => $validated['start_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]));

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

        if (! $installment) {
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

    public function cekPegawai(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $perusahaan = $this->findCompanyByNip($request->nip);

        if (! $perusahaan || ! $perusahaan->karyawanSingle) {
            return response()->json([
                'status' => 400,
                'message' => 'Mohon maaf, data Anda belum ditemukan di aplikasi Tomi. Silakan hubungi HRD Anda agar data Anda dapat segera didaftarkan.',
            ], 400);
        }

        $tanggalBergabung = Carbon::parse($perusahaan->karyawanSingle->tanggal_bergabung);
        $masaKerjaBulan = intval($tanggalBergabung->floatDiffInMonths(Carbon::now()));

        if ($perusahaan->karyawanSingle->status_sp != 0) {
            return response()->json([
                'status' => 400,
                'message' => 'Mohon maaf, cicilan gadget hanya dapat dilakukan dengan karyawan berstatus bebas surat peringatan',
            ], 400);
        }

        if ($perusahaan->karyawanSingle->status !== 'tetap' && $masaKerjaBulan < 6) {
            return response()->json([
                'status' => 400,
                'message' => 'Mohon maaf, hanya karyawan dengan status tetap / masa kerja > 6 Bulan untuk melakukan cicilan gadget',
            ], 400);
        }

        $pendingOtp = $this->activeInstallmentQuery($perusahaan->karyawanSingle->id)
            ->where('status', 'on_proses');

        if ($this->hasInstallmentColumn('status_otp')) {
            $pendingOtp->where('status_otp', 'pending');
        }

        if ($pendingOtp->first()) {
            return response()->json([
                'status' => 200,
                'pinjaman' => false,
                'message' => 'anda memiliki validasi otp cicilan gadget yang belum diselesaikan',
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'data' => new DataKaryawanResource($perusahaan),
        ], 200);
    }

    public function maximalMinimal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $perusahaan = $this->findCompanyByNip($request->nip, ['amount_pinjaman']);

        if (! $perusahaan || ! $perusahaan->karyawanSingle || ! $perusahaan->amount_pinjaman) {
            return response()->json([
                'status' => 400,
                'message' => 'Data NIP tidak ditemukan',
            ], 400);
        }

        $maksimal = min(
            (float) $perusahaan->karyawanSingle->max_plafon,
            (float) $perusahaan->amount_pinjaman->maximal
        );

        return response()->json([
            'status' => 200,
            'maximal' => 'Rp  '.number_format($maksimal),
            'minimal' => 'Rp  '.number_format((float) $perusahaan->amount_pinjaman->minimal),
        ], 200);
    }

    public function getTenor(Request $request)
    {
        $this->normalizeGetTenorData($request);

        $validator = Validator::make($request->all(), [
            'nip' => ['required', 'string'],
            'nominal' => ['nullable', 'numeric', 'min:0'],
            'product_id' => ['nullable', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 400);
        }

        $perusahaan = $this->findCompanyByNip($request->nip);

        if (! $perusahaan || ! $perusahaan->karyawanSingle) {
            return response()->json([
                'status' => 404,
                'message' => 'Data pegawai tidak ditemukan',
            ], 404);
        }

        $nominal = $this->resolveNominalFromRequest($request);

        $tenor = GadgetTenor::where(function ($query) use ($perusahaan) {
            $query->where('company_id', $perusahaan->id)
                ->orWhereNull('company_id');
        })
            ->where('is_active', true)
            ->when($nominal > 0, function ($query) use ($nominal) {
                $query->where(function ($range) use ($nominal) {
                    $range->where(function ($q) use ($nominal) {
                        $q->whereNull('minimum_amount')
                            ->orWhere('minimum_amount', '<=', $nominal);
                    })
                        ->where(function ($q) use ($nominal) {
                            $q->whereNull('maximum_amount')
                                ->orWhere('maximum_amount', '>=', $nominal);
                        });
                });
            })
            ->orderBy('tenor_months')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'success get gadget tenor',
            'produk' => $tenor->map(fn (GadgetTenor $item) => $this->formatGadgetTenor($item))->values(),
        ], 200);
    }

    public function getSimulation(Request $request)
    {
        return $this->simulation($request);
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
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:1'],
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

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak ditemukan atau tidak aktif',
            ], 404);
        }

        $variant = ProductVariant::where('id', $request->variant_id)
            ->where('product_id', $request->product_id)
            ->where('is_active', true)
            ->first();

        if (! $variant) {
            return response()->json([
                'success' => false,
                'message' => 'Variant tidak ditemukan atau tidak sesuai dengan product',
            ], 422);
        }

        $gadgetTenor = null;
        $loanTenor = null;
        $tenorMonths = (int) $request->installment_month;
        $interestRate = (float) ($request->interest_rate ?? 0);

        if ($request->filled('gadget_tenor_id')) {
            $gadgetTenor = GadgetTenor::where('id', $request->gadget_tenor_id)
                ->where('is_active', true)
                ->first();

            if (! $gadgetTenor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenor gadget tidak ditemukan atau tidak aktif',
                ], 404);
            }

            $productPrice = (float) $variant->price * (int) ($request->quantity ?? 1);

            if (
                ($gadgetTenor->minimum_amount !== null && $productPrice < (float) $gadgetTenor->minimum_amount)
                || ($gadgetTenor->maximum_amount !== null && $productPrice > (float) $gadgetTenor->maximum_amount)
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harga gadget tidak sesuai dengan range nominal tenor gadget',
                    'data' => [
                        'price' => round($productPrice, 2),
                        'minimum_amount' => $gadgetTenor->minimum_amount !== null ? round((float) $gadgetTenor->minimum_amount, 2) : null,
                        'maximum_amount' => $gadgetTenor->maximum_amount !== null ? round((float) $gadgetTenor->maximum_amount, 2) : null,
                    ],
                ], 422);
            }

            $tenorMonths = (int) $gadgetTenor->tenor_months;
            $interestRate = (float) $gadgetTenor->interest_rate;
        } elseif ($request->filled('id_tenor_pinjaman')) {
            $loanTenor = tenor_pinjaman::find($request->id_tenor_pinjaman);

            if (! $loanTenor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenor pinjaman tidak ditemukan',
                ], 404);
            }

            $productPrice = (float) $variant->price * (int) ($request->quantity ?? 1);

            if ($productPrice < (float) $loanTenor->minimal_nominal || $productPrice > (float) $loanTenor->maximal_nominal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harga gadget tidak sesuai dengan range nominal tenor pinjaman',
                ], 422);
            }

            $tenorMonths = (int) $loanTenor->tenor;
            $interestRate = (float) $loanTenor->rate;
        }

        $calculation = $this->calculateInstallment(
            (float) $variant->price,
            (int) ($request->quantity ?? 1),
            (float) ($request->down_payment ?? 0),
            $tenorMonths,
            $interestRate,
            $gadgetTenor,
            $loanTenor !== null
        );

        if ($calculation['remaining_amount'] < 0) {
            return response()->json([
                'success' => false,
                'message' => 'DP tidak boleh lebih besar dari harga produk.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Simulasi cicilan gadget berhasil dihitung',
            'data' => [
                'product_id' => (int) $request->product_id,
                'variant_id' => (int) $request->variant_id,
                'gadget_tenor_id' => $gadgetTenor ? (int) $gadgetTenor->id : null,
                'id_tenor_pinjaman' => $loanTenor ? (int) $loanTenor->id : null,
                ...$this->formatCalculation($calculation),
                'gadget_tenor' => $gadgetTenor ? $this->formatGadgetTenor($gadgetTenor) : null,
                'tenor_pinjaman' => $loanTenor ? [
                    'id' => $loanTenor->id,
                    'tenor' => $loanTenor->tenor.' bulan',
                    'rate' => $loanTenor->rate.'%',
                    'minimal_nominal' => 'Rp  '.number_format((float) $loanTenor->minimal_nominal),
                    'maximal_nominal' => 'Rp  '.number_format((float) $loanTenor->maximal_nominal),
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

    public function actionGadgetInstallment(Request $request)
    {
        $this->normalizeSimulationData($request);

        $validator = Validator::make($request->all(), [
            'nip' => ['required', 'string'],
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['required', 'exists:product_variants,id'],
            'gadget_tenor_id' => ['required_without:id_tenor_pinjaman', 'nullable', 'exists:gadget_tenors,id'],
            'id_tenor_pinjaman' => ['nullable', 'exists:tenor_pinjamen,id'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 400);
        }

        $perusahaan = $this->findCompanyByNip($request->nip, ['amount_pinjaman']);

        if (! $perusahaan || ! $perusahaan->karyawanSingle) {
            return response()->json([
                'status' => 400,
                'message' => 'Data NIP tidak ditemukan',
            ], 400);
        }

        $user = User::where('id', auth('api')->user()->id)->with('kyc')->first();
        $kycValidation = $this->validateUserKycAndPrivy($user);

        if ($kycValidation) {
            return $kycValidation;
        }

        $active = $this->activeInstallmentQuery($perusahaan->karyawanSingle->id)
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'on_proses')
                    ->orWhere('status', 'approve')
                    ->orWhere('status', 'approved');
            });

        if ($this->hasInstallmentColumn('status_otp')) {
            $active->where('status_otp', 'success');
        }

        if ($active->first()) {
            return response()->json([
                'status' => 400,
                'message' => 'Mohon maaf, saat ini Anda masih memiliki cicilan gadget aktif. Terima kasih',
            ], 400);
        }

        $pendingOtp = $this->activeInstallmentQuery($perusahaan->karyawanSingle->id)
            ->where('status', 'on_proses');

        if ($this->hasInstallmentColumn('status_otp')) {
            $pendingOtp->where('status_otp', 'pending');
        }

        if ($pendingOtp->first()) {
            return response()->json([
                'status' => 200,
                'pinjaman' => false,
                'message' => 'Anda memiliki validasi otp cicilan gadget yang belum diselesaikan',
            ], 200);
        }

        if ($perusahaan->karyawanSingle->updated_at && Carbon::parse($perusahaan->karyawanSingle->updated_at)->lessThan(Carbon::now()->startOfMonth())) {
            return response()->json([
                'status' => 400,
                'message' => 'Kami melihat pembaruan data Anda sudah melewati batas waktu yang ditentukan, Mohon bantuannya untuk segera menghubungi Human Capital (HC) Perusahaan Anda agar mereka dapat memproses dan mengirimkan data terbaru Anda ke aplikasi TOMI. Kami siap membantu menyelesaikan prosesnya setelah data dari HC kami terima. Terima kasih!',
            ], 400);
        }

        $variant = ProductVariant::where('id', $request->variant_id)
            ->where('product_id', $request->product_id)
            ->where('is_active', true)
            ->first();

        if (! $variant) {
            return response()->json([
                'status' => 400,
                'message' => 'Variant tidak ditemukan atau tidak sesuai dengan product',
            ], 400);
        }

        $gadgetTenor = null;
        $loanTenor = null;

        if ($request->filled('gadget_tenor_id')) {
            $gadgetTenor = GadgetTenor::where('id', $request->gadget_tenor_id)->where('is_active', true)->first();
            if (! $gadgetTenor) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Tenor gadget tidak ditemukan atau tidak aktif',
                ], 400);
            }
        } else {
            $loanTenor = tenor_pinjaman::find($request->id_tenor_pinjaman);
        }

        $calculation = $this->calculateInstallment(
            (float) $variant->price,
            (int) ($request->quantity ?? 1),
            (float) ($request->down_payment ?? 0),
            (int) ($gadgetTenor?->tenor_months ?? $loanTenor?->tenor),
            (float) ($gadgetTenor?->interest_rate ?? $loanTenor?->rate ?? 0),
            $gadgetTenor,
            $loanTenor !== null
        );

        if ($calculation['remaining_amount'] < 0) {
            return response()->json([
                'status' => 400,
                'message' => 'DP tidak boleh lebih besar dari harga produk.',
            ], 400);
        }

        $installment = GadgetInstallment::create($this->filterInstallmentPayload([
            'invoice_number' => $this->generateInvoiceNumber(),
            'user_id' => $user->id,
            'seller_id' => Product::find($request->product_id)?->seller_id,
            'product_id' => $request->product_id,
            'variant_id' => $variant->id,
            'product_variant_id' => $variant->id,
            'gadget_tenor_id' => $gadgetTenor?->id,
            'id_tenor_pinjaman' => $loanTenor?->id,
            'id_perusahaan' => $perusahaan->id,
            'company_id' => $perusahaan->id,
            'id_karyawan' => $perusahaan->karyawanSingle->id,
            'id_kyc' => $user->kyc?->id,
            'quantity' => $calculation['quantity'],
            'unit_price' => $calculation['unit_price'],
            'product_price' => $calculation['product_price'],
            'price' => $calculation['product_price'],
            'down_payment' => $calculation['down_payment'],
            'tenor_months' => $calculation['tenor_months'],
            'installment_month' => $calculation['tenor_months'],
            'interest_rate' => $calculation['interest_rate'],
            'admin_fee' => $calculation['admin_fee'],
            'total_interest' => $calculation['total_interest'],
            'total_amount' => $calculation['total_amount'],
            'monthly_installment' => $calculation['monthly_installment'],
            'start_date' => $request->start_date,
            'notes' => $request->notes,
            'status' => 'on_proses',
            'status_otp' => 'pending',
            'ttd' => 'n',
        ]));

        $codeOTP = rand(1000, 9999);
        $otp = new verif_otp_pengajuan;
        $otp->user_id = $user->id;
        $otp->pinjaman_id = $installment->id;
        $otp->failed = 0;
        $otp->otp = $codeOTP;
        $otp->expired = Carbon::now()->addHour(24);
        $otp->save();

        sendOTPWhatsapp($perusahaan->karyawanSingle->whatsapp, $codeOTP);

        $nomorWa = $perusahaan->karyawanSingle->whatsapp;
        $mask = substr($nomorWa, 0, 4)
            .str_repeat('x', max(strlen($nomorWa) - 7, 0))
            .substr($nomorWa, -3);

        return response()->json([
            'status' => 200,
            'pinjaman' => true,
            'message' => 'success, harap periksa OTP yang telah dikirimkan melalui whatsapp '.$mask,
            'data' => $this->formatInstallmentForDisplay($installment->fresh($this->installmentRelationsForDisplay())),
        ], 200);
    }

    public function actionGadgetInstallmentOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $verif = verif_otp_pengajuan::where('user_id', auth('api')->user()->id)
            ->where('otp', $request->otp)
            ->first();

        if (! $verif) {
            $verifFailed = verif_otp_pengajuan::where('user_id', auth('api')->user()->id)->first();

            if (! $verifFailed) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Silahkan request OTP kembali',
                ], 400);
            }

            if ($verifFailed->failed >= 3) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Anda telah 3x melakukan kesalahan pada memasukan kode OTP, Silahkan hubungi customer service kami',
                ], 400);
            }

            $verifFailed->failed++;
            $verifFailed->save();

            return response()->json([
                'status' => 400,
                'message' => 'OTP tidak benar',
            ], 400);
        }

        if ($verif->failed >= 3) {
            return response()->json([
                'status' => 400,
                'message' => 'Anda telah 3x melakukan kesalahan pada memasukan kode OTP, Silahkan hubungi customer service kami',
            ], 400);
        }

        if ($verif->expired < Carbon::now()) {
            return response()->json([
                'status' => 400,
                'message' => 'OTP kadaluarsa',
            ], 400);
        }

        $installment = GadgetInstallment::where('id', $verif->pinjaman_id)
            ->where('user_id', auth('api')->user()->id)
            ->first();

        if (! $installment) {
            return response()->json([
                'status' => 400,
                'message' => 'Data pengajuan cicilan gadget tidak ditemukan',
            ], 400);
        }

        $this->fillIfColumn($installment, 'status_otp', 'success');
        $this->fillIfColumn($installment, 'status', 'on_proses');
        $installment->save();
        $verif->delete();

        return response()->json([
            'status' => 200,
            'message' => $this->hasInstallmentColumn('document_name')
                ? 'OTP benar, Silahkan tanda tanganin berkas untuk melanjutkan'
                : 'OTP benar, pengajuan cicilan gadget berhasil dikirim untuk diproses',
        ], 200);
    }

    public function actionGadgetInstallmentOTPUlang()
    {
        $query = GadgetInstallment::where('user_id', auth('api')->user()->id);

        if ($this->hasInstallmentColumn('status_otp')) {
            $query->where('status_otp', 'pending');
        } else {
            $query->where('status', 'on_proses');
        }

        $installment = $query->latest()->first();

        if (! $installment) {
            return response()->json([
                'status' => 400,
                'message' => 'Data pengajuan anda tidak ditemukan',
            ], 400);
        }

        $perusahaan = null;
        if ($this->hasInstallmentColumn('id_karyawan') && $installment->id_karyawan) {
            $perusahaan = ListPerusahaan::with(['karyawanSingle' => function ($query) use ($installment) {
                $query->where('id', $installment->id_karyawan);
            }])->whereHas('karyawanSingle', function ($query) use ($installment) {
                $query->where('id', $installment->id_karyawan);
            })->first();
        }

        if (! $perusahaan || ! $perusahaan->karyawanSingle) {
            return response()->json([
                'status' => 400,
                'message' => 'Data karyawan pengajuan tidak ditemukan',
            ], 400);
        }

        $codeOTP = rand(1000, 9999);
        $verifOTP = verif_otp_pengajuan::where('pinjaman_id', $installment->id)
            ->where('user_id', auth('api')->user()->id)
            ->first();

        if (! $verifOTP) {
            $verifOTP = new verif_otp_pengajuan;
            $verifOTP->pinjaman_id = $installment->id;
            $verifOTP->user_id = auth('api')->user()->id;
        }

        $verifOTP->failed = 0;
        $verifOTP->otp = $codeOTP;
        $verifOTP->expired = Carbon::now()->addHour(23);
        $verifOTP->save();

        sendOTPWhatsapp($perusahaan->karyawanSingle->whatsapp, $codeOTP);

        return response()->json([
            'status' => 200,
            'message' => 'OTP berhasil dikirim',
        ], 200);
    }

    public function showberkas()
    {
        if (! $this->hasInstallmentColumn('document_name')) {
            return response()->json([
                'status' => 200,
                'message' => 'Berkas cicilan gadget belum tersedia',
            ], 200);
        }

        $installment = GadgetInstallment::where('user_id', auth('api')->user()->id)
            ->where('status', 'on_proses')
            ->when($this->hasInstallmentColumn('status_otp'), fn ($q) => $q->where('status_otp', 'success'))
            ->when($this->hasInstallmentColumn('ttd'), fn ($q) => $q->where('ttd', 'n'))
            ->first();

        if (! $installment || ! $installment->document_name) {
            return response()->json([
                'status' => 200,
                'message' => 'Anda tidak memiliki berkas cicilan gadget yang harus ditanda tangani',
            ], 200);
        }

        $path = 'assets/pk/'.$installment->document_name;

        if (! Storage::exists($path)) {
            return response()->json([
                'status' => 400,
                'message' => 'Berkas cicilan gadget tidak ditemukan',
            ], 400);
        }

        $document = 'data:application/pdf;base64,'.base64_encode(Storage::get($path));

        return response()->json([
            'status' => 200,
            'message' => '',
            'document' => $document,
        ], 200);
    }

    public function showBerkasHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_gadget_installment' => ['required_without:id_pinjaman', 'nullable', 'integer'],
            'id_pinjaman' => ['required_without:id_gadget_installment', 'nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $id = $request->id_gadget_installment ?? $request->id_pinjaman;
        $installment = GadgetInstallment::where('id', $id)
            ->where('user_id', auth('api')->user()->id)
            ->first();

        if (! $installment) {
            return response()->json([
                'status' => 400,
                'message' => 'Cicilan gadget tidak tersedia',
            ], 400);
        }

        if (! $this->hasInstallmentColumn('document_name') || ! $installment->document_name) {
            return response()->json([
                'status' => 200,
                'message' => 'Berkas cicilan gadget belum tersedia',
            ], 200);
        }

        $path = 'assets/pk/'.$installment->document_name;

        if (! Storage::exists($path)) {
            return response()->json([
                'status' => 400,
                'message' => 'Berkas cicilan gadget tidak ditemukan',
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => '',
            'document' => 'data:application/pdf;base64,'.base64_encode(Storage::get($path)),
        ], 200);
    }

    public function tandaTangan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $user = User::where('id', auth('api')->user()->id)->first();

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 400,
                'message' => 'Password tidak sesuai',
            ], 400);
        }

        $installment = GadgetInstallment::where('user_id', $user->id)
            ->where('status', 'on_proses')
            ->when($this->hasInstallmentColumn('status_otp'), fn ($q) => $q->where('status_otp', 'success'))
            ->first();

        if (! $installment) {
            return response()->json([
                'status' => 200,
                'message' => 'Anda tidak memiliki cicilan gadget yang harus ditanda tangani',
            ], 200);
        }

        $this->fillIfColumn($installment, 'ttd', 'y');
        $this->fillIfColumn($installment, 'status', 'pending');
        $installment->save();

        return response()->json([
            'status' => 200,
            'message' => 'Verifikasi tanda tangan berhasil! Pengajuan cicilan gadget Anda segera diproses.',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $installment = GadgetInstallment::find($id);

        if (! $installment) {
            return response()->json([
                'success' => false,
                'message' => 'Gadget installment not found',
            ], 404);
        }

        $this->normalizeSimulationData($request);

        $validator = Validator::make($request->all(), [
            'user_id' => ['sometimes', 'exists:users,id'],
            'seller_id' => ['sometimes', 'exists:sellers,id'],
            'product_id' => ['sometimes', 'exists:products,id'],
            'variant_id' => ['sometimes', 'exists:product_variants,id'],
            'product_variant_id' => ['sometimes', 'exists:product_variants,id'],
            'gadget_tenor_id' => ['sometimes', 'exists:gadget_tenors,id'],
            'unit_price' => ['sometimes', 'numeric', 'min:0'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'tenor_months' => ['sometimes', 'integer', 'min:1'],
            'interest_rate' => ['sometimes', 'numeric', 'min:0'],
            'start_date' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $unitPrice = (float) ($validated['unit_price'] ?? $installment->unit_price);
        $quantity = (int) ($validated['quantity'] ?? $installment->quantity ?? 1);
        $downPayment = (float) ($validated['down_payment'] ?? $installment->down_payment ?? 0);
        $tenorMonths = (int) ($validated['tenor_months'] ?? $installment->tenor_months);
        $interestRate = (float) ($validated['interest_rate'] ?? $installment->interest_rate);
        $tenor = isset($validated['gadget_tenor_id'])
            ? GadgetTenor::find($validated['gadget_tenor_id'])
            : $installment->gadgetTenor;

        $calculation = $this->calculateInstallment($unitPrice, $quantity, $downPayment, $tenorMonths, $interestRate, $tenor);

        if ($calculation['remaining_amount'] < 0) {
            return response()->json([
                'success' => false,
                'message' => 'DP tidak boleh lebih besar dari harga produk.',
            ], 422);
        }

        $payload = array_merge($validated, [
            'variant_id' => $validated['variant_id'] ?? $validated['product_variant_id'] ?? null,
            'product_variant_id' => $validated['variant_id'] ?? $validated['product_variant_id'] ?? null,
            'quantity' => $calculation['quantity'],
            'unit_price' => $calculation['unit_price'],
            'product_price' => $calculation['product_price'],
            'down_payment' => $calculation['down_payment'],
            'tenor_months' => $calculation['tenor_months'],
            'interest_rate' => $calculation['interest_rate'],
            'admin_fee' => $calculation['admin_fee'],
            'total_interest' => $calculation['total_interest'],
            'total_amount' => $calculation['total_amount'],
            'monthly_installment' => $calculation['monthly_installment'],
        ]);

        $installment->update($this->filterInstallmentPayload($payload));

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

        if (! $installment) {
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

    private function calculateInstallment(
        float $unitPrice,
        int $quantity,
        float $downPayment,
        int $tenorMonths,
        float $interestRate,
        ?GadgetTenor $tenor = null,
        bool $multiplyInterestByTenor = false
    ): array {
        $quantity = max($quantity, 1);
        $tenorMonths = max($tenorMonths, 1);
        $productPrice = $unitPrice * $quantity;
        $remainingAmount = $productPrice - $downPayment;
        $baseAmount = max($remainingAmount, 0);
        $totalInterest = $multiplyInterestByTenor
            ? $baseAmount * ($interestRate / 100) * $tenorMonths
            : $baseAmount * ($interestRate / 100);
        $fees = [
            'admin_fee' => (float) ($tenor?->admin_fee ?? 0),
            'insurance_fee' => (float) ($tenor?->insurance_fee ?? 0),
            'provision_fee' => (float) ($tenor?->provision_fee ?? 0),
            'service_fee' => (float) ($tenor?->service_fee ?? 0),
            'tomi_fee' => (float) ($tenor?->tomi_fee ?? 0),
        ];
        $totalFees = $fees['admin_fee'] + $fees['insurance_fee'] + $fees['provision_fee'] + $fees['service_fee'];
        $totalAmount = $baseAmount + $totalInterest + $totalFees;
        $monthlyInstallment = $multiplyInterestByTenor
            ? ceil(($totalAmount / $tenorMonths) / 1000) * 1000
            : $totalAmount / $tenorMonths;

        return [
            'quantity' => $quantity,
            'unit_price' => round($unitPrice, 2),
            'product_price' => round($productPrice, 2),
            'down_payment' => round($downPayment, 2),
            'remaining_amount' => round($remainingAmount, 2),
            'tenor_months' => $tenorMonths,
            'interest_rate' => round($interestRate, 2),
            'total_interest' => round($totalInterest, 2),
            'admin_fee' => round($fees['admin_fee'], 2),
            'insurance_fee' => round($fees['insurance_fee'], 2),
            'provision_fee' => round($fees['provision_fee'], 2),
            'service_fee' => round($fees['service_fee'], 2),
            'tomi_fee' => round($fees['tomi_fee'], 2),
            'total_fees' => round($totalFees, 2),
            'total_amount' => round($totalAmount, 2),
            'monthly_installment' => round($monthlyInstallment, 2),
        ];
    }

    private function formatCalculation(array $calculation): array
    {
        return [
            ...$calculation,
            'installment_month' => $calculation['tenor_months'],
            'price' => $calculation['product_price'],
            'interest_amount' => $calculation['total_interest'],
            'formatted' => [
                'unit_price' => 'Rp  '.number_format($calculation['unit_price']),
                'price' => 'Rp  '.number_format($calculation['product_price']),
                'down_payment' => 'Rp  '.number_format($calculation['down_payment']),
                'remaining_amount' => 'Rp  '.number_format($calculation['remaining_amount']),
                'interest_rate' => $calculation['interest_rate'].'%',
                'interest_amount' => 'Rp  '.number_format($calculation['total_interest']),
                'total_fees' => 'Rp  '.number_format($calculation['total_fees']),
                'total_amount' => 'Rp  '.number_format($calculation['total_amount']),
                'monthly_installment' => 'Rp  '.number_format($calculation['monthly_installment']),
            ],
        ];
    }

    private function generateInvoiceNumber(): string
    {
        do {
            $invoiceNumber = 'GI-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (GadgetInstallment::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    private function normalizeGetTenorData(Request $request): void
    {
        if (! $request->filled('variant_id') && $request->filled('product_variant_id')) {
            $request->merge(['variant_id' => $request->product_variant_id]);
        }

        if (! $request->filled('nominal') && $request->filled('amount')) {
            $request->merge(['nominal' => $request->amount]);
        }

        if (! $request->filled('nominal') && $request->filled('harga')) {
            $request->merge(['nominal' => $request->harga]);
        }
    }

    private function normalizeSimulationData(Request $request): void
    {
        if (! $request->filled('variant_id') && $request->filled('product_variant_id')) {
            $request->merge(['variant_id' => $request->product_variant_id]);
        }

        if (! $request->filled('installment_month') && $request->filled('tenor_months')) {
            $request->merge(['installment_month' => $request->tenor_months]);
        }

        if (! $request->filled('id_tenor_pinjaman') && $request->filled('tenor_pinjaman_id')) {
            $request->merge(['id_tenor_pinjaman' => $request->tenor_pinjaman_id]);
        }

        if (! $request->filled('gadget_tenor_id') && $request->filled('tenor_gadget_id')) {
            $request->merge(['gadget_tenor_id' => $request->tenor_gadget_id]);
        }
    }

    private function resolveNominalFromRequest(Request $request): float
    {
        if ($request->filled('nominal')) {
            return (float) $request->nominal;
        }

        if ($request->filled('variant_id')) {
            return (float) ProductVariant::where('id', $request->variant_id)->value('price');
        }

        return 0;
    }

    private function findCompanyByNip(string $nip, array $extraRelations = []): ?ListPerusahaan
    {
        $parts = explode('-', $nip);

        if (count($parts) < 2) {
            return null;
        }

        $relations = array_merge([
            'karyawanSingle' => function ($query) use ($parts) {
                $query->where('nip', $parts[1]);
            },
        ], $extraRelations);

        return ListPerusahaan::where('unique_code', $parts[0])
            ->with($relations)
            ->first();
    }

    private function validateUserKycAndPrivy(?User $user)
    {
        if (! $user) {
            return response()->json([
                'status' => 400,
                'message' => 'User tidak ditemukan',
            ], 400);
        }

        if (auth('api')->user()->verified_privy === 'n') {
            if (isset($user->nik, $user->nama_lengkap, $user->dob, $user->kyc->selfie, $user->kyc->ktp)) {
                if ($user->register_token_privy === null) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Tampilkan consent privy untuk register',
                    ], 400);
                }

                if ($user->resend_privy == 1) {
                    $reason = json_decode($user->reason_privy);

                    return response()->json([
                        'status' => 400,
                        'reason' => $reason?->reason,
                        'message' => ($reason?->reason ? $reason->reason.', ' : '').'Harap perbarui kembali data anda',
                    ], 400);
                }

                if ($user->resend_privy == 0 && $user->reason_privy !== null) {
                    $reason = json_decode($user->reason_privy);

                    return response()->json([
                        'status' => 400,
                        'reason' => $reason?->reason,
                        'message' => ($reason?->reason ? $reason->reason.', ' : '').'Harap hubungi customer service untuk merubah data anda',
                    ], 400);
                }

                return response()->json([
                    'status' => 400,
                    'message' => 'Harap menunggu, data anda sedang proses verifikasi',
                ], 400);
            }

            if (! isset($user->nik) || ! isset($user->nama_lengkap) || ! isset($user->dob)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Harap lengkapi data anda telebih dahulu',
                ], 400);
            }
        }

        if ($user->kyc === null) {
            return response()->json([
                'status' => 400,
                'message' => 'Maaf, proses tidak dapat dilanjutkan. Kami memerlukan pembaruan foto selfie dan KTP Anda.',
            ], 400);
        }

        if ($user->register_token_privy !== null && $user->id_privy === null && function_exists('checkStatus')) {
            $checkStatusPrivy = checkStatus();
            if (isset($checkStatusPrivy['data']['privy_id'])) {
                $user->id_privy = $checkStatusPrivy['data']['privy_id'];
                $user->save();
            }
        }

        return null;
    }

    private function activeInstallmentQuery(int $employeeId)
    {
        $query = GadgetInstallment::query();

        if ($this->hasInstallmentColumn('id_karyawan')) {
            $query->where('id_karyawan', $employeeId);
        } else {
            $query->where('user_id', auth('api')->user()->id);
        }

        return $query;
    }

    private function formatGadgetTenor(GadgetTenor $tenor): array
    {
        return [
            'id_gadget_tenor' => $tenor->id,
            'gadget_tenor_id' => $tenor->id,
            'company_id' => $tenor->company_id,
            'tenor_months' => $tenor->tenor_months,
            'tenor' => $tenor->tenor_months.' bulan',
            'interest_rate' => round((float) $tenor->interest_rate, 2),
            'rate' => round((float) $tenor->interest_rate, 2).' %',
            'insurance_fee' => round((float) $tenor->insurance_fee, 2),
            'provision_fee' => round((float) $tenor->provision_fee, 2),
            'admin_fee' => round((float) $tenor->admin_fee, 2),
            'tomi_fee' => round((float) $tenor->tomi_fee, 2),
            'service_fee' => round((float) ($tenor->service_fee ?? 0), 2),
            'minimum_amount' => $tenor->minimum_amount !== null ? round((float) $tenor->minimum_amount, 2) : null,
            'maximum_amount' => $tenor->maximum_amount !== null ? round((float) $tenor->maximum_amount, 2) : null,
            'formatted' => [
                'insurance_fee' => 'Rp  '.number_format((float) $tenor->insurance_fee),
                'provision_fee' => 'Rp  '.number_format((float) $tenor->provision_fee),
                'admin_fee' => 'Rp  '.number_format((float) $tenor->admin_fee),
                'tomi_fee' => 'Rp  '.number_format((float) $tenor->tomi_fee),
                'service_fee' => 'Rp  '.number_format((float) ($tenor->service_fee ?? 0)),
                'minimum_amount' => $tenor->minimum_amount !== null ? 'Rp  '.number_format((float) $tenor->minimum_amount) : null,
                'maximum_amount' => $tenor->maximum_amount !== null ? 'Rp  '.number_format((float) $tenor->maximum_amount) : null,
            ],
        ];
    }

    private function installmentRelationsForDisplay(): array
    {
        return [
            'user:id,name,email,nama_lengkap,nohp',
            'seller:id,store_name,owner_name,email,phone,logo_url,address,city,province,is_active,is_verified',
            'product' => function ($query) {
                $query->select('id', 'seller_id', 'name', 'slug', 'brand', 'model', 'description', 'is_active');
            },
            'product.images' => function ($query) {
                $query->select('id', 'product_id', 'image_url')
                    ->orderBy('id', 'asc');
            },
            'productVariant:id,product_id,sku,color,ram_gb,storage_gb,price,stock,image_url,is_active',
            'gadgetTenor',
        ];
    }

    private function formatInstallmentForDisplay(GadgetInstallment $installment): GadgetInstallment
    {
        $installment->setAttribute('product_invoice', [
            'invoice_number' => $installment->invoice_number,
            'unit_price' => $installment->unit_price,
            'product_price' => $installment->product_price,
            'down_payment' => $installment->down_payment,
            'tenor_months' => $installment->tenor_months,
            'interest_rate' => $installment->interest_rate,
            'total_interest' => $installment->total_interest,
            'total_amount' => $installment->total_amount,
            'monthly_installment' => $installment->monthly_installment,
            'start_date' => $installment->start_date,
            'status' => $installment->status,
            'notes' => $installment->notes,
        ]);

        return $installment;
    }

    private function filterInstallmentPayload(array $payload): array
    {
        return collect($payload)
            ->filter(fn ($value, $key) => $value !== null && $this->hasInstallmentColumn((string) $key))
            ->all();
    }

    private function fillIfColumn(GadgetInstallment $installment, string $column, mixed $value): void
    {
        if ($this->hasInstallmentColumn($column)) {
            $installment->{$column} = $value;
        }
    }

    private function hasInstallmentColumn(string $column): bool
    {
        if (! isset($this->columnCache['gadget_installments'])) {
            $this->columnCache['gadget_installments'] = Schema::hasTable('gadget_installments')
                ? array_flip(Schema::getColumnListing('gadget_installments'))
                : [];
        }

        return isset($this->columnCache['gadget_installments'][$column]);
    }
}
