{{--
    Kerangka backoffice: sidebar bertingkat per produk, header dengan judul/breadcrumb otomatis, tema terang/gelap.
    Halaman native (dasbor) extends layout ini langsung; halaman lama lewat layout.app (skin legacy).
--}}
@php
    $navUser = auth('admin')->user();
    $nav = \App\Support\BackofficeNav::build($navUser);
    $roleLabel = ['master' => 'Admin master', 'admin' => 'Admin'][$navUser?->type] ?? 'Admin';
    $initials = strtoupper(substr($navUser?->username ?? 'A', 0, 2));
    $here = \App\Support\BackofficeNav::locate($nav);
    // Halaman menu → judul = label menu; halaman turunan (detail) → judul halaman itu sendiri, breadcrumb = menu induknya.
    $fromNav = $here && $here['exact'];
    $pageTitle = $__env->hasSection('page-title') ? trim($__env->yieldContent('page-title'))
        : ($fromNav ? $here['label'] : trim($__env->yieldContent('title', 'Backoffice')));
    $crumbs = $here ? ($fromNav ? ($here['group'] ?? 'Menu') : implode(' / ', array_filter([$here['group'], $here['label']]))) : 'Backoffice';
    // ?embed=1 → halaman dimuat di dalam modal (iframe): tanpa sidebar & header, hanya konten.
    $embed = request()->boolean('embed');
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head-start')
    <title>{{ $pageTitle }} · TOMI</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <script>
        // Tema dipasang sebelum halaman tampil supaya tidak berkedip.
        (function () {
            var t = null;
            try { t = localStorage.getItem('tomi-theme'); } catch (e) {}
            if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.dataset.theme = t;
        })();
    </script>
    @vite([trim($__env->yieldContent('ui-css', 'resources/css/backoffice.css')), 'resources/js/backoffice.js'])
    @stack('head')
    @if ($embed)
        {{-- Di dalam modal, judul & breadcrumb halaman sudah diwakili judul modal --}}
        <style>.page-title-box { display: none !important; }</style>
    @endif
</head>
<body class="font-sans" x-data="{ navOpen: false }">
@include('layout.partials.shell.icons')

@if ($embed)
<main style="padding: 16px;">
    @hasSection('shell-main')
        @yield('shell-main')
    @else
        @yield('content')
    @endif
