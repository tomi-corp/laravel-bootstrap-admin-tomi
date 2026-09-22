<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lender')</title>
    @include('lender.layouts.partials.head-assets')
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('lender.layouts.partials.sidebar')

    <div class="min-h-screen lg:pl-64">
        @include('lender.layouts.partials.topbar')

        <main class="min-h-[calc(100vh-8rem)]">
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
            sidebar.classList.toggle('-translate-x-full');
            sidebarBackdrop.classList.toggle('hidden');
        });

        sidebarBackdrop?.addEventListener('click', closeSidebar);
    </script>
    @stack('scripts')
</body>
</html>
