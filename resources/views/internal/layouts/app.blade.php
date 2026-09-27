<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TOMI - @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Superapp All-in-One Backend Aplikasi Cepat" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Vendor CSS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" rel="stylesheet" />

    @stack('css')

    {{-- jQuery must load before Select2, Toastr, and DataTables. --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="bg-slate-50 text-slate-800">
    <div class="min-h-screen">
        <aside
            id="internalSidebar"
            class="fixed inset-y-0 left-0 z-[1040] w-64 -translate-x-full bg-slate-900 text-slate-300 transition-transform duration-200 lg:translate-x-0"
            aria-label="Internal navigation"
        >
            <div class="flex h-16 items-center border-b border-white/10 px-5">
                <a href="{{ route('internal.companies.index') }}" class="flex items-center text-white no-underline">
                    <span class="mr-2 inline-flex h-9 w-9 items-center justify-center rounded-md bg-blue-600 text-white">
                        <i class="fa-solid fa-layer-group"></i>
                    </span>
                    <span class="font-bold">TOMI Internal</span>
                </a>
            </div>

            <nav class="space-y-1 px-3 py-4">
                <div class="px-3 py-2 text-xs font-bold uppercase tracking-wider text-slate-400">Menu</div>
                <a
                    href="{{ route('internal.companies.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-semibold no-underline transition {{ request()->routeIs('internal.companies.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white focus:bg-slate-800 focus:text-white' }}"
                >
                    <i class="fa-solid fa-building w-5 text-center"></i>
                    <span>Perusahaan</span>
                </a>
            </nav>
        </aside>

        <div id="internalSidebarBackdrop" class="fixed inset-0 z-[1030] hidden bg-slate-950/50 lg:hidden"></div>

        <div class="min-h-screen transition-[margin] duration-200 lg:ml-64">
            <header class="sticky top-0 z-[1020] border-b border-slate-200 bg-white">
                <div class="mx-auto flex w-full items-center justify-between px-4 py-3 sm:px-6">
                    <div class="flex items-center">
                        <button
                            type="button"
                            class="mr-3 inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 lg:hidden"
                            id="internalSidebarToggle"
                            aria-label="Toggle sidebar"
                        >
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <a href="{{ route('internal.companies.index') }}" class="flex items-center text-slate-950 no-underline">
                            <span class="font-bold">TOMI Internal</span>
                        </a>
                    </div>

                    <nav class="flex items-center">
                        <a
                            href="{{ route('internal.companies.index') }}"
                            class="inline-flex items-center gap-2 rounded-md border border-blue-600 bg-white px-3 py-2 text-sm font-semibold text-blue-600 no-underline transition hover:bg-blue-50 hover:text-blue-700"
                        >
                            <i class="fa-solid fa-building"></i> Perusahaan
                        </a>
                    </nav>
                </div>
            </header>

            <main class="p-4 sm:p-6">
                <div class="mx-auto w-full">
                    @yield('content')
                </div>
            </main>

            <footer class="py-3 text-center text-sm text-slate-500">
                TOMI Internal
            </footer>
        </div>
    </div>

    {{-- Vendor JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/keytable/2.12.1/js/dataTables.keyTable.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script>
        (function ($) {
            function prepareModal($modal) {
                $modal.addClass('fixed inset-0 z-[1050] overflow-y-auto bg-slate-950/50 p-4');
                $modal.children('.modal-dialog').addClass('mx-auto flex min-h-full w-full max-w-6xl items-center');
                $modal.find('.modal-content').addClass('flex max-h-[calc(100vh-3.5rem)] w-full flex-col overflow-hidden rounded-lg bg-white shadow-2xl');
                $modal.find('.modal-header').addClass('flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4');
                $modal.find('.modal-footer').addClass('flex items-center justify-end gap-3 border-t border-slate-200 px-5 py-4');
                $modal.find('.modal-title').addClass('text-lg font-bold text-slate-950');
                $modal.find('.modal-body').addClass('overflow-y-auto px-5 py-4');
                $modal.find('.close').addClass('text-2xl leading-none text-slate-500 transition hover:text-slate-950');

                if (!$modal.hasClass('show')) {
                    $modal.addClass('hidden');
                }
            }

            $('.modal').each(function () {
                prepareModal($(this));
            });

            $.fn.modal = function (action) {
                return this.each(function () {
                    var $modal = $(this);
                    prepareModal($modal);

                    if (action === 'hide') {
                        $modal.removeClass('show').addClass('hidden');
                        $('body').removeClass('overflow-hidden');
                        return;
                    }

                    $modal.removeClass('hidden').addClass('show');
                    $('body').addClass('overflow-hidden');
                });
            };

            $(document).on('click', '[data-dismiss="modal"]', function () {
                $(this).closest('.modal').modal('hide');
            });

            $(document).on('click', '.modal', function (event) {
                if (event.target === this) {
                    $(this).modal('hide');
                }
            });
        })(jQuery);
    </script>
    <script>
        (function () {
            const sidebar = document.getElementById('internalSidebar');
            const backdrop = document.getElementById('internalSidebarBackdrop');
            const toggle = document.getElementById('internalSidebarToggle');

            function openSidebar() {
                sidebar?.classList.remove('-translate-x-full');
                sidebar?.classList.add('translate-x-0');
                backdrop?.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar?.classList.add('-translate-x-full');
                sidebar?.classList.remove('translate-x-0');
                backdrop?.classList.add('hidden');
            }

            toggle?.addEventListener('click', openSidebar);
            backdrop?.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });
        })();
    </script>

    @stack('js')
</body>

</html>
