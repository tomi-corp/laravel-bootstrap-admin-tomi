<?php

namespace App\Http\Controllers\Admin\TomiDana;

use App\Http\Controllers\Controller;
use App\Models\history_invoicing_lender;
use App\Models\lender;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApplicationFeeController extends Controller
{
    public function adminInvoiceDashboard(Request $request)
    {
        return $this->lenderInvoiceDashboard($request, 'admin.dashboard.index');
    }

    public function lenderInvoiceDashboard(Request $request, string $view = 'admin.tomi-dana.aplication-fee.index')
    {
        $month = $request->filled('month') ? $request->integer('month') : null;
        $year = $request->filled('year') ? $request->integer('year') : null;

        abort_if($month !== null && ($month < 1 || $month > 12), 404);

        $historyInvoices = history_invoicing_lender::query()
            ->with(['lender.pinjaman.disbursement'])
            ->when($year !== null, fn ($query) => $query->whereYear('period', $year))
            ->when($month !== null, fn ($query) => $query->whereMonth('period', $month))
            ->when($request->filled('lender_id'), fn ($query) => $query->where('id_lender', $request->integer('lender_id')))
            ->orderByDesc('period')
            ->orderByDesc('id')
            ->get();

        $statusStyles = [
            'draft' => ['label' => 'Draf'],
            'menunggu' => ['label' => 'Menunggu Penagihan'],
            'mulai_jatuh_tempo' => ['label' => 'Mulai Jatuh Tempo'],
            'jatuh_tempo' => ['label' => 'Jatuh Tempo'],
            'lunas' => ['label' => 'Sudah Terbayar'],
        ];

        $invoiceRows = $historyInvoices->map(function ($invoice) use ($statusStyles) {
            $period = Carbon::parse($invoice->period)->startOfMonth();
            $dueDate = $period->copy()->endOfMonth();
            $loans = collect($invoice->lender?->pinjaman ?? [])
                ->filter(fn ($loan) => $loan->disbursement
                    && in_array(strtolower((string) $loan->status), ['approve', 'done'], true)
                    && Carbon::parse($loan->disbursement->created_at)->format('Y-m') === $period->format('Y-m'))
                ->values();

            $serviceFee = 0;
            $adminFee = 0;
            $totalBilling = 0;

            foreach ($loans as $loan) {
                $nominal = (float) ($loan->nominal ?? 0);
                $cutoff = Carbon::create($period->year, 4, 17)->startOfDay();

                if (Carbon::parse($loan->created_at)->startOfDay()->lt($cutoff)) {
                    $serviceFee += 20000;
                    $totalBilling += ($nominal * 0.03) + 20000;
                } else {
                    $adminFee += $nominal * 0.01;
                    $serviceFee += 10000;
                    $totalBilling += ($nominal * 0.01) + 10000;
                }
            }

            $rawStatus = strtolower((string) $invoice->status);
            $status = $rawStatus === 'lunas'
                ? 'lunas'
                : ($dueDate->isPast()
                    ? 'jatuh_tempo'
                    : ($dueDate->diffInDays(today()) <= 3 ? 'mulai_jatuh_tempo' : 'menunggu'));

            return [
                'id' => $invoice->id,
                'invoice_number' => $invoice->no_invoice ?? '-',
                'lender_name' => $invoice->lender?->nama ?? '-',
                'period_label' => $period->format('M Y'),
                'transaction_count' => $loans->count(),
                'transaction_value' => $loans->sum(fn ($loan) => (float) ($loan->nominal ?? 0)),
                'service_fee' => $serviceFee,
                'admin_fee' => $adminFee,
                'amount' => $totalBilling,
                'due_date' => $dueDate->format('d M Y'),
                'status' => $status,
                'status_label' => $statusStyles[$status]['label'],
                'payment_date' => $invoice->tanggal_pembayaran
                    ? Carbon::parse($invoice->tanggal_pembayaran)->format('Y-m-d\\TH:i')
                    : null,
                'download_url' => route('admin.tomi-dana.lender.pinjaman.invoice-fee', [
                    'month' => $period->format('Y-m'),
                    'lender_id' => $invoice->id_lender,
                ]),
            ];
        })->filter(function ($invoice) use ($request) {
            return ! $request->filled('status') || $invoice['status'] === $request->input('status');
        })->values();

        $statusSummary = collect($statusStyles)->mapWithKeys(function ($style, $status) use ($invoiceRows) {
            $rows = $invoiceRows->where('status', $status);

            return [$status => [
                'count' => $rows->count(),
                'amount' => $rows->sum('amount'),
            ]];
        })->all();

        $paidRows = $invoiceRows->where('status', 'lunas');
        $dueRows = $invoiceRows->whereIn('status', ['mulai_jatuh_tempo', 'jatuh_tempo']);
        $summary = [
            'total_lenders' => $invoiceRows->pluck('lender_name')->filter()->unique()->count(),
            'total_invoices' => $invoiceRows->count(),
            'service_fee' => $invoiceRows->sum('service_fee'),
            'admin_fee' => $invoiceRows->sum('admin_fee'),
            'total_billing' => $invoiceRows->sum('amount'),
            'paid_count' => $paidRows->count(),
            'paid_amount' => $paidRows->sum('amount'),
            'due_count' => $dueRows->count(),
            'due_amount' => $dueRows->sum('amount'),
        ];

        $selectedInvoice = $request->filled('invoice_id')
            ? $invoiceRows->firstWhere('id', $request->integer('invoice_id'))
            : $invoiceRows->first();

        return view($view, [
            'summary' => $summary,
            'statusSummary' => $statusSummary,
            'actionInvoices' => $invoiceRows->whereNotIn('status', ['lunas'])->take(5)->values(),
            'invoices' => $invoiceRows,
            'selectedInvoice' => $selectedInvoice,
            'lenders' => lender::query()->orderBy('nama')->get(['id', 'nama']),
        ]);
    }



    public function updateLenderInvoicePayment(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:history_invoicing_lenders,id',
            'tanggal' => 'required|date|date_format:Y-m-d\\TH:i',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'pernyataan' => 'accepted',
        ]);

        $invoice = history_invoicing_lender::findOrFail($data['id']);
        $invoice->status = 'lunas';
        $invoice->tanggal_pembayaran = $data['tanggal'];
        $invoice->bukti_transfer = $request->file('bukti_transfer')->store('assets/invoice-lender');
        $invoice->update_by = auth('admin')->id();
        $invoice->saveQuietly();

        return response()->json([
            'status' => 200,
            'message' => 'Pembayaran invoice berhasil disimpan.',
        ]);
    }
}

