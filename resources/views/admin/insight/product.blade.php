@extends('layout.shell')
@section('title', 'Dasbor ' . $product['name'])
@section('crumbs', 'Produk / ' . $product['name'])
@section('page-title', 'Dasbor ' . $product['name'])

@section('content')
<div x-data="insight(@js($cfg))">
    @include('admin.insight.partials.filters')

    <div class="mt-4 rounded-lg border px-4 py-3 text-sm" style="border-color: var(--crit-bg); background: var(--crit-bg); color: var(--crit)" x-show="error" x-cloak x-text="error"></div>

    <div class="transition-opacity duration-200" :class="loading && res ? 'opacity-50' : ''" x-show="d" x-cloak>
        {{-- ===================== 1. Ringkasan ===================== --}}
        <div class="mb-3 mt-7 flex flex-wrap items-baseline gap-x-3">
            <h2 class="section-title">Ringkasan</h2>
            <p class="section-sub">{{ $product['desc'] }}</p>
        </div>
        <section class="panel fade-in overflow-hidden">
            <div class="kpi-strip [--cols:1] sm:[--cols:2] xl:[--cols:4]">
                <template x-for="k in d?.kpis || []" :key="k.label">
                    <div class="kpi-cell kpi-click flex flex-col justify-between gap-3" role="button" tabindex="0" aria-haspopup="dialog" :aria-label="'Lihat rincian ' + k.label"
                         @click="openDetail(k, $el)" @keydown.enter.prevent="openDetail(k, $el)" @keydown.space.prevent="openDetail(k, $el)">
                        <div>
                            <div class="flex items-center gap-2"><span class="kpi-label" x-text="k.label"></span><span class="more" aria-hidden="true">Lihat rincian<svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></span></div>
                            <div class="figure-lg mt-1.5" x-count:rp="k.format === 'rp' ? k.value : null" x-show="k.format === 'rp'"></div>
                            <div class="figure-lg mt-1.5" x-count:num="k.format === 'num' ? k.value : null" x-show="k.format === 'num'"></div>
                            <div class="figure-lg mt-1.5" x-count:pct="k.format === 'pct' ? k.value : null" x-show="k.format === 'pct'"></div>
                        </div>
                        <div class="flex items-end justify-between gap-3">
                            <div class="min-w-0">
                                <div class="delta" :class="delta(k.value, k.prev, k.up_is_good).cls"><span x-text="delta(k.value, k.prev, k.up_is_good).text"></span> <small x-text="delta(k.value, k.prev, k.up_is_good).sub"></small></div>
                                <div class="kpi-hint mt-0.5" x-text="k.hint" x-show="k.hint"></div>
                            </div>
                            <svg class="spark" viewBox="0 0 88 28" preserveAspectRatio="none" aria-hidden="true" x-spark="k.spark" x-show="k.spark"></svg>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        {{-- ===================== 2. Tren & alur ===================== --}}
        <div class="mb-3 mt-8 flex flex-wrap items-baseline gap-x-3">
            <h2 class="section-title">Tren &amp; alur</h2>
            <p class="section-sub">Arus dana dan perjalanan pengajuan</p>
        </div>
        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
            <section class="panel panel-pad fade-in" style="--i:1">
                <div class="mb-2 flex items-start gap-3">
                    <div>
                        <div class="panel-title" x-text="(d?.trend.series || []).map(s => s.name).join(' vs ')"></div>
                        <div class="panel-sub" x-text="'Per ' + ({month:'bulan', day:'hari', hour:'jam'}[res?.period.granularity] || 'bulan') + partialNote()"></div>
                    </div>
                    <button type="button" class="chip ml-auto" :aria-pressed="!!tables.trend" @click="toggleTable('trend')"><svg class="i !h-3.5 !w-3.5"><use href="#i-table"/></svg>Tabel</button>
                </div>
                <div x-ref="trend" class="min-h-[300px]" x-show="!tables.trend"></div>
                <div class="max-h-[320px] overflow-auto" x-show="tables.trend" x-cloak>
                    <table class="tbl">
                        <thead><tr><th>Periode</th><template x-for="s in d?.trend.series || []" :key="s.name"><th class="r" x-text="s.name"></th></template><th class="r">NOA penyaluran</th><th class="r" x-show="d?.trend.noa_out">NOA outstanding</th><th class="r" x-show="d?.trend.outstanding">Sisa berjalan</th></tr></thead>
                        <tbody>
                            <template x-for="(c, i) in d?.trend.categories || []" :key="c + i">
                                <tr><td x-text="c"></td><template x-for="s in d.trend.series" :key="s.name"><td class="r" x-text="fmt.rp(s.data[i])"></td></template><td class="r" x-text="fmt.num(d.trend.noa?.[i] ?? 0)"></td><td class="r" x-show="d.trend.noa_out" x-text="fmt.num(d.trend.noa_out?.[i] ?? 0)"></td><td class="r" x-show="d.trend.outstanding" x-text="fmt.rp(d.trend.outstanding?.[i] ?? 0)"></td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel panel-pad fade-in" style="--i:2">
                <div class="panel-title" x-text="d?.funnel.title"></div>
                <div class="panel-sub">Jumlah pada periode ini dan persentase yang lanjut ke tahap berikutnya · klik tahap untuk melihat pengajuannya</div>
                <div class="mt-4 space-y-2.5">
                    <template x-for="(s, i) in d?.funnel.stages || []" :key="s.name">
                        <div class="click-row -mx-2 grid grid-cols-[minmax(0,130px)_minmax(0,1fr)_64px] items-center gap-3 rounded-lg px-2 py-1 row-hover"
                             role="button" tabindex="0" aria-haspopup="dialog" :aria-label="'Lihat pengajuan tahap ' + s.name"
                             @click="openDetail(stageKpi(i), $el)" @keydown.enter.prevent="openDetail(stageKpi(i), $el)" @keydown.space.prevent="openDetail(stageKpi(i), $el)"
                             x-tip="{ title: s.name, value: fmt.num(s.value), sub: i ? fmt.pct(share(s.value, d.funnel.stages[0].value)) + ' dari tahap pertama' : null }">
                            <div class="min-w-0 text-[13px]"><div class="truncate" x-text="s.name"></div><div class="truncate text-xs text-muted" x-text="s.sub"></div></div>
                            <div class="hbar"><span :style="`background: var(--o${i + 1}); width: ${share(s.value, Math.max(1, d.funnel.stages[0].value)) * 100}%`"></span></div>
                            <div class="text-right">
                                <div class="num font-semibold" x-text="fmt.num(s.value)"></div>
                                <div class="text-[11px] text-muted" x-text="i ? (d.funnel.stages[i-1].value ? fmt.pct(s.value / d.funnel.stages[i-1].value, 0) + ' lanjut' : '—') : 'awal'"></div>
                            </div>
                        </div>
                    </template>
                </div>
                <p class="mt-4 flex gap-2 text-xs leading-relaxed text-muted" x-show="d?.funnel.note"><svg class="i mt-px !h-3.5 !w-3.5"><use href="#i-info"/></svg><span x-text="d?.funnel.note"></span></p>
            </section>
        </div>

        {{-- ===================== 3. Pendapatan ===================== --}}
        <div class="mb-3 mt-8 flex flex-wrap items-baseline gap-x-3">
            <h2 class="section-title">Pendapatan</h2>
            <p class="section-sub">Gabungan, bisa dilihat per komponen</p>
        </div>
        @include('admin.insight.partials.revenue')

        {{-- ===================== 4. Rincian ===================== --}}
        <div class="mb-3 mt-8 flex flex-wrap items-baseline gap-x-3">
            <h2 class="section-title">Rincian</h2>
            <p class="section-sub">Siapa yang paling banyak memakai, kondisi pembayaran, dan pekerjaan yang menunggu</p>
        </div>
        <div class="grid gap-4" :class="(d?.health || d?.columns) ? 'xl:grid-cols-2' : ''">
            <section class="panel panel-pad fade-in">
                <div class="panel-title" x-text="d?.breakdown.title"></div>
                <div class="panel-sub" x-text="d?.breakdown.sub + (d?.breakdown.rows?.length ? ' · klik baris untuk rinciannya' : '')"></div>
                <div class="mt-4 space-y-1">
                    <template x-for="(r, ri) in d?.breakdown.rows || []" :key="ri + r.name + r.sub">
                        <div class="click-row row-hover -mx-2 grid grid-cols-[minmax(0,1.1fr)_minmax(0,1.6fr)_auto] items-center gap-3 rounded-lg px-2 py-1.5 text-[13px]"
                             role="button" tabindex="0" aria-haspopup="dialog" :aria-label="'Lihat rincian ' + r.name + ' ' + r.sub"
                             @click="openDetail(rowKpi(r.name, r.value, r.filters, d.breakdown.title + ' · ' + r.sub), $el)"
                             @keydown.enter.prevent="openDetail(rowKpi(r.name, r.value, r.filters, d.breakdown.title + ' · ' + r.sub), $el)"
                             @keydown.space.prevent="openDetail(rowKpi(r.name, r.value, r.filters, d.breakdown.title + ' · ' + r.sub), $el)"
                             x-tip="{ title: r.name, value: fmt.rp(r.value, false), sub: r.sub }">
                            <div class="min-w-0"><div class="truncate" x-text="r.name"></div><div class="truncate text-xs text-muted" x-text="r.sub"></div></div>
                            <div class="hbar"><span :style="`background: var(--s{{ $product['slot'] }}); width: ${Math.max(1, share(r.value, max(d.breakdown.rows, 'value')) * 100)}%`"></span></div>
                            <div class="num min-w-[84px] text-right font-semibold" x-text="fmt.rp(r.value)"></div>
                        </div>
                    </template>
                    <p class="py-6 text-center text-sm text-muted" x-show="!(d?.breakdown.rows || []).length">Belum ada data pada periode ini</p>
                </div>
            </section>

            <div class="grid content-start gap-4" x-show="d?.health || d?.columns">
                @include('admin.insight.partials.health')
                <section class="panel panel-pad fade-in" x-show="d?.columns">
                    <div class="mb-1 flex items-start gap-3">
                        <div><div class="panel-title" x-text="d?.columns?.title"></div><div class="panel-sub" x-text="d?.columns?.sub"></div></div>
                        <button type="button" class="chip ml-auto" :aria-pressed="!!tables.columns" @click="toggleTable('columns')"><svg class="i !h-3.5 !w-3.5"><use href="#i-table"/></svg>Tabel</button>
                    </div>
                    <div x-ref="columns" class="min-h-[220px]" x-show="!tables.columns"></div>
                    <table class="tbl mt-2" x-show="tables.columns" x-cloak>
                        <tbody><template x-for="(c, i) in d?.columns?.categories || []" :key="c"><tr><td x-text="c"></td><td class="r" x-text="fmt.num(d.columns.data[i]) + ' ' + d.columns.unit"></td></tr></template></tbody>
                    </table>
                </section>
            </div>
        </div>

        <div class="mt-4 grid gap-4 xl:grid-cols-2">
            <template x-for="t in d?.tables || []" :key="t.title">
                <section class="panel panel-pad fade-in">
                    <div class="flex items-start gap-3">
                        <div><div class="panel-title" x-text="t.title"></div><div class="panel-sub" x-text="t.sub"></div></div>
                        <span class="badge info ml-auto" x-show="t.badge" x-text="t.badge"></span>
                        <a :href="t.link" class="chip" :class="t.badge ? '' : 'ml-auto'" x-show="t.link">Buka<svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></a>
                    </div>
                    <div class="-mx-3 mt-3 overflow-x-auto">
                        <table class="tbl">
                            <thead><tr><template x-for="(h, i) in t.columns" :key="h"><th :class="t.num.includes(i) && 'r'" x-text="h"></th></template></tr></thead>
                            <tbody>
                                <template x-for="(row, ri) in t.rows" :key="ri">
                                    <tr>
                                        <template x-for="(cell, ci) in row" :key="ci">
                                            <td :class="t.num.includes(ci) && 'r'">
                                                <template x-if="cell.badge"><span class="badge" :class="cell.badge.tone"><svg class="i"><use :href="'#i-' + cell.badge.icon"/></svg><span x-text="cell.badge.label"></span></span></template>
                                                <template x-if="cell.money !== undefined"><span x-text="fmt.rp(cell.money, false)"></span></template>
                                                <template x-if="cell.text !== undefined"><div><span :class="cell.strong && 'font-semibold'" x-text="cell.text"></span><div class="text-xs text-muted" x-show="cell.sub" x-text="cell.sub"></div></div></template>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                                <tr x-show="!t.rows.length"><td :colspan="t.columns.length" class="py-8 text-center text-muted" x-text="t.empty"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </template>
        </div>

        <p class="mt-8 text-center text-xs text-muted">Definisi angka masih sementara dan perlu dikonfirmasi tim bisnis.</p>
    </div>

    @include('admin.insight.partials.detail')

    <div class="mt-7 grid gap-4" x-show="!d && !error">
        <div class="panel h-40 animate-pulse"></div><div class="panel h-72 animate-pulse"></div>
    </div>
</div>
@endsection
