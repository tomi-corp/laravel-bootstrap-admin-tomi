@extends('lender.layouts.app')

@section('title', 'Disbursement TomiDana')
@section('page-title', 'TomiDana')
@section('page-subtitle', 'Monitoring pencairan dana')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-normal">Disbursement</h1>
            <p class="mt-1 text-sm text-slate-600">Pantau proses pencairan dana TomiDana.</p>
        </div>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-700">Total Lender</p>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['total_lenders']) }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-700">Dalam Proses</p>
                <p class="mt-4 text-3xl font-semibold text-slate-950">0</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-700">Selesai</p>
                <p class="mt-4 text-3xl font-semibold text-slate-950">0</p>
            </div>
        </section>
    </div>
@endsection

