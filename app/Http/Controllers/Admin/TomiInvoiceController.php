<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TomiInvoiceController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        $query = $this->clientQuery();

        if ($admin->role === 'pic' || $admin->type === 'pic') {
            $query->where('id', $admin->company_id);
        }

        $perusahaans = $query
            ->orderByDesc('id')
            ->get()
            ->map(fn($client) => $this->mapClient($client));

        $invoiceSummary = $this->getInvoiceSummary();

        $perusahaans = $perusahaans->map(function (array $perusahaan) use ($invoiceSummary) {
            $summary = $invoiceSummary->get((int) $perusahaan['id'], [
                'total_invoice' => 0,
                'total_pinjaman' => 0,
                'total_dibayarkan' => 0,
                'total_fee' => 0,
            ]);

            return array_merge($perusahaan, $summary);
        });

        return view('admin.tomi-invoice.index', [
            'perusahaans' => $perusahaans,
            'totalPerusahaan' => $perusahaans->count(),
            'totalInvoice' => $perusahaans->sum('total_invoice'),
            'totalPinjaman' => $perusahaans->sum('total_pinjaman'),
            'totalDibayarkan' => $perusahaans->sum('total_dibayarkan'),
            'totalFee' => $perusahaans->sum('total_fee'),
        ]);
    }

    public function show($id)
    {
        $company = $this->clientQuery()
            ->where('id', $id)
            ->first();

        if (!$company) {
            abort(404, 'Data tidak ditemukan');
        }

        $perusahaan = $this->mapClient($company);
        $details = $this->getInvoiceDetails($perusahaan);

        $perusahaan['details'] = $details;
        $perusahaan['total_invoice'] = $details->count();
        $perusahaan['total_pinjaman'] = $details->sum('total_pinjaman');
        $perusahaan['total_dibayarkan'] = $details->sum('total_dibayarkan');
        $perusahaan['total_fee'] = $details->sum('total_fee');

        return view('admin.tomi-invoice.show', compact('perusahaan'));
    }

    public function downloadTermInvoice($id, $term)
    {
        $term = (int) $term;

        if (!in_array($term, [1, 2, 3], true)) {
            abort(404, 'Termin tidak valid.');
        }

        if (
            !Schema::hasTable('financing_request_invoices')
            || !Schema::hasTable('clients')
            || !Schema::hasTable('vendors')
        ) {
            abort(404, 'Data invoice tidak tersedia.');
        }

        $invoice = $this->financingInvoiceBaseQuery()
            ->where('financing_request_invoices.id', $id)
            ->first();

        if (!$invoice) {
            abort(404, 'Data invoice tidak ditemukan.');
        }

        $client = [
            'id' => $invoice->client_id,
            'nama' => $invoice->client_name ?? '-',
            'rate' => (float) ($invoice->client_rate ?? 0),
            'admin_fee' => (float) ($invoice->client_admin_fee ?? 0),
        ];

        $termInvoice = $this->mapInvoiceTerms($invoice, $client)
            ->firstWhere('term', $term);

        if (!$termInvoice) {
            abort(404, 'Data termin tidak ditemukan.');
        }

        if (!$this->canDownloadTerm($invoice, $term)) {
            abort(403, 'Termin sebelumnya belum selesai diproses.');
        }

        $pdf = Pdf::loadView('admin.tomi-invoice.term-invoice-pdf', [
            'invoice' => $termInvoice,
            'rawInvoice' => $invoice,
        ])->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $pdf->stream($termInvoice['nomor_invoice'] . '.pdf');
    }

    private function value(object $row, array $keys, mixed $default = null): mixed
    {
        foreach ($keys as $key) {
            $value = data_get($row, $key);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    }

    private function clientQuery()
    {
        if (Schema::hasTable('clients')) {
            $query = DB::table('clients');

            if (Schema::hasColumn('clients', 'deleted_at')) {
                $query->whereNull('deleted_at');
            }

            return $query;
        }

        return DB::table('list_perusahaans');
    }

    private function mapClient(object $client): array
    {
        $rawStatus = $this->value($client, ['status'], 'active');
        $status = is_numeric($rawStatus)
            ? ((int) $rawStatus === 1 ? 'active' : 'nonactive')
            : strtolower((string) $rawStatus);
        $status = in_array($status, ['inactive', 'non_active', 'nonactive', '0'], true) ? 'nonactive' : 'active';

        return [
            'id' => $this->value($client, ['id']),
            'nama' => $this->value($client, ['name', 'nama', 'nama_perusahaan', 'company_name'], '-'),
            'status' => $status,
            'max_plafon' => (float) $this->value($client, ['max_plafon', 'maximum_plafon', 'plafon_max', 'max_limit'], 0),
            'logo' => $this->value($client, ['logo']),
            'person_in_charge' => $this->value($client, ['person_in_charge', 'pic_perusahaan', 'pic'], '-'),
            'pic_position' => $this->value($client, ['pic_position'], '-'),
            'email' => $this->value($client, ['email', 'email_perusahaan'], '-'),
            'phone' => $this->value($client, ['phone', 'telp', 'telepon'], '-'),
            'account_name' => $this->value($client, ['account_name'], '-'),
            'account_number' => $this->value($client, ['account_number'], '-'),
            'address' => $this->value($client, ['address', 'alamat_lengkap', 'alamat'], '-'),
            'rate' => (float) $this->value($client, ['rate'], 0),
            'admin_fee' => (float) $this->value($client, ['admin_fee'], 0),
        ];
    }

    private function getInvoiceSummary()
    {
        if (
            !Schema::hasTable('financing_request_invoices')
            || !Schema::hasTable('clients')
            || !Schema::hasTable('vendors')
        ) {
            return collect();
        }

        return $this->financingInvoiceBaseQuery()
            ->select(
                'financing_request_invoices.client_id',
                DB::raw('COUNT(*) as total_invoice'),
                DB::raw('COALESCE(SUM(COALESCE(financing_request_invoices.approved_financing_amount, financing_request_invoices.purchase_order_amount, 0)), 0) as total_pinjaman'),
                DB::raw('COALESCE(SUM(
                    COALESCE(financing_request_invoices.finance_invoice_term_amount_1, COALESCE(financing_request_invoices.approved_financing_amount, financing_request_invoices.purchase_order_amount, 0) * COALESCE(vendor_payment_terms.term_1_percentage, 100) / 100)
                    + COALESCE(financing_request_invoices.finance_invoice_term_amount_2, COALESCE(financing_request_invoices.approved_financing_amount, financing_request_invoices.purchase_order_amount, 0) * COALESCE(vendor_payment_terms.term_2_percentage, 0) / 100)
                    + COALESCE(financing_request_invoices.finance_invoice_term_amount_3, COALESCE(financing_request_invoices.approved_financing_amount, financing_request_invoices.purchase_order_amount, 0) * COALESCE(vendor_payment_terms.term_3_percentage, 0) / 100)
                ), 0) as total_dibayarkan'),
                DB::raw('COALESCE(SUM(
                    (
                        COALESCE(financing_request_invoices.finance_invoice_term_amount_1, COALESCE(financing_request_invoices.approved_financing_amount, financing_request_invoices.purchase_order_amount, 0) * COALESCE(vendor_payment_terms.term_1_percentage, 100) / 100)
                        + COALESCE(financing_request_invoices.finance_invoice_term_amount_2, COALESCE(financing_request_invoices.approved_financing_amount, financing_request_invoices.purchase_order_amount, 0) * COALESCE(vendor_payment_terms.term_2_percentage, 0) / 100)
                        + COALESCE(financing_request_invoices.finance_invoice_term_amount_3, COALESCE(financing_request_invoices.approved_financing_amount, financing_request_invoices.purchase_order_amount, 0) * COALESCE(vendor_payment_terms.term_3_percentage, 0) / 100)
                    ) * COALESCE(clients.rate, 0) / 100
                    + COALESCE(clients.admin_fee, 0)
                ), 0) as total_fee')
            )
            ->groupBy('financing_request_invoices.client_id')
            ->get()
            ->mapWithKeys(fn($item) => [
                (int) $item->client_id => [
                    'total_invoice' => (int) $item->total_invoice,
                    'total_pinjaman' => (float) $item->total_pinjaman,
                    'total_dibayarkan' => (float) $item->total_dibayarkan,
                    'total_fee' => (float) $item->total_fee,
                ],
            ]);
    }

    private function getInvoiceDetails(array $client)
    {
        if (
            !Schema::hasTable('financing_request_invoices')
            || !Schema::hasTable('clients')
            || !Schema::hasTable('vendors')
        ) {
            return collect();
        }

        return $this->financingInvoiceBaseQuery()
            ->where('financing_request_invoices.client_id', $client['id'])
            ->orderByDesc('financing_request_invoices.created_at')
            ->orderByDesc('financing_request_invoices.id')
            ->get()
            ->map(fn($invoice) => $this->mapInvoiceDetail($invoice, $client));
    }

    private function financingInvoiceBaseQuery()
    {
        return DB::table('financing_request_invoices')
            ->leftJoin('clients', 'clients.id', '=', 'financing_request_invoices.client_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'financing_request_invoices.vendor_id')
            ->leftJoin('vendor_payment_terms', function ($join) {
                $join->on('vendor_payment_terms.id', '=', 'financing_request_invoices.vendor_payment_term_id')
                    ->orOn(function ($query) {
                        $query->on('vendor_payment_terms.vendor_id', '=', 'financing_request_invoices.vendor_id')
                            ->whereNull('financing_request_invoices.vendor_payment_term_id');
                    });
            })
            ->whereNull('financing_request_invoices.deleted_at')
            ->select([
                'financing_request_invoices.*',
                'clients.name as client_name',
                'clients.rate as client_rate',
                'clients.admin_fee as client_admin_fee',
                'vendors.name as vendor_name',
                'vendor_payment_terms.term_1_percentage',
                'vendor_payment_terms.term_2_percentage',
                'vendor_payment_terms.term_3_percentage',
            ]);
    }

    private function mapInvoiceTerms(object $invoice, array $client)
    {
        $principal = (float) ($invoice->approved_financing_amount ?? $invoice->purchase_order_amount ?? 0);
        $clientRate = (float) ($invoice->client_rate ?? $client['rate'] ?? 0);
        $adminFee = (float) ($invoice->client_admin_fee ?? $client['admin_fee'] ?? 0);
        $percentages = [
            1 => (float) ($invoice->term_1_percentage ?? 100),
            2 => (float) ($invoice->term_2_percentage ?? 0),
            3 => (float) ($invoice->term_3_percentage ?? 0),
        ];

        $activeTermCount = collect($percentages)->filter(fn($percentage) => $percentage > 0)->count() ?: 1;

        return collect([1, 2, 3])
            ->map(function (int $term) use ($invoice, $client, $principal, $clientRate, $adminFee, $percentages, $activeTermCount) {
                $percentage = $percentages[$term] ?? 0;
                $amountColumn = "finance_invoice_term_amount_{$term}";
                $dateColumn = "finance_invoice_term_date_{$term}";
                $fileColumn = "finance_invoice_term_file_{$term}";

                $termAmount = $invoice->{$amountColumn} !== null
                    ? (float) $invoice->{$amountColumn}
                    : $principal * ($percentage / 100);

                if ($termAmount <= 0 && $percentage <= 0) {
                    return null;
                }

                $termAdminFee = $adminFee > 0 ? $adminFee / $activeTermCount : 0;
                $termFee = ($termAmount * ($clientRate / 100)) + $termAdminFee;

                return [
                    'id' => $invoice->id . '-' . $term,
                    'invoice_id' => $invoice->id,
                    'nama_perusahaan' => $invoice->client_name ?? $client['nama'],
                    'vendor_name' => $invoice->vendor_name ?? '-',
                    'nomor_invoice' => 'INV-TOMI-' . str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT) . '-T' . $term,
                    'purchase_order_number' => $invoice->purchase_order_number ?? '-',
                    'purchase_order_amount' => (float) ($invoice->purchase_order_amount ?? 0),
                    'approved_financing_amount' => $principal,
                    'term' => $term,
                    'term_percentage' => $percentage,
                    'term_amount' => $termAmount,
                    'total_pinjaman' => $principal,
                    'total_dibayarkan' => $termAmount,
                    'total_fee' => $termFee,
                    'rate' => $clientRate,
                    'admin_fee' => $termAdminFee,
                    'finance_invoice_term_date' => $invoice->{$dateColumn} ?? null,
                    'finance_invoice_term_file' => $invoice->{$fileColumn} ?? null,
                    'is_process_complete' => $this->isTermProcessComplete($invoice, $term),
                    'can_download' => $this->canDownloadTerm($invoice, $term),
                    'status' => $invoice->status ?? '-',
                    'created_at' => $invoice->created_at ?? null,
                ];
            })
            ->filter()
            ->values();
    }

    private function mapInvoiceDetail(object $invoice, array $client): array
    {
        $terms = $this->mapInvoiceTerms($invoice, $client)
            ->keyBy('term');

        $principal = (float) ($invoice->approved_financing_amount ?? $invoice->purchase_order_amount ?? 0);

        return [
            'id' => $invoice->id,
            'invoice_id' => $invoice->id,
            'nama_perusahaan' => $invoice->client_name ?? $client['nama'],
            'vendor_name' => $invoice->vendor_name ?? '-',
            'nomor_invoice' => 'INV-TOMI-' . str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT),
            'purchase_order_number' => $invoice->purchase_order_number ?? '-',
            'purchase_order_amount' => (float) ($invoice->purchase_order_amount ?? 0),
            'approved_financing_amount' => $principal,
            'total_pinjaman' => $principal,
            'total_dibayarkan' => $terms->sum('total_dibayarkan'),
            'total_fee' => $terms->sum('total_fee'),
            'status' => $invoice->status ?? '-',
            'created_at' => $invoice->created_at ?? null,
            'terms' => $terms,
        ];
    }

    private function isTermProcessComplete(object $invoice, int $term): bool
    {
        $dateColumn = "finance_invoice_term_date_{$term}";
        $fileColumn = "finance_invoice_term_file_{$term}";

        return !empty($invoice->{$dateColumn}) || !empty($invoice->{$fileColumn});
    }

    private function canDownloadTerm(object $invoice, int $term): bool
    {
        if ($term <= 1) {
            return true;
        }

        return $this->isTermProcessComplete($invoice, $term - 1);
    }

    private function normalizeCompanyName(?string $name): string
    {
        return mb_strtolower(trim((string) $name));
    }
}
