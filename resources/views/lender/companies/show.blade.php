@extends('lender.layouts.app')

@section('title', 'Detail Perusahaan')
@section('page-title', 'List Perusahaan')
@section('page-subtitle', 'Detail data perusahaan')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Detail Perusahaan</h1>
                <p class="mt-1 text-sm text-white/90">{{ $company->nama }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('lender.companies.index') }}" class="rounded-md bg-primary-100 px-4 py-2 text-sm font-medium text-primary-700 hover:bg-primary-200">
                    Kembali
                </a>
                <a href="{{ route('lender.companies.edit', $company) }}" class="rounded-md bg-yellow-500 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-600">
                    Edit
                </a>
            </div>
        </div>

        <section class="rounded-lg border border-primary-100 bg-white p-5 text-primary-950 shadow-sm shadow-primary-950/10">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-sm text-primary-700">Kode Perusahaan</p>
                    <p class="mt-1 font-semibold">{{ $company->id_code_perusahaan }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Unique Code</p>
                    <p class="mt-1 font-semibold">{{ $company->unique_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Nama Perusahaan</p>
                    <p class="mt-1 font-semibold">{{ $company->nama }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Lender</p>
                    <p class="mt-1 font-semibold">{{ optional($company->lender)->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">PIC Perusahaan</p>
                    <p class="mt-1 font-semibold">{{ $company->pic_perusahaan }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">PIC Internal</p>
                    <p class="mt-1 font-semibold">{{ $company->pic_internal }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Email</p>
                    <p class="mt-1 font-semibold">{{ $company->email_perusahaan ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Telepon</p>
                    <p class="mt-1 font-semibold">{{ $company->telp }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Nomor PKS</p>
                    <p class="mt-1 font-semibold">{{ $company->nomor_pks }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Status</p>
                    <p class="mt-1 font-semibold">{{ $company->status === 'active' ? 'Aktif' : 'Nonaktif' }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Provinsi</p>
                    <p class="mt-1 font-semibold">{{ optional($company->provinsi)->provinsi ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Kabupaten/Kota</p>
                    <p class="mt-1 font-semibold">{{ optional($company->kabupaten)->kabupaten ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Kecamatan</p>
                    <p class="mt-1 font-semibold">{{ optional($company->kecamatan)->kecamatan ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-primary-700">Desa/Kelurahan</p>
                    <p class="mt-1 font-semibold">{{ optional($company->desa)->desa ?? '-' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-primary-700">Alamat Lengkap</p>
                    <p class="mt-1 font-semibold">{{ $company->alamat_lengkap }}</p>
                </div>
            </div>
        </section>
    </div>
@endsection

