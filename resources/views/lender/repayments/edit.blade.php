@extends('lender.layouts.app')

@section('title', 'Edit Repayment')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-normal">Edit Repayment</h1>
            <p class="mt-1 text-sm text-white/90">Rp {{ number_format($repayment->amount_total, 0, ',', '.') }}</p>
        </div>
        <form action="{{ route('lender.repayments.update', $repayment) }}" method="POST" class="rounded-lg border border-primary-100 bg-white p-5 shadow-sm shadow-primary-950/10">
            @method('PUT')
            @include('lender.repayments._form')
        </form>
    </div>
@endsection
