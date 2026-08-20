<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\FinanceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceRequestController extends Controller
{
    /**
     * List Finance Request.
     */
    public function index()
    {
        $financeRequests = FinanceRequest::with('company')
            ->latest()
            ->get();

        return view(
            'admin.finance-request.index',
            compact('financeRequests')
        );
    }

    /**
     * Form create.
     */
    public function create()
    {
        $companies = Company::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.finance-request.create',
            compact('companies')
        );
    }

    /**
     * Simpan Finance Request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => [
                'required',
                'exists:companies,id',
            ],

            'request_date' => [
                'required',
                'date',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:Pending,Process,Approved,Rejected,Revision',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        DB::beginTransaction();

        try {

            $validated['reference_number'] =
                $this->generateReferenceNumber();

            FinanceRequest::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.finance-request.index')
                ->with(
                    'success',
                    'Finance Request berhasil dibuat.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Finance Request gagal dibuat: ' .
                    $e->getMessage()
                );
        }
    }

    /**
     * Detail Finance Request.
     */
    public function show($id)
    {
        $financeRequest = FinanceRequest::with('company')
            ->findOrFail($id);

        return view(
            'admin.finance-request.show',
            compact('financeRequest')
        );
    }

    /**
     * Form edit.
     */
    public function edit($id)
    {
        $financeRequest =
            FinanceRequest::findOrFail($id);

        $companies = Company::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.finance-request.edit',
            compact(
                'financeRequest',
                'companies'
            )
        );
    }

    /**
     * Update Finance Request.
     */
    public function update(
        Request $request,
        $id
    ) {
        $financeRequest =
            FinanceRequest::findOrFail($id);

        $validated = $request->validate([
            'company_id' => [
                'required',
                'exists:companies,id',
            ],

            'request_date' => [
                'required',
                'date',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:Pending,Process,Approved,Rejected,Revision',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        DB::beginTransaction();

        try {

            $financeRequest->update(
                $validated
            );

            DB::commit();

            return redirect()
                ->route(
                    'admin.finance-request.show',
                    $financeRequest->id
                )
                ->with(
                    'success',
                    'Finance Request berhasil diperbarui.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Finance Request gagal diperbarui: ' .
                    $e->getMessage()
                );
        }
    }

    /**
     * Soft delete Finance Request.
     */
    public function destroy($id)
    {
        $financeRequest =
            FinanceRequest::findOrFail($id);

        try {

            $financeRequest->delete();

            return redirect()
                ->route('admin.finance-request.index')
                ->with(
                    'success',
                    'Finance Request berhasil dihapus.'
                );

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                'Finance Request gagal dihapus: ' .
                $e->getMessage()
            );
        }
    }

    /**
     * Approve Finance Request.
     */
    public function approve($id)
    {
        $financeRequest =
            FinanceRequest::findOrFail($id);

        $financeRequest->update([
            'status' => 'Approved',
        ]);

        return back()->with(
            'success',
            'Finance Request berhasil disetujui.'
        );
    }

    /**
     * Reject Finance Request.
     */
    public function reject(
        Request $request,
        $id
    ) {
        $request->validate([
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $financeRequest =
            FinanceRequest::findOrFail($id);

        $financeRequest->update([
            'status' => 'Rejected',
            'notes' => $request->notes,
        ]);

        return back()->with(
            'success',
            'Finance Request berhasil ditolak.'
        );
    }

    /**
     * Tandai Finance Request sebagai revisi.
     */
    public function revision(
        Request $request,
        $id
    ) {
        $request->validate([
            'notes' => [
                'required',
                'string',
            ],
        ]);

        $financeRequest =
            FinanceRequest::findOrFail($id);

        $financeRequest->update([
            'status' => 'Revision',
            'notes' => $request->notes,
        ]);

        return back()->with(
            'success',
            'Finance Request dikembalikan untuk revisi.'
        );
    }

    /**
     * Generate invoice PDF.
     */
    public function invoicePdf($id)
    {
        $financeRequest =
            FinanceRequest::with('company')
                ->findOrFail($id);

        $pdf = Pdf::loadView(
            'admin.finance-request.invoice-pdf',
            compact('financeRequest')
        );

        $pdf->setPaper(
            'A4',
            'portrait'
        );

        return $pdf->stream(
            'finance-request-' .
            $financeRequest->reference_number .
            '.pdf'
        );
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoicePdf($id)
    {
        $financeRequest =
            FinanceRequest::with('company')
                ->findOrFail($id);

        $pdf = Pdf::loadView(
            'admin.finance-request.invoice-pdf',
            compact('financeRequest')
        );

        $pdf->setPaper(
            'A4',
            'portrait'
        );

        return $pdf->download(
            'finance-request-' .
            $financeRequest->reference_number .
            '.pdf'
        );
    }

    /**
     * Generate reference number.
     */
    private function generateReferenceNumber()
    {
        $prefix =
            'FR-' . date('Ym') . '-';

        $last = FinanceRequest::withTrashed()
            ->where(
                'reference_number',
                'like',
                $prefix . '%'
            )
            ->orderByDesc('id')
            ->first();

        $number = 1;

        if ($last) {

            $lastNumber =
                (int) substr(
                    $last->reference_number,
                    -5
                );

            $number =
                $lastNumber + 1;
        }

        return $prefix .
            str_pad(
                $number,
                5,
                '0',
                STR_PAD_LEFT
            );
    }
}