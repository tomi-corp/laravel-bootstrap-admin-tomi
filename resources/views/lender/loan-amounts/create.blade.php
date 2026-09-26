@extends('lender.layouts.app')

@section('title', 'Tambah Konfigurasi DSR')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-normal">Tambah Konfigurasi DSR</h1>
            <p class="mt-1 text-sm text-slate-600">Lengkapi batas Konfigurasi DSR perusahaan.</p>
        </div>

        <form action="{{ route('lender.loan-amounts.store') }}" method="POST" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/10">
            @include('lender.loan-amounts._form')
        </form>
    </div>
@endsection
