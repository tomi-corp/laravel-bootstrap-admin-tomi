@extends('lender.layouts.app')

@section('title', 'Dashboard Lender')
@section('page-title', 'Dashboard Lender')
@section('page-subtitle', 'Ringkasan performa dan status lender')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal text-slate-950">Dashboard Lender</h1>
                <p class="mt-1 text-sm text-slate-500">Ringkasan awal untuk modul lender.</p>
            </div>
            <a
                href="{{ route('lender.dashboard.summary') }}"
                class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-slate-950 px-4 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800"
            >
                <i class="fa-solid fa-chart-simple"></i>
                Data Summary
            </a>
        </div>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Total Lender</p>
                    <i class="fa-solid fa-building-columns text-slate-400"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['total_lenders']) }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Lender Aktif</p>
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['active_lenders']) }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Lender Nonaktif</p>
                    <i class="fa-solid fa-circle-pause text-amber-500"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['inactive_lenders']) }}</p>
            </div>
        </section>

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-950">Komposisi Lender</h2>
                <i class="fa-solid fa-chart-pie text-slate-400"></i>
            </div>
            <div class="h-80">
                <canvas id="lenderStatusChart"></canvas>
            </div>
        </section>

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-semibold text-slate-950">Sample DataTables</h2>
                <select class="js-select2 w-full rounded-md border border-slate-300 text-sm sm:w-56" data-placeholder="Filter status">
                    <option></option>
                    <option value="active">Aktif</option>
                    <option value="nonactive">Nonaktif</option>
                </select>
            </div>
            <table id="lenderTable" class="display nowrap w-full">
                <thead>
                    <tr>
                        <th>Metric</th>
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

        new Chart(lenderStatusChart, {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Nonaktif'],
                datasets: [{
                    data: [
                        {{ (int) $summary['active_lenders'] }},
                        {{ (int) $summary['inactive_lenders'] }},
                    ],
                    backgroundColor: ['#10b981', '#f59e0b'],
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

        $('#lenderTable').DataTable({
            responsive: true,
            pageLength: 5,
            lengthChange: false,
        });

        $('.js-select2').select2({
            allowClear: true,
            placeholder: function () {
                return $(this).data('placeholder');
            },
            width: 'resolve',
        });
    </script>
@endpush
