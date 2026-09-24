<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login Lender')</title>
    @include('lender.layouts.partials.head-assets')
</head>
<body class="min-h-screen bg-black text-slate-950 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        @yield('content')
    </main>

    @include('lender.layouts.partials.scripts')
    @include('lender.layouts.partials.flash')
</body>
</html>
