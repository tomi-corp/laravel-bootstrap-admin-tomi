{{--
    Layout halaman lama panel internal (Bootstrap/DataTables) di dalam kerangka internal (internal.layouts.shell).
    Disalin dari layout.app: CSS lama ditaruh di cascade layer `legacy` lalu diberi skin baru (resources/css/legacy-skin.css).
--}}
@extends('internal.layouts.shell')

@section('ui-css', 'resources/css/backoffice-legacy.css')

@section('shell-main')
    <div class="legacy" id="legacy-root">@yield('content')</div>
@endsection

@push('head-start')
    <meta content="Superapp All-in-One Backend Aplikasi Cepat" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style>
        @layer theme, base, legacy, components, utilities;
        @import url("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css") layer(legacy);
        @import url("{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.css') }}") layer(legacy);
        @import url("{{ asset('assets/libs/datatables/responsive.bootstrap4.min.css') }}") layer(legacy);
        @import url("{{ asset('assets/libs/datatables/buttons.bootstrap4.min.css') }}") layer(legacy);
        @import url("{{ asset('assets/libs/datatables/select.bootstrap4.min.css') }}") layer(legacy);
        @import url("{{ asset('assets/css/bootstrap.min.css') }}") layer(legacy);
        @import url("{{ asset('assets/css/icons.min.css') }}") layer(legacy);
        @import url("{{ asset('assets/css/app.min.css') }}") layer(legacy);
        @import url("{{ asset('assets/css/lightbox.css') }}") layer(legacy);
        @import url("{{ asset('assets/css/toastr.min.css') }}") layer(legacy);
        @import url("{{ asset('assets/css/custom.css') }}") layer(legacy);
        @import url("{{ asset('assets/libs/bootstrap-daterangepicker/daterangepicker.css') }}") layer(legacy);
        @import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css") layer(legacy);
        @import url("{{ asset('assets/libs/chartist/chartist.min.css') }}") layer(legacy);
        .fl-wrapper { z-index: 99999 !important; position: fixed !important; }
    </style>
    {{-- CSS tambahan halaman: <link> ikut dimasukkan ke layer legacy (beberapa halaman memuat Bootstrap sendiri dari CDN) --}}
    {!! preg_replace_callback('#<link\b[^>]*\brel=["\']stylesheet["\'][^>]*>#i', function ($m) {
        return preg_match('#\bhref=["\']([^"\']+)["\']#i', $m[0], $h)
            ? '<style>@import url("' . str_replace(['"', '\\'], ['%22', '%5C'], $h[1]) . '") layer(legacy);</style>'
            : $m[0];
    }, $__env->yieldPushContent('css')) !!}
    <script src="{{ asset('assets/js/jquery.js') }}"></script>
@endpush

@push('scripts')
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/js/lightbox.js') }}"></script>
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.select.min.js') }}"></script>
    @include('layout.partials.datatables-empty-row-shim')
    <script>
        if (window.jQuery && jQuery.fn.dataTable) {
            jQuery.extend(true, jQuery.fn.dataTable.defaults, {
                language: {
                    emptyTable: 'Belum ada data untuk ditampilkan',
                    info: '_START_–_END_ dari _TOTAL_ data',
                    infoEmpty: '0 data',
                    infoFiltered: '(disaring dari _MAX_ data)',
                    lengthMenu: 'Tampilkan _MENU_',
                    loadingRecords: 'Memuat...',
                    processing: '<span class="ui-spinner" aria-hidden="true"></span> Memuat data...',
                    search: '',
                    searchPlaceholder: 'Cari di tabel...',
                    zeroRecords: 'Tidak ada data yang cocok dengan pencarian',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                    aria: { sortAscending: ': urutkan naik', sortDescending: ': urutkan turun' },
                    buttons: { copy: 'Salin', print: 'Cetak', copyTitle: 'Disalin ke clipboard', copySuccess: { _: '%d baris disalin', 1: '1 baris disalin' } },
                    select: { rows: { _: '%d baris dipilih', 0: '', 1: '1 baris dipilih' } },
                },
            });
        }
    </script>
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/libs/chart-js/Chart.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
    <script src="{{ asset('assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    @stack('js')

    @include('layout.partials.shell.legacy-enhance')
@endpush
