@extends('lender.layouts.auth.app')

@section('title', 'Login Lender')

@section('content')
    <section class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl shadow-slate-950/20">
        <div class="mb-6 text-center">
            <img src="{{ asset('assets/images/logo.jpg') }}" alt="TOMI" class="mx-auto mb-4 h-14 w-auto">
            <h1 class="text-2xl font-semibold text-slate-950">Login Lender</h1>
            <p class="mt-1 text-sm text-slate-700">Masuk ke portal lender TOMI.</p>
        </div>

        @if (session('error'))
            <div class="mb-4 rounded-md border border-red-100 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('lender.login.action') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-900">Username atau Email</label>
                <input
                    type="text"
                    name="identity"
                    value="{{ old('identity') }}"
                    class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20"
                    required
                    autofocus
                >
                @error('identity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-900">Password</label>
                <input
                    type="password"
                    name="password"
                    class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20"
                    required
                >
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between gap-3">
                <label class="flex items-center gap-2 text-sm text-slate-900">
                    <input type="checkbox" name="remember" class="rounded border-slate-200 text-black focus:ring-black">
                    Ingat saya
                </label>

                <a href="{{ route('lender.forgot') }}" class="text-sm font-medium text-slate-700 hover:text-black">
                    Lupa password?
                </a>
            </div>

            <button type="submit" class="flex bg-black text-white hover:bg-slate-800 w-full items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold">
                <i class="fa-solid fa-right-to-bracket"></i>
                Masuk
            </button>
        </form>
    </section>
@endsection
