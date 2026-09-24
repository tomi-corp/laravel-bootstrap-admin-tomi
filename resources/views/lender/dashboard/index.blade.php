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
                <p class="mt-1 text-sm text-white/90">Pantau ringkasan data dan status operasional lender.</p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-md border border-sky-200 bg-white px-3 py-2 text-sm text-sky-800 shadow-sm">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <section class="mb-6 overflow-hidden rounded-lg border border-sky-100 bg-white shadow-sm shadow-slate-950/10">
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
                    class="js-banner-prev absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-sky-500 shadow-sm transition hover:bg-white"
                    aria-label="Banner sebelumnya"
                >
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button
                    type="button"
                    class="js-banner-next absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-sky-500 shadow-sm transition hover:bg-white"
                    aria-label="Banner berikutnya"
                >
                    <i class="fa-solid fa-chevron-right"></i>
                </button>

                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2">
                    @foreach ($banners as $index => $banner)
                        <button
                            type="button"
                            class="js-banner-dot h-2.5 w-2.5 rounded-full bg-white/70 transition data-[active=true]:w-6 data-[active=true]:bg-sky-500"
                            data-index="{{ $index }}"
                            data-active="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-label="Banner {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-sky-100 bg-white p-5 shadow-sm shadow-slate-950/10">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-sky-700">Total Lender</p>
                    <i class="fa-solid fa-building-columns text-sky-500"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['total_lenders']) }}</p>
            </div>
            <div class="rounded-lg border border-sky-100 bg-white p-5 shadow-sm shadow-slate-950/10">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-sky-700">Lender Aktif</p>
                    <i class="fa-solid fa-circle-check text-sky-500"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['active_lenders']) }}</p>
            </div>
            <div class="rounded-lg border border-sky-100 bg-white p-5 shadow-sm shadow-slate-950/10">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-sky-700">Lender Nonaktif</p>
                    <i class="fa-solid fa-circle-pause text-sky-300"></i>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ number_format($summary['inactive_lenders']) }}</p>
            </div>
        </section>

        <section class="mt-6 grid gap-4 lg:grid-cols-3">
            <div class="rounded-lg border border-sky-100 bg-white p-5 shadow-sm shadow-slate-950/10 lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-950">Komposisi Status Lender</h2>
                        <p class="mt-1 text-sm text-sky-700">Perbandingan lender aktif dan nonaktif.</p>
                    </div>
                    <i class="fa-solid fa-chart-pie text-sky-500"></i>
                </div>
                <div class="h-80">
                    <canvas id="lenderStatusChart"></canvas>
                </div>
            </div>

            <div class="rounded-lg border border-sky-100 bg-white p-5 shadow-sm shadow-slate-950/10">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-slate-950">Aksi Cepat</h2>
                    <i class="fa-solid fa-bolt text-sky-500"></i>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('lender.dashboard.summary') }}" class="flex items-center justify-between rounded-md bg-sky-500 px-4 py-3 text-sm font-medium text-white transition hover:bg-sky-600">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-code text-white"></i>
                            API Summary
                        </span>
                        <i class="fa-solid fa-chevron-right text-xs text-white/80"></i>
                    </a>
                    <button type="button" id="refreshDashboard" class="flex w-full items-center justify-between rounded-md bg-sky-500 px-4 py-3 text-sm font-medium text-white transition hover:bg-sky-600">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-rotate text-white"></i>
                            Refresh Data
                        </span>
                        <i class="fa-solid fa-chevron-right text-xs text-white/80"></i>
                    </button>
                </div>
            </div>
        </section>

        <section class="mt-6 rounded-lg border border-sky-100 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Ringkasan Data</h2>
                    <p class="mt-1 text-sm text-sky-700">Data agregat modul lender saat ini.</p>
                </div>
                <i class="fa-solid fa-table text-sky-500"></i>
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

