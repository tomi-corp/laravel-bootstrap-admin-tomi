<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Company')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    @stack('styles')
</head>
<body class="min-h-screen bg-white text-black antialiased">
    <div class="flex min-h-screen">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col border-r border-black bg-black text-white lg:flex">
            <div class="flex h-16 items-center gap-3 border-b border-white/20 px-5">
                <div class="flex h-10 w-10 items-center justify-center rounded-md bg-white text-black">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold leading-none">Company Portal</p>
                    <p class="mt-1 text-xs leading-none text-white/70">TOMI</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4">
                <a href="{{ route('company.dashboard') }}" class="flex items-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-medium text-black">
                    <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                    Dashboard
                </a>
            </nav>
        </aside>

        <div class="flex min-h-screen flex-1 flex-col lg:pl-64">
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div>
                        <p class="text-sm font-semibold leading-none text-black">Company Portal</p>
                        <p class="mt-1 text-xs leading-none text-slate-500">Dashboard Perusahaan</p>
                    </div>

                    <a href="{{ route('company.dashboard') }}" class="rounded-md border border-black bg-white px-3 py-2 text-sm font-medium text-black hover:bg-black hover:text-white">
                        Dashboard
                    </a>
                </div>
            </header>

            <div class="flex-1 bg-slate-50">
                <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
