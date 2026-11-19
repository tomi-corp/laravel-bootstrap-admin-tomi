@extends('layout.shell')
@section('title', 'Dasbor utama')
@section('crumbs', 'Dasbor')
@section('page-title', 'Dasbor utama')

@section('content')
<div x-data="insight(@js($cfg))">
    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.dashboard.export-report') }}" class="chip" aria-label="Export report dashboard">
            Export Report
        </a>
    </div>

    @include('admin.insight.partials.filters')

    <div class="mt-4 rounded-lg border px-4 py-3 text-sm" style="border-color: var(--crit-bg); background: var(--crit-bg); color: var(--crit)" x-show="error" x-cloak x-text="error"></div>

    <div class="transition-opacity duration-200" :class="loading && res ? 'opacity-50' : ''" x-show="d" x-cloak>
        {{-- ===================== 1. Ringkasan ===================== --}}
        <div class="mb-3 mt-7 flex flex-wrap items-baseline gap-x-3">
            <h2 class="section-title">Ringkasan</h2>
            <p class="section-sub">Semua produk sesuai filter</p>
        </div>
        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,2fr)]">
            <section class="panel panel-pad fade-in kpi-click flex flex-col" role="button" tabindex="0" aria-haspopup="dialog" aria-label="Lihat rincian Total dana tersalurkan"
                     @click="openDetail(findKpi('disbursed'), $el)" @keydown.enter.prevent="openDetail(findKpi('disbursed'), $el)" @keydown.space.prevent="openDetail(findKpi('disbursed'), $el)">
                <div class="flex items-center gap-2 text-[13px] text-ink-2">Total dana tersalurkan <span class="more" aria-hidden="true">Lihat rincian<svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></span></div>
                <div class="figure-xl mt-2" x-count:rp="d?.hero.value"></div>
                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[13px]">
                    <template x-if="d"><span class="delta" :class="delta(d.hero.value, d.hero.prev).cls"><span x-text="delta(d.hero.value, d.hero.prev).text"></span> <small x-text="delta(d.hero.value, d.hero.prev).sub"></small></span></template>
                    <span class="text-muted" x-text="d ? 'NOA ' + fmt.num(d.hero.count) + (d.hero.avg ? ' · rata-rata ' + fmt.rp(d.hero.avg) + ' per akun' : '') : ''"></span>
                </div>
                <svg class="spark mt-5 !h-14 !w-full" viewBox="0 0 300 56" preserveAspectRatio="none" aria-hidden="true" x-spark="d?.hero.series"></svg>
                <p class="mt-4 border-t border-line pt-3 text-[13px] leading-relaxed text-ink-2" x-text="d?.hero.summary"></p>
            </section>

            <section class="panel fade-in overflow-hidden" style="--i:1">
                <div class="kpi-strip h-full [--cols:1] sm:[--cols:2]">
                    <template x-for="(k, i) in d?.kpis || []" :key="k.label">
                        <div class="kpi-cell kpi-click flex flex-col justify-between gap-3" role="button" tabindex="0" aria-haspopup="dialog" :aria-label="'Lihat rincian ' + k.label"
                             @click="openDetail(k, $el)" @keydown.enter.prevent="openDetail(k, $el)" @keydown.space.prevent="openDetail(k, $el)">
                            <div>
                                <div class="flex items-center gap-2"><span class="kpi-label" x-text="k.label"></span><span class="more" aria-hidden="true">Lihat rincian<svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></span></div>
                                <div class="figure-lg mt-1.5" x-count:rp="k.format === 'rp' ? k.value : null" x-show="k.format === 'rp'"></div>
                                <div class="figure-lg mt-1.5" x-count:num="k.format === 'num' ? k.value : null" x-show="k.format === 'num'"></div>
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
        </div>

        {{-- ===================== 2. Per produk ===================== --}}
        <div class="mb-3 mt-8 flex flex-wrap items-baseline gap-x-3">
            <h2 class="section-title">Penyaluran per produk</h2>
            <p class="section-sub">Klik produk atau batangnya untuk membuka dasbor produk</p>
        </div>
        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)]">
            <section class="panel panel-pad fade-in" style="--i:2">
                <div class="mb-2 flex items-start gap-3">
                    <div>
                        <div class="panel-title">Tren penyaluran</div>
                        <div class="panel-sub" x-text="'Dana tersalurkan per ' + ({month:'bulan', day:'hari', hour:'jam'}[res?.period.granularity] || 'bulan') + partialNote()"></div>
                    </div>
                    <button type="button" class="chip ml-auto" :aria-pressed="!!tables.trend" @click="toggleTable('trend')"><svg class="i !h-3.5 !w-3.5"><use href="#i-table"/></svg>Tabel</button>
                </div>
                <div x-ref="trend" class="min-h-[300px]" x-show="!tables.trend"></div>
                <div class="max-h-[320px] overflow-auto" x-show="tables.trend" x-cloak>
                    <table class="tbl">
                        <thead><tr><th>Periode</th><template x-for="s in d?.trend.series || []" :key="s.key"><th class="r" x-text="s.name"></th></template><th class="r">NOA</th></tr></thead>
                        <tbody>
                            <template x-for="(c, i) in d?.trend.categories || []" :key="c + i">
                                <tr><td x-text="c"></td><template x-for="s in d.trend.series" :key="s.key"><td class="r" x-text="fmt.rp(s.data[i])"></td></template><td class="r" x-text="fmt.num(d.trend.noa?.[i] ?? 0)"></td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel panel-pad fade-in" style="--i:3">
                <div class="panel-title">Porsi per produk</div>
                <div class="panel-sub">Dari total dana tersalurkan</div>
                <div class="segbar mt-4" role="img" :aria-label="(d?.products || []).map(p => p.name + ' ' + fmt.pct(share(p.disbursed, d.hero.value), 0)).join(', ')">
                    <template x-for="p in d?.products || []" :key="p.key">
                        <span :style="`background: var(--s${p.slot}); flex: ${share(p.disbursed, d.hero.value)} 1 0`" x-show="p.disbursed > 0"
                              x-tip="{ title: p.name, value: fmt.rp(p.disbursed), sub: fmt.pct(share(p.disbursed, d.hero.value)) + ' dari total' }"></span>
                    </template>
                </div>
                <ul class="mt-4 space-y-2.5">
                    <template x-for="p in d?.products || []" :key="p.key">
                        <li class="flex items-center gap-2.5 text-[13px]">
                            <span class="h-2.5 w-2.5 rounded-[3px]" :style="`background: var(--s${p.slot})`"></span>
                            <span class="text-ink" x-text="p.name"></span>
                            <span class="num ml-auto font-semibold" x-text="fmt.rp(p.disbursed)"></span>
                            <span class="num w-12 text-right text-xs text-muted" x-text="fmt.pct(share(p.disbursed, d.hero.value), 0)"></span>
                        </li>
                    </template>
                </ul>
                <div class="mt-6 border-t border-line pt-4">
                    <div class="mb-3 text-[13px] font-semibold">Per pemberi dana</div>
                    <div class="space-y-3">
                        <template x-for="l in d?.lenders || []" :key="l.name">
                            <div x-tip="{ title: l.name, value: fmt.rp(l.value), sub: fmt.pct(share(l.value, sum(d.lenders, 'value'))) }">
                                <div class="mb-1.5 flex items-baseline gap-2 text-[13px]">
                                    <span x-text="l.name"></span>
                                    <span class="num ml-auto font-semibold" x-text="fmt.rp(l.value)"></span>
                                    <span class="num w-12 text-right text-xs text-muted" x-text="fmt.pct(share(l.value, sum(d.lenders, 'value')), 0)"></span>
                                </div>
                                <div class="track"><span :style="`background: var(--s1); width: ${share(l.value, max(d.lenders, 'value')) * 100}%`"></span></div>
                            </div>
                        </template>
                    </div>
                </div>
            </section>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <template x-for="(p, i) in d?.products || []" :key="p.key">
                <a :href="p.url" class="panel panel-pad fade-in flex flex-col gap-1" :style="`--i:${i + 4}`">
                    <div class="flex items-center gap-2 font-semibold">
                        <span class="h-2.5 w-2.5 rounded-[3px]" :style="`background: var(--s${p.slot})`"></span>
                        <span x-text="p.name"></span>
                        <svg class="i go ml-auto !h-4 !w-4"><use href="#i-arrow"/></svg>
                    </div>
                    <div class="text-xs text-muted" x-text="p.desc"></div>
                    {{-- angka rupiah ditulis lengkap: satu baris sendiri, grafik kecil di bawahnya --}}
                    <div class="mt-3">
                        <div class="figure-lg whitespace-nowrap" x-text="p.disbursed > 0 || p.outstanding > 0 ? fmt.rp(p.disbursed) : 'Belum ada data'"></div>
                        <div class="mt-1 flex items-end justify-between gap-3">
                            <div class="delta" :class="delta(p.disbursed, p.prev).cls"><span x-text="delta(p.disbursed, p.prev).text"></span> <small x-text="delta(p.disbursed, p.prev).sub"></small></div>
                            <svg class="spark" viewBox="0 0 88 28" preserveAspectRatio="none" aria-hidden="true" x-spark="{ data: p.series, color: `var(--s${p.slot})` }"></svg>
                        </div>
                    </div>
                    <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1 border-t border-line pt-3 text-xs">
                        <dt class="text-muted">NOA penyaluran</dt><dd class="num text-right font-semibold text-ink-2" x-text="fmt.num(p.count)"></dd>
                        <dt class="text-muted">NOA outstanding</dt><dd class="num text-right font-semibold text-ink-2" x-text="fmt.num(p.noa_out)"></dd>
                        <dt class="text-muted">Sisa berjalan</dt><dd class="num text-right font-semibold text-ink-2" x-text="fmt.rp(p.outstanding)"></dd>
                    </dl>
                </a>
            </template>
        </div>

        {{-- ===================== 3. Perusahaan mitra ===================== --}}
        <div class="mb-3 mt-8 flex flex-wrap items-baseline gap-x-3">
            <h2 class="section-title">Perusahaan mitra</h2>
            <p class="section-sub">Perusahaan yang bekerja sama di semua produk · klik kotak angka untuk melihat daftarnya</p>
        </div>
        @include('admin.insight.partials.partners')

        {{-- ===================== 4. Pendapatan ===================== --}}
        <div class="mb-3 mt-8 flex flex-wrap items-baseline gap-x-3">
            <h2 class="section-title">Pendapatan</h2>
            <p class="section-sub">Bagian TOMI dari semua produk, bisa dilihat per komponen</p>
        </div>
        @include('admin.insight.partials.revenue')

        {{-- ===================== 5. Kesehatan & detail ===================== --}}
        <div class="mb-3 mt-8 flex flex-wrap items-baseline gap-x-3">
            <h2 class="section-title">Kesehatan &amp; detail</h2>
            <p class="section-sub">Perusahaan mitra, kualitas portofolio TomiDana, aktivitas terbaru</p>
        </div>
        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
            <section class="panel panel-pad fade-in">
                <div class="panel-title">Perusahaan mitra teratas</div>
                <div class="panel-sub">Penyaluran TomiDana &amp; TomiGaji pada periode ini · klik baris untuk rinciannya</div>
                <div class="-mx-3 mt-3 overflow-x-auto">
                    <table class="tbl">
                        <thead><tr><th>Perusahaan</th><th>Pemberi dana</th><th class="r">TomiDana</th><th class="r">TomiGaji</th><th class="w-28"></th></tr></thead>
                        <tbody>
                            <template x-for="c in d?.companies || []" :key="c.name">
                                <tr class="click-row" role="button" tabindex="0" aria-haspopup="dialog" :aria-label="'Lihat rincian ' + c.name"
                                    @click="openDetail(rowKpi(c.name, c.dana + c.gaji, { company: c.name }, 'Perusahaan mitra · TomiDana & TomiGaji', 'tomidana,tomigaji'), $el)"
                                    @keydown.enter.prevent="openDetail(rowKpi(c.name, c.dana + c.gaji, { company: c.name }, 'Perusahaan mitra · TomiDana & TomiGaji', 'tomidana,tomigaji'), $el)"
                                    @keydown.space.prevent="openDetail(rowKpi(c.name, c.dana + c.gaji, { company: c.name }, 'Perusahaan mitra · TomiDana & TomiGaji', 'tomidana,tomigaji'), $el)">
                                    <td><span class="font-semibold" x-text="c.name"></span><div class="text-xs text-muted" x-text="c.dana_count ? c.dana_count + ' NOA TomiDana' : ''"></div></td>
                                    <td class="text-ink-2" x-text="c.lender"></td>
                                    <td class="r" x-text="c.dana ? fmt.rp(c.dana) : '—'"></td>
                                    <td class="r" x-text="c.gaji ? fmt.rp(c.gaji) : '—'"></td>
                                    <td>
                                        <div class="segbar !h-1.5" :style="`width: ${((c.dana + c.gaji) / Math.max(1, ...d.companies.map(x => x.dana + x.gaji))) * 100}%`">
                                            <span style="background: var(--s1)" :style="`flex: ${c.dana} 1 0`" x-show="c.dana"></span>
                                            <span style="background: var(--s2)" :style="`flex: ${c.gaji} 1 0`" x-show="c.gaji"></span>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!(d?.companies || []).length"><td colspan="5" class="py-8 text-center text-muted">Belum ada penyaluran pada periode ini</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="grid content-start gap-4">
                @include('admin.insight.partials.health')
                <section class="panel panel-pad fade-in">
                    <div class="panel-title">Aktivitas terbaru</div>
                    <div class="panel-sub">Semua produk, tidak terpengaruh filter periode</div>
                    <ul class="mt-2">
                        <template x-for="(e, i) in d?.feed || []" :key="i">
                            <li class="flex gap-3 border-b border-line py-2.5 last:border-0">
                                <span class="mt-1.5 h-2 w-2 flex-none rounded-full" :style="`background: var(--${ {good:'good-fill', info:'s1', warn:'warn-fill'}[e.tone] || 's1' })`"></span>
                                <div class="min-w-0">
                                    <div class="text-[13px]"><span x-text="e.title"></span> <span class="text-muted" x-text="'· ' + e.product"></span></div>
                                    <div class="truncate text-xs text-muted" x-text="(e.sub ? e.sub + ' · ' : '') + e.when"></div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </section>
            </div>
        </div>

        <p class="mt-8 text-center text-xs text-muted">Penyaluran, NOA penyaluran, dan pendapatan mengikuti definisi catatan kantor. Sisa, NOA outstanding, dan pembayaran masih definisi sementara.</p>
    </div>

    @include('admin.insight.partials.detail')

    {{-- kerangka saat pertama kali memuat --}}
    <div class="mt-7 grid gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,2fr)]" x-show="!d && !error">
        <div class="panel h-64 animate-pulse"></div><div class="panel h-64 animate-pulse"></div>
    </div>
</div>
@endsection
