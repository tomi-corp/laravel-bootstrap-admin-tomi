<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>TOMI - Internal Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 text-center">
                <div class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-md bg-blue-600 text-white">
                    <span class="text-xl font-bold">T</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-950">TOMI Internal</h1>
                <p class="mt-1 text-sm text-slate-500">Masuk menggunakan akun admin internal.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('internal.login.action') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="identity" class="mb-2 block text-sm font-semibold text-slate-700">Username atau Email</label>
                    <input
                        id="identity"
                        name="identity"
                        value="{{ old('identity') }}"
                        required
                        autofocus
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        placeholder="username atau email"
                    >
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        placeholder="password"
                    >
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    Remember me
                </label>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    Log In
                </button>
            </form>
        </section>
    </main>
</body>

</html>
