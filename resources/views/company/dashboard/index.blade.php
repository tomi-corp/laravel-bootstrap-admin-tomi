@extends('company.layouts.app')

@section('title', 'Dashboard Company')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-normal text-slate-950">Dashboard Company</h1>
        <p class="mt-1 text-sm text-slate-500">Ringkasan aktivitas dan informasi perusahaan.</p>
    </div>

    <section class="grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Total Karyawan</p>
                <i class="fa-solid fa-users text-black"></i>
            </div>
            <p class="mt-4 text-3xl font-semibold text-slate-950">0</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Pengajuan Aktif</p>
                <i class="fa-solid fa-file-invoice-dollar text-black"></i>
            </div>
            <p class="mt-4 text-3xl font-semibold text-slate-950">0</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Status Akun</p>
                <i class="fa-solid fa-circle-check text-black"></i>
            </div>
            <span class="mt-4 inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-900">
                Aktif
            </span>
        </div>
    </section>

    <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-950">Aktivitas Terbaru</h2>
                <p class="mt-1 text-sm text-slate-500">Belum ada aktivitas perusahaan.</p>
            </div>
            <i class="fa-solid fa-clock-rotate-left text-black"></i>
        </div>
    </section>
@endsection
