@extends('lender.layouts.auth.app')

@section('title', 'Reset Password Lender')

@section('content')
    <section class="w-full max-w-md rounded-lg bg-white p-6 shadow-md">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-600 text-white">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 class="text-2xl font-semibold text-slate-950">Reset Password</h1>
            <p class="mt-1 text-sm text-slate-700">Buat password baru untuk akun lender.</p>
        </div>

        <form action="{{ route('lender.reset.action', $token) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-900">Password Baru</label>
                <input type="password" name="password" class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" required>
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-900">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" required>
            </div>

            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-md border border-transparent bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Password
            </button>
        </form>
    </section>
@endsection
