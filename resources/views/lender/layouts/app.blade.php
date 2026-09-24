<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lender')</title>
    @include('lender.layouts.partials.head-assets')
    <style>
        .select2-container {
            width: 100% !important;
            max-width: 100%;
        }

        .select2-container--default .select2-selection--single {
            min-height: 42px;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            padding-left: 0.75rem;
            padding-right: 2.25rem;
            font-size: 0.875rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 0.5rem;
        }

        .select2-dropdown {
            border-radius: 0.375rem;
            max-width: 100%;
            overflow-x: hidden;
        }

        .select2-results__option {
            white-space: normal;
            word-break: break-word;
        }

        body.lender-sidebar-collapsed #lenderSidebar {
            width: 5rem !important;
        }

        body.lender-sidebar-collapsed #lenderShell {
            padding-left: 5rem !important;
        }

        body.lender-sidebar-collapsed #lenderSidebar .sidebar-label,
        body.lender-sidebar-collapsed #lenderSidebar .sidebar-submenu {
            display: none !important;
        }

        body.lender-sidebar-collapsed #lenderSidebar nav {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        body.lender-sidebar-collapsed #lenderSidebar a,
        body.lender-sidebar-collapsed #lenderSidebar button {
            justify-content: center;
        }

        body.lender-sidebar-collapsed #lenderSidebar i {
            margin-left: 0;
            margin-right: 0;
        }

        #toast-container {
            z-index: 2147483647 !important;
        }
    </style>
    @stack('styles')
</head>
<body class="relative min-h-screen bg-slate-50 text-slate-950 antialiased">
    @include('lender.layouts.partials.sidebar')

    <div id="lenderShell" class="min-h-screen bg-slate-50 transition-all lg:pl-64">
        @include('lender.layouts.partials.topbar')

        <main class="lender-content min-h-[calc(100vh-8rem)] bg-slate-50 text-slate-950">
            @yield('content')
        </main>

        @include('lender.layouts.partials.footer')
    </div>

    @include('lender.layouts.partials.scripts')
    @include('lender.layouts.partials.flash')
    <script>
        const sidebar = document.getElementById('lenderSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        const closeSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden');
        };

        sidebarToggle?.addEventListener('click', function () {
            if (window.innerWidth >= 1024) {
                document.body.classList.toggle('lender-sidebar-collapsed');
                return;
            }

            sidebar.classList.toggle('-translate-x-full');
            sidebarBackdrop.classList.toggle('hidden');
        });

        sidebarBackdrop?.addEventListener('click', closeSidebar);
    </script>
    @stack('scripts')
</body>
</html>
