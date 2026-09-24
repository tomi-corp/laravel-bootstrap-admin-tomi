@extends('lender.layouts.app')

@section('title', 'Konfigurasi TomiDana')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-normal text-slate-950">Konfigurasi TomiDana</h1>
            <p class="mt-1 text-sm text-slate-600">Pilih perusahaan untuk mengatur konfigurasi tenor pinjaman.</p>
        </div>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white text-slate-950 shadow-sm shadow-slate-950/10">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Perusahaan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Jumlah Konfigurasi</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($companies as $company)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-950">{{ $company->nama }}</div>
                                    <div class="mt-1 text-xs text-slate-500">ID Perusahaan: {{ $company->id }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-700">
                                    {{ $company->loan_tenors_count }} konfigurasi
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('lender.configurations.index', ['company_id' => $company->id]) }}" class="inline-flex items-center gap-2 rounded-md bg-black px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800">
                                        <i class="fa-solid fa-sliders"></i>
                                        Konfigurasi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-10 text-center text-sm text-slate-500">
                                    Belum ada perusahaan yang terdaftar untuk lender ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
