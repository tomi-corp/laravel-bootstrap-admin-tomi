@extends('lender.layouts.app')

@section('title', 'Tambah Perusahaan')
@section('page-title', 'List Perusahaan')
@section('page-subtitle', 'Tambah data perusahaan')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-normal">Tambah Perusahaan</h1>
            <p class="mt-1 text-sm text-white/90">Lengkapi data utama perusahaan.</p>
        </div>

        <form action="{{ route('lender.companies.store') }}" method="POST" class="rounded-lg border border-primary-100 bg-white p-5 shadow-sm shadow-primary-950/10">
            @include('lender.companies._form')
        </form>
    </div>
@endsection

