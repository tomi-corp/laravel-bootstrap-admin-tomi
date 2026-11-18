{{-- Kualitas portofolio TomiDana: status selalu ikon + teks, warna hanya pendukung. --}}
<section class="panel panel-pad fade-in" x-show="d?.health" x-cloak>
    <div class="panel-title">Kualitas portofolio</div>
    <div class="panel-sub" x-text="d?.health ? 'Sisa cicilan TomiDana ' + fmt.rp(d.health.total) + ' per ' + d.health.as_of : ''"></div>
    <div class="segbar mt-4" role="img" :aria-label="(d?.health?.parts || []).map(p => p.label + ' ' + fmt.pct(share(p.value, d.health.total))).join(', ')">
        <template x-for="p in d?.health?.parts || []" :key="p.tone">
            <span :style="`background: var(--${p.tone}-fill); flex: ${p.value} 1 0`" x-show="p.value > 0" x-tip="{ title: p.label, value: fmt.rp(p.value), sub: fmt.pct(share(p.value, d.health.total)) }"></span>
        </template>
    </div>
    <ul class="mt-4 space-y-3">
        <template x-for="p in d?.health?.parts || []" :key="p.tone">
            <li class="flex items-center gap-2 text-[13px]">
                <span class="badge" :class="p.tone"><svg class="i"><use :href="'#i-' + {good:'check', warn:'clock', crit:'alert'}[p.tone]"/></svg><span x-text="p.label"></span></span>
                <span class="num ml-auto font-semibold" x-text="fmt.rp(p.value)"></span>
                <span class="num w-14 text-right text-xs text-muted" x-text="fmt.pct(share(p.value, d.health.total))"></span>
            </li>
        </template>
    </ul>
    <p class="mt-3 text-xs text-muted" x-show="d?.health && !d.health.total">Tidak ada cicilan berjalan pada tanggal ini.</p>
</section>
