<header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6">
    <div>
        <h2 class="text-lg font-semibold text-slate-800">
            @yield('title', 'Dashboard')
        </h2>
    </div>

    <div class="flex items-center gap-4">
        <button class="relative text-slate-600 hover:text-blue-600">
            <i class="fa-regular fa-bell text-xl"></i>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full px-1">
                3
            </span>
        </button>

        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                A
            </div>

            <div class="hidden md:block">
                <p class="text-sm font-semibold text-slate-800">
                    Admin
                </p>
                <p class="text-xs text-slate-500">
                    Administrator
                </p>
            </div>
        </div>
    </div>
</header>