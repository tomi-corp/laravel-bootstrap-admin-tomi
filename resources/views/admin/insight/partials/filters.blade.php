{{-- Baris filter: satu baris di atas semua isi dasbor; semua angka di bawahnya mengikuti filter ini. --}}
<div class="flex flex-wrap items-center gap-2.5" role="group" aria-label="Filter laporan">
    <div class="seg" x-ref="seg" role="group" aria-label="Periode laporan">
        <span class="thumb" x-ref="thumb" aria-hidden="true"></span>
        <template x-for="m in modes" :key="m.key">
            <button type="button" :data-mode="m.key" :aria-pressed="mode === m.key" @click="setMode(m.key)" x-text="m.label"></button>
        </template>
    </div>

    <label class="field" x-show="mode === 'year'">
        <svg class="i !h-4 !w-4"><use href="#i-calendar"/></svg><span class="sr-only">Tahun</span>
        <select x-model.number="year" @change="load()">
            <template x-for="y in years" :key="y"><option :value="y" x-text="y" :selected="y === year"></option></template>
        </select>
    </label>
    <label class="field" x-show="mode === 'month'" x-cloak>
        <svg class="i !h-4 !w-4"><use href="#i-calendar"/></svg><span class="sr-only">Bulan</span>
        <input type="month" x-model="month" :min="minMonth" :max="maxMonth" @change="month && load()">
    </label>
    <label class="field" x-show="mode === 'day'" x-cloak>
        <svg class="i !h-4 !w-4"><use href="#i-calendar"/></svg><span class="sr-only">Tanggal</span>
        <input type="date" x-model="day" :min="minDate" :max="maxDate" @change="day && load()">
    </label>
    <div class="flex items-center gap-2" x-show="mode === 'custom'" x-cloak>
        <label class="field"><span class="sr-only">Dari</span><input type="date" x-model="from" :min="minDate" :max="maxDate" @change="from && to && load()"></label>
        <span class="text-muted">–</span>
        <label class="field"><span class="sr-only">Sampai</span><input type="date" x-model="to" :min="minDate" :max="maxDate" @change="from && to && load()"></label>
    </div>

    @if ($canSwitchLender)
        <label class="field">
            <svg class="i !h-4 !w-4"><use href="#i-bank"/></svg><span class="sr-only">Pemberi dana</span>
            <select x-model="lender" @change="load()">
                <option value="all">Semua pemberi dana</option>
                @foreach ($lenders as $l)
                    <option value="{{ $l->id }}">{{ $l->nama }}</option>
                @endforeach
            </select>
        </label>
    @else
        <span class="field"><svg class="i !h-4 !w-4"><use href="#i-bank"/></svg>{{ $lockedLenderName }}</span>
    @endif

    <div class="w-full text-left text-xs text-muted xl:ml-auto xl:w-auto xl:text-right" x-show="res" x-cloak>
        <span class="block text-[13px] font-semibold text-ink" x-text="res?.period.label"></span>
        <span x-text="rangeText()"></span>
    </div>
</div>
