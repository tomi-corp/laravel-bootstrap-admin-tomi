@extends('lender.layouts.auth.app')

@section('title', 'Lupa Password Lender')

@section('content')
    <section class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl shadow-primary-950/20">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary text-white">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1 class="text-2xl font-semibold text-primary-950">Lupa Password</h1>
            <p class="mt-1 text-sm text-primary-700">Masukkan email akun lender Anda.</p>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-md border border-primary-100 bg-primary-50 px-3 py-2 text-sm text-primary-700">
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
                <label class="mb-1 block text-sm font-medium text-primary-800">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border border-primary-100 px-3 py-2 text-primary-950 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" required autofocus>
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="auth-primary-button flex w-full items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold">
                <i class="fa-solid fa-paper-plane"></i>
                Kirim Link Reset
            </button>

            <a href="{{ route('lender.login') }}" class="block text-center text-sm font-medium text-primary-700 hover:text-primary-900">
                Kembali ke login
            </a>
        </form>
    </section>
@endsection
