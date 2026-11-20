@extends('internal.layouts.shell')
@section('title', 'Dashboard Tomi Dana')
@section('crumbs', 'Tomi Dana')
@section('page-title', 'Dashboard')

{{--
    Dashboard TomiDana. Memakai komponen tema yang sama dengan Dashboard internal (panel, kpi-label, badge, tbl)
    dan token warna (--brand, --good, dst.) sehingga ikut mode terang/gelap.
--}}
@php
    $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');
    $num = fn ($value) => number_format((float) $value, 0, ',', '.');
    $kpiIcons = ['blue' => 'table', 'green' => 'bolt', 'violet' => 'bank', 'pink' => 'check'];
    $kpiTones = ['blue' => 'var(--s1)', 'green' => 'var(--good-fill)', 'violet' => 'var(--s3)', 'pink' => 'var(--s2)'];
@endphp

@section('content')
    {{-- Pilih produk & periode --}}
    <div class="-mt-1 mb-5 flex flex-wrap items-center gap-3">
        <p class="section-sub">Ringkasan kinerja produk TomiDana · {{ $periodLabel }}</p>
        <div class="ml-auto flex flex-wrap items-center gap-2">
            @foreach ($products as $product)
                <a href="{{ route($product['route']) }}" class="chip" aria-pressed="{{ $product['active'] ? 'true' : 'false' }}">{{ $product['name'] }}</a>
            @endforeach
        </div>
    </div>

    <form method="GET" class="panel panel-pad mb-4 flex flex-wrap items-center gap-3 fade-in" style="--i: 0">
        <span class="kpi-label">Periode</span>
        <select class="ui-btn" id="periode" name="periode" onchange="togglePeriode()">
            <option value="bulan" @selected($filter['periode'] === 'bulan')>Bulan</option>
            <option value="tahun" @selected($filter['periode'] === 'tahun')>Tahun</option>
            <option value="semua" @selected($filter['periode'] === 'semua')>Semua waktu</option>
        </select>
        <input class="ui-btn" type="month" id="inputBulan" name="bulan" value="{{ $filter['bulan'] }}">
        <input class="ui-btn w-28" type="number" id="inputTahun" name="tahun" min="2020" max="2100" value="{{ $filter['tahun'] }}">
        <button class="ui-btn primary" type="submit">Tampilkan</button>
    </form>

    {{-- ===================== KPI ===================== --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kpis as $i => $kpi)
            <section class="panel panel-pad fade-in flex items-start gap-4" style="--i: {{ $i + 1 }}">
                <span class="grid h-12 w-12 flex-none place-items-center rounded-[12px]"
                      style="color: {{ $kpiTones[$kpi['tone']] }}; background: color-mix(in srgb, {{ $kpiTones[$kpi['tone']] }} 12%, transparent)">
                    <svg class="i !h-6 !w-6"><use href="#i-{{ $kpiIcons[$kpi['tone']] }}"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="kpi-label">{{ $kpi['label'] }}</div>
                    <div class="figure-lg num mt-1 truncate">{{ $rupiah($kpi['value']) }}</div>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[12px]">
                        @if ($kpi['delta'])
                            <span class="badge {{ $kpi['delta']['up'] ? 'good' : 'crit' }}">{!! $kpi['delta']['up'] ? '&uarr;' : '&darr;' !!} {{ $kpi['delta']['text'] }}</span>
                            <span class="text-muted">vs. {{ $prevLabel }}</span>
                        @else
                            <span class="text-muted">{{ $kpi['sub'] }}</span>
                        @endif
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    {{-- ===================== Tren & status ===================== --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
        <section class="panel panel-pad fade-in" style="--i: 5">
            <div class="flex flex-wrap items-start gap-3">
                <div>
                    <div class="panel-title">Tren Bulanan</div>
                    <div class="panel-sub">Pencairan dan pendapatan TOMI per bulan</div>
                </div>
                <div class="ml-auto flex flex-wrap items-center gap-3 text-[12px] text-ink-2">
                    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-[3px]" style="background: var(--s1)"></span>Pencairan</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-[3px] w-4 rounded" style="background: var(--s2)"></span>Pendapatan TOMI</span>
                </div>
            </div>
            <div class="relative mt-4 h-[300px]"><canvas id="trendChart"></canvas></div>
        </section>

        <section class="panel panel-pad fade-in" style="--i: 6">
            <div class="panel-title">Status Pengajuan</div>
            <div class="panel-sub">Pengajuan pada {{ strtolower($periodLabel) }}</div>
            @if ($status['total'] > 0)
                <div class="mt-4 grid items-center gap-4 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                    <div class="relative mx-auto w-[200px] max-w-full">
                        <canvas id="statusChart" width="200" height="200"></canvas>
                        <div class="pointer-events-none absolute inset-0 grid place-items-center text-center">
                            <div>
                                <div class="figure-lg num">{{ $num($status['total']) }}</div>
                                <div class="text-[11.5px] text-muted">Total pengajuan</div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach ($status['items'] as $item)
                            <div class="flex items-start gap-2.5">
                                <span class="mt-1 h-2.5 w-2.5 flex-none rounded-[3px]" style="background: var({{ $item['token'] }})"></span>
                                <div class="min-w-0">
                                    <div class="text-[13px] font-semibold">{{ $item['label'] }}</div>
                                    <div class="num text-[12px] text-muted">{{ $num($item['value']) }} ({{ number_format($item['percent'], 1, ',', '.') }}%)</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="mt-4 text-[13px] text-muted">Belum ada pengajuan pada periode ini.</p>
            @endif
        </section>
    </div>

    {{-- ===================== Mitra & insight ===================== --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
        <section class="panel panel-pad fade-in" style="--i: 7">
            <div class="flex items-start gap-3">
                <div>
                    <div class="panel-title">Perusahaan Mitra</div>
                    <div class="panel-sub">Perusahaan ber-PKS TomiDana dengan pencairan terbesar</div>
                </div>
                <a href="{{ route('internal.tomi-dana.companies') }}" class="link ml-auto inline-flex items-center gap-1 text-[12.5px] font-medium">Lihat Semua<svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></a>
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="tbl">
                    <thead><tr><th>Perusahaan</th><th class="r">Pengguna</th><th class="r">Nominal Pencairan</th><th class="r">Status</th></tr></thead>
                    <tbody>
                        @forelse ($companies as $company)
                            <tr>
                                <td>
                                    <a href="{{ route('internal.tomi-dana.applications', $company['id']) }}" class="flex items-center gap-2.5 hover:underline">
                                        <span class="grid h-7 w-7 flex-none place-items-center rounded-[8px]" style="background: var(--surface-2); color: var(--ink-2)">
                                            <svg class="i !h-4 !w-4"><use href="#i-bank"/></svg>
                                        </span>
                                        <span class="whitespace-nowrap">{{ $company['name'] }}</span>
                                    </a>
                                </td>
                                <td class="r">{{ $num($company['employees']) }}</td>
                                <td class="r">{{ $rupiah($company['nominal']) }}</td>
                                <td class="r"><span class="badge {{ $company['active'] ? 'good' : 'mute' }}">{{ $company['active'] ? 'Aktif' : 'Tidak Aktif' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">Belum ada perusahaan ber-PKS TomiDana.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel panel-pad fade-in" style="--i: 8; background: color-mix(in srgb, var(--brand-wash) 60%, var(--surface))">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 flex-none place-items-center rounded-[10px]" style="background: var(--brand-wash); color: var(--brand-ink)">
                    <svg class="i !h-5 !w-5"><use href="#i-bolt"/></svg>
                </span>
                <div class="panel-title">Insight</div>
            </div>
            <p class="mt-4 text-[16px] font-semibold leading-snug">{{ $insights[0] ?? '-' }}</p>
            @foreach (array_slice($insights, 1) as $line)
                <p class="mt-2 text-[12.5px] text-ink-2">{{ $line }}</p>
            @endforeach
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/chart-js/Chart.bundle.min.js') }}"></script>
    <script>
        function togglePeriode() {
            const periode = document.getElementById('periode').value;
            document.getElementById('inputBulan').style.display = periode === 'bulan' ? '' : 'none';
            document.getElementById('inputTahun').style.display = periode === 'tahun' ? '' : 'none';
        }
        togglePeriode();

        (function () {
            // Warna diambil dari token tema agar grafik ikut mode terang/gelap.
            const css = getComputedStyle(document.documentElement);
            const token = (name) => css.getPropertyValue(name).trim();
            const color = { bar: token('--s1'), line: token('--s2'), grid: token('--grid'), text: token('--muted'), surface: token('--surface') };

            Chart.defaults.global.defaultFontFamily = getComputedStyle(document.body).fontFamily;
            Chart.defaults.global.defaultFontColor = color.text;

            const rupiah = (value) => 'Rp. ' + Math.round(value).toLocaleString('id-ID');
            // Label sumbu dipersingkat (Rb / Jt / M) agar muat; tooltip tetap angka lengkap.
            const shortRupiah = (value) => {
                if (value >= 1e9) return Math.round(value / 1e9).toLocaleString('id-ID') + ' M';
                if (value >= 1e6) return Math.round(value / 1e6).toLocaleString('id-ID') + ' Jt';
                if (value >= 1e3) return Math.round(value / 1e3).toLocaleString('id-ID') + ' Rb';
                return Math.round(value).toLocaleString('id-ID');
            };

            const trend = @json($trend);
            new Chart(document.getElementById('trendChart'), {
                type: 'bar',
                data: {
                    labels: trend.labels,
                    datasets: [
                        { type: 'line', label: 'Pendapatan TOMI', data: trend.revenue, yAxisID: 'revenue', borderColor: color.line, backgroundColor: color.surface,
                          pointBorderColor: color.line, pointBackgroundColor: color.surface, pointRadius: 4, pointBorderWidth: 2, borderWidth: 2, fill: false, lineTension: 0 },
                        { type: 'bar', label: 'Pencairan', data: trend.disbursed, yAxisID: 'disbursed', backgroundColor: color.bar },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    legend: { display: false },
                    tooltips: { mode: 'index', intersect: false, callbacks: { label: (item, data) => data.datasets[item.datasetIndex].label + ': ' + rupiah(item.yLabel) } },
                    scales: {
                        xAxes: [{ gridLines: { display: false }, barPercentage: 0.6, categoryPercentage: 0.8 }],
                        yAxes: [
                            { id: 'disbursed', position: 'left', ticks: { beginAtZero: true, callback: shortRupiah }, gridLines: { color: color.grid, zeroLineColor: color.grid, drawBorder: false } },
                            { id: 'revenue', position: 'right', ticks: { beginAtZero: true, callback: shortRupiah }, gridLines: { display: false, drawBorder: false } },
                        ],
                    },
                },
            });

            const status = @json($status);
            const statusCanvas = document.getElementById('statusChart');
            if (statusCanvas) {
                new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: status.items.map(item => item.label),
                        datasets: [{ data: status.items.map(item => item.value), backgroundColor: status.items.map(item => token(item.token)), borderColor: color.surface, borderWidth: 2 }],
                    },
                    options: {
                        cutoutPercentage: 64,
                        legend: { display: false },
                        tooltips: { callbacks: { label: (item, data) => data.labels[item.index] + ': ' + data.datasets[0].data[item.index].toLocaleString('id-ID') } },
                    },
                });
            }
        })();
    </script>
@endpush
