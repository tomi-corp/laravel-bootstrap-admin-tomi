@extends('lender.layouts.app')

@section('title', 'Edit Perusahaan')
@section('page-title', 'List Perusahaan')
@section('page-subtitle', 'Perbarui data perusahaan')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-normal">Edit Perusahaan</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $company->nama }}</p>
        </div>

        <form action="{{ route('lender.companies.update', $company) }}" method="POST" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/10">
            @method('PUT')
            @include('lender.companies._form')
        </form>
    </div>
@endsection