</main>
@else
<div class="min-h-screen lg:grid lg:grid-cols-[244px_minmax(0,1fr)]">
    {{-- ===================== Sidebar ===================== --}}
    <div class="fixed inset-0 z-40 bg-black/30 lg:hidden" x-show="navOpen" x-transition.opacity x-cloak @click="navOpen = false"></div>
    <aside class="fixed inset-y-0 left-0 z-50 flex w-[260px] -translate-x-full flex-col border-r border-line bg-surface transition-transform duration-300 ease-out-soft lg:sticky lg:top-0 lg:h-screen lg:w-auto lg:translate-x-0"
           :class="navOpen && '!translate-x-0'" aria-label="Navigasi utama">
        <div class="flex items-center gap-2.5 px-[20px] pb-[12px] pt-[20px]">
            <img src="{{ asset('assets/images/tomi-logo.jpg') }}" alt="tomi" class="h-[22px] w-auto">
            <span class="border-l border-line pl-2.5 text-[11px] font-semibold uppercase tracking-[.08em] text-[color:var(--muted)]">Panel admin</span>
        </div>
        <nav class="flex-1 overflow-y-auto px-[12px] pb-6">
            @foreach ($nav as $item)
                @if ($item['type'] === 'label')
                    <div class="nav-label">{{ $item['label'] }}</div>
                @elseif ($item['type'] === 'link')
                    <a href="{{ route($item['route'], $item['params'] ?? []) }}" class="nav-item {{ ! empty($item['active']) ? 'active' : '' }}" @if (! empty($item['active'])) aria-current="page" @endif>
                        <svg class="i"><use href="#i-{{ $item['icon'] }}"/></svg>{{ $item['label'] }}
                    </a>
                @else
                    <div x-data="{ open: {{ $item['active'] ? 'true' : 'false' }} }">
                        <button type="button" class="nav-item w-full" @click="open = !open" :aria-expanded="open">
                            @if ($item['slot'])
                                <span class="mx-[5px] h-2 w-2 rounded-[3px]" style="background: var(--s{{ $item['slot'] }})"></span>
                            @else
                                <svg class="i"><use href="#i-{{ $item['icon'] }}"/></svg>
                            @endif
                            <span class="flex-1 text-left">{{ $item['label'] }}</span>
                            <svg class="i !h-4 !w-4 text-[color:var(--muted)] transition-transform duration-200" :class="open && 'rotate-90'"><use href="#i-chevron"/></svg>
                        </button>
                        <div x-show="open" x-transition.opacity.duration.200ms {{ $item['active'] ? '' : 'x-cloak' }} class="pb-[4px]">
                            @if ($item['dashboard'] && Route::has($item['dashboard']['route']))
                                <a href="{{ route($item['dashboard']['route'], $item['dashboard']['params']) }}"
                                   class="nav-sub {{ ! empty($item['dashboardActive']) ? 'active' : '' }}" @if (! empty($item['dashboardActive'])) aria-current="page" @endif>Dasbor</a>
                            @endif
                            @foreach ($item['children'] as $child)
                                <a href="{{ route($child['route']) }}" class="nav-sub {{ ! empty($child['active']) ? 'active' : '' }}" @if (! empty($child['active'])) aria-current="page" @endif>{{ $child['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>
    </aside>

    {{-- ===================== Konten ===================== --}}
    <div class="flex min-w-0 flex-col">
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-line bg-page/85 px-[16px] py-[12px] backdrop-blur-md sm:px-7">
            <button type="button" class="grid h-9 w-9 place-items-center rounded-[8px] border-[1px] border-line bg-surface text-ink-2 lg:hidden" @click="navOpen = true" aria-label="Buka menu">
                <svg class="i"><use href="#i-menu"/></svg>
            </button>
            <div class="min-w-0">
                <div class="truncate text-xs text-[color:var(--muted)]" id="page-crumbs">@hasSection('crumbs')@yield('crumbs')@else{{ $crumbs }}@endif</div>
                <h1 class="truncate text-lg font-semibold tracking-tight" id="page-title" data-from-nav="{{ $fromNav ? '1' : '0' }}">{{ $pageTitle }}</h1>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <button type="button" class="grid h-9 w-9 place-items-center rounded-[8px] border-[1px] border-line bg-surface text-ink-2 transition hover:text-ink"
                        x-data @click="$store.theme.toggle()" :aria-label="$store.theme.dark ? 'Pakai tema terang' : 'Pakai tema gelap'">
                    <svg class="i" x-show="!$store.theme.dark"><use href="#i-moon"/></svg>
                    <svg class="i" x-show="$store.theme.dark" x-cloak><use href="#i-sun"/></svg>
                </button>
                @if ($navUser)
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" class="flex items-center gap-2.5 rounded-[8px] px-1.5 py-[4px] transition hover:bg-surface-2" @click="open = !open" :aria-expanded="open">
                            <span class="grid h-8 w-8 place-items-center rounded-full bg-brand text-xs font-semibold text-white">{{ $initials }}</span>
                            <span class="hidden text-left sm:block">
                                <span class="block text-[13px] font-semibold leading-tight">{{ $navUser->username }}</span>
                                <span class="block text-[11px] text-[color:var(--muted)]">{{ $roleLabel }}</span>
                            </span>
                        </button>
                        <div x-show="open" x-transition.origin.top.right x-cloak class="panel absolute right-0 mt-[8px] w-48 p-1.5 shadow-[0_12px_32px_rgba(15,23,42,.14)]">
                            <a href="{{ route('admin.profile.index') }}" class="nav-item"><svg class="i"><use href="#i-gear"/></svg>Profil</a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="nav-item w-full"><svg class="i"><use href="#i-logout"/></svg>Keluar</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </header>

        <main class="flex-1 px-[16px] pb-12 pt-[20px] sm:px-7">
            @hasSection('shell-main')
                @yield('shell-main')
            @else
                @yield('content')
            @endif
        </main>
    </div>
</div>
@endif
<div class="tip" id="tip" role="tooltip"></div>
@stack('scripts')
</body>
</html>
