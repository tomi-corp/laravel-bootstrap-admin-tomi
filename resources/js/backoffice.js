/*
 * Backoffice TOMI — kerangka baru: Alpine + ApexCharts untuk dasbor analitik.
 * Angka diambil dari /backoffice/insight/data sesuai filter; grafik digambar ulang saat filter/tema berubah.
 */
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const cssVar = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();

/* ===================== Format angka (Indonesia) ===================== */
const nf = new Intl.NumberFormat('id-ID');
const nf1 = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 });
export const fmt = {
    rp(n, compact = true) {
        n = Number(n) || 0;
        if (!compact) return 'Rp ' + nf.format(Math.round(n));
        const a = Math.abs(n);
        if (a >= 1e12) return 'Rp ' + nf1.format(n / 1e12) + ' T';
        if (a >= 1e9) return 'Rp ' + nf1.format(n / 1e9) + ' M';
        if (a >= 1e6) return 'Rp ' + nf1.format(n / 1e6) + ' jt';
        if (a >= 1e3) return 'Rp ' + nf.format(Math.round(n / 1e3)) + ' rb';
        return 'Rp ' + nf.format(Math.round(n));
    },
    axis(n) {
        const a = Math.abs(n);
        if (a >= 1e9) return nf1.format(n / 1e9) + ' M';
        if (a >= 1e6) return nf.format(Math.round(n / 1e6)) + ' jt';
        if (a >= 1e3) return nf.format(Math.round(n / 1e3)) + ' rb';
        return nf.format(n);
    },
    num: (n) => nf.format(Math.round(Number(n) || 0)),
    pct: (n, d = 1) => (n == null ? '—' : new Intl.NumberFormat('id-ID', { maximumFractionDigits: d }).format(n * 100) + '%'),
    value(v, format) {
        if (v == null) return '—';
        return format === 'rp' ? fmt.rp(v) : format === 'pct' ? fmt.pct(v) : fmt.num(v);
    },
};
window.fmt = fmt;

/* ===================== Tema ===================== */
Alpine.store('theme', {
    dark: document.documentElement.dataset.theme === 'dark',
    toggle() {
        this.dark = !this.dark;
        document.documentElement.dataset.theme = this.dark ? 'dark' : 'light';
        try { localStorage.setItem('tomi-theme', this.dark ? 'dark' : 'light'); } catch (e) { /* storage diblokir */ }
        window.dispatchEvent(new CustomEvent('theme-changed'));
    },
});

