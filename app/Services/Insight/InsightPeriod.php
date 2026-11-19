<?php

namespace App\Services\Insight;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

/**
 * Periode laporan dasbor: semua waktu, tahunan, bulanan, harian, atau rentang kustom,
 * beserta periode pembanding (panjang sama, tepat sebelum periode aktif) dan granularitas grafik.
 */
final class InsightPeriod
{
    private const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    private const MONTHS_LONG = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    private function __construct(
        public readonly string $mode,
        public readonly CarbonImmutable $from,
        public readonly CarbonImmutable $to,
        public readonly string $granularity,
        public readonly string $label,
        public readonly ?CarbonImmutable $prevFrom,
        public readonly ?CarbonImmutable $prevTo,
        public readonly ?string $prevLabel,
    ) {}

    public static function fromRequest(Request $request, CarbonImmutable $earliest, ?CarbonImmutable $today = null): self
    {
        $today = ($today ?? CarbonImmutable::now())->startOfDay();
        $mode = in_array($request->query('mode'), ['all', 'year', 'month', 'day', 'custom'], true) ? $request->query('mode') : 'year';

        $prevFrom = $prevTo = null;
        $prevLabel = null;

        switch ($mode) {
            case 'all':
                $from = $earliest->startOfMonth();
                $to = $today;
                $gran = 'month';
                $label = 'Semua waktu';
                break;

            case 'month':
                $month = self::parse($request->query('month').'-01') ?? $today->startOfMonth();
                $from = $month->startOfMonth();
                $to = $month->endOfMonth()->startOfDay();
                $gran = 'day';
                $label = self::MONTHS_LONG[$from->month - 1].' '.$from->year;
                $prevFrom = $from->subMonthNoOverflow()->startOfMonth();
                $prevTo = $prevFrom->endOfMonth()->startOfDay();
                $prevLabel = 'bulan lalu';
                if ($to->gt($today)) {
                    $to = $today;
                    $prevTo = $prevFrom->addDays(min($today->day, $prevFrom->daysInMonth) - 1);
                    $prevLabel = 'periode sama bulan lalu';
                }
                break;

            case 'day':
                $day = self::parse($request->query('day')) ?? $today;
                $from = $to = $day->gt($today) ? $today : $day;
                $gran = 'hour';
                $label = self::date($from);
                $prevFrom = $prevTo = $from->subDay();
                $prevLabel = 'hari sebelumnya';
                break;

            case 'custom':
                $from = self::parse($request->query('from')) ?? $today->subDays(29);
                $to = self::parse($request->query('to')) ?? $today;
                if ($to->lt($from)) {
                    [$from, $to] = [$to, $from];
                }
                if ($to->gt($today)) {
                    $to = $today;
                }
                $days = $from->diffInDays($to) + 1;
                $gran = $days <= 1 ? 'hour' : ($days <= 62 ? 'day' : 'month');
                $label = self::date($from).' – '.self::date($to);
                $prevTo = $from->subDay();
                $prevFrom = $prevTo->subDays($days - 1);
                $prevLabel = $days.' hari sebelumnya';
                break;

            default: // year
                $year = (int) ($request->query('year') ?: $today->year);
                $year = max(2000, min($today->year, $year));
                $from = CarbonImmutable::create($year, 1, 1);
                $to = CarbonImmutable::create($year, 12, 31);
                $gran = 'month';
                $label = 'Tahun '.$year;
                $prevFrom = $from->subYear();
                $prevTo = $to->subYear();
                $prevLabel = 'tahun '.($year - 1);
                if ($to->gt($today)) {
                    $to = $today;
                    $prevTo = $today->subYear();
                    $prevLabel = 'periode sama '.($year - 1);
                }
                $mode = 'year';
        }

        return new self($mode, $from->startOfDay(), $to->startOfDay(), $gran, $label, $prevFrom?->startOfDay(), $prevTo?->startOfDay(), $prevLabel);
    }

    /** Periode pembanding dengan granularitas yang sama (tanpa pembanding lagi). */
    public function previous(): ?self
    {
        if (! $this->prevFrom) {
            return null;
        }

        return new self($this->mode, $this->prevFrom, $this->prevTo, $this->granularity, $this->prevLabel, null, null, null);
    }

    public function start(): string
    {
        return $this->from->toDateTimeString();
    }

    public function end(): string
    {
        return $this->to->endOfDay()->toDateTimeString();
    }

    /** Format SQL DATE_FORMAT untuk mengelompokkan per bucket grafik. */
    public function sqlFormat(): string
    {
        return ['month' => '%Y-%m', 'day' => '%Y-%m-%d', 'hour' => '%H'][$this->granularity];
    }

    /** @return array<int, array{key:string,label:string}> */
    public function buckets(): array
    {
        $list = [];
        if ($this->granularity === 'hour') {
            for ($h = 0; $h < 24; $h++) {
                $list[] = ['key' => sprintf('%02d', $h), 'label' => sprintf('%02d.00', $h)];
            }
        } elseif ($this->granularity === 'day') {
            for ($d = $this->from; $d->lte($this->to); $d = $d->addDay()) {
                $list[] = ['key' => $d->format('Y-m-d'), 'label' => $d->day.' '.self::MONTHS[$d->month - 1]];
            }
        } else {
            $multiYear = $this->from->year !== $this->to->year;
            for ($d = $this->from->startOfMonth(); $d->lte($this->to); $d = $d->addMonthNoOverflow()) {
                $list[] = ['key' => $d->format('Y-m'), 'label' => self::MONTHS[$d->month - 1].($multiYear ? ' '.$d->format('y') : '')];
            }
        }

        return $list;
    }

    /**
     * Akhir tiap bucket (dibatasi akhir periode), untuk angka "per tanggal" seperti NOA outstanding.
     * Null untuk granularitas jam (posisi per jam tidak bermakna untuk saldo).
     *
     * @return array<int, CarbonImmutable>|null
     */
    public function bucketEnds(): ?array
    {
        if ($this->granularity === 'hour') {
            return null;
        }
        $ends = [];
        foreach ($this->buckets() as $b) {
            $d = $this->granularity === 'day'
                ? CarbonImmutable::createFromFormat('!Y-m-d', $b['key'])
                : CarbonImmutable::createFromFormat('!Y-m', $b['key'])->endOfMonth()->startOfDay();
            $ends[] = $d->gt($this->to) ? $this->to : $d;
        }

        return $ends;
    }

    /** Catatan untuk bucket terakhir yang belum penuh (bulan/hari berjalan). */
    public function partialNote(): ?string
    {
        $today = CarbonImmutable::now()->startOfDay();
        if ($this->granularity === 'month' && $this->to->lt($this->to->endOfMonth()->startOfDay())) {
            return self::MONTHS_LONG[$this->to->month - 1].' baru sampai '.self::date($this->to);
        }
        if ($this->granularity === 'hour' && $this->to->equalTo($today)) {
            return 'hari ini masih berjalan';
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'mode' => $this->mode,
            'from' => $this->from->toDateString(),
            'to' => $this->to->toDateString(),
            'granularity' => $this->granularity,
            'label' => $this->label,
            'range' => self::date($this->from).' – '.self::date($this->to),
            'prev_label' => $this->prevLabel,
            'partial_note' => $this->partialNote(),
        ];
    }

    public static function date(CarbonImmutable $d): string
    {
        return $d->day.' '.self::MONTHS[$d->month - 1].' '.$d->year;
    }

    private static function parse(?string $value): ?CarbonImmutable
    {
        if (! $value || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        try {
            return CarbonImmutable::createFromFormat('!Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
    }
}
