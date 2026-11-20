<?php

namespace App\Services\Insight;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Perusahaan mitra di Dasbor utama: berapa perusahaan yang bekerja sama, bertambah, berkurang, dan bertransaksi per periode.
 *
 * Definisi (keputusan Bintang 19 Nov 2026, tanpa ubah database):
 * - Sumber: list_perusahaans (TomiDana & TomiGaji), clients (mitra Tomi Invoice), dan perusahaan upload manual
 *   (invoice_generate_manuals) yang tidak tercatat di list_perusahaans. Nama yang sama (abaikan huruf besar/kecil &
 *   spasi berlebih) dihitung satu perusahaan.
 * - Data tes (EXCLUDED_COMPANY_IDS) tidak dihitung.
 * - Mulai kerja sama = tanggal paling awal antara tanggal didaftarkan (created_at) dan transaksi pertama.
 *   Contoh: PT Toko Mikro didaftarkan 1 Jan 2026, tapi pinjaman pertamanya 8 Okt 2025 → mulai 8 Okt 2025.
 * - Berhenti = data perusahaan dihapus (deleted_at). Perusahaan dari beberapa sumber baru berhenti kalau semua datanya dihapus.
 * - Terdaftar per tanggal X = sudah mulai ≤ X dan belum berhenti per X.
 * - Bertransaksi = punya penyaluran dalam periode, dengan definisi penyaluran yang sama dengan dasbor
 *   (TomiDana approve/done, upload manual, TomiGaji yang ditransfer, termin Tomi Invoice yang dibayar).
 * - Status aktif/nonaktif hanya posisi SEKARANG: riwayat perubahan status belum tersimpan di database.
 * - Filter pemberi dana memakai pemberi dana perusahaan sekarang (list_perusahaans.id_lender / clients.lender_id).
 *   Perusahaan upload manual yang tidak tercatat tidak punya pemberi dana, jadi tidak ikut saat filter dipilih.
 */
final class CompanyInsight
{
    /** Data tes di list_perusahaans yang tidak dihitung: PT ABCDEFG (7) dan GOOGLE REVIEW (14). */
    public const EXCLUDED_COMPANY_IDS = [7, 14];

    private const PRODUCT_NAMES = ['tomidana' => 'TomiDana', 'manual' => 'Upload manual', 'tomigaji' => 'TomiGaji', 'tomiinvoice' => 'Tomi Invoice'];

    private ?Collection $partners = null;

    public function __construct(private readonly InsightService $insight) {}

    /** Tanggal mulai kerja sama paling awal (untuk batas awal "Semua waktu"). */
    public function earliestStart(): ?string
    {
        return $this->partners()->min('start');
    }

