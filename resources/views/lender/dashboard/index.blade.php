@extends('lender.layouts.app')

@section('title', 'Dashboard Lender')
@section('page-title', 'Dashboard Lender')
@section('page-subtitle', 'Ringkasan performa dan status lender')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Dashboard Lender</h1>
                <p class="mt-1 text-sm text-white/90">Pantau ringkasan data dan status operasional lender.</p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-md border border-primary-200 bg-white px-3 py-2 text-sm text-primary-800 shadow-sm">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <section class="mb-6 overflow-hidden rounded-lg border border-primary-100 bg-white shadow-sm shadow-primary-950/10">
            <img
                src="{{ asset('lender/banners/dashboard-tomi-gaji.png') }}"
                alt="Banner TOMI Gaji"
                class="block aspect-[2048/768] w-full object-cover"
            >
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-primary-100 bg-white p-5 shadow-sm shadow-primary-950/10">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-primary-700">Total Lender</p>
                    <i class="fa-solid fa-building-columns text-primary"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-primary-950">{{ number_format($summary['total_lenders']) }}</p>
            </div>
            <div class="rounded-lg border border-primary-100 bg-white p-5 shadow-sm shadow-primary-950/10">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-primary-700">Lender Aktif</p>
                    <i class="fa-solid fa-circle-check text-primary"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-primary-950">{{ number_format($summary['active_lenders']) }}</p>
            </div>
            <div class="rounded-lg border border-primary-100 bg-white p-5 shadow-sm shadow-primary-950/10">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-primary-700">Lender Nonaktif</p>
                    <i class="fa-solid fa-circle-pause text-primary-300"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-primary-950">{{ number_format($summary['inactive_lenders']) }}</p>
            </div>
        </section>

        <section class="mt-6 grid gap-4 lg:grid-cols-3">
            <div class="rounded-lg border border-primary-100 bg-white p-5 shadow-sm shadow-primary-950/10 lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-primary-950">Komposisi Status Lender</h2>
                        <p class="mt-1 text-sm text-primary-700">Perbandingan lender aktif dan nonaktif.</p>
                    </div>
                    <i class="fa-solid fa-chart-pie text-primary"></i>
                </div>
                <div class="h-80">
                    <canvas id="lenderStatusChart"></canvas>
                </div>
            </div>

            <div class="rounded-lg border border-primary-100 bg-white p-5 shadow-sm shadow-primary-950/10">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-primary-950">Aksi Cepat</h2>
                    <i class="fa-solid fa-bolt text-primary"></i>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('lender.dashboard.summary') }}" class="flex items-center justify-between rounded-md bg-primary px-4 py-3 text-sm font-medium text-white transition hover:bg-primary-600">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-code text-white"></i>
                            API Summary
                        </span>
                        <i class="fa-solid fa-chevron-right text-xs text-white/80"></i>
                    </a>
                    <button type="button" id="refreshDashboard" class="flex w-full items-center justify-between rounded-md bg-primary px-4 py-3 text-sm font-medium text-white transition hover:bg-primary-600">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-rotate text-white"></i>
                            Refresh Data
                        </span>
                        <i class="fa-solid fa-chevron-right text-xs text-white/80"></i>
                    </button>
                </div>
            </div>
        </section>

        <section class="mt-6 rounded-lg border border-primary-100 bg-white p-5 text-primary-950 shadow-sm shadow-primary-950/10">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-primary-950">Ringkasan Data</h2>
                    <p class="mt-1 text-sm text-primary-700">Data agregat modul lender saat ini.</p>
                </div>
                <i class="fa-solid fa-table text-primary"></i>
            </div>
            <table id="lenderSummaryTable" class="display nowrap w-full">
                <thead>
                    <tr>
                        <th>Indikator</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Lender</td>
                        <td>{{ number_format($summary['total_lenders']) }}</td>
                    </tr>
                    <tr>
                        <td>Lender Aktif</td>
                        <td>{{ number_format($summary['active_lenders']) }}</td>
                    </tr>
                    <tr>
                        <td>Lender Nonaktif</td>
                        <td>{{ number_format($summary['inactive_lenders']) }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const lenderStatusChart = document.getElementById('lenderStatusChart');
        const activeLenders = {{ (int) $summary['active_lenders'] }};
        const inactiveLenders = {{ (int) $summary['inactive_lenders'] }};

        new Chart(lenderStatusChart, {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Nonaktif'],
                datasets: [{
                    data: [activeLenders, inactiveLenders],
                    backgroundColor: ['#0B8FE8', '#7DCEFF'],
                    borderColor: '#ffffff',
                    borderWidth: 4,
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });

        $('#lenderSummaryTable').DataTable({
            responsive: true,
            pageLength: 5,
            lengthChange: false,
            searching: false,
            info: false,
            paging: false,
        });

        $('#refreshDashboard').on('click', function () {
            AppToast.info('Data dashboard sudah diperbarui.');
        });
    </script>
@endpush

