<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Insight\InsightDetail;
use App\Services\Insight\InsightPeriod;
use App\Services\Insight\InsightService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Dasbor utama + dasbor per produk.
 * Halaman hanya kerangka; angka diambil dari endpoint JSON `data` sesuai filter periode & pemberi dana.
 *
 * Akses: semua akun backoffice yang sudah login (belum ada permission per menu di aplikasi ini).
 * Pemberi dana default = semua; setiap akun boleh memilih pemberi dana.
 */
class InsightController extends Controller
{
    public function __construct(private readonly InsightService $insight)
    {
    }

    public function index()
    {
        return view('admin.insight.index', $this->pageContext('overview'));
    }

    public function product(string $product)
    {
        abort_unless(isset(InsightService::PRODUCTS[$product]), 404);

        return view('admin.insight.product', $this->pageContext($product) + [
            'product' => ['key' => $product] + InsightService::PRODUCTS[$product],
        ]);
    }

    public function data(Request $request)
    {
        $scope = (string) $request->query('scope', 'overview');
        abort_unless($scope === 'overview' || isset(InsightService::PRODUCTS[$scope]), 404);

        $lenderId = $this->resolveLender($request);
        $period = InsightPeriod::fromRequest($request, $this->insight->earliest());

        $data = $scope === 'overview'
            ? $this->insight->overview($period, $lenderId)
            : $this->insight->product($scope, $period, $lenderId);

        return response()->json([
            'period' => $period->toArray(),
            'lender' => ['id' => $lenderId, 'name' => $this->lenderName($lenderId)],
            'data' => $data,
        ]);
    }

    /**
     * Rincian satu kartu KPI (panel detail): daftar akun/transaksi + pecahannya, filter sama dengan dasbor.
     * format=csv → unduh seluruh baris yang cocok dengan pencarian.
     */
    public function detail(Request $request, InsightDetail $detail)
    {
        $scope = (string) $request->query('scope', 'overview');
        $metric = (string) $request->query('metric', '');
        abort_unless(($scope === 'overview' || isset(InsightService::PRODUCTS[$scope])) && InsightDetail::supports($scope, $metric), 404);

        $lenderId = $this->resolveLender($request);
        $period = InsightPeriod::fromRequest($request, $this->insight->earliest());
        $opts = [
            'q' => mb_substr((string) $request->query('q', ''), 0, 100),
            'sort' => (string) $request->query('sort', ''),
            'dir' => (string) $request->query('dir', ''),
            'page' => (int) $request->query('page', 1),
            'stage' => (int) $request->query('stage', 0),
            // f[company]=..&f[lender]=.. (kunci diperiksa lagi di InsightDetail::FILTER_KEYS)
            'filters' => collect((array) $request->query('f', []))->filter(fn ($v) => is_string($v))->map(fn ($v) => mb_substr($v, 0, 200))->all(),
            'only' => array_values(array_intersect(explode(',', (string) $request->query('only', '')), array_keys(InsightService::PRODUCTS))),
        ];

        if ($request->query('format') === 'csv') {
            $data = $detail->build($scope, $metric, $period, $lenderId, $opts, true);

            return $this->csv($data, sprintf('dasbor-%s-%s-%s_%s.csv', $scope, $metric, $period->from->toDateString(), $period->to->toDateString()));
        }

        return response()->json([
            'period' => $period->toArray(),
            'lender' => ['id' => $lenderId, 'name' => $this->lenderName($lenderId)],
            'data' => $detail->build($scope, $metric, $period, $lenderId, $opts),
        ]);
    }

    private function csv(array $data, string $filename)
    {
        // Pemisah ";" supaya langsung terbaca per kolom di Excel berlocale Indonesia; BOM untuk UTF-8.
        $safe = fn ($v) => is_string($v) && $v !== '' && in_array($v[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'" . $v : $v;

        return response()->streamDownload(function () use ($data, $safe) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, array_column($data['columns'], 'label'), ';');
            foreach ($data['rows'] as $r) {
                fputcsv($out, array_map(function ($c) use ($r, $safe) {
                    $v = $r[$c['key']] ?? null;
                    if ($c['key'] === 'status') {
                        return $v['label'] ?? '';
                    }
                    if ($c['key'] === 'amount') {
                        return round((float) $v);
                    }

                    return $safe($v ?? '');
                }, $data['columns']), ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Pemberi dana dari filter: kosong / `all` = semua, id yang tidak ada → 422. */
    private function resolveLender(Request $request): ?int
    {
        $value = $request->query('lender');
        if ($value === null || $value === '' || $value === 'all') {
            return null;
        }

        $id = (int) $value;
        abort_unless(DB::table('lenders')->where('id', $id)->exists(), 422, 'Pemberi dana tidak ditemukan');

        return $id;
    }

    private function lenderName(?int $lenderId): string
    {
        return $lenderId ? (string) DB::table('lenders')->where('id', $lenderId)->value('nama') : 'Semua pemberi dana';
    }

    private function pageContext(string $scope): array
    {
        $earliest = $this->insight->earliest();
        $today = now()->startOfDay();

        return [
            'scope' => $scope,
            'lenders' => DB::table('lenders')->orderBy('id')->get(['id', 'nama']),
            'canSwitchLender' => true,
            'lockedLenderName' => null,
            'cfg' => [
                'scope' => $scope,
                'endpoint' => route('admin.insight.data'),
                'detailEndpoint' => route('admin.insight.detail'),
                'productUrls' => collect(InsightService::PRODUCTS)->map(fn ($p, $key) => route('admin.insight.product', $key))->all(),
                'years' => range($today->year, min($earliest->year, $today->year)),
                'minDate' => $earliest->toDateString(),
                'maxDate' => $today->toDateString(),
                'defaultFrom' => $today->copy()->subDays(29)->max($earliest)->toDateString(),
                'lender' => 'all',
                'canSwitch' => true,
            ],
        ];
    }
}
