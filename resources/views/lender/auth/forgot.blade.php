@extends('lender.layouts.auth.app')

@section('title', 'Lupa Password Lender')

@section('content')
    <section class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl shadow-slate-950/20">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-sky-500 text-white">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1 class="text-2xl font-semibold text-slate-950">Lupa Password</h1>
            <p class="mt-1 text-sm text-sky-700">Masukkan email akun lender Anda.</p>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-md border border-sky-100 bg-sky-50 px-3 py-2 text-sm text-sky-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-md border border-red-100 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('lender.forgot.action') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-sky-800">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20" required autofocus>
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="flex bg-sky-500 text-white hover:bg-sky-600 w-full items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold">
                <i class="fa-solid fa-paper-plane"></i>
                Kirim Link Reset
            </button>

            <a href="{{ route('lender.login') }}" class="block text-center text-sm font-medium text-sky-700 hover:text-sky-900">
                Kembali ke login
            </a>
        </form>
    </section>
@endsection
