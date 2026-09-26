<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lender\Resources\DashboardSummaryResource;
use App\Modules\Lender\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {
    }

    public function index(): View
    {
        return view('lender.dashboard.index', [
            'summary' => $this->dashboardService->summary($this->lenderId()),
        ]);
    }

    public function summary(): JsonResponse
    {
        return response()->json([
            'data' => new DashboardSummaryResource($this->dashboardService->summary($this->lenderId())),
        ]);
    }

    public function disbursement(): View
    {
        return view('lender.tomi-dana.disbursement', [
            'summary' => $this->dashboardService->summary($this->lenderId()),
        ]);
    }

    public function repayment(): RedirectResponse
    {
        return redirect()->route('lender.repayments.index');
    }

    private function lenderId(): int
    {
        return (int) auth('lender')->user()->id_lender;
    }
}
