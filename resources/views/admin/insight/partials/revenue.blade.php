{{-- Pendapatan bagian TOMI: gabungan (bertumpuk per komponen) + pilihan melihat satu komponen. Definisi = catatan kantor. --}}
<section class="panel panel-pad fade-in">
    <template x-if="revenueComponents().length">
        <div>
            <div class="flex flex-wrap items-start gap-x-6 gap-y-3">
                <div>
                    <div class="text-[13px] text-ink-2" x-text="'Pendapatan · ' + revenueSelected().label"></div>
                    <div class="figure-lg mt-1" x-count:rp="revenueSelected().total"></div>
                    <div class="delta mt-1" :class="delta(revenueSelected().total, revenueSelected().prev).cls">
                        <span x-text="delta(revenueSelected().total, revenueSelected().prev).text"></span> <small x-text="delta(revenueSelected().total, revenueSelected().prev).sub"></small>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5 sm:ml-auto" role="group" aria-label="Komponen pendapatan">
                    <button type="button" class="chip" :aria-pressed="revenueKey === 'all'" @click="setRevenue('all')">Gabungan</button>
                    <template x-for="(c, i) in revenueComponents()" :key="c.key">
                        <button type="button" class="chip" :aria-pressed="revenueKey === c.key" @click="setRevenue(c.key)">
                            <i :style="`background: var(--s${c.slot})`"></i><span x-text="c.label"></span>
                        </button>
                    </template>
                </div>
            </div>
            <div class="mt-3 grid gap-6 lg:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)]">
                <div x-ref="revenue" class="min-h-[300px]"></div>
                <div>
                    <div class="mb-2 text-[13px] font-semibold">Rincian komponen</div>
                    <table class="tbl">
                        <tbody>
                            <template x-for="(c, i) in revenueComponents()" :key="c.key">
                                <tr class="cursor-pointer" @click="setRevenue(revenueKey === c.key ? 'all' : c.key)" :class="revenueKey !== 'all' && revenueKey !== c.key ? 'opacity-50' : ''">
                                    <td class="!pl-0"><span class="mr-2 inline-block h-2.5 w-2.5 rounded-[3px] align-middle" :style="`background: var(--s${c.slot})`"></span><span x-text="c.label"></span><div class="pl-[18px] text-xs text-muted" x-text="c.product + (c.folded ? ' · digabung ke Lainnya di grafik' : '')"></div></td>
                                    <td class="r font-semibold" x-text="fmt.rp(c.total)"></td>
                                    <td class="r w-14 text-xs text-muted" x-text="fmt.pct(share(c.total, d.revenue.total), 0)"></td>
                                </tr>
                            </template>
                            <tr><td class="!pl-0 font-semibold">Gabungan</td><td class="r font-semibold" x-text="fmt.rp(d.revenue.total)"></td><td></td></tr>
                        </tbody>
                    </table>
                    <p class="mt-3 flex gap-2 text-xs leading-relaxed text-muted">
                        <svg class="i mt-px !h-3.5 !w-3.5"><use href="#i-info"/></svg>
                        <span>Bagian TOMI saja (catatan kantor): TomiDana 1% × nominal + biaya layanan, upload manual = fee dari file Buat Tagihan, TomiGaji 1,5%, Tomi Invoice 1% dari nilai termin. Diakui pada tanggal realisasi.</span>
                    </p>
                    <template x-for="n in d.revenue.notes || []" :key="n"><p class="mt-1 pl-[22px] text-xs text-muted" x-text="n"></p></template>
                </div>
            </div>
        </div>
    </template>
    <template x-if="!revenueComponents().length">
        <div class="py-6 text-center text-sm text-muted">
            <div>Belum ada pendapatan pada periode ini.</div>
            <template x-for="n in d?.revenue.notes || []" :key="n"><div class="mt-1 text-xs" x-text="n"></div></template>
        </div>
    </template>
</section>
