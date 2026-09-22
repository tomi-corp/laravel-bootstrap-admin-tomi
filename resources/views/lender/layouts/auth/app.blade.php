<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login Lender')</title>
    @include('lender.layouts.partials.head-assets')
    <style>
        .auth-primary-button {
            background-color: #0B8FE8 !important;
            color: #ffffff !important;
        }

        .auth-primary-button:hover {
            background-color: #0878C4 !important;
        }
    </style>
</head>
<body class="min-h-screen bg-primary text-primary-950 antialiased" style="background-color: #0B8FE8;">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        @yield('content')
    </main>

    @include('lender.layouts.partials.scripts')
    @include('lender.layouts.partials.flash')
</body>
</html>
