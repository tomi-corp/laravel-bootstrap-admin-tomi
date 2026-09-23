<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Models\history_invoicing_lender;
use App\Models\invoicing_pinjaman;
use App\Models\rekening_pandu;
use App\Modules\Lender\Models\Repayment;
use App\Modules\Lender\Requests\RepaymentRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RepaymentController extends Controller
{
    public function index(): View
    {
        $lenderId = auth('lender')->user()?->id_lender;
        $invoices = invoicing_pinjaman::query()
            ->with([
                'pinjaman' => function ($query) use ($lenderId) {
                    $query->where('id_lender', $lenderId)
                        ->with(['data_karyawan', 'perusahaan', 'disbursement', 'jatuh_tempoSesuaiInvoice', 'user']);
                },
                'repayment.admin',
            ])
            ->whereHas('pinjaman', fn ($query) => $query->where('id_lender', $lenderId))
            ->latest()
            ->get();

        $invoiceDownloadIds = history_invoicing_lender::query()
            ->where('id_lender', $lenderId)
            ->whereIn('period', $invoices->pluck('period')->filter()->map(
                fn ($period) => Carbon::parse($period)->firstOfMonth()->toDateString()
            )->unique())
            ->get()
            ->keyBy(fn ($invoice) => Carbon::parse($invoice->period)->format('Y-m'));

        return view('lender.repayments.index', [
            'invoices' => $invoices,
            'invoiceDownloadIds' => $invoiceDownloadIds,
        ]);
    }

    public function downloadInvoice(Request $request, int $invoice): mixed
    {
        $historyInvoicingLenderPending = history_invoicing_lender::query()
            ->whereKey($invoice)
            ->where('id_lender', auth('lender')->user()?->id_lender)
            ->with([
                'lender.pinjaman.disbursement',
                'lender.pinjaman.data_karyawan',
                'lender.pinjaman.perusahaan',
                'pendingApproval',
            ])
            ->firstOrFail();

        if ($historyInvoicingLenderPending->lender) {
            $filteredPinjaman = $historyInvoicingLenderPending->lender->pinjaman
                ->filter(function ($pinjaman) use ($historyInvoicingLenderPending) {
                    return $pinjaman->disbursement &&
                        Carbon::parse($pinjaman->disbursement->created_at)->format('Y-m') ===
                        Carbon::parse($historyInvoicingLenderPending->period)->format('Y-m');
                })
                ->values();

            $historyInvoicingLenderPending->lender->setRelation('pinjaman', $filteredPinjaman);
        }

        $selectedColumns = $request->get('columns', []);
        $allowedColumns = [
            'tanggal_pinjaman',
            'nip',
            'nominal_pinjaman',
            'rate',
            'fee_tomi',
            'biaya_layanan',
            'biaya_admin',
            'total',
        ];

        if (! is_array($selectedColumns)) {
            $selectedColumns = [];
        }

        $selectedColumns = array_values(array_intersect($selectedColumns, $allowedColumns));

        if (empty($selectedColumns)) {
            $selectedColumns = $allowedColumns;
        }

        $rekening = rekening_pandu::with('bank')->first();

        return Pdf::loadView(
            'admin.ksp.lender.invoicing.invoicing',
            compact('historyInvoicingLenderPending', 'rekening', 'selectedColumns')
        )
            ->setPaper('legal', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
            ])
            ->stream('invoice-lender-'.$historyInvoicingLenderPending->no_invoice.'.pdf');
    }

    public function create(): View
    {
        return view('lender.repayments.create', $this->formData(new Repayment));
    }

    public function store(RepaymentRequest $request): RedirectResponse
    {
        Repayment::query()->create($this->withAdmin($request->validated()));

        return redirect()->route('lender.repayments.index')->with('success', 'Repayment berhasil ditambahkan.');
    }

    public function show(Repayment $repayment): View
    {
        return view('lender.repayments.show', [
            'repayment' => $repayment->load(['invoice', 'admin']),
        ]);
    }

    public function edit(Repayment $repayment): View
    {
        return view('lender.repayments.edit', $this->formData($repayment));
    }

    public function update(RepaymentRequest $request, Repayment $repayment): RedirectResponse
    {
        $repayment->update($this->withAdmin($request->validated()));

        return redirect()->route('lender.repayments.index')->with('success', 'Repayment berhasil diperbarui.');
    }

    public function destroy(Repayment $repayment): RedirectResponse
    {
        $repayment->delete();

        return redirect()->route('lender.repayments.index')->with('success', 'Repayment berhasil dihapus.');
    }

    private function formData(Repayment $repayment): array
    {
        return [
            'repayment' => $repayment,
            'invoices' => invoicing_pinjaman::query()->latest()->limit(500)->get(['id', 'no_invoice']),
        ];
    }

    private function withAdmin(array $data): array
    {
        $data['ksp_admin_id'] = auth('lender')->id();

        return $data;
    }
}