    /** Blok "Perusahaan mitra" untuk Dasbor utama. */
    public function block(InsightPeriod $period, ?int $lenderId): array
    {
        $list = $this->partners()->filter(fn ($p) => ! $lenderId || in_array($lenderId, $p['lender_ids'], true))->values();
        $prev = $period->previous();
        $cur = $this->counts($list, $period->start(), $period->end());
        $old = $prev ? $this->counts($list, $prev->start(), $prev->end()) : null;
        $buckets = $period->buckets();
        $bucketOf = fn (string $at) => match ($period->granularity) {
            'month' => substr($at, 0, 7),
            'day' => substr($at, 0, 10),
            default => substr($at, 11, 2),
        };
        $perBucket = function (callable $datesOf) use ($list, $period, $bucketOf, $buckets) {
            $hits = [];
            foreach ($list as $p) {
                foreach (array_unique(array_map($bucketOf, array_filter($datesOf($p), fn ($d) => $d >= $period->start() && $d <= $period->end()))) as $k) {
                    $hits[$k] = ($hits[$k] ?? 0) + 1;
                }
            }

            return array_map(fn ($b) => (int) ($hits[$b['key']] ?? 0), $buckets);
        };
        $ends = $period->bucketEnds();
        $registeredSeries = $ends === null ? null
            : array_map(fn ($at) => $this->registeredAt($list, $at->endOfDay()->toDateTimeString()), $ends);
        $added = $perBucket(fn ($p) => [$p['start']]);
        $removed = $perBucket(fn ($p) => $p['ended'] ? [$p['ended']] : []);
        $transacting = $perBucket(fn ($p) => array_column($p['tx'], 'at'));
        $date = InsightPeriod::date($period->to);
        $t = fn (string $k) => ['value' => $cur[$k], 'prev' => $old[$k] ?? null];

        $snapshot = ['Aktif' => 0, 'Nonaktif' => 0, 'Tidak tercatat' => 0];
        foreach ($list as $p) {
            if (! $p['ended']) {
                $snapshot[$p['status']] = ($snapshot[$p['status']] ?? 0) + 1;
            }
        }

        return [
            'kpis' => [
                $this->kpi('registered', 'Perusahaan terdaftar', $t('registered'), true, $registeredSeries, 'Bekerja sama per '.$date),
                $this->kpi('added', 'Perusahaan baru', $t('added'), true, $added, 'Mulai kerja sama pada periode ini'),
                $this->kpi('removed', 'Perusahaan berhenti', $t('removed'), false, $removed, 'Data perusahaan dihapus pada periode ini'),
                $this->kpi('transacting', 'Perusahaan bertransaksi', $t('transacting'), true, $transacting, 'Ada penyaluran pada periode ini'),
            ],
            'trend' => [
                'categories' => array_column($buckets, 'label'),
                'registered' => $registeredSeries,
                'added' => $added,
                'removed' => $removed,
                'transacting' => $transacting,
            ],
            'start_count' => $this->registeredAt($list, $period->from->subSecond()->toDateTimeString()),
            'snapshot' => collect($snapshot)->map(fn ($n, $label) => ['label' => $label, 'value' => $n, 'tone' => ['Aktif' => 'good', 'Nonaktif' => 'warn'][$label] ?? 'mute'])
                ->filter(fn ($s) => $s['value'] > 0 || $s['label'] !== 'Tidak tercatat')->values()->all(),
            'rows' => $this->rows($list, $period),
            'notes' => [
                'Mulai kerja sama = tanggal paling awal antara tanggal didaftarkan dan transaksi pertama.',
                'Status aktif/nonaktif hanya posisi sekarang; riwayat perubahan status belum tersimpan.',
            ],
        ];
    }

    /** @return array{registered:int, added:int, removed:int, transacting:int} */
    private function counts(Collection $list, string $start, string $end): array
    {
        $in = fn (?string $d) => $d !== null && $d >= $start && $d <= $end;

        return [
            'registered' => $this->registeredAt($list, $end),
            'added' => $list->filter(fn ($p) => $in($p['start']))->count(),
            'removed' => $list->filter(fn ($p) => $in($p['ended']))->count(),
            'transacting' => $list->filter(fn ($p) => $this->productsIn($p, $start, $end) !== [])->count(),
        ];
    }

    private function registeredAt(Collection $list, string $at): int
    {
        return $list->filter(fn ($p) => $p['start'] <= $at && (! $p['ended'] || $p['ended'] > $at))->count();
    }

    /** Nama produk yang punya penyaluran untuk perusahaan ini di antara $start–$end. */
    private function productsIn(array $p, string $start, string $end): array
    {
        $names = [];
        foreach ($p['tx'] as $t) {
            if ($t['at'] >= $start && $t['at'] <= $end) {
                $names[$t['product']] = true;
            }
        }

        return array_values(array_intersect(array_values(self::PRODUCT_NAMES), array_keys($names)));
    }

