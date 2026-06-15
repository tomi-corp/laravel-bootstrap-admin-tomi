@extends('partner.layouts.auth')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Login</h1>
        <p class="text-sm text-slate-500 mt-1">
            Masuk ke dashboard partner
        </p>
    </div>

    <form action="{{ route('partners.login.process') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Email / Username
            </label>
            <input type="text" name="identity" value="{{ old('identity') }}"
                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:ring focus:ring-blue-100 outline-none"
                placeholder="Masukkan email atau username">

            @error('identity')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Password
            </label>
            <input type="password" name="password"
                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:ring focus:ring-blue-100 outline-none"
                placeholder="Masukkan password">

            @error('password')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember"
                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">

                <span class="text-sm text-slate-600">
                    Ingat Saya
                </span>
            </label>

            <a href="{{ route('partners.password.request') }}"
                class="text-sm text-blue-600 hover:text-blue-700 hover:underline">
                Lupa Password?
            </a>
        </div>

        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition">
            Login
        </button>
    </form>

</div>
@endsection