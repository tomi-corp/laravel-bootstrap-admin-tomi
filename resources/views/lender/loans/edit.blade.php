@extends('lender.layouts.app')

@section('title', 'Edit Pinjaman')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-normal">Edit Pinjaman</h1>
            <p class="mt-1 text-sm text-white/90">{{ $loan->id_pinjaman }}</p>
        </div>
        <form action="{{ route('lender.loans.update', $loan) }}" method="POST" class="rounded-lg border border-primary-100 bg-white p-5 shadow-sm shadow-primary-950/10">
            @method('PUT')
            @include('lender.loans._form')
        </form>
    </div>
@endsection