    /** Baris tabel: semua perusahaan yang sudah mulai s.d. akhir periode (termasuk yang sudah berhenti). */
    private function rows(Collection $list, InsightPeriod $period): array
    {
        $start = $period->start();
        $end = $period->end();
        $in = fn (?string $d) => $d !== null && $d >= $start && $d <= $end;

        return $list->filter(fn ($p) => $p['start'] <= $end)
            ->map(function ($p) use ($start, $end, $in) {
                $endedBy = $p['ended'] !== null && $p['ended'] <= $end;
                $periodProducts = $this->productsIn($p, $start, $end);

                return [
                    'name' => $p['name'],
                    'source' => implode(' · ', $p['sources']),
                    'lender' => $p['lenders'] ? implode(', ', $p['lenders']) : '-',
                    'start' => substr($p['start'], 0, 10),
                    'start_note' => ! $p['start_from_tx'] ? null : ($p['registered']
                        ? 'Transaksi pertama; didaftarkan '.InsightPeriod::date(CarbonImmutable::parse($p['registered']))
                        : 'Transaksi pertama; tidak tercatat di daftar perusahaan'),
                    'ended' => $p['ended'] ? substr($p['ended'], 0, 10) : null,
                    'status' => $endedBy
                        ? ['label' => 'Berhenti', 'tone' => 'crit']
                        : ['label' => $p['status'], 'tone' => ['Aktif' => 'good', 'Nonaktif' => 'warn'][$p['status']] ?? 'mute'],
                    'products' => $p['products'],
                    'period_products' => $periodProducts,
                    'registered' => ! $endedBy,
                    'added' => $in($p['start']),
                    'removed' => $in($p['ended']),
                    'transacting' => $periodProducts !== [],
                ];
            })
            ->sortBy([['registered', 'desc'], ['start', 'asc'], ['name', 'asc']])
            ->values()->all();
    }

