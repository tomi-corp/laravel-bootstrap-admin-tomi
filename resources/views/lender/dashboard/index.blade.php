@extends('lender.layouts.app')

@section('title', 'Dashboard Lender')
@section('page-title', 'Dashboard Lender')
@section('page-subtitle', 'Ringkasan performa dan status lender')

@push('styles')
    <style>
        .dashboard-banner-track {
            transform: translateX(calc(var(--banner-index, 0) * -100%));
        }
    </style>
@endpush

@section('content')
    @php
        $banners = [
            ['src' => 'lender/banners/tomi-gaji.png', 'alt' => 'Banner TOMI Gaji'],
            ['src' => 'lender/banners/tomi-invoice.png', 'alt' => 'Banner TOMI Invoice'],
            ['src' => 'lender/banners/tomi-house.png', 'alt' => 'Banner TOMI House'],
            ['src' => 'lender/banners/tomi-moto.png', 'alt' => 'Banner TOMI Moto'],
            ['src' => 'lender/banners/tomi-auto.png', 'alt' => 'Banner TOMI Auto'],
            ['src' => 'lender/banners/tomi-haji.png', 'alt' => 'Banner TOMI Haji'],
            ['src' => 'lender/banners/tomi-umroh.png', 'alt' => 'Banner TOMI Umroh'],
            ['src' => 'lender/banners/tomiin.png', 'alt' => 'Banner TomiiN'],
            ['src' => 'lender/banners/tomi-emas.png', 'alt' => 'Banner TOMI Emas'],
            ['src' => 'lender/banners/tomi-gawai.png', 'alt' => 'Banner TOMI Gawai'],
            ['src' => 'lender/banners/tomi-dana.png', 'alt' => 'Banner TOMI Dana'],
            ['src' => 'lender/banners/tomi-dana-plus.png', 'alt' => 'Banner TOMI Dana Plus'],
            ['src' => 'lender/banners/tomi-simpan-pinjam.png', 'alt' => 'Banner TOMI Simpan Pinjam'],
        ];
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Dashboard Lender</h1>
                <p class="mt-1 text-sm text-slate-600">Pantau ringkasan data dan status operasional lender.</p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <section class="mb-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm shadow-slate-950/10">
            <div id="dashboardBannerCarousel" class="relative" style="--banner-index: 0;">
                <div class="dashboard-banner-track flex transition-transform duration-500 ease-in-out">
                    @foreach ($banners as $banner)
                        <div class="min-w-full">
                            <img
                                src="{{ asset($banner['src']) }}"
                                alt="{{ $banner['alt'] }}"
                                class="block aspect-[2048/768] w-full object-cover"
                            >
                        </div>
                    @endforeach
                </div>

                <button
                    type="button"
                    class="js-banner-prev absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-black shadow-sm transition hover:bg-white"
                    aria-label="Banner sebelumnya"
                >
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button
                    type="button"
                    class="js-banner-next absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-black shadow-sm transition hover:bg-white"
                    aria-label="Banner berikutnya"
                >
                    <i class="fa-solid fa-chevron-right"></i>
                </button>

                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2">
                    @foreach ($banners as $index => $banner)
                        <button
                            type="button"
                            class="js-banner-dot h-2.5 w-2.5 rounded-full bg-white/70 transition data-[active=true]:w-6 data-[active=true]:bg-black"
                            data-index="{{ $index }}"
                            data-active="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-label="Banner {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/10">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-700">Perusahaan</p>
                    <i class="fa-solid fa-building text-black"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['total_companies']) }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/10">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-700">Karyawan Terdaftar</p>
                    <i class="fa-solid fa-users text-black"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['total_employees']) }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/10">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-700">Total Pengajuan</p>
                    <i class="fa-solid fa-file-signature text-black"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['total_loans']) }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/10">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-700">Pengajuan Pending</p>
                    <i class="fa-solid fa-clock text-slate-400"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['pending_loans']) }}</p>
            </div>
        </section>

        <section class="mt-6 grid gap-4 lg:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/10 lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Chart Perusahaan</h2>
                        <p class="mt-1 text-sm text-slate-700">Perbandingan karyawan terdaftar dan pengajuan per perusahaan.</p>
                    </div>
                    <i class="fa-solid fa-chart-column text-black"></i>
                </div>
                <div class="h-80">
                    <canvas id="companyChart"></canvas>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/10">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Status Pengajuan</h2>
                        <p class="mt-1 text-sm text-slate-700">Diagram donat status pinjaman.</p>
                    </div>
                    <i class="fa-solid fa-chart-pie text-black"></i>
                </div>
                <div class="h-80">
                    <canvas id="loanStatusChart"></canvas>
                </div>
            </div>
        </section>

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">History Aktivitas</h2>
                    <p class="mt-1 text-sm text-slate-700">Karyawan yang mendaftar dan pinjaman yang diajukan terbaru.</p>
                </div>
                <i class="fa-solid fa-table text-black"></i>
            </div>
            <table id="lenderHistoryTable" class="display nowrap w-full">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Aktivitas</th>
                        <th>Nama</th>
                        <th>Perusahaan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($summary['histories'] as $history)
                        <tr>
                            <td>{{ $history['date']?->translatedFormat('d M Y H:i') ?? '-' }}</td>
                            <td>
                                <span class="rounded-full {{ $history['type'] === 'Pengajuan' ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }} px-2 py-1 text-xs font-medium">
                                    {{ $history['type'] }}
                                </span>
                            </td>
                            <td>{{ $history['name'] }}</td>
                            <td>{{ $history['company'] }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($history['status'])) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const companyChart = document.getElementById('companyChart');
        const companyChartData = @json($summary['company_chart']);
        const loanStatusChart = document.getElementById('loanStatusChart');
        const loanStatusData = @json($summary['loan_status_chart']);

        new Chart(companyChart, {
            type: 'bar',
            data: {
                labels: companyChartData.labels,
                datasets: [
                    {
                        label: 'Karyawan Terdaftar',
                        data: companyChartData.employees,
                        backgroundColor: '#188ae2',
                        borderRadius: 6,
                    },
                    {
                        label: 'Pengajuan',
                        data: companyChartData.loans,
                        backgroundColor: '#31ce77',
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                        },
                    },
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });

        new Chart(loanStatusChart, {
            type: 'doughnut',
            data: {
                labels: loanStatusData.map((item) => item.label),
                datasets: [{
                    data: loanStatusData.map((item) => item.total),
                    backgroundColor: ['#fbcc5c', '#188ae2', '#31ce77'],
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

        $('#lenderHistoryTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: false,
            searching: false,
            order: [],
            language: {
                emptyTable: 'Belum ada aktivitas.',
            },
        });

        const bannerCarousel = $('#dashboardBannerCarousel');
        const bannerDots = $('.js-banner-dot');
        const totalBanners = {{ count($banners) }};
        let bannerIndex = 0;

        function setBanner(index) {
            bannerIndex = (index + totalBanners) % totalBanners;
            bannerCarousel[0].style.setProperty('--banner-index', bannerIndex);
            bannerDots.attr('data-active', 'false');
            bannerDots.eq(bannerIndex).attr('data-active', 'true');
        }

        $('.js-banner-prev').on('click', function () {
            setBanner(bannerIndex - 1);
        });

        $('.js-banner-next').on('click', function () {
            setBanner(bannerIndex + 1);
        });

        bannerDots.on('click', function () {
            setBanner(Number($(this).data('index')));
        });
    </script>
@endpush

