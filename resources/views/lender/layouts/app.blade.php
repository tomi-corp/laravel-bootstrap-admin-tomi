<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lender')</title>
    @include('lender.layouts.partials.head-assets')
    <style>
        .lender-content {
            background-color: var(--lender-blue-page);
            color: #ffffff;
        }

        .lender-content .bg-white,
        .lender-content input,
        .lender-content select,
        .lender-content textarea,
        .lender-content table {
            color: #062B49;
        }

        .select2-container {
            width: 100% !important;
            max-width: 100%;
        }

        .select2-container--default .select2-selection--single {
            min-height: 42px;
            border: 1px solid #DFF1FF;
            border-radius: 0.375rem;
            background-color: #ffffff;
            color: #062B49;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            padding-left: 0.75rem;
            padding-right: 2.25rem;
            color: var(--lender-blue-text);
            font-size: 0.875rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: var(--lender-blue-hover);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 0.5rem;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--lender-blue);
            box-shadow: 0 0 0 2px rgba(11, 143, 232, 0.18);
            outline: none;
        }

        .select2-dropdown {
            border: 1px solid #B9E4FF;
            border-radius: 0.375rem;
            color: #062B49;
            max-width: 100%;
            overflow-x: hidden;
        }

        .select2-results__option {
            white-space: normal;
            word-break: break-word;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: var(--lender-blue);
            color: #ffffff;
        }

        .select2-container--default .select2-results__option--selected {
            background-color: #DFF1FF;
            color: #062B49;
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
<body class="relative min-h-screen bg-primary-400 text-primary-950 antialiased">
    @include('lender.layouts.partials.sidebar')

    <div id="lenderShell" class="min-h-screen bg-primary-400 transition-all lg:pl-64" style="background-color: var(--lender-blue-page);">
        @include('lender.layouts.partials.topbar')

        <main class="lender-content min-h-[calc(100vh-8rem)] bg-primary-400 text-white">
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
