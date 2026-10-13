@extends('partner.layouts.app')

@section('title', 'Konfigurasi Perusahaan')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                KSP Konfigurasi {{ $list_perusahaan->nama ?? '' }}
            </h1>
            <p class="text-sm text-slate-500">
                Pengaturan limit, biaya, dan tenor perusahaan
            </p>
        </div>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT FORM --}}
        <div class="bg-white border border-slate-100 rounded-xl shadow-sm p-5 space-y-4">

            <h2 class="font-semibold text-slate-700">Konfigurasi Pinjaman</h2>

            {{-- IURAN --}}
            <div>
                <label class="text-sm text-slate-600">Iuran Anggota</label>
                <div class="flex items-center mt-1">
                    <span class="px-3 py-2 bg-slate-100 border border-slate-200 rounded-l-lg text-sm">Rp</span>
                    <input type="number" id="iuran_anggota"
                        class="w-full border border-slate-200 rounded-r-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100"
                        value="{{ $list_perusahaan->amount_pinjaman->iuran_anggota ?? 0 }}">
                </div>
                <small class="text-red-500 text-xs" id="errIuran_anggota"></small>
            </div>

            {{-- MIN --}}
            <div>
                <label class="text-sm text-slate-600">Min Plafon</label>
                <div class="flex items-center mt-1">
                    <span class="px-3 py-2 bg-slate-100 border border-slate-200 rounded-l-lg text-sm">Rp</span>
                    <input type="number" id="minimal_pinjaman"
                        class="w-full border border-slate-200 rounded-r-lg px-3 py-2 text-sm"
                        value="{{ $list_perusahaan->amount_pinjaman->minimal ?? 0 }}">
                </div>
            </div>

            {{-- MAX VIEW --}}
            <div>
                <label class="text-sm text-slate-600">Max Plafon</label>
                <input type="text" id="maximal_pinjaman_view" disabled
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 bg-slate-50 text-sm"
                    value="{{ number_format($max_plafon ?? 0, 0, ',', '.') }}">

                <input type="hidden" id="maximal_pinjaman">
            </div>

            {{-- DSR --}}
            <div>
                <label class="text-sm text-slate-600">DSR Maksimal (%)</label>
                <div class="flex items-center mt-1">
                    <input type="number" id="maximal_persentase"
                        class="w-full border border-slate-200 rounded-l-lg px-3 py-2 text-sm"
                        value="{{ $list_perusahaan->amount_pinjaman->maximal_persentase ?? 0 }}">
                    <span class="px-3 py-2 bg-slate-100 border border-slate-200 rounded-r-lg text-sm">%</span>
                </div>
            </div>

            <button onclick="amountPinjamanSave()"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-semibold">
                Save
            </button>

        </div>

        {{-- RIGHT TABLE --}}
        <div class="lg:col-span-2 bg-white border border-slate-100 rounded-xl shadow-sm p-5">

            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-slate-700">Konfigurasi Tenor</h2>

                <button data-modal-target="modalAdd"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    + Tambah
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="tableTenorPinjaman" class="w-full text-sm text-left border border-slate-100 rounded-lg">

                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3">Tenor</th>
                            <th class="px-4 py-3">Biaya Bunga</th>
                            <th class="px-4 py-3">Biaya Asuransi</th>
                            <th class="px-4 py-3">Biaya Provisi</th>
                            <th class="px-4 py-3">Biaya Layanan</th>
                            <th class="px-4 py-3">Biaya Admin</th>
                            <th class="px-4 py-3">Lender</th>
                            <th class="px-4 py-3">Tomi</th>
                            <th class="px-4 py-3">Ref 1</th>
                            <th class="px-4 py-3">Ref 2</th>
                            <th class="px-4 py-3">Maximal Nominal</th>
                            <th class="px-4 py-3">Minimal Nominal</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>


{{-- MODAL ADD (TAILWIND) --}}
<div id="modalAdd" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg p-6">

        <div class="flex justify-between mb-4">
            <h2 class="font-semibold text-lg">Tambah Konfigurasi</h2>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')">✕</button>
        </div>

        <div class="grid grid-cols-1 gap-4">

            <input id="tenor" class="border p-2 rounded-lg text-sm" placeholder="Tenor">
            <input id="rate" class="border p-2 rounded-lg text-sm" placeholder="Biaya Bunga">

            <input id="asuransi" class="border p-2 rounded-lg text-sm" placeholder="Biaya Asuransi">
            <input id="provisi" class="border p-2 rounded-lg text-sm" placeholder="Biaya Provisi">
            <input id="biayalayanan" class="border p-2 rounded-lg text-sm" placeholder="Biaya Layanan">
            <input id="biayaadmin" class="border p-2 rounded-lg text-sm" placeholder="Biaya Admin">
            <input id="ksp" class="border p-2 rounded-lg text-sm" placeholder="Lender">
            <input id="feeTomi" class="border p-2 rounded-lg text-sm" placeholder="Tomi">
            <input id="profesional_fee_1" class="border p-2 rounded-lg text-sm" placeholder="Ref 1">
            <input id="profesional_fee_2" class="border p-2 rounded-lg text-sm" placeholder="Ref 2">
            <div class="grid grid-cols-2 gap-4">
                <input id="maximalNominalTenor" class="border p-2 rounded-lg text-sm" placeholder="Maximal Nominal">
                <input id="minimalNominalTenor" class="border p-2 rounded-lg text-sm" placeholder="Minimal Nominal">
            </div>

        </div>

        <div class="mt-5 flex justify-end gap-2">
            <button class="px-4 py-2 rounded-lg bg-slate-200"
                onclick="document.getElementById('modalAdd').classList.add('hidden')">
                Cancel
            </button>

            <button onclick="buttonSubmit()" class="px-4 py-2 rounded-lg bg-blue-600 text-white">
                Save
            </button>
        </div>

    </div>
</div>

@endsection


@push('scripts')

<script>
    // toggle modal simple tailwind
    document.addEventListener('click', function (e) {
        if (e.target.dataset.modalTarget === 'modalAdd') {
            document.getElementById('modalAdd').classList.remove('hidden')
        }
    })
</script>

@endpush
