@extends('internal.layouts.app')
@section('title', 'Dashboard Tomi Dana')

@php
    $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');
    $num = fn ($value) => number_format((float) $value, 0, ',', '.');
@endphp

@push('css')
    <style>
        .td-dash { --td-ink: #1f2a44; --td-muted: #6b7280; --td-line: #e8ecf4; --td-blue: #2f7cf6; --td-violet: #7c3aed; }
        .td-dash .td-head { display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; margin-bottom: 1.25rem; }
        .td-dash .td-head-icon { width: 56px; height: 56px; border-radius: 14px; display: grid; place-items: center; background: #e8f0ff; color: var(--td-blue); font-size: 30px; flex: none; }
        .td-dash .td-title { font-size: 26px; font-weight: 700; color: var(--td-ink); margin: 0; line-height: 1.2; }
        .td-dash .td-title span { color: var(--td-blue); }
        .td-dash .td-subtitle { color: var(--td-muted); margin: 2px 0 0; }
        .td-dash .td-products { margin-left: auto; display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }
        .td-dash .td-pill { border: 1px solid #d5dbe8; border-radius: 999px; padding: .35rem 1rem; color: var(--td-ink); background: #fff; font-size: 13px; white-space: nowrap; }
        .td-dash .td-pill:hover { border-color: var(--td-blue); color: var(--td-blue); text-decoration: none; }
        .td-dash .td-pill.active { background: var(--td-blue); border-color: var(--td-blue); color: #fff; }

        .td-dash .td-panel { background: #fff; border: 1px solid var(--td-line); border-radius: 16px; padding: 1.25rem; height: 100%; }
        .td-dash .td-panel-title { font-size: 18px; font-weight: 700; color: var(--td-ink); margin: 0; }

        .td-dash .td-kpi { position: relative; overflow: hidden; border-radius: 16px; border: 1px solid var(--td-line); padding: 1.1rem 1.1rem 1.4rem; height: 100%; background: #fff; }
        .td-dash .td-kpi::after { content: ""; position: absolute; left: -10%; right: -10%; bottom: -38px; height: 70px; border-radius: 50%; opacity: .55; }
        .td-dash .td-kpi.blue::after { background: linear-gradient(90deg, #dbeafe, #eff6ff); }
        .td-dash .td-kpi.green::after { background: linear-gradient(90deg, #d1fae5, #ecfdf5); }
        .td-dash .td-kpi.violet::after { background: linear-gradient(90deg, #ede9fe, #f5f3ff); }
        .td-dash .td-kpi.pink::after { background: linear-gradient(90deg, #fae8ff, #fdf4ff); }
        .td-dash .td-kpi-icon { width: 40px; height: 40px; border-radius: 10px; display: grid; place-items: center; font-size: 22px; flex: none; }
        .td-dash .blue .td-kpi-icon { background: #e0ecff; color: var(--td-blue); }
        .td-dash .green .td-kpi-icon { background: #dcfce7; color: #16a34a; }
        .td-dash .violet .td-kpi-icon { background: #ede9fe; color: var(--td-violet); }
        .td-dash .pink .td-kpi-icon { background: #fae8ff; color: #c026d3; }
        .td-dash .td-kpi-label { font-size: 15px; color: var(--td-ink); font-weight: 500; }
        .td-dash .td-kpi-value { font-size: 22px; font-weight: 700; color: var(--td-ink); white-space: nowrap; }
        .td-dash .td-delta { display: inline-flex; align-items: center; gap: 2px; font-size: 12px; font-weight: 600; border-radius: 6px; padding: 2px 6px; white-space: nowrap; }
        .td-dash .td-delta.up { background: #e0f2fe; color: #0369a1; }
        .td-dash .td-delta.down { background: #fee2e2; color: #b91c1c; }
        .td-dash .td-kpi-sub { position: relative; z-index: 1; font-size: 12px; color: var(--td-muted); }

        .td-dash .td-legend { display: flex; flex-wrap: wrap; gap: 1rem; font-size: 13px; color: var(--td-ink); }
        .td-dash .td-legend i { display: inline-block; width: 14px; height: 14px; border-radius: 3px; vertical-align: -2px; margin-right: 4px; }
        .td-dash .td-legend .line { width: 22px; height: 3px; border-radius: 2px; vertical-align: 3px; }

        .td-dash .td-donut-wrap { position: relative; width: 220px; max-width: 100%; margin: 0 auto; }
        .td-dash .td-donut-center { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none; }
        .td-dash .td-donut-center b { font-size: 28px; color: var(--td-ink); line-height: 1; }
        .td-dash .td-donut-center small { color: var(--td-muted); }
        .td-dash .td-status-item { display: flex; gap: .6rem; margin-bottom: .9rem; }
        .td-dash .td-status-item i { width: 14px; height: 14px; border-radius: 3px; margin-top: 3px; flex: none; }

        .td-dash .td-table { width: 100%; }
        .td-dash .td-table th { background: #f1f4fa; font-weight: 600; color: var(--td-ink); padding: .6rem .75rem; white-space: nowrap; }
        .td-dash .td-table th:first-child { border-radius: 8px 0 0 8px; }
        .td-dash .td-table th:last-child { border-radius: 0 8px 8px 0; }
        .td-dash .td-table td { padding: .7rem .75rem; border-bottom: 1px solid var(--td-line); vertical-align: middle; white-space: nowrap; }
        .td-dash .td-company-icon { width: 30px; height: 30px; border-radius: 8px; display: inline-grid; place-items: center; background: #eef2fb; color: #4b5b85; margin-right: .5rem; }
        .td-dash .td-badge { border-radius: 999px; padding: 2px 12px; font-size: 12px; font-weight: 600; }
        .td-dash .td-badge.on { background: #dcfce7; color: #15803d; }
        .td-dash .td-badge.off { background: #f3f4f6; color: #6b7280; }

        .td-dash .td-insight { background: linear-gradient(160deg, #ffffff 0%, #f5f3ff 60%, #ede9fe 100%); }
        .td-dash .td-insight-icon { width: 46px; height: 46px; border-radius: 12px; display: grid; place-items: center; background: #ede9fe; color: var(--td-violet); font-size: 26px; }
        .td-dash .td-insight-main { font-size: 20px; font-weight: 700; color: var(--td-ink); line-height: 1.35; }

        .td-dash .td-filter { font-size: 13px; }
        @media (max-width: 767.98px) { .td-dash .td-products { margin-left: 0; } .td-dash .td-title { font-size: 21px; } }
    </style>
@endpush

@section('content')
    <div class="td-dash">
        {{-- Judul & pilih produk --}}
        <div class="td-head">
            <div class="td-head-icon"><i class="mdi mdi-layers-plus"></i></div>
            <div>
                <h1 class="td-title">Dashboard <span>TomiDana</span></h1>
                <p class="td-subtitle">Ringkasan Kinerja Produk · {{ $periodLabel }}</p>
            </div>
            <div class="td-products">
                <span class="mr-1">Pilih Produk:</span>
                @foreach ($products as $product)
                    <a href="{{ route($product['route']) }}" class="td-pill {{ $product['active'] ? 'active' : '' }}">{{ $product['name'] }}</a>
                @endforeach
            </div>
        </div>

        {{-- Filter periode --}}
        <form method="GET" class="form-inline td-filter mb-3" style="gap: .5rem;">
            <label for="periode" class="mr-1">PERIODE</label>
            <select class="form-control form-control-sm" id="periode" name="periode" onchange="togglePeriode()">
                <option value="bulan" @selected($filter['periode'] === 'bulan')>BULAN</option>
                <option value="tahun" @selected($filter['periode'] === 'tahun')>TAHUN</option>
                <option value="semua" @selected($filter['periode'] === 'semua')>SEMUA WAKTU</option>
            </select>
            <input class="form-control form-control-sm" type="month" id="inputBulan" name="bulan" value="{{ $filter['bulan'] }}">
            <input class="form-control form-control-sm" type="number" id="inputTahun" name="tahun" min="2020" max="2100" value="{{ $filter['tahun'] }}" style="width: 100px;">
            <button class="btn btn-sm btn-primary" type="submit">TAMPILKAN</button>
        </form>

        {{-- KPI --}}
        <div class="row">
            @foreach ($kpis as $kpi)
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="td-kpi {{ $kpi['tone'] }}">
                        <div class="d-flex align-items-center mb-3" style="gap: .75rem;">
                            <div class="td-kpi-icon"><i class="mdi {{ $kpi['icon'] }}"></i></div>
                            <div class="td-kpi-label">{{ $kpi['label'] }}</div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center" style="gap: .5rem;">
                            <div class="td-kpi-value">{{ $rupiah($kpi['value']) }}</div>
                            @if ($kpi['delta'])
                                <span class="td-delta {{ $kpi['delta']['up'] ? 'up' : 'down' }}">
                                    <i class="mdi {{ $kpi['delta']['up'] ? 'mdi-arrow-up' : 'mdi-arrow-down' }}"></i>{{ $kpi['delta']['up'] ? '+' : '-' }}{{ $kpi['delta']['text'] }}
                                </span>
                            @endif
                        </div>
                        <div class="td-kpi-sub mt-1">
                            {{ $kpi['sub'] }}@if ($kpi['delta']) · vs. {{ $prevLabel }}@endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tren bulanan & status pengajuan --}}
        <div class="row">
            <div class="col-xl-8 mb-3">
                <div class="td-panel">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3" style="gap: .5rem;">
                        <h2 class="td-panel-title">Tren Bulanan</h2>
                        <div class="td-legend">
                            <span><i style="background: #2f7cf6;"></i>Pencairan</span>
                            <span><i class="line" style="background: #7c3aed;"></i>Pendapatan TOMI</span>
                        </div>
                    </div>
                    <div style="position: relative; height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 mb-3">
                <div class="td-panel">
                    <h2 class="td-panel-title mb-3">Status Pengajuan</h2>
                    @if ($status['total'] > 0)
                        <div class="row align-items-center">
                            <div class="col-sm-7 col-xl-12 mb-3">
                                <div class="td-donut-wrap">
                                    <canvas id="statusChart" width="220" height="220"></canvas>
                                    <div class="td-donut-center">
                                        <b>{{ $num($status['total']) }}</b>
                                        <small>Pengajuan</small>
                                        <small>Total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 col-xl-12">
                                @foreach ($status['items'] as $item)
                                    <div class="td-status-item">
                                        <i style="background: {{ $item['color'] }};"></i>
                                        <div>
                                            <b>{{ $item['label'] }}</b><br>
                                            <span class="text-muted">{{ $num($item['value']) }} ({{ number_format($item['percent'], 1, ',', '.') }}%)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Belum ada pengajuan pada periode ini.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Perusahaan mitra & insight --}}
        <div class="row">
            <div class="col-xl-8 mb-3">
                <div class="td-panel">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="td-panel-title">Perusahaan Mitra</h2>
                        <a href="{{ route('internal.tomi-dana.companies') }}" class="small">Lihat semua <i class="mdi mdi-arrow-right"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="td-table">
                            <thead>
                                <tr>
                                    <th>PERUSAHAAN</th>
                                    <th class="text-right">PENGGUNA</th>
                                    <th class="text-right">NOMINAL PENCAIRAN</th>
                                    <th class="text-center">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($companies as $company)
                                    <tr>
                                        <td>
                                            <a href="{{ route('internal.tomi-dana.applications', $company['id']) }}" class="text-reset">
                                                <span class="td-company-icon"><i class="mdi mdi-office-building-outline"></i></span>{{ $company['name'] }}
                                            </a>
                                        </td>
                                        <td class="text-right">{{ $num($company['employees']) }}</td>
                                        <td class="text-right">{{ $rupiah($company['nominal']) }}</td>
                                        <td class="text-center"><span class="td-badge {{ $company['active'] ? 'on' : 'off' }}">{{ $company['active'] ? 'AKTIF' : 'TIDAK AKTIF' }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">Belum ada perusahaan ber-PKS TomiDana.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 mb-3">
                <div class="td-panel td-insight">
                    <div class="d-flex align-items-center mb-3" style="gap: .75rem;">
                        <div class="td-insight-icon"><i class="mdi mdi-trending-up"></i></div>
                        <h2 class="td-panel-title">Insight</h2>
                    </div>
                    <p class="td-insight-main">{{ $insights[0] ?? '-' }}</p>
                    @foreach (array_slice($insights, 1) as $line)
                        <p class="text-muted mb-2">{{ $line }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/libs/chart-js/Chart.bundle.min.js') }}"></script>
    <script>
        function togglePeriode() {
            const periode = document.getElementById('periode').value;
            document.getElementById('inputBulan').style.display = periode === 'bulan' ? '' : 'none';
            document.getElementById('inputTahun').style.display = periode === 'tahun' ? '' : 'none';
        }
        togglePeriode();

        const rupiah = (value) => 'Rp. ' + Math.round(value).toLocaleString('id-ID');
        // Label sumbu dipersingkat (Jt / M) agar muat; tooltip tetap angka lengkap.
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
                    {
                        type: 'line',
                        label: 'Pendapatan TOMI',
                        data: trend.revenue,
                        yAxisID: 'revenue',
                        borderColor: '#7c3aed',
                        backgroundColor: '#ffffff',
                        pointBorderColor: '#7c3aed',
                        pointBackgroundColor: '#ffffff',
                        pointRadius: 5,
                        pointBorderWidth: 2,
                        borderWidth: 2.5,
                        fill: false,
                        lineTension: 0,
                    },
                    {
                        type: 'bar',
                        label: 'Pencairan',
                        data: trend.disbursed,
                        yAxisID: 'disbursed',
                        backgroundColor: '#3b8bf7',
                        hoverBackgroundColor: '#2f7cf6',
                    },
                ],
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: { mode: 'index', intersect: false, callbacks: { label: (item, data) => data.datasets[item.datasetIndex].label + ': ' + rupiah(item.yLabel) } },
                scales: {
                    xAxes: [{ gridLines: { display: false }, barPercentage: 0.7, categoryPercentage: 0.8 }],
                    yAxes: [
                        { id: 'disbursed', position: 'left', ticks: { beginAtZero: true, callback: shortRupiah }, scaleLabel: { display: true, labelString: 'Pencairan (Rp)' }, gridLines: { color: '#eef1f6' } },
                        { id: 'revenue', position: 'right', ticks: { beginAtZero: true, fontColor: '#7c3aed', callback: shortRupiah }, scaleLabel: { display: true, labelString: 'Pendapatan TOMI (Rp)', fontColor: '#7c3aed' }, gridLines: { display: false } },
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
                    datasets: [{ data: status.items.map(item => item.value), backgroundColor: status.items.map(item => item.color), borderWidth: 0 }],
                },
                options: {
                    cutoutPercentage: 58,
                    legend: { display: false },
                    tooltips: { callbacks: { label: (item, data) => data.labels[item.index] + ': ' + data.datasets[0].data[item.index].toLocaleString('id-ID') } },
                },
            });
        }
    </script>
@endpush
