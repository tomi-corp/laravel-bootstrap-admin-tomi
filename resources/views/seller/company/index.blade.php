@extends('partner.layouts.app')

@section('title', 'Perusahaan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Data Perusahaan
            </h1>
            <p class="text-sm text-slate-500">
                Kelola daftar perusahaan E-Kasbon
            </p>
        </div>

        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Tambah Perusahaan
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-slate-100 rounded-xl shadow-sm overflow-hidden">

        <div class="p-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-700">
                List Perusahaan
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left" id="companyTable">

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

                    {{-- Contoh data statis (nanti diganti loop DB) --}}
                    <tr>
                        <td class="px-6 py-4">1</td>
                        <td class="px-6 py-4 font-medium text-slate-800">PT Maju Jaya</td>
                        <td class="px-6 py-4">info@majujaya.com</td>
                        <td class="px-6 py-4">08123456789</td>
                        <td class="px-6 py-4">Jakarta</td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <button class="text-blue-600 hover:underline text-sm">Edit</button>
                            <button class="text-red-600 hover:underline text-sm">Hapus</button>
                        </td>
                    </tr>

                    <tr>
                        <td class="px-6 py-4">2</td>
                        <td class="px-6 py-4 font-medium text-slate-800">PT Sinar Abadi</td>
                        <td class="px-6 py-4">contact@sinarabadi.com</td>
                        <td class="px-6 py-4">08129876543</td>
                        <td class="px-6 py-4">Bandung</td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <button class="text-blue-600 hover:underline text-sm">Edit</button>
                            <button class="text-red-600 hover:underline text-sm">Hapus</button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#companyTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush