{{-- Perapian UX halaman lama di kerangka baru. Hanya memperbaiki tampilan/navigasi, tidak mengubah logika halaman. --}}
<div class="page-progress" id="page-progress" aria-hidden="true"></div>
<script>
(function () {
    var root = document.getElementById('legacy-root');
    if (!root) return;

    // 1) Judul halaman: judul dari menu sudah tampil di header; halaman turunan (detail dsb.) memakai judul halamannya.
    var h1 = document.getElementById('page-title');
    var oldTitle = root.querySelector('.page-title-box .page-title');
    if (h1 && oldTitle && h1.dataset.fromNav !== '1') {
        var text = oldTitle.textContent.replace(/\s+/g, ' ').trim();
        if (text) { h1.textContent = text; document.title = text + ' · TOMI'; }
    }

    // 2) Bilah progres tipis saat pindah halaman / kirim form / memuat data (umpan balik bahwa sistem bekerja).
    var bar = document.getElementById('page-progress');
    var pending = 0, timer = null;
    function start() { clearTimeout(timer); bar.classList.remove('done'); bar.classList.add('run'); }
    function done() { bar.classList.add('done'); timer = setTimeout(function () { bar.classList.remove('run', 'done'); }, 350); }
    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href]');
        if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
        var href = a.getAttribute('href') || '';
        if (a.target === '_blank' || a.hasAttribute('download') || href.charAt(0) === '#' || /^(javascript|mailto|tel):/i.test(href)) return;
        if (a.origin !== location.origin || a.dataset.toggle || a.dataset.lightbox) return;
        start();
    });
    document.addEventListener('submit', function (e) { if (!e.defaultPrevented) start(); });
    window.addEventListener('pageshow', function () { bar.classList.remove('run', 'done'); });
    if (window.jQuery) {
        jQuery(document).on('ajaxSend', function () { if (pending++ === 0) start(); });
        jQuery(document).on('ajaxComplete', function () { if (--pending <= 0) { pending = 0; done(); } });
    }

    // 3) Tekan "/" untuk langsung mencari di tabel pertama.
    document.addEventListener('keydown', function (e) {
        if (e.key !== '/' || e.metaKey || e.ctrlKey || e.altKey) return;
        var t = e.target;
        if (t && (t.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName))) return;
        if (document.querySelector('.modal.show')) return;
        var input = root.querySelector('.dataTables_filter input');
        if (input) { e.preventDefault(); input.focus(); input.select(); }
    });

    // 4) Kolom pencarian tabel: beri label aksesibel & petunjuk pintasan.
    function decorate() {
        root.querySelectorAll('.dataTables_filter input:not([data-enhanced])').forEach(function (input) {
            input.setAttribute('data-enhanced', '1');
            input.setAttribute('aria-label', 'Cari di tabel');
            if (!input.placeholder) input.placeholder = 'Cari di tabel...';
            input.closest('.dataTables_filter').classList.add('dt-search');
        });
    }
    decorate();
    if (window.jQuery) jQuery(document).on('init.dt', decorate);

    // 5) Fokus otomatis ke isian pertama saat modal dibuka (lebih cepat untuk keyboard).
    if (window.jQuery) {
        jQuery(document).on('shown.bs.modal', '.modal', function () {
            var f = this.querySelector('.modal-body input:not([type=hidden]):not([readonly]):not([disabled]), .modal-body select:not([disabled]), .modal-body textarea:not([readonly])');
            var active = document.activeElement;
            if (f && (active === this || !this.contains(active))) { try { f.focus(); } catch (err) {} }
        });
    }
})();
</script>
