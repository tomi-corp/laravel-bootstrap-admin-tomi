@extends('partner.layouts.app')

@section('title', 'Perusahaan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            Data Perusahaan
        </h1>
        <p class="text-sm text-slate-500">
            Daftar perusahaan E-Kasbon
        </p>
    </div>

    {{-- Table Card --}}
    <div class="bg-white border border-slate-100 rounded-xl shadow-sm overflow-hidden">

        {{-- Table --}}
        <div class="p-4">

            <div class="overflow-x-auto">

                <table class="min-w-full text-sm text-left whitespace-nowrap" id="companyTable">

                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Nama Perusahaan</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Telepon</th>
                            <th class="px-6 py-3">Alamat</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        <tr>
                            <td class="px-6 py-4">1</td>
                            <td class="px-6 py-4 font-medium text-slate-800">PT Maju Jaya</td>
                            <td class="px-6 py-4">info@majujaya.com</td>
                            <td class="px-6 py-4">08123456789</td>
                            <td class="px-6 py-4">Jakarta</td>

                            {{-- ACTION ICONS --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Konfigurasi --}}
                                    <a href="{{ route('partners.configuration.index') }}"
                                        class="p-2 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-600 hover:text-blue-600 transition"
                                        title="Konfigurasi">
                                        <i class="fa-solid fa-gear"></i>
                                    </a>

                                    {{-- Karyawan --}}
                                    <a href="{{ route('partners.employees.index') }}"
                                        class="p-2 rounded-lg bg-slate-100 hover:bg-green-100 text-slate-600 hover:text-green-600 transition"
                                        title="Karyawan">
                                        <i class="fa-solid fa-users"></i>
                                    </a>

                                    {{-- Pengajuan Pinjaman --}}
                                    <a href="{{ route('partners.loan-applications.index') }}"
                                        class="p-2 rounded-lg bg-slate-100 hover:bg-orange-100 text-slate-600 hover:text-orange-500 transition"
                                        title="Pengajuan Pinjaman">
                                        <i class="fa-solid fa-hand-holding-dollar"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4">2</td>
                            <td class="px-6 py-4 font-medium text-slate-800">PT Sinar Abadi</td>
                            <td class="px-6 py-4">contact@sinarabadi.com</td>
                            <td class="px-6 py-4">08129876543</td>
                            <td class="px-6 py-4">Bandung</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Konfigurasi --}}
                                    <a href="{{ route('partners.configuration.index') }}"
                                        class="p-2 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-600 hover:text-blue-600 transition"
                                        title="Konfigurasi">
                                        <i class="fa-solid fa-gear"></i>
                                    </a>

                                    {{-- Karyawan --}}
                                    <a href="{{ route('partners.employees.index') }}"
                                        class="p-2 rounded-lg bg-slate-100 hover:bg-green-100 text-slate-600 hover:text-green-600 transition"
                                        title="Karyawan">
                                        <i class="fa-solid fa-users"></i>
                                    </a>

                                    {{-- Pengajuan Pinjaman --}}
                                    <a href="{{ route('partners.loan-applications.index') }}"
                                        class="p-2 rounded-lg bg-slate-100 hover:bg-orange-100 text-slate-600 hover:text-orange-500 transition"
                                        title="Pengajuan Pinjaman">
                                        <i class="fa-solid fa-hand-holding-dollar"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
@endsection

{{-- DataTables --}}
@push('scripts')
<script>
    $(document).ready(function () {
        $('#companyTable').DataTable({
            responsive: true,
            autoWidth: false
        });
    });
</script>
@endpush

{{-- Styling --}}
@push('styles')
<style>
    table.dataTable {
        width: 100% !important;
        margin: 0 !important;
    }

    .dataTables_wrapper {
        padding: 12px;
    }

    .dataTables_filter,
    .dataTables_length {
        margin-bottom: 10px;
    }

    .dataTables_paginate {
        margin-top: 10px;
    }
</style>
@endpush