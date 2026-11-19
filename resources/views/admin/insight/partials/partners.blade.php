{{-- Perusahaan mitra: terdaftar, baru, berhenti, bertransaksi per periode. Definisi: App\Services\Insight\CompanyInsight. --}}
<section class="panel fade-in overflow-hidden">
    <div class="kpi-strip [--cols:1] sm:[--cols:2] xl:[--cols:4]" role="group" aria-label="Saring tabel perusahaan mitra">
        <template x-for="k in d?.partners?.kpis || []" :key="k.metric">
            <button type="button" class="kpi-cell kpi-click flex flex-col justify-between gap-3 text-left" :aria-pressed="partnerTab === k.metric"
                    :style="partnerTab === k.metric ? 'background: var(--surface-2)' : ''" @click="partnerTab = k.metric">
                <div>
                    <div class="flex items-center gap-2"><span class="kpi-label" x-text="k.label"></span><span class="more" aria-hidden="true">Lihat daftar<svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></span></div>
                    <div class="figure-lg mt-1.5" x-count:num="k.value"></div>
                </div>
                <div class="flex items-end justify-between gap-3">
                    <div class="min-w-0">
                        <div class="delta" :class="countDelta(k).cls"><span x-text="countDelta(k).text"></span> <small x-text="countDelta(k).sub"></small></div>
                        <div class="kpi-hint mt-0.5" x-text="k.hint"></div>
                    </div>
                    <svg class="spark" viewBox="0 0 88 28" preserveAspectRatio="none" aria-hidden="true" x-spark="k.spark" x-show="k.spark"></svg>
                </div>
            </button>
        </template>
    </div>
</section>

<div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
    <section class="panel panel-pad fade-in">
        <div class="mb-2 flex items-start gap-3">
            <div>
                <div class="panel-title">Pertumbuhan perusahaan mitra</div>
                <div class="panel-sub" x-text="partnerChartSub()"></div>
            </div>
            <button type="button" class="chip ml-auto" :aria-pressed="!!tables.partners" @click="toggleTable('partners')"><svg class="i !h-3.5 !w-3.5"><use href="#i-table"/></svg>Tabel</button>
        </div>
        <div x-ref="partners" class="min-h-[280px]" x-show="!tables.partners"></div>
        <div class="max-h-[300px] overflow-auto" x-show="tables.partners" x-cloak>
            <table class="tbl">
                <thead><tr><th>Periode</th><th class="r" x-show="d?.partners?.trend.registered">Terdaftar</th><th class="r">Baru</th><th class="r">Berhenti</th><th class="r">Bertransaksi</th></tr></thead>
                <tbody>
                    <template x-for="(c, i) in d?.partners?.trend.categories || []" :key="c + i">
                        <tr>
                            <td x-text="c"></td>
                            <td class="r" x-show="d.partners.trend.registered" x-text="fmt.num(d.partners.trend.registered?.[i] ?? 0)"></td>
                            <td class="r" x-text="fmt.num(d.partners.trend.added[i])"></td>
                            <td class="r" x-text="fmt.num(d.partners.trend.removed[i])"></td>
                            <td class="r" x-text="fmt.num(d.partners.trend.transacting[i])"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel panel-pad fade-in">
        <div class="panel-title">Status perusahaan sekarang</div>
        <div class="panel-sub">Perusahaan yang masih terdaftar hari ini, tidak terpengaruh filter periode</div>
        <div class="segbar mt-4" role="img" :aria-label="(d?.partners?.snapshot || []).map(s => s.label + ' ' + s.value).join(', ')">
            <template x-for="s in d?.partners?.snapshot || []" :key="s.label">
                <span :style="`background: var(--${ {good:'good-fill', warn:'warn-fill'}[s.tone] || 'axis' }); flex: ${s.value} 1 0`" x-show="s.value > 0"
                      x-tip="{ title: s.label, value: fmt.num(s.value) + ' perusahaan' }"></span>
            </template>
        </div>
        <ul class="mt-4 space-y-3">
            <template x-for="s in d?.partners?.snapshot || []" :key="s.label">
                <li class="flex items-center gap-2 text-[13px]">
                    <span class="badge" :class="s.tone" x-text="s.label"></span>
                    <span class="num ml-auto font-semibold" x-text="fmt.num(s.value) + ' perusahaan'"></span>
                </li>
            </template>
        </ul>
        <div class="mt-5 border-t border-line pt-4 text-[13px] leading-relaxed text-ink-2" x-show="d?.partners" x-text="partnerSummary()"></div>
        <template x-for="n in d?.partners?.notes || []" :key="n">
            <p class="mt-2 flex gap-2 text-xs leading-relaxed text-muted"><svg class="i mt-px !h-3.5 !w-3.5 flex-none"><use href="#i-info"/></svg><span x-text="n"></span></p>
        </template>
    </section>
</div>

<section class="panel panel-pad fade-in mt-4">
    <div class="flex flex-wrap items-start gap-3">
        <div>
            <div class="panel-title">Daftar perusahaan mitra</div>
            <div class="panel-sub" x-text="partnerTabSub()"></div>
        </div>
        <div class="flex flex-wrap gap-1.5 sm:ml-auto" role="group" aria-label="Saring daftar perusahaan">
            <template x-for="k in d?.partners?.kpis || []" :key="k.metric">
                <button type="button" class="chip" :aria-pressed="partnerTab === k.metric" @click="partnerTab = k.metric">
                    <span x-text="{ registered: 'Terdaftar', added: 'Baru', removed: 'Berhenti', transacting: 'Bertransaksi' }[k.metric]"></span><span class="num text-muted" x-text="fmt.num(partnerRows(k.metric).length)"></span>
                </button>
            </template>
        </div>
    </div>
    <div class="-mx-3 mt-3 overflow-x-auto">
        <table class="tbl min-w-[760px]">
            <thead><tr><th>Perusahaan</th><th>Pemberi dana</th><th>Mulai kerja sama</th><th x-text="partnerTab === 'transacting' ? 'Produk pada periode ini' : 'Produk'"></th><th>Status</th></tr></thead>
            <tbody>
                <template x-for="r in partnerRows(partnerTab)" :key="r.name">
                    <tr>
                        <td><span class="font-semibold" x-text="r.name"></span><div class="text-xs text-muted" x-text="r.source"></div></td>
                        <td class="text-ink-2" x-text="r.lender"></td>
                        <td><span class="whitespace-nowrap" x-text="fmtDate(r.start)"></span><div class="max-w-[260px] text-xs text-muted" x-text="r.start_note" x-show="r.start_note"></div></td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="p in (partnerTab === 'transacting' ? r.period_products : r.products)" :key="p"><span class="badge info" x-text="p"></span></template>
                                <span class="text-xs text-muted" x-show="!(partnerTab === 'transacting' ? r.period_products : r.products).length">Belum ada penyaluran</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap"><span class="badge" :class="r.status.tone" x-text="r.status.label"></span><div class="text-xs text-muted" x-show="r.ended" x-text="r.ended ? 'Dihapus ' + fmtDate(r.ended) : ''"></div></td>
                    </tr>
                </template>
                <tr x-show="!partnerRows(partnerTab).length"><td colspan="5" class="py-8 text-center text-muted" x-text="partnerEmpty()"></td></tr>
            </tbody>
        </table>
    </div>
</section>
