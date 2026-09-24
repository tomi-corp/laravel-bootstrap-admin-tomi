@extends('lender.layouts.auth.app')

@section('title', 'Reset Password Lender')

@section('content')
    <section class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl shadow-slate-950/20">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-sky-500 text-white">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 class="text-2xl font-semibold text-slate-950">Reset Password</h1>
            <p class="mt-1 text-sm text-sky-700">Buat password baru untuk akun lender.</p>
        </div>

        <form action="{{ route('lender.reset.action', $token) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-sky-800">Password Baru</label>
                <input type="password" name="password" class="w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20" required>
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-sky-800">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20" required>
            </div>

            <button type="submit" class="flex bg-sky-500 text-white hover:bg-sky-600 w-full items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold">
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Password
            </button>
        </form>
    </section>
@endsection
