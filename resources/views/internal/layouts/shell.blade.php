{{--
    Kerangka panel internal (route internal.*). Style disalin dari kerangka admin (layout.shell),
    tapi menu sidebar ditulis sendiri di sini. Halaman native extends layout ini langsung;
    halaman lama (Bootstrap/DataTables) lewat internal.layouts.app.
--}}
@php
    $navUser = auth('admin')->user();
    $roleLabel = ['master' => 'Admin master', 'admin' => 'Admin'][$navUser?->type] ?? 'Admin';
    $initials = strtoupper(substr($navUser?->username ?? 'A', 0, 2));

    // Menu sidebar. `active` = pola nama route yang menandai menu ini (halaman turunan ikut menandai induknya).
    $menu = [
        ['label' => 'Dasbor', 'icon' => 'home', 'route' => 'internal.dashboard', 'active' => ['internal.dashboard', 'internal.dashboard.page']],
        ['section' => 'Produk'],
        ['label' => 'Tomi Dana', 'slot' => 1, 'children' => [
            ['label' => 'Daftar Perusahaan', 'route' => 'internal.tomi-dana.companies', 'active' => [
                'internal.tomi-dana.companies*', 'internal.tomi-dana.configuration*', 'internal.tomi-dana.applications*', 'internal.tomi-dana.employees*',
            ]],
            ['label' => 'Pencairan Dana', 'route' => 'internal.tomi-dana.disbursement', 'active' => ['internal.tomi-dana.disbursement*']],
            ['label' => 'Pembayaran Tagihan', 'route' => 'internal.tomi-dana.repayment', 'active' => ['internal.tomi-dana.repayment*']],
            ['label' => 'Biaya Aplikasi', 'route' => 'internal.tomi-dana.invoice-fee', 'active' => ['internal.tomi-dana.invoice-fee*']],
            ['label' => 'Buat Tagihan', 'route' => 'internal.tomi-dana.generate-invoice', 'active' => ['internal.tomi-dana.generate-invoice*']],
        ]],
        // Produk yang baru punya satu halaman: tampil sebagai menu tunggal dengan titik warna produk.
        ['label' => 'Tomi Dana Plus', 'slot' => 1, 'route' => 'internal.tomi-dana-plus', 'active' => ['internal.tomi-dana-plus*']],
        ['label' => 'TomiGaji', 'slot' => 2, 'route' => 'internal.tomi-gaji', 'active' => ['internal.tomi-gaji*']],
        ['label' => 'TomiInvoice', 'slot' => 3, 'route' => 'internal.tomi-invoice', 'active' => ['internal.tomi-invoice*']],
    ];

    // Menu yang sedang dibuka → judul & breadcrumb header. Halaman menu (route persis) memakai label menu;
    // halaman turunan memakai judul halamannya sendiri dengan breadcrumb menu induknya.
    $current = null;
    foreach ($menu as $i => $item) {
        foreach (isset($item['children']) ? $item['children'] : (isset($item['route']) ? [$item] : []) as $j => $link) {
            if (request()->routeIs(...$link['active'])) {
                $current = ['group' => isset($item['children']) ? $item['label'] : null, 'label' => $link['label'], 'exact' => request()->routeIs($link['route'])];
                if (isset($item['children'])) {
                    $menu[$i]['open'] = true;
                    $menu[$i]['children'][$j]['current'] = true;
                } else {
                    $menu[$i]['current'] = true;
                }
            }
        }
    }

    $fromNav = $current && $current['exact'];
    $pageTitle = $__env->hasSection('page-title') ? trim($__env->yieldContent('page-title'))
        : ($fromNav ? $current['label'] : trim($__env->yieldContent('title', 'Internal')));
    $crumbs = $current
        ? ($fromNav ? ($current['group'] ?? 'Internal') : implode(' / ', array_filter([$current['group'], $current['label']])))
        : 'Internal';
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
    <title>{{ $pageTitle }} · TOMI Internal</title>
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
            <span class="border-l border-line pl-2.5 text-[11px] font-semibold uppercase tracking-[.08em] text-[color:var(--muted)]">Panel internal</span>
        </div>
        <nav class="flex-1 overflow-y-auto px-[12px] pb-6">
            @foreach ($menu as $item)
                @if (isset($item['section']))
                    <div class="nav-label">{{ $item['section'] }}</div>
                @elseif (isset($item['children']))
                    @php $open = ! empty($item['open']); @endphp
                    <div x-data="{ open: {{ $open ? 'true' : 'false' }} }">
                        <button type="button" class="nav-item w-full" @click="open = !open" :aria-expanded="open">
                            <span class="mx-[5px] h-2 w-2 rounded-[3px]" style="background: var(--s{{ $item['slot'] }})"></span>
                            <span class="flex-1 text-left">{{ $item['label'] }}</span>
                            <svg class="i !h-4 !w-4 text-[color:var(--muted)] transition-transform duration-200" :class="open && 'rotate-90'"><use href="#i-chevron"/></svg>
                        </button>
                        <div x-show="open" x-transition.opacity.duration.200ms {{ $open ? '' : 'x-cloak' }} class="pb-[4px]">
                            @foreach ($item['children'] as $child)
                                <a href="{{ route($child['route']) }}" class="nav-sub {{ ! empty($child['current']) ? 'active' : '' }}" @if (! empty($child['current'])) aria-current="page" @endif>{{ $child['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ route($item['route']) }}" class="nav-item {{ ! empty($item['current']) ? 'active' : '' }}" @if (! empty($item['current'])) aria-current="page" @endif>
                        @if (isset($item['slot']))
                            <span class="mx-[5px] h-2 w-2 rounded-[3px]" style="background: var(--s{{ $item['slot'] }})"></span>
                        @else
                            <svg class="i"><use href="#i-{{ $item['icon'] }}"/></svg>
                        @endif
                        {{ $item['label'] }}
                    </a>
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
