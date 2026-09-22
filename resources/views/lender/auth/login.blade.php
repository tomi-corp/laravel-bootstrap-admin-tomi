@extends('lender.layouts.auth.app')

@section('title', 'Login Lender')

@section('content')
    <section class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl shadow-primary-950/20">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary text-white">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <h1 class="text-2xl font-semibold text-primary-950">Login Lender</h1>
            <p class="mt-1 text-sm text-primary-700">Masuk ke portal lender TOMI.</p>
        </div>

        @if (session('error'))
            <div class="mb-4 rounded-md border border-red-100 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('lender.login.action') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-primary-800">Username atau Email</label>
                <input
                    type="text"
                    name="identity"
                    value="{{ old('identity') }}"
                    class="w-full rounded-md border border-primary-100 px-3 py-2 text-primary-950 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                    required
                    autofocus
                >
                @error('identity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-primary-800">Password</label>
                <input
                    type="password"
                    name="password"
                    class="w-full rounded-md border border-primary-100 px-3 py-2 text-primary-950 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                    required
                >
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-primary-800">
                <input type="checkbox" name="remember" class="rounded border-primary-200 text-primary focus:ring-primary">
                Ingat saya
            </label>

            <button type="submit" class="auth-primary-button flex w-full items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold">
                <i class="fa-solid fa-right-to-bracket"></i>
                Masuk
            </button>

            <a href="{{ route('lender.forgot') }}" class="block text-center text-sm font-medium text-primary-700 hover:text-primary-900">
                Lupa password?
            </a>
        </form>
    </section>
@endsection
