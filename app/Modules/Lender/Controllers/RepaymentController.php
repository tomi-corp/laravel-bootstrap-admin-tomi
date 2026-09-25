<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Models\history_invoicing_lender;
use App\Models\invoicing_pinjaman;
use App\Models\jatuh_tempo;
use App\Models\notification;
use App\Models\pinjaman;
use App\Models\rekening_pandu;
use App\Modules\Lender\Models\Repayment;
use App\Modules\Lender\Requests\RepaymentRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
                        ->where('status', '!=', 'done')
                        ->with(['data_karyawan', 'perusahaan', 'disbursement', 'jatuh_tempoSesuaiInvoice', 'angsuran', 'user']);
                },
                'perusahaan',
                'repayment.admin',
            ])
            ->whereHas('perusahaan', fn ($query) => $query->where('id_lender', $lenderId))
            ->latest()
            ->get()
            ->sort(function ($first, $second) {
                $firstStatusOrder = $first->status === 'lunas' ? 1 : 0;
                $secondStatusOrder = $second->status === 'lunas' ? 1 : 0;

                if ($firstStatusOrder !== $secondStatusOrder) {
                    return $firstStatusOrder <=> $secondStatusOrder;
                }

                $firstPeriod = Carbon::parse($first->period ?? $first->created_at)->timestamp;
                $secondPeriod = Carbon::parse($second->period ?? $second->created_at)->timestamp;

                if ($firstPeriod !== $secondPeriod) {
                    return $secondPeriod <=> $firstPeriod;
                }

                return $second->id <=> $first->id;
            })
            ->values();

        $invoiceDownloadIds = history_invoicing_lender::query()
            ->where('id_lender', $lenderId)
            ->whereIn('period', $invoices->pluck('period')->filter()->map(
                fn ($period) => Carbon::parse($period)->firstOfMonth()->toDateString()
            )->unique())
            ->get()
            ->keyBy(fn ($invoice) => Carbon::parse($invoice->period)->format('Y-m'));

        $invoiceDownloadAvailability = $invoices->mapWithKeys(fn ($invoice) => [
            $invoice->id => $this->isInCompanyDueWindow($invoice),
        ]);

        return view('lender.repayments.index', [
            'invoices' => $invoices,
            'invoiceDownloadIds' => $invoiceDownloadIds,
            'invoiceDownloadAvailability' => $invoiceDownloadAvailability,
        ]);
    }

    public function downloadInvoice(Request $request, int $invoice): mixed
    {
        $companyInvoice = invoicing_pinjaman::query()
            ->whereKey($request->integer('company_invoice'))
            ->whereHas('pinjaman', fn ($query) => $query->where('id_lender', auth('lender')->user()?->id_lender))
            ->with('perusahaan')
            ->firstOrFail();

        abort_unless($this->isInCompanyDueWindow($companyInvoice), 403, 'Invoice hanya bisa didownload pada periode H-jatuh tempo perusahaan.');

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

    public function uploadProof(Request $request, int $invoice): RedirectResponse
    {
        $lenderId = auth('lender')->user()?->id_lender;

        $invoice = invoicing_pinjaman::query()
            ->whereKey($invoice)
            ->whereHas('perusahaan', fn ($query) => $query->where('id_lender', $lenderId))
            ->with(['perusahaan', 'jatuh_tempo.pinjaman'])
            ->firstOrFail();

        abort_unless($this->isInCompanyDueWindow($invoice), 403, 'Upload bukti hanya bisa dilakukan pada periode H-jatuh tempo perusahaan.');

        $data = $request->validate([
            'tanggal_pembayaran' => ['nullable', 'date'],
            'payment_proof_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $file = $request->file('payment_proof_file');
        $fileName = Str::slug($invoice->no_invoice ?? 'invoice-'.$invoice->id).'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();

        DB::transaction(function () use ($invoice, $data, $file, $fileName) {
            $totalTagihan = $this->tagihanPinjamanQuery($invoice)->sum('total_angsuran_perbulan');

            foreach ($this->validJatuhTempo($invoice) as $jatuhTempo) {
                $loan = $jatuhTempo->pinjaman;

                $jatuhTempoUpdate = jatuh_tempo::query()
                    ->where('id_pinjaman', $loan->id)
                    ->where('status', 'belum')
                    ->orderBy('pembayaran')
                    ->first();

                if ($jatuhTempoUpdate) {
                    $jatuhTempoUpdate->status = 'lunas';
                    $jatuhTempoUpdate->save();
                }

                $notification = new notification();
                $notification->user_id = $loan->user_id;
                $notification->title = 'Cicilanmu Berhasil Dibayarkan';
                $notification->description = 'Selamat! Pembayaran cicilanmu untuk periode ini sudah berhasil kami terima!';
                $notification->type = 'pinjaman_payroll';
                $notification->read = 'belum';
                $notification->save();

                $paidCount = jatuh_tempo::query()
                    ->where('id_pinjaman', $loan->id)
                    ->where('status', 'lunas')
                    ->count();

                if ((int) $loan->tenor === $paidCount) {
                    $notification = new notification();
                    $notification->user_id = $loan->user_id;
                    $notification->title = 'Akhirnya Lunas!';
                    $notification->description = 'Hebat! Pembayaran cicilan Anda telah lunas. Sekarang, Anda bebas dari kewajiban pembayaran. Kami tunggu transaksi selanjutnya!';
                    $notification->type = 'pinjaman_payroll';
                    $notification->read = 'belum';
                    $notification->save();

                    $loan->status = 'done';
                    $loan->save();
                }
            }

            $invoice->status = 'lunas';
            $invoice->save();

            Repayment::query()->updateOrCreate(
                ['id_invoicing' => $invoice->id],
                $this->withAdmin([
                    'amount_total' => $totalTagihan,
                    'tanggal_pembayaran' => $data['tanggal_pembayaran'] ?? now(),
                    'payment_proof_file' => $file->storeAs('repayments/bukti-pembayaran', $fileName, 'public'),
                    'payment_proof_date' => now()->toDateString(),
                ])
            );
        });

        return redirect()->route('lender.repayments.index')->with('success', 'Repayment berhasil diproses.');
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

    private function tagihanPinjamanQuery(invoicing_pinjaman $invoice)
    {
        $period = Carbon::parse($invoice->period);

        return pinjaman::query()
            ->where('id_perusahaan', $invoice->id_list_perusahaan)
            ->where('id_lender', auth('lender')->user()?->id_lender)
            ->where('status', 'approve')
            ->whereHas('angsuran', function ($query) use ($period) {
                $query->whereYear('jatuh_tempo', $period->year)
                    ->whereMonth('jatuh_tempo', $period->month)
                    ->where('status', '!=', 'lunas');
            });
    }

    private function validJatuhTempo(invoicing_pinjaman $invoice): Collection
    {
        $period = Carbon::parse($invoice->period);

        return $invoice->jatuh_tempo->filter(function ($item) use ($invoice, $period) {
            if (! $item->pinjaman) {
                return false;
            }

            if ((int) $item->pinjaman->id_perusahaan !== (int) $invoice->id_list_perusahaan) {
                return false;
            }

            if ((int) $item->pinjaman->id_lender !== (int) auth('lender')->user()?->id_lender) {
                return false;
            }

            if ($item->pinjaman->status !== 'approve') {
                return false;
            }

            return $item->status !== 'lunas'
                && Carbon::parse($item->jatuh_tempo)->format('Y-m') === $period->format('Y-m');
        })->values();
    }

    private function isInCompanyDueWindow(invoicing_pinjaman $invoice): bool
    {
        $company = $invoice->perusahaan;

        if (! $company || blank($invoice->period) || blank($company->payment_date)) {
            return false;
        }

        $period = Carbon::parse($invoice->period);
        $paymentDay = min((int) $company->payment_date, $period->daysInMonth);
        $dueDate = $period->copy()->day($paymentDay)->startOfDay();
        $availableFrom = $dueDate->copy()->subDays((int) ($company->h_min_jatuh_tempo ?? 0));

        return now()->startOfDay()->betweenIncluded($availableFrom, $dueDate);
    }
}
