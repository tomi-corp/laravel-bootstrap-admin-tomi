@extends('lender.layouts.app')

@section('title', 'Tambah Nominal Pinjaman')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-normal">Tambah Nominal Pinjaman</h1>
            <p class="mt-1 text-sm text-white/90">Lengkapi batas nominal pinjaman perusahaan.</p>
        </div>

        <form action="{{ route('lender.loan-amounts.store') }}" method="POST" class="rounded-lg border border-sky-100 bg-white p-5 shadow-sm shadow-slate-950/10">
            @include('lender.loan-amounts._form')
        </form>
    </div>
@endsection
