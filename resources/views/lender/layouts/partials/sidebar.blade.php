@php
    $menus = [
        [
            'label' => 'Dashboard',
            'icon' => 'fa-solid fa-gauge-high',
            'route' => 'lender.dashboard',
            'active' => request()->routeIs('lender.dashboard'),
            'development' => false,
        ],
        [
            'label' => 'TomiDana',
            'icon' => 'fa-solid fa-hand-holding-dollar',
            'active' => request()->routeIs('lender.tomi-dana.*'),
            'children' => [
                [
                    'label' => 'Disbursement',
                    'icon' => 'fa-solid fa-money-bill-transfer',
                    'route' => 'lender.tomi-dana.disbursement',
                    'active' => request()->routeIs('lender.tomi-dana.disbursement'),
                ],
                [
                    'label' => 'Repayment',
                    'icon' => 'fa-solid fa-rotate-left',
                    'route' => 'lender.tomi-dana.repayment',
                    'active' => request()->routeIs('lender.tomi-dana.repayment'),
                ],
            ],
        ],
        [
            'label' => 'TomiDana',
            'suffix' => '+',
            'icon' => 'fa-solid fa-circle-plus',
            'development' => true,
        ],
        [
            'label' => 'TomiGawai',
            'icon' => 'fa-solid fa-mobile-screen-button',
            'development' => true,
        ],
        [
            'label' => 'TomiEmas',
            'icon' => 'fa-solid fa-coins',
            'development' => true,
        ],
        [
            'label' => 'TomiGaji',
            'icon' => 'fa-solid fa-money-check-dollar',
            'development' => true,
        ],
        [
            'label' => 'TomiInvoice',
            'icon' => 'fa-solid fa-file-invoice-dollar',
            'development' => true,
        ],
        [
            'label' => 'PPOB',
            'icon' => 'fa-solid fa-receipt',
            'development' => true,
        ],
    ];
@endphp

<aside
    id="lenderSidebar"
    class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-primary bg-primary transition-transform lg:translate-x-0"
    style="background-color: #0B8FE8; border-color: #0B8FE8;"
>
    <div class="flex h-16 items-center gap-3 border-b border-white/20 px-5">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 text-white shadow-sm shadow-primary-950/20">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-white">TOMI Lender</p>
            <p class="text-xs text-white/80">Portal Pendanaan</p>
        </div>
    </div>

    <nav class="space-y-1 px-3 py-4">
        @foreach ($menus as $menu)
            @if (isset($menu['children']))
                <div>
                    <div class="flex items-center justify-between rounded-md px-3 py-2 text-sm font-medium text-white">
                        <span class="flex items-center gap-3">
                            <i class="{{ $menu['icon'] }} w-5 text-center"></i>
                            <span>{{ $menu['label'] }}</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-white/80"></i>
                    </div>
                    <div class="mt-1 space-y-1 pl-8">
                        @foreach ($menu['children'] as $child)
                            <a
                                href="{{ route($child['route']) }}"
                                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ $child['active'] ? 'bg-white/25 text-white shadow-sm shadow-primary-950/10' : 'text-white/90 hover:bg-white/15 hover:text-white' }}"
                            >
                                <i class="{{ $child['icon'] }} w-4 text-center"></i>
                                <span>{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a
                    href="{{ ($menu['development'] ?? false) ? '#' : route($menu['route']) }}"
                    class="{{ ($menu['development'] ?? false) ? 'js-development-menu' : '' }} flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ ($menu['active'] ?? false) ? 'bg-white/25 text-white shadow-sm shadow-primary-950/10' : 'text-white hover:bg-white/15 hover:text-white' }}"
                    data-menu-label="{{ $menu['label'] }}{{ $menu['suffix'] ?? '' }}"
                >
                    <i class="{{ $menu['icon'] }} w-5 text-center"></i>
                    <span>
                        {{ $menu['label'] }}@isset($menu['suffix'])<sup class="text-[10px] leading-none">{{ $menu['suffix'] }}</sup>@endisset
                    </span>
                </a>
            @endif
        @endforeach
    </nav>
</aside>

<div
    id="sidebarBackdrop"
    class="fixed inset-0 z-30 hidden bg-primary-950/40 lg:hidden"
></div>

@push('scripts')
    <script>
        $('.js-development-menu').on('click', function (event) {
            event.preventDefault();

            AppAlert.fire({
                icon: 'info',
                title: 'Sedang dalam pengembangan',
                text: `${$(this).data('menu-label')} belum tersedia saat ini.`,
                confirmButtonColor: '#0B8FE8',
                confirmButtonText: 'Mengerti',
            });
        });
    </script>
@endpush
