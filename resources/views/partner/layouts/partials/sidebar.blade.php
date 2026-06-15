<aside class="w-64 bg-white border-r border-slate-200 min-h-screen hidden md:block">

    {{-- Brand --}}
    <div class="h-16 flex items-center px-6 border-b border-slate-200">
        <h1 class="text-xl font-bold text-blue-600">
            E-Office
        </h1>
    </div>

    {{-- Menu --}}
    <nav class="p-4 space-y-2">

        {{-- Dashboard --}}
        <a href="{{ route('partners.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-600">
            <i class="fa-solid fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>

        {{-- E-Kasbon (Dropdown) --}}
        <div x-data="{ open: false }" class="space-y-1">

            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-600">

                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-hand-holding-dollar w-5"></i>
                    <span>E-Kasbon</span>
                </div>

                <i class="fa-solid fa-chevron-down text-xs transition" :class="{ 'rotate-180': open }"></i>
            </button>

            {{-- Submenu --}}
            <div x-show="open" x-transition class="pl-10 space-y-1">

                <a href="{{ route('partners.company.index') }}"
                    class="block py-2 text-sm text-slate-600 hover:text-blue-600">
                    Perusahaan
                </a>

                <a href="#" class="block py-2 text-sm text-slate-600 hover:text-blue-600">
                    Lender
                </a>

                <a href="#" class="block py-2 text-sm text-slate-600 hover:text-blue-600">
                    Cost
                </a>

                <a href="#" class="block py-2 text-sm text-slate-600 hover:text-blue-600">
                    Generate Invoice
                </a>

            </div>
        </div>

        {{-- E-Gaji --}}
        <a href="#"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-600">
            <i class="fa-solid fa-money-bill-wave w-5"></i>
            <span>E-Gaji</span>
        </a>

        {{-- E-Gawai --}}
        <a href="#"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-600">
            <i class="fa-solid fa-mobile-screen-button w-5"></i>
            <span>E-Gawai</span>
        </a>

        {{-- Divider --}}
        <div class="my-3 border-t border-slate-200"></div>

        {{-- Settings --}}
        <a href="#"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-700 hover:bg-blue-50 hover:text-blue-600">
            <i class="fa-solid fa-gear w-5"></i>
            <span>Settings</span>
        </a>

    </nav>
</aside>