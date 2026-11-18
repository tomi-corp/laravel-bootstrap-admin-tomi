{{--
    Perbaikan error DataTables "Cannot set properties of undefined (setting '_DT_CellIndex')".

    Banyak tabel lama memakai @forelse … @empty <tr><td colspan="N">Belum ada data…</td></tr>. Baris satu sel itu
    tidak cocok dengan jumlah kolom header, jadi DataTables gagal membaca tabel (tabel kosong tidak ter-inisialisasi:
    tombol Excel/cari/paginasi hilang). Sebelum tabel di-inisialisasi, baris placeholder itu dibuang dan teksnya dipakai
    sebagai pesan "data kosong" bawaan DataTables — pesannya tetap sama, tanpa mengubah view satu per satu.

    Dipasang langsung setelah library DataTables di layout, lalu dicek ulang saat document ready untuk halaman yang
    memuat versi DataTables sendiri dari CDN (inisialisasinya di dalam ready).
--}}
<script>
(function () {
    function install($) {
        if (!$ || !$.fn || !$.fn.dataTable || $.fn.dataTable.__emptyRowShim) return;

        // buang baris placeholder (satu sel ber-colspan) di tbody; kembalikan teksnya
        function takePlaceholder(table) {
            var msg = null;
            $(table).children('tbody').children('tr').each(function () {
                var cells = $(this).children('td, th');
                if (cells.length === 1 && (parseInt(cells.attr('colspan'), 10) || 1) > 1) {
                    msg = msg || $.trim(cells.text()).replace(/\s+/g, ' ');
                    $(this).remove();
                }
            });
            return msg;
        }

        ['dataTable', 'DataTable'].forEach(function (name) {
            var original = $.fn[name];
            if (typeof original !== 'function') return;
            var wrapped = function (opts) {
                var args = Array.prototype.slice.call(arguments);
                // hanya saat inisialisasi (argumen kosong / objek opsi), bukan pemanggilan API pada tabel yang sudah jadi
                if (this.length && (opts === undefined || $.isPlainObject(opts))) {
                    this.each(function () {
                        if (this.nodeName !== 'TABLE' || $.fn.dataTable.isDataTable(this)) return;
                        var msg = takePlaceholder(this);
                        if (msg) {
                            var o = $.extend(true, {}, args[0] || {});
                            if (!o.language || !o.language.emptyTable) {
                                o.language = $.extend({}, o.language, { emptyTable: msg });
                            }
                            args[0] = o;
                        }
                    });
                }
                return original.apply(this, args);
            };
            $.extend(wrapped, original); // properti statis: ext, defaults, Api, isDataTable, plugin, dst.
            wrapped.prototype = original.prototype;
            $.fn[name] = wrapped;
        });
        $.fn.dataTable.__emptyRowShim = true;
    }

    install(window.jQuery);
    if (window.jQuery) {
        window.jQuery(function () { install(window.jQuery); });
    }
})();
</script>
