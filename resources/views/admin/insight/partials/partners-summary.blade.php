{{--
    Ringkasan perusahaan mitra untuk halaman lain di luar dasbor (mis. menu Perusahaan): filter periode + kotak angka + grafik.
    Butuh $insight = InsightService::pageContext('partners'). Angka sama persis dengan blok "Perusahaan mitra" di Dasbor utama.
    Halaman lama memakai backoffice-legacy.css: partial ini & yang di-include terdaftar sebagai @source di sana.
--}}
<div class="mb-4" x-data="insight(@js($insight['cfg']))">
    @include('admin.insight.partials.filters', $insight)
    <div class="mt-4 rounded-lg border px-4 py-3 text-sm" style="border-color: var(--crit-bg); background: var(--crit-bg); color: var(--crit)" x-show="error" x-cloak x-text="error"></div>
    <div class="mt-4 transition-opacity duration-200" :class="loading && res ? 'opacity-50' : ''" x-show="d" x-cloak>
        @include('admin.insight.partials.partners', ['compact' => true])
    </div>
    <div class="panel mt-4 h-40 animate-pulse" x-show="!d && !error"></div>
</div>
