@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Register</h1>
            <p class="text-sm text-slate-500 mt-1">Buat akun admin baru</p>
        </div>

        <form action="{{ route('register.process') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Nama Lengkap
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:ring focus:ring-blue-100 outline-none"
                    placeholder="Masukkan nama lengkap">

                @error('name')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Username
                </label>
                <input type="text" name="username" value="{{ old('username') }}"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:ring focus:ring-blue-100 outline-none"
                    placeholder="Masukkan username">

                @error('username')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Email
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:ring focus:ring-blue-100 outline-none"
                    placeholder="Masukkan email">

                @error('email')
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

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Konfirmasi Password
                </label>
                <input type="password" name="password_confirmation"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:ring focus:ring-blue-100 outline-none"
                    placeholder="Ulangi password">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition">
                Register
            </button>
        </form>

        <p class="text-sm text-center text-slate-500 mt-5">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">
                Login
            </a>
        </p>

    </div>
</div>
@endsection