/* ===================== Directive kecil ===================== */
// x-count="nilai" x-count:format="rp|num|pct" → angka menghitung naik saat nilainya berubah.
Alpine.directive('count', (el, { expression, value }, { evaluateLater, effect }) => {
    const get = evaluateLater(expression);
    let current = 0;
    effect(() => get((target) => {
        if (target == null || Number.isNaN(Number(target))) { el.textContent = '—'; current = 0; return; }
        const to = Number(target);
        const from = current;
        current = to;
        const render = (v) => { el.textContent = fmt.value(v, value || 'num'); };
        if (REDUCED || from === to) { render(to); return; }
        const t0 = performance.now();
        const dur = 700;
        const step = (now) => {
            const k = Math.min(1, (now - t0) / dur);
            render(from + (to - from) * (1 - Math.pow(1 - k, 3)));
            if (k < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }));
});

// x-spark="[angka]" (+ x-spark:color="var(--s1)") → garis kecil tanpa sumbu.
Alpine.directive('spark', (el, { expression, value }, { evaluateLater, effect }) => {
    const get = evaluateLater(expression);
    // nilai bisa berupa array angka, atau { data: [...], color: 'var(--s2)' }
    effect(() => get((v) => {
        const data = Array.isArray(v) ? v : (v && v.data) || [];
        const color = (v && !Array.isArray(v) && v.color) || (value ? `var(--${value})` : 'var(--brand)');
        drawSpark(el, data, color);
    }));
});

function drawSpark(svg, values, color) {
    const ns = 'http://www.w3.org/2000/svg';
    svg.replaceChildren();
    if (!values.length || values.every((v) => !v)) return;
    const vb = svg.viewBox.baseVal;
    const W = vb && vb.width ? vb.width : 88;
    const H = vb && vb.height ? vb.height : 28;
    const pad = 3;
    const max = Math.max(...values);
    const min = Math.min(0, ...values);
    const pts = values.map((v, i) => [
        values.length === 1 ? W / 2 : pad + (i * (W - 2 * pad)) / (values.length - 1),
        H - pad - ((v - min) / (max - min || 1)) * (H - 2 * pad),
    ]);
    const d = pts.map((p, i) => (i ? 'L' : 'M') + p[0].toFixed(1) + ' ' + p[1].toFixed(1)).join(' ');
    const area = document.createElementNS(ns, 'path');
    area.setAttribute('d', `${d} L ${pts[pts.length - 1][0]} ${H} L ${pts[0][0]} ${H} Z`);
    area.setAttribute('fill', color);
    area.setAttribute('fill-opacity', '0.08');
    const line = document.createElementNS(ns, 'path');
    line.setAttribute('class', 'line');
    line.setAttribute('d', d);
    line.setAttribute('stroke', color);
    line.setAttribute('vector-effect', 'non-scaling-stroke');
    svg.append(area, line);
    if (!REDUCED) {
        const len = line.getTotalLength ? line.getTotalLength() : 400;
        line.style.strokeDasharray = len;
        line.style.strokeDashoffset = len;
        line.getBoundingClientRect();
        line.style.transition = 'stroke-dashoffset .9s cubic-bezier(.2,.8,.2,1)';
        requestAnimationFrame(() => { line.style.strokeDashoffset = 0; });
    }
}

// x-tip="{ title, value, sub }" → tooltip untuk batang/segmen buatan sendiri (hover & fokus keyboard).
Alpine.directive('tip', (el, { expression }, { evaluateLater, cleanup }) => {
    const get = evaluateLater(expression);
    const tip = document.getElementById('tip');
    if (!tip) return;
    if (!el.hasAttribute('tabindex')) el.tabIndex = 0;
    const show = (e) => get((d) => {
        if (!d) return;
        const b = document.createElement('b');
        b.textContent = d.value ?? '';
        const t = document.createElement('div');
        t.textContent = d.title ?? '';
        tip.replaceChildren(b, t);
        if (d.sub) { const s = document.createElement('div'); s.style.opacity = '.7'; s.textContent = d.sub; tip.append(s); }
        tip.classList.add('on');
        const r = el.getBoundingClientRect();
        const x = e && e.clientX ? e.clientX : r.left + r.width / 2;
        const y = e && e.clientY ? e.clientY : r.top;
        tip.style.left = Math.min(window.innerWidth - tip.offsetWidth - 8, Math.max(8, x - tip.offsetWidth / 2)) + 'px';
        tip.style.top = y - tip.offsetHeight - 12 + 'px';
    });
    const hide = () => tip.classList.remove('on');
    el.addEventListener('pointermove', show);
    el.addEventListener('focus', show);
    el.addEventListener('pointerleave', hide);
    el.addEventListener('blur', hide);
    cleanup(() => { el.removeEventListener('pointermove', show); el.removeEventListener('focus', show); el.removeEventListener('pointerleave', hide); el.removeEventListener('blur', hide); });
});

/* ===================== Grafik ===================== */
function baseChart(height) {
    const dark = document.documentElement.dataset.theme === 'dark';
    return {
        chart: {
            height,
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'DM Sans, ui-sans-serif, system-ui, sans-serif',
            background: 'transparent',
            foreColor: cssVar('--muted'),
            animations: { enabled: !REDUCED, easing: 'easeinout', speed: 500, animateGradually: { enabled: true, delay: 30 }, dynamicAnimation: { enabled: !REDUCED, speed: 350 } },
        },
        theme: { mode: dark ? 'dark' : 'light' },
        grid: { borderColor: cssVar('--grid'), strokeDashArray: 0, padding: { left: 4, right: 8 }, xaxis: { lines: { show: false } } },
        dataLabels: { enabled: false },
        legend: { position: 'top', horizontalAlign: 'left', fontSize: '12px', markers: { size: 5, shape: 'square', offsetX: -2 }, itemMargin: { horizontal: 10 } },
        xaxis: {
            axisBorder: { color: cssVar('--axis') },
            axisTicks: { show: false },
            labels: { style: { colors: cssVar('--muted'), fontSize: '11px' }, rotate: 0, hideOverlappingLabels: true },
            tooltip: { enabled: false },
            crosshairs: { show: true, stroke: { color: cssVar('--axis'), width: 1, dashArray: 0 } },
        },
        yaxis: { labels: { style: { colors: cssVar('--muted'), fontSize: '11px' }, formatter: fmt.axis }, tickAmount: 4, forceNiceScale: true, min: 0 },
        states: { hover: { filter: { type: 'lighten', value: 0.06 } }, active: { filter: { type: 'none' } } },
        tooltip: { shared: true, intersect: false, theme: dark ? 'dark' : 'light', y: { formatter: (v) => fmt.rp(v, false) } },
    };
}

// Batang maksimal ±24px, berapa pun jumlah kategorinya.
const columnWidth = (count, host) => Math.max(10, Math.min(60, (24 / (((host?.clientWidth || 600) - 60) / Math.max(count, 1))) * 100)) + '%';

// Nilai kecil (mis. Rp 26 jt di skala 4 M) bisa < 2px lalu tertutup garis pemisah 2px → batang hilang padahal datanya ada.
// Sumbu Y memakai batas atas "rapi" yang dihitung sendiri, lalu nilai > 0 diberi tinggi minimum ±7px (4% sumbu)
// supaya tetap terlihat; tooltip, label & tabel tetap memakai nilai asli.
const MIN_VISIBLE = 0.04;
function niceScale(v) {
    if (!(v > 0)) return { max: 4, ticks: 4 };
    const e = Math.pow(10, Math.floor(Math.log10(v)));
    const f = v / e;
    const [n, ticks] = f <= 1 ? [1, 4] : f <= 2 ? [2, 4] : f <= 2.5 ? [2.5, 5] : f <= 4 ? [4, 4] : f <= 5 ? [5, 5] : [10, 5];
    return { max: n * e, ticks };
}
function visibleData(seriesData, stacked) {
    const len = Math.max(0, ...seriesData.map((d) => d.length));
    const totals = Array.from({ length: len }, (_, i) => seriesData.reduce((acc, d) => (stacked ? acc + (+d[i] || 0) : Math.max(acc, +d[i] || 0)), 0));
    const scale = niceScale(Math.max(0, ...totals) * 1.04);
    const floor = scale.max * MIN_VISIBLE;
    const data = seriesData.map((d) => d.map((v) => { v = +v || 0; return v > 0 ? Math.max(v, floor) : v; }));
    // setelah dinaikkan, total bisa melewati batas atas → hitung ulang
    const shownMax = Math.max(0, ...Array.from({ length: len }, (_, i) => data.reduce((acc, d) => (stacked ? acc + d[i] : Math.max(acc, d[i])), 0)));
    return { data, scale: shownMax > scale.max ? niceScale(shownMax * 1.04) : scale };
}
const realValue = (seriesData) => (v, opts) => {
    const real = opts && opts.seriesIndex !== undefined && opts.dataPointIndex !== undefined ? seriesData[opts.seriesIndex]?.[opts.dataPointIndex] : undefined;
    return real === undefined ? v : +real || 0;
};

function stackedBars(host, categories, series, { onSelect } = {}) {
    const o = baseChart(300);
    const raw = series.map((s) => s.data);
    const { data: shown, scale } = visibleData(raw, true);
    const real = realValue(raw);
    return new ApexCharts(host, {
        ...o,
        chart: { ...o.chart, type: 'bar', stacked: true, events: onSelect ? { dataPointSelection: (e, c, cfg) => onSelect(cfg.seriesIndex) } : {} },
        series: series.map((s, i) => ({ name: s.name, data: shown[i] })),
        tooltip: { ...o.tooltip, y: { formatter: (v, opts) => fmt.rp(real(v, opts), false) } },
        yaxis: { ...o.yaxis, max: scale.max, tickAmount: scale.ticks, forceNiceScale: false },
        colors: series.map((s) => cssVar(`--s${s.slot}`)),
        plotOptions: { bar: { columnWidth: columnWidth(categories.length, host), borderRadius: 4, borderRadiusApplication: 'end', borderRadiusWhenStacked: 'last' } },
        stroke: { show: true, width: 2, colors: [cssVar('--surface')] },
        xaxis: { ...o.xaxis, categories, tickAmount: Math.min(categories.length, 12) },
        legend: { ...o.legend, show: series.length > 1 },
    });
}

function areaLines(host, categories, series) {
    const o = baseChart(300);
    return new ApexCharts(host, {
        ...o,
        chart: { ...o.chart, type: 'area' },
        series: series.map((s) => ({ name: s.name, data: s.data })),
        colors: series.map((s) => cssVar(`--s${s.slot}`)),
        stroke: { width: 2, curve: 'monotoneCubic' },
        fill: { type: 'gradient', gradient: { shadeIntensity: 0, opacityFrom: 0.14, opacityTo: 0.01, stops: [0, 100] } },
        markers: { size: 0, strokeColors: cssVar('--surface'), strokeWidth: 2, hover: { size: 5 } },
        xaxis: { ...o.xaxis, categories, tickAmount: Math.min(categories.length, 12) },
    });
}

function columns(host, categories, data, unit) {
    const o = baseChart(220);
    const real = realValue([data]);
    const { data: [shown], scale } = visibleData([data], false);
    return new ApexCharts(host, {
        ...o,
        chart: { ...o.chart, type: 'bar' },
        series: [{ name: unit, data: shown }],
        colors: [cssVar('--s1')],
        plotOptions: { bar: { columnWidth: columnWidth(categories.length, host), borderRadius: 4, borderRadiusApplication: 'end', dataLabels: { position: 'top' } } },
        dataLabels: { enabled: true, offsetY: -18, style: { fontSize: '11px', fontWeight: 600, colors: [cssVar('--ink-2')] }, formatter: (v, opts) => fmt.num(real(v, opts)) },
        xaxis: { ...o.xaxis, categories, crosshairs: { show: false } },
        yaxis: { ...o.yaxis, max: scale.max, tickAmount: scale.ticks, forceNiceScale: false, labels: { ...o.yaxis.labels, formatter: (v) => fmt.num(v) } },
        tooltip: { ...o.tooltip, shared: false, intersect: true, y: { formatter: (v, opts) => fmt.num(real(v, opts)) + ' ' + unit } },
        legend: { show: false },
    });
}


/* ===================== Komponen dashboard ===================== */
Alpine.data('insight', (cfg) => {
    const charts = {}; // di luar state Alpine supaya objek ApexCharts tidak dibungkus proxy
    const destroy = (key) => { if (charts[key]) { charts[key].destroy(); delete charts[key]; } };

    return {
        scope: cfg.scope,
        endpoint: cfg.endpoint,
        modes: [
            { key: 'all', label: 'Semua waktu' },
            { key: 'year', label: 'Tahunan' },
            { key: 'month', label: 'Bulanan' },
            { key: 'day', label: 'Harian' },
            { key: 'custom', label: 'Rentang tanggal' },
        ],
        years: cfg.years,
        minDate: cfg.minDate,
        maxDate: cfg.maxDate,
        minMonth: cfg.minDate.slice(0, 7),
        maxMonth: cfg.maxDate.slice(0, 7),
        mode: 'year',
        year: cfg.years[0],
        month: cfg.maxDate.slice(0, 7),
        day: cfg.maxDate,
        from: cfg.defaultFrom,
        to: cfg.maxDate,
        lender: cfg.lender,
        revenueKey: 'all',
        tables: {},
        // panel rincian kartu KPI
        detail: { open: false, kpi: null, res: null, loading: false, error: null, q: '', page: 1, sort: null, dir: null, req: 0, trigger: null, filters: {} },
        detailEndpoint: cfg.detailEndpoint,
        pendingDetail: null,
        res: null,
        loading: false,
        error: null,
        requestId: 0,

        init() {
            const q = new URLSearchParams(window.location.search);
            if (this.modes.some((m) => m.key === q.get('mode'))) this.mode = q.get('mode');
            if (q.get('year')) this.year = Number(q.get('year'));
            ['month', 'day', 'from', 'to'].forEach((k) => { if (q.get(k)) this[k] = q.get(k); });
            if (q.get('lender') && cfg.canSwitch) this.lender = q.get('lender');
            if (q.get('detail')) {
                const filters = {};
                for (const [k, v] of q.entries()) { const m = k.match(/^f\[(\w+)\]$/); if (m) filters[m[1]] = v; }
                this.pendingDetail = { metric: q.get('detail'), stage: q.get('stage'), only: q.get('only'), filters };
            }
            this.$nextTick(() => this.moveThumb());
            if (document.fonts) document.fonts.ready.then(() => this.moveThumb());
            window.addEventListener('theme-changed', () => this.draw());
            let t;
            window.addEventListener('resize', () => { clearTimeout(t); t = setTimeout(() => { this.moveThumb(); this.draw(); }, 200); });
            this.load();
        },

        get d() { return this.res ? this.res.data : null; },

        params() {
            const p = { scope: this.scope, mode: this.mode };
            if (this.mode === 'year') p.year = this.year;
            if (this.mode === 'month') p.month = this.month;
            if (this.mode === 'day') p.day = this.day;
            if (this.mode === 'custom') { p.from = this.from; p.to = this.to; }
            if (cfg.canSwitch) p.lender = this.lender || 'all';
            return new URLSearchParams(p);
        },

        async load() {
            const id = ++this.requestId;
            this.loading = true;
            this.error = null;
            const params = this.params();
            try {
                const resp = await fetch(`${this.endpoint}?${params}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                if (!resp.ok) throw new Error(resp.status === 403 ? 'Akses ditolak' : `Gagal memuat data (${resp.status})`);
                const json = await resp.json();
                if (id !== this.requestId) return; // ada permintaan yang lebih baru
                this.res = json;
                if (this.revenueKey !== 'all' && !json.data.revenue.components.some((c) => c.key === this.revenueKey)) this.revenueKey = 'all';
                this.syncUrl();
                await this.$nextTick();
                this.draw();
                if (this.pendingDetail) { const pd = this.pendingDetail; this.pendingDetail = null; const k = this.kpiFromState(pd); if (k) this.openDetail(k); }
                else if (this.detail.open) { this.detail.kpi = this.refreshKpi(this.detail.kpi); this.detail.page = 1; this.loadDetail(); }
            } catch (e) {
                if (id === this.requestId) this.error = e.message || 'Gagal memuat data';
            } finally {
                if (id === this.requestId) this.loading = false;
            }
        },

        syncUrl() {
            const params = this.params();
            params.delete('scope');
            if (this.detail.open && this.detail.kpi) {
                params.set('detail', this.detail.kpi.metric);
                if (this.detail.kpi.stage != null) params.set('stage', this.detail.kpi.stage);
                if (this.detail.kpi.only) params.set('only', this.detail.kpi.only);
                Object.entries(this.detail.filters).forEach(([k, v]) => params.set(`f[${k}]`, v));
            }
            window.history.replaceState(null, '', `${window.location.pathname}?${params}`);
        },

        /* ---------- panel rincian ---------- */
        findKpi(metric) {
            if (!this.d) return null;
            if (metric === 'disbursed' && this.d.hero) {
                return { metric: 'disbursed', label: 'Total dana tersalurkan', value: this.d.hero.value, prev: this.d.hero.prev, format: 'rp', up_is_good: true, spark: this.d.hero.series };
            }
            return (this.d.kpis || []).find((k) => k.metric === metric) || null;
        },
        // tahap alur pengajuan → panel rincian berisi pengajuan yang sampai tahap itu
        stageKpi(i) {
            const st = this.d?.funnel?.stages?.[i];
            if (!st) return null;
            return { metric: 'funnel', stage: i, label: st.name, kicker: this.d.funnel.title, value: st.value, prev: null, format: 'num', spark: null };
        },
        // baris perusahaan/pelanggan → rincian dana cair yang tersaring ke baris itu
        rowKpi(label, value, filters, kicker, only = null) {
            return { metric: 'disbursed', label, kicker, value, prev: null, format: 'rp', spark: null, filters: { ...filters }, only };
        },
        kpiFromState(st) {
            if (st.metric === 'funnel') return this.stageKpi(Number(st.stage) || 0);
            const base = this.findKpi(st.metric);
            if (!base) return null;
            if (!Object.keys(st.filters || {}).length) return base;
            return { ...base, label: Object.values(st.filters).join(' · '), kicker: base.label, value: null, prev: null, spark: null, filters: st.filters, only: st.only || null };
        },
        refreshKpi(k) {
            if (k.metric === 'funnel') return this.stageKpi(k.stage) || k;
            if (k.filters && Object.keys(k.filters).length) return { ...k, value: null };
            return this.findKpi(k.metric) || k;
        },
        openDetail(kpi, trigger = null) {
            if (!kpi || !kpi.metric) return;
            Object.assign(this.detail, { open: true, kpi, res: null, error: null, q: '', page: 1, sort: null, dir: null, trigger: trigger || document.activeElement, filters: { ...(kpi.filters || {}) } });
            document.documentElement.style.overflow = 'hidden';
            this.syncUrl();
            this.loadDetail();
            this.$nextTick(() => this.$refs.detailClose?.focus());
        },
        closeDetail() {
            this.detail.open = false;
            document.documentElement.style.overflow = '';
            this.syncUrl();
            const t = this.detail.trigger;
            if (t && typeof t.focus === 'function') this.$nextTick(() => t.focus());
        },
        detailParams(withPage = true) {
            const p = this.params();
            p.set('metric', this.detail.kpi.metric);
            if (this.detail.kpi.stage != null) p.set('stage', this.detail.kpi.stage);
            if (this.detail.kpi.only) p.set('only', this.detail.kpi.only);
            Object.entries(this.detail.filters).forEach(([k, v]) => p.set(`f[${k}]`, v));
            if (this.detail.q) p.set('q', this.detail.q);
            if (this.detail.sort) { p.set('sort', this.detail.sort); p.set('dir', this.detail.dir); }
            if (withPage) p.set('page', this.detail.page);
            return p;
        },
        async loadDetail() {
            if (!this.detail.kpi) return;
            const id = ++this.detail.req;
            this.detail.loading = true;
            this.detail.error = null;
            try {
                const resp = await fetch(`${this.detailEndpoint}?${this.detailParams()}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                if (!resp.ok) throw new Error(`Gagal memuat rincian (${resp.status})`);
                const json = await resp.json();
                if (id !== this.detail.req) return;
                this.detail.res = json.data;
                this.detail.page = json.data.pagination.page;
            } catch (e) {
                if (id === this.detail.req) this.detail.error = e.message || 'Gagal memuat rincian';
            } finally {
                if (id === this.detail.req) this.detail.loading = false;
            }
        },
        csvUrl() {
            if (!this.detail.kpi) return '#';
            const p = this.detailParams(false);
            p.set('format', 'csv');
            return `${this.detailEndpoint}?${p}`;
        },
        sortDetail(key) {
            const cur = this.detail.res?.sort;
            this.detail.sort = key;
            this.detail.dir = cur && cur.key === key && cur.dir === 'desc' ? 'asc' : 'desc';
            this.detail.page = 1;
            this.loadDetail();
        },
        pageDetail(n) {
            this.detail.page = n;
            this.loadDetail();
            this.$refs.detailBody?.scrollTo({ top: this.$refs.detailBody.scrollHeight, behavior: REDUCED ? 'auto' : 'smooth' });
        },
        // judul = label kartu/baris yang diklik selama saringannya belum diubah; kalau diubah → nama metriknya
        filtersChanged() {
            const a = this.detail.kpi?.filters || {};
            const b = this.detail.filters;
            const ka = Object.keys(a), kb = Object.keys(b);
            return ka.length !== kb.length || ka.some((k) => a[k] !== b[k]);
        },
        detailTitle() {
            const k = this.detail.kpi;
            if (!k) return 'Rincian';
            if (!this.filtersChanged()) return k.label;
            return (k.filters ? this.findKpi(k.metric)?.label : k.label) || k.label;
        },
        detailContext() {
            if (!this.res) return '';
            const kicker = this.filtersChanged() ? null : this.detail.kpi?.kicker;
            return [kicker, this.res.period.label, this.res.period.range, this.res.lender.name].filter(Boolean).join(' · ');
        },
        /* filter di panel: klik item pecahan untuk menyaring, × pada chip untuk melepas */
        hasFilters() { return Object.keys(this.detail.filters).length > 0; },
        filterLabel(key) {
            return { company: this.scope === 'tomiinvoice' ? 'Mitra' : 'Perusahaan', name: this.scope === 'tomiinvoice' ? 'Pelanggan' : 'Peminjam', lender: 'Pemberi dana', product: 'Produk', status: 'Status' }[key] || key;
        },
        toggleFilter(key, value) {
            if (!value || /^Lainnya \(/.test(value)) return;
            if (this.detail.filters[key] === value) delete this.detail.filters[key];
            else this.detail.filters[key] = value;
            this.detail.filters = { ...this.detail.filters };
            this.detail.page = 1;
            this.syncUrl();
            this.loadDetail();
        },
        removeFilter(key) { delete this.detail.filters[key]; this.detail.filters = { ...this.detail.filters }; this.detail.page = 1; this.syncUrl(); this.loadDetail(); },
        // angka utama: angka kartu; bila tersaring → total hasil saring
        detailFigure() {
            const k = this.detail.kpi;
            if (!this.hasFilters() && k?.value != null) return fmt.value(k.value, k.format);
            const s = this.detail.res?.summary;
            if (!s) return '…';
            const kind = this.detail.res.kind;
            return kind === 'money' ? fmt.rp(s.value) : kind === 'rate' ? fmt.pct(s.value) : fmt.num(s.value);
        },
        hasCol(key) { return !!this.detail.res?.columns.some((c) => c.key === key); },
        col(key) { return this.detail.res?.columns.find((c) => c.key === key)?.label || ''; },
        sortMark(key) { const s = this.detail.res?.sort; return s && s.key === key ? (s.dir === 'asc' ? '↑' : '↓') : '↕'; },
        detailSortAria(key) { const s = this.detail.res?.sort; return s && s.key === key ? (s.dir === 'asc' ? 'ascending' : 'descending') : null; },
        detailCountText() {
            const pg = this.detail.res?.pagination;
            if (!pg) return '';
            return this.detail.q ? `${fmt.num(pg.total)} dari ${fmt.num(pg.all)} baris cocok` : `${fmt.num(pg.all)} baris`;
        },
        detailBars() {
            const k = this.detail.kpi;
            if (this.hasFilters()) return []; // deret kartu belum tersaring
            const data = Array.isArray(k?.spark) ? k.spark : k?.spark?.data;
            const cats = this.d?.trend?.categories || [];
            if (!data || data.length !== cats.length) return [];
            const top = Math.max(...data.map((v) => Math.abs(Number(v) || 0)), 0) || 1;
            return data.map((v, i) => ({ label: cats[i], value: v, h: (Math.abs(Number(v) || 0) / top) * 100 }));
        },
        breakdownValue(v) {
            const kind = this.detail.res?.kind;
            if (kind === 'money') return fmt.rp(v);
            if (kind === 'sum') return fmt.num(v) + ' karyawan';
            return fmt.num(v) + ' akun';
        },
        itemColor(key, it) {
            if (key === 'status') return `var(--${this.toneVar(it.tone)})`;
            if (key === 'product' && it.slot) return `var(--s${it.slot})`;
            return 'var(--s1)';
        },
        toneVar(t) { return { good: 'good-fill', warn: 'warn-fill', crit: 'crit-fill', info: 'brand', mute: 'axis' }[t] || 's1'; },
        fmtDate(s) {
            if (!s) return '-';
            const [d, t] = s.split(' ');
            const [y, m, day] = d.split('-').map(Number);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${day} ${months[m - 1]} ${y}` + (t && t !== '00:00' ? ' ' + t : '');
        },
        trapFocus(e) {
            const els = [...this.$refs.detailPanel.querySelectorAll('a[href], button:not([disabled]), input, select, [tabindex]:not([tabindex="-1"])')].filter((el) => el.offsetParent !== null);
            if (!els.length) return;
            const first = els[0];
            const last = els[els.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        },

        setMode(m) {
            if (this.mode === m) return;
            this.mode = m;
            this.$nextTick(() => this.moveThumb());
            this.load();
        },

        moveThumb() {
            const seg = this.$refs.seg;
            const btn = seg && seg.querySelector(`button[data-mode="${this.mode}"]`);
            if (!btn) return;
            this.$refs.thumb.style.left = btn.offsetLeft + 'px';
            this.$refs.thumb.style.width = btn.offsetWidth + 'px';
        },

        rangeText() {
            if (!this.res) return '';
            const per = { month: 'bulan', day: 'hari', hour: 'jam' }[this.res.period.granularity];
            return `${this.res.period.range} · grafik per ${per} · ${this.res.lender.name}`;
        },

        delta(cur, prev, upIsGood = true) {
            if (prev == null || !this.res?.period.prev_label) return { cls: 'flat', text: '—', sub: '' };
            if (!prev && !cur) return { cls: 'flat', text: 'Belum ada', sub: 'pada periode ini' };
            if (!prev) return { cls: 'flat', text: 'Baru', sub: 'vs ' + this.res.period.prev_label };
            const ch = (cur - prev) / Math.abs(prev);
            const up = ch >= 0;
            const flat = Math.abs(ch) < 0.005;
            return {
                cls: flat ? 'flat' : up === upIsGood ? 'good' : 'bad',
                text: (flat ? '' : up ? '▲ ' : '▼ ') + fmt.pct(Math.abs(ch)),
                sub: 'vs ' + this.res.period.prev_label,
            };
        },

        share(value, total) { return total > 0 ? value / total : 0; },
        sum(list, key) { return (list || []).reduce((s, x) => s + (Number(x[key]) || 0), 0); },
        max(list, key) { return Math.max(1, ...(list || []).map((x) => Number(x[key]) || 0)); },
        partialNote() { return this.res?.period.partial_note ? ' · *' + this.res.period.partial_note : ''; },
        withPartial(categories) {
            if (!this.res?.period.partial_note || !categories.length) return categories;
            return categories.map((c, i) => (i === categories.length - 1 ? c + '*' : c));
        },

        /* ---------- pendapatan ---------- */
        // Komponen diurutkan dari terbesar. Maksimal 4 warna: kalau lebih, komponen ke-4 dst. memakai
        // warna slot 4 dan di grafik gabungan dilipat jadi satu seri "Lainnya".
        revenueComponents() {
            const comps = [...(this.d?.revenue?.components || [])].sort((a, b) => b.total - a.total);
            const fold = comps.length > 4;
            return comps.map((c, i) => ({ ...c, slot: Math.min(i + 1, 4), folded: fold && i >= 3 }));
        },
        revenueSelected() {
            const comps = this.revenueComponents();
            if (this.revenueKey === 'all') return { label: 'Gabungan', total: this.d?.revenue?.total || 0, prev: this.d?.revenue?.prev ?? null };
            return comps.find((c) => c.key === this.revenueKey) || { label: '-', total: 0, prev: null };
        },
        setRevenue(key) { this.revenueKey = key; this.$nextTick(() => this.drawRevenue()); },

        /* ---------- grafik ---------- */
        draw() {
            if (!this.d) return;
            this.drawTrend();
            this.drawRevenue();
            this.drawColumns();
        },
        drawTrend() {
            const host = this.$refs.trend;
            destroy('trend');
            if (!host || !this.d.trend) return;
            const cats = this.withPartial(this.d.trend.categories);
            if (this.scope === 'overview') {
                const series = this.d.trend.series.filter((s) => s.data.some((v) => v > 0));
                charts.trend = stackedBars(host, cats, series.length ? series : this.d.trend.series.slice(0, 1), {
                    onSelect: (i) => { const s = series[i]; if (s && cfg.productUrls[s.key]) window.location.href = cfg.productUrls[s.key]; },
                });
            } else {
                charts.trend = areaLines(host, cats, this.d.trend.series);
            }
            charts.trend.render();
        },
        drawRevenue() {
            const host = this.$refs.revenue;
            destroy('revenue');
            if (!host || !this.revenueComponents().length) return;
            const comps = this.revenueComponents();
            let shown;
            if (this.revenueKey !== 'all') {
                shown = comps.filter((c) => c.key === this.revenueKey).map((c) => ({ name: c.label, data: c.data, slot: c.slot }));
            } else {
                shown = comps.filter((c) => !c.folded).map((c) => ({ name: c.label, data: c.data, slot: c.slot }));
                const rest = comps.filter((c) => c.folded);
                if (rest.length) shown.push({ name: 'Lainnya', slot: 4, data: rest[0].data.map((_, i) => rest.reduce((s, c) => s + (c.data[i] || 0), 0)) });
            }
            charts.revenue = stackedBars(host, this.withPartial(this.d.revenue.categories), shown);
            charts.revenue.render();
        },
        drawColumns() {
            const host = this.$refs.columns;
            destroy('columns');
            if (!host || !this.d.columns || !this.d.columns.data.length) return;
            charts.columns = columns(host, this.d.columns.categories, this.d.columns.data, this.d.columns.unit);
            charts.columns.render();
        },

        toggleTable(key) { this.tables[key] = !this.tables[key]; if (!this.tables[key]) this.$nextTick(() => this.draw()); },
    };
});

window.Alpine = Alpine;
Alpine.start();
