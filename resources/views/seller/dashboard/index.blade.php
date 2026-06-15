@extends('partner.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            Dashboard Partner
        </h1>
        <p class="text-sm text-slate-500">
            Ringkasan aktivitas akun Anda
        </p>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition">
            <p class="text-sm text-slate-500">Total Order</p>
            <h2 class="text-2xl font-bold text-slate-800 mt-1">0</h2>
        </div>

        <div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition">
            <p class="text-sm text-slate-500">Pendapatan</p>
            <h2 class="text-2xl font-bold text-slate-800 mt-1">Rp 0</h2>
        </div>

        <div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition">
            <p class="text-sm text-slate-500">Status Akun</p>
            <span class="inline-block mt-2 px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">
                Aktif
            </span>
        </div>

    </div>

    {{-- Chart Section --}}
    <div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm">

        <h3 class="font-semibold text-slate-700 mb-4">
            Statistik Bulanan
        </h3>

        <canvas id="partnerChart" height="100"></canvas>

    </div>

</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('partnerChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Pendapatan',
                data: [0, 0, 0, 0, 0, 0],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        color: '#f1f5f9'
                    }
                }
            }
        }
    });
</script>
@endpush