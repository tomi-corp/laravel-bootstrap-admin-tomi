{{--
    Panel rincian kartu KPI (geser dari kanan). Dibuka lewat openDetail(); isinya dari /backoffice/insight/detail
    dengan filter yang sama dengan dasbor. Tutup: tombol ×, klik latar, atau Esc.
--}}
<div class="fixed inset-0 z-[60]" x-show="detail.open" x-cloak @keydown.escape.window="detail.open && closeDetail()">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-[1px]" x-show="detail.open" x-transition.opacity.duration.200ms @click="closeDetail()"></div>

    <aside class="absolute inset-y-0 right-0 flex w-full max-w-[920px] flex-col border-l border-line bg-page shadow-2xl"
           role="dialog" aria-modal="true" aria-labelledby="detail-title" x-ref="detailPanel" @keydown.tab="trapFocus($event)"
           x-show="detail.open"
           x-transition:enter="transition duration-300 ease-out-soft" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">

        {{-- kepala --}}
        <header class="flex items-start gap-3 border-b border-line bg-surface px-5 py-4 sm:px-6">
            <div class="min-w-0 flex-1">
                <div class="truncate text-xs text-muted" x-text="detailContext()"></div>
                <h2 id="detail-title" class="mt-0.5 truncate text-lg font-semibold tracking-tight" x-text="detailTitle()"></h2>
            </div>
            <a class="chip" :href="csvUrl()" x-show="detail.res && detail.res.pagination.all > 0" download>
                <svg class="i !h-3.5 !w-3.5"><use href="#i-table"/></svg>Unduh CSV
            </a>
            <button type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-line bg-surface text-ink-2 transition hover:text-ink"
                    x-ref="detailClose" @click="closeDetail()" aria-label="Tutup rincian">
                <svg class="i" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </header>

        <div class="flex-1 overflow-y-auto px-5 pb-10 pt-5 sm:px-6" x-ref="detailBody">
            {{-- angka utama + definisi --}}
            <section class="panel panel-pad">
                <div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-2">
                    <div>
                        <div class="figure-xl" x-text="detailFigure()"></div>
                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[13px]" x-show="!hasFilters() && detail.kpi?.prev != null">
                            <span class="delta" :class="delta(detail.kpi?.value, detail.kpi?.prev, detail.kpi?.up_is_good ?? true).cls">
                                <span x-text="delta(detail.kpi?.value, detail.kpi?.prev, detail.kpi?.up_is_good ?? true).text"></span>
                                <small x-text="delta(detail.kpi?.value, detail.kpi?.prev, detail.kpi?.up_is_good ?? true).sub"></small>
                            </span>
                            <span class="text-muted" x-show="detail.kpi?.prev != null" x-text="'sebelumnya ' + fmt.value(detail.kpi?.prev, detail.kpi?.format)"></span>
                        </div>
                    </div>
                    <div class="text-right text-[13px] text-ink-2" x-show="detail.res">
                        <div><span class="num font-semibold text-ink" x-text="fmt.num(detail.res?.summary.count)"></span> <span x-text="detail.res?.kind === 'count' || detail.res?.kind === 'rate' ? 'akun' : 'baris data'"></span></div>
                        <div x-show="detail.res?.kind !== 'money' && detail.res?.summary.amount > 0">nominal <span class="num font-semibold text-ink" x-text="fmt.rp(detail.res?.summary.amount)"></span></div>
                        <div x-show="detail.res?.as_of" x-text="'posisi per ' + detail.res?.as_of"></div>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-2" x-show="hasFilters()">
                    <span class="text-xs text-muted">Disaring:</span>
                    <template x-for="[k, v] in Object.entries(detail.filters)" :key="k">
                        <button type="button" class="chip" aria-pressed="true" @click="removeFilter(k)" :aria-label="'Hapus saringan ' + filterLabel(k) + ' ' + v">
                            <span x-text="filterLabel(k) + ': ' + v"></span><span aria-hidden="true" class="text-[14px] leading-none">×</span>
                        </button>
                    </template>
                </div>
                <p class="mt-4 flex gap-2 border-t border-line pt-3 text-[13px] leading-relaxed text-ink-2" x-show="detail.res?.definition">
                    <svg class="i mt-0.5 !h-4 !w-4 text-muted"><use href="#i-info"/></svg>
                    <span><b class="font-semibold text-ink">Cara hitung:</b> <span x-text="detail.res?.definition"></span></span>
                </p>
            </section>

            <div class="mt-4 rounded-lg border px-4 py-3 text-sm" style="border-color: var(--crit-bg); background: var(--crit-bg); color: var(--crit)" x-show="detail.error" x-text="detail.error"></div>

            {{-- tren per bucket (dari deret kartu) --}}
            <section class="panel panel-pad mt-4" x-show="detailBars().length > 1">
                <div class="panel-title" x-text="'Per ' + ({month:'bulan', day:'hari', hour:'jam'}[res?.period.granularity] || 'bulan')"></div>
                <div class="panel-sub" x-text="res?.period.range"></div>
                <div class="mt-4 flex h-28 items-end gap-[3px]" role="img" :aria-label="'Grafik ' + (detail.kpi?.label || '')">
                    <template x-for="(b, i) in detailBars()" :key="i">
                        <div class="flex h-full min-w-0 flex-1 flex-col justify-end"
                             x-tip="{ title: b.label, value: fmt.value(b.value, detail.kpi?.format) }">
                            <span class="block rounded-t-[3px] transition-[height] duration-500" :style="`height: ${b.h}%; min-height: ${b.value > 0 ? 3 : 0}px; background: var(--s1)`"></span>
                        </div>
                    </template>
                </div>
                <div class="mt-1.5 flex justify-between text-[11px] text-muted">
                    <span x-text="detailBars()[0]?.label"></span><span x-text="detailBars().at(-1)?.label"></span>
                </div>
            </section>

            {{-- pecahan --}}
            <div class="mt-4 grid gap-4 md:grid-cols-2" x-show="detail.res?.breakdowns?.length">
                <template x-for="b in detail.res?.breakdowns || []" :key="b.key">
                    <section class="panel panel-pad">
                        <div class="flex items-baseline gap-2"><div class="panel-title" x-text="b.title"></div><span class="ml-auto text-[11px] text-muted" x-show="b.items.length > 1">klik untuk menyaring</span></div>
                        <ul class="mt-3 space-y-1">
                            <template x-for="it in b.items" :key="it.label">
                                <li class="click-row -mx-2 rounded-lg px-2 py-1.5" :class="detail.filters[b.key] === it.label && 'is-on'"
                                    role="button" :aria-pressed="detail.filters[b.key] === it.label" :aria-label="'Saring ' + b.title.toLowerCase() + ': ' + it.label"
                                    @click="toggleFilter(b.key, it.label)" @keydown.enter.prevent="toggleFilter(b.key, it.label)" @keydown.space.prevent="toggleFilter(b.key, it.label)"
                                    x-tip="{ title: it.label, value: breakdownValue(it.value), sub: fmt.num(it.count) + ' akun · ' + fmt.pct(share(it.value, sum(b.items, 'value')), 0) }">
                                    <div class="mb-1 flex items-baseline gap-2 text-[13px]">
                                        <span class="h-2 w-2 shrink-0 rounded-[2px]" :style="`background: ${itemColor(b.key, it)}`"></span>
                                        <span class="truncate" x-text="it.label"></span>
                                        <span class="num ml-auto shrink-0 font-semibold" x-text="breakdownValue(it.value)"></span>
                                        <span class="num w-10 shrink-0 text-right text-xs text-muted" x-text="fmt.pct(share(it.value, sum(b.items, 'value')), 0)"></span>
                                    </div>
                                    <div class="track"><span :style="`width: ${share(it.value, max(b.items, 'value')) * 100}%; background: ${itemColor(b.key, it)}`"></span></div>
                                </li>
                            </template>
                        </ul>
                    </section>
                </template>
            </div>

            {{-- daftar akun / transaksi --}}
            <section class="panel mt-4 overflow-hidden">
                <div class="flex flex-wrap items-center gap-3 border-b border-line px-5 py-3.5">
                    <div>
                        <div class="panel-title">Daftar</div>
                        <div class="panel-sub" x-text="detailCountText()"></div>
                    </div>
                    <label class="field ml-auto w-full sm:w-64">
                        <svg class="i !h-4 !w-4"><use href="#i-search"/></svg>
                        <span class="sr-only">Cari di daftar</span>
                        <input type="search" class="w-full" placeholder="Cari akun, nama, perusahaan..." x-model="detail.q" @input.debounce.350ms="detail.page = 1; loadDetail()">
                    </label>
                </div>
                {{-- Tabel ringkas 2 tingkat (kolom lengkap ada di unduhan CSV) --}}
                <div class="relative overflow-x-auto" :class="detail.loading && 'refetch'">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th :aria-sort="detailSortAria('date')">
                                    <button type="button" class="inline-flex items-center gap-1 hover:text-ink" @click="sortDetail('date')">
                                        <span x-text="col('ref') + ' · ' + col('date').toLowerCase()"></span><span aria-hidden="true" x-text="sortMark('date')"></span>
                                    </button>
                                </th>
                                <th x-show="hasCol('name') || hasCol('company')" x-text="[col('name'), col('company')].filter(Boolean).join(' / ')"></th>
                                <th x-show="hasCol('lender')">Pemberi dana</th>
                                <th x-show="hasCol('status') || hasCol('extra')">Status</th>
                                <th class="r" :aria-sort="detailSortAria('amount')">
                                    <button type="button" class="inline-flex items-center gap-1 hover:text-ink" @click="sortDetail('amount')">
                                        <span x-text="col('amount')"></span><span aria-hidden="true" x-text="sortMark('amount')"></span>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(r, i) in detail.res?.rows || []" :key="i + '-' + r.ref + '-' + r.date">
                                <tr>
                                    <td class="min-w-[170px]">
                                        <div class="whitespace-nowrap font-semibold" x-text="r.ref || '-'"></div>
                                        <div class="mt-0.5 flex items-center gap-1.5 whitespace-nowrap text-xs text-muted">
                                            <template x-if="hasCol('product')"><span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-[2px]" :style="`background: var(--s${r.slot})`"></span><span x-text="r.product"></span> ·</span></template>
                                            <span class="num" x-text="fmtDate(r.date)"></span>
                                        </div>
                                    </td>
                                    <td class="min-w-[160px]" x-show="hasCol('name') || hasCol('company')">
                                        <div x-text="r.name || r.company || '-'"></div>
                                        <div class="mt-0.5 text-xs text-muted" x-show="r.name && r.company" x-text="r.company"></div>
                                    </td>
                                    <td class="text-ink-2" x-show="hasCol('lender')" x-text="r.lender || '-'"></td>
                                    <td class="min-w-[140px]" x-show="hasCol('status') || hasCol('extra')">
                                        <span class="badge" :class="r.status?.tone" x-text="r.status?.label" x-show="r.status"></span>
                                        <div class="mt-1 text-xs text-muted" x-show="r.extra" x-text="r.extra"></div>
                                    </td>
                                    <td class="r whitespace-nowrap font-semibold" x-text="detail.res.kind === 'sum' ? fmt.num(r.amount) : fmt.rp(r.amount, false)"></td>
                                </tr>
                            </template>
                            <tr x-show="detail.res && !detail.res.rows.length">
                                <td colspan="5" class="!py-10 text-center text-muted" x-text="detail.q ? 'Tidak ada yang cocok dengan “' + detail.q + '”' : 'Tidak ada data pada periode ini'"></td>
                            </tr>
                            <tr x-show="!detail.res && detail.loading"><td colspan="5" class="!py-10 text-center text-muted">Memuat rincian...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-wrap items-center gap-2 border-t border-line px-5 py-3 text-[13px]" x-show="detail.res?.pagination.pages > 1">
                    <span class="text-muted" x-text="'Halaman ' + detail.res?.pagination.page + ' dari ' + detail.res?.pagination.pages"></span>
                    <div class="ml-auto flex gap-1.5">
                        <button type="button" class="chip" :disabled="detail.res?.pagination.page <= 1" @click="pageDetail(detail.res.pagination.page - 1)">‹ Sebelumnya</button>
                        <button type="button" class="chip" :disabled="detail.res?.pagination.page >= detail.res?.pagination.pages" @click="pageDetail(detail.res.pagination.page + 1)">Berikutnya ›</button>
                    </div>
                </div>
            </section>

            {{-- ke halaman operasional --}}
            <div class="mt-4 flex flex-wrap gap-2" x-show="detail.res?.links?.length">
                <template x-for="l in detail.res?.links || []" :key="l.url">
                    <a :href="l.url" class="chip"><span x-text="l.label"></span><svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></a>
                </template>
            </div>
        </div>
    </aside>
</div>
