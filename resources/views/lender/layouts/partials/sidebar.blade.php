@php
    $menus = [
        [
            'label' => 'Dashboard',
            'icon' => 'fa-solid fa-gauge-high',
            'route' => 'lender.dashboard',
            'active' => request()->routeIs('lender.dashboard'),
        ],
        [
            'label' => 'Summary',
            'icon' => 'fa-solid fa-chart-simple',
            'route' => 'lender.dashboard.summary',
            'active' => request()->routeIs('lender.dashboard.summary'),
        ],
    ];
@endphp

<aside
    id="lenderSidebar"
    class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-slate-200 bg-white transition-transform lg:translate-x-0"
>
    <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-5">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-950 text-white">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-950">TOMI Lender</p>
            <p class="text-xs text-slate-500">Funding Portal</p>
        </div>
    </div>

    <nav class="space-y-1 px-3 py-4">
        @foreach ($menus as $menu)
            <a
                href="{{ route($menu['route']) }}"
                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ $menu['active'] ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}"
            >
                <i class="{{ $menu['icon'] }} w-5 text-center"></i>
                <span>{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>

<div
    id="sidebarBackdrop"
    class="fixed inset-0 z-30 hidden bg-slate-950/40 lg:hidden"
></div>