    /**
     * Semua perusahaan mitra (tanpa filter periode), satu entri per nama.
     * Entri: key, name, sources[], lenders[], lender_ids[], registered, start, start_from_tx, ended, status, products[], tx[{at, product}].
     */
    public function partners(): Collection
    {
        if ($this->partners !== null) {
            return $this->partners;
        }

        $companies = DB::table('list_perusahaans as lp')->leftJoin('lenders as l', 'l.id', '=', 'lp.id_lender')
            ->whereNotIn('lp.id', self::EXCLUDED_COMPANY_IDS)
            ->get(['lp.id', 'lp.nama', 'lp.id_lender', 'l.nama as lender', 'lp.status', 'lp.created_at', 'lp.deleted_at']);
        $clients = DB::table('clients as c')->leftJoin('lenders as l', 'l.id', '=', 'c.lender_id')
            ->get(['c.id', 'c.name', 'c.lender_id', 'l.nama as lender', 'c.status', 'c.created_at', 'c.deleted_at']);

        $out = [];
        $entry = function (string $name, string $source) use (&$out): ?string {
            $key = self::key($name);
            if ($key === '') {
                return null;
            }
            $out[$key] ??= ['key' => $key, 'name' => $key, 'sources' => [], 'lenders' => [], 'lender_ids' => [],
                'registry' => [], 'tx' => []];
            if (! in_array($source, $out[$key]['sources'], true)) {
                $out[$key]['sources'][] = $source;
            }

            return $key;
        };
        $register = function (string $key, $lenderId, ?string $lender, bool $active, ?string $created, ?string $deleted) use (&$out) {
            $out[$key]['registry'][] = ['active' => $active, 'created' => $created, 'deleted' => $deleted];
            if ($lenderId && ! in_array((int) $lenderId, $out[$key]['lender_ids'], true)) {
                $out[$key]['lender_ids'][] = (int) $lenderId;
                $out[$key]['lenders'][] = $lender ?? '-';
            }
        };

        $companyKey = [];
        foreach ($companies as $c) {
            if ($key = $entry($c->nama, 'Perusahaan')) {
                $companyKey[$c->id] = $key;
                $register($key, $c->id_lender, $c->lender, $c->status === 'active', $c->created_at, $c->deleted_at);
            }
        }
        $clientKey = [];
        foreach ($clients as $c) {
            if ($key = $entry($c->name, 'Mitra Tomi Invoice')) {
                $clientKey[$c->id] = $key;
                $register($key, $c->lender_id, $c->lender, (int) $c->status === 1, $c->created_at, $c->deleted_at);
            }
        }

        $hour = fn (string $col) => DB::raw("DATE_FORMAT($col, '%Y-%m-%d %H:00:00') as at");
        $tx = function (?string $key, string $product, $at) use (&$out) {
            if ($key !== null && $at) {
                $out[$key]['tx'][] = ['at' => (string) $at, 'product' => self::PRODUCT_NAMES[$product]];
            }
        };

        // TomiDana aplikasi: perusahaan saat pengajuan
        DB::table('pinjamen as p')->whereNull('p.deleted_at')->whereIn('p.status', InsightService::DANA_STATUS)->whereNotNull('p.id_perusahaan')
            ->groupBy('p.id_perusahaan', 'at')->get(['p.id_perusahaan as cid', $hour('p.created_at')])
            ->each(fn ($r) => $tx($companyKey[$r->cid] ?? null, 'tomidana', $r->at));
        // Upload manual: lewat company_id, atau nama perusahaan bila company_id kosong
        DB::table('invoice_generate_manuals as m')->groupBy('m.company_id', 'm.nama_perusahaan', 'at')
            ->get(['m.company_id as cid', 'm.nama_perusahaan as name', $hour('m.created_at')])
            ->each(function ($r) use ($tx, $entry, $companyKey) {
                $key = $r->cid ? ($companyKey[$r->cid] ?? null) : (self::key((string) $r->name) !== '' ? $entry((string) $r->name, 'Upload manual') : null);
                $tx($key, 'manual', $r->at);
            });
        // TomiGaji yang sudah ditransfer
        DB::table('salary_financings as s')->whereIn('s.status', InsightService::GAJI_DISBURSED)
            ->groupBy('s.id_perusahaan', 's.company_name', 'at')->get(['s.id_perusahaan as cid', 's.company_name as name', $hour('s.created_at')])
            ->each(fn ($r) => $tx($r->cid ? ($companyKey[$r->cid] ?? null) : ($out[self::key((string) $r->name)]['key'] ?? null), 'tomigaji', $r->at));
        // Tomi Invoice: termin yang sudah dibayar ke pelanggan
        $allTime = InsightPeriod::fromRequest(Request::create('/', 'GET', ['mode' => 'custom', 'from' => '2000-01-01']), CarbonImmutable::create(2000));
        $this->insight->invoiceTerms($allTime, null)->groupBy('it.client_id', 'at')->get(['it.client_id as cid', $hour('it.dt')])
            ->each(fn ($r) => $tx($clientKey[$r->cid] ?? null, 'tomiinvoice', $r->at));

        $this->partners = collect($out)->map(function ($p) {
            $created = array_filter(array_column($p['registry'], 'created'));
            $registered = $created ? min($created) : null;
            $firstTx = $p['tx'] ? min(array_column($p['tx'], 'at')) : null;
            // transaksi pertama baru dipakai kalau HARINYA lebih awal dari tanggal didaftarkan (jam transaksi dibulatkan per jam)
            $fromTx = $firstTx !== null && ($registered === null || substr($firstTx, 0, 10) < substr($registered, 0, 10));
            $start = $fromTx ? $firstTx : ($registered ?? $firstTx);
            $deleted = array_column($p['registry'], 'deleted');
            $ended = $p['registry'] && ! in_array(null, $deleted, true) ? max($deleted) : null;
            $status = $p['registry'] === [] ? 'Tidak tercatat'
                : (collect($p['registry'])->contains(fn ($r) => $r['active'] && $r['deleted'] === null) ? 'Aktif' : 'Nonaktif');
            $products = array_values(array_intersect(array_values(self::PRODUCT_NAMES), array_unique(array_column($p['tx'], 'product'))));

            return [
                'key' => $p['key'], 'name' => $p['name'], 'sources' => $p['sources'], 'lenders' => $p['lenders'], 'lender_ids' => $p['lender_ids'],
                'registered' => $registered, 'start' => $start, 'start_from_tx' => $fromTx,
                'ended' => $ended, 'status' => $status, 'products' => $products, 'tx' => $p['tx'],
            ];
        })->filter(fn ($p) => $p['start'] !== null)->values();

        return $this->partners;
    }

    private function kpi(string $metric, string $label, array $t, bool $upIsGood, ?array $spark, string $hint): array
    {
        return ['metric' => $metric, 'label' => $label, 'value' => $t['value'], 'prev' => $t['prev'], 'format' => 'num', 'up_is_good' => $upIsGood, 'spark' => $spark, 'hint' => $hint];
    }

    /** Kunci pencocokan nama perusahaan: huruf besar, spasi dirapikan. */
    private static function key(string $name): string
    {
        return mb_strtoupper(trim(preg_replace('/\s+/', ' ', $name)));
    }
}
