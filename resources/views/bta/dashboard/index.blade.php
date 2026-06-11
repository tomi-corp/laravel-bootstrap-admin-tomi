@extends('bta.layout.app')
@section('title', 'Dashboard')

@section('content')
<style>
    :root {
        --primary: #2563eb;
        --success: #16a34a;
        --warning: #d97706;
        --danger: #dc2626;
        --info: #0891b2;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border-soft: #e2e8f0;
        --bg-soft: #f8fafc;
        --shadow-soft: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .dashboard-wrapper {
        padding-bottom: 18px;
    }

    .dashboard-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
    }

    .dashboard-heading h4 {
        margin-bottom: 4px;
        color: var(--text-main);
        font-weight: 700;
    }

    .dashboard-heading p {
        margin-bottom: 0;
        color: var(--text-muted);
        max-width: 680px;
    }

    .dashboard-date {
        color: var(--text-muted);
        font-size: 13px;
        white-space: nowrap;
    }

    .summary-panel,
    .disbursement-card,
    .stat-card,
    .chart-card {
        background: #fff;
        border: 1px solid var(--border-soft);
        border-radius: 8px;
        box-shadow: var(--shadow-soft);
    }

    .summary-panel {
        height: 100%;
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 244px;
    }

    .summary-label,
    .stat-label {
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    .summary-number {
        margin: 12px 0 10px;
        color: var(--text-main);
        font-size: 46px;
        font-weight: 800;
        line-height: 1;
    }

    .summary-caption {
        color: var(--text-muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .disbursement-card {
        padding: 22px 24px;
        margin-bottom: 18px;
        border-left: 4px solid var(--success);
    }

    .disbursement-card-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .disbursement-title {
        margin-bottom: 4px;
        color: var(--text-main);
        font-size: 17px;
        font-weight: 700;
    }

    .disbursement-subtitle {
        margin-bottom: 0;
        color: var(--text-muted);
        font-size: 13px;
    }

    .disbursement-total {
        display: flex;
        align-items: center;
        gap: 14px;
        color: var(--success);
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
        white-space: nowrap;
    }

    .summary-icon,
    .stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 8px;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        font-size: 21px;
    }

    .stat-card {
        min-height: 112px;
        padding: 18px;
    }

    .stat-card-link {
        display: block;
        color: inherit;
        text-decoration: none;
        border-radius: 8px;
        transition: transform 0.16s ease, box-shadow 0.16s ease;
    }

    .stat-card-link:hover,
    .stat-card-link:focus {
        color: inherit;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .stat-card-link:hover .stat-card,
    .stat-card-link:focus .stat-card {
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.11);
    }

    .stat-card-inner {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .stat-number {
        margin-top: 8px;
        color: var(--text-main);
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        font-size: 18px;
    }

    .soft-primary {
        background: rgba(37, 99, 235, 0.11);
        color: var(--primary);
    }

    .soft-success {
        background: rgba(22, 163, 74, 0.11);
        color: var(--success);
    }

    .soft-warning {
        background: rgba(217, 119, 6, 0.12);
        color: var(--warning);
    }

    .soft-danger {
        background: rgba(220, 38, 38, 0.11);
        color: var(--danger);
    }

    .soft-info {
        background: rgba(8, 145, 178, 0.11);
        color: var(--info);
    }

    .chart-card {
        padding: 22px;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 18px;
    }

    .chart-title {
        margin-bottom: 4px;
        color: var(--text-main);
        font-size: 17px;
        font-weight: 700;
    }

    .chart-subtitle {
        margin-bottom: 0;
        color: var(--text-muted);
        font-size: 13px;
    }

    .chart-legend {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px 14px;
        color: var(--text-muted);
        font-size: 12px;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .chart-wrap {
        position: relative;
        height: 380px;
    }

    @media (max-width: 991.98px) {
        .dashboard-heading,
        .disbursement-card-inner,
        .chart-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .dashboard-date,
        .chart-legend {
            justify-content: flex-start;
            white-space: normal;
        }
    }

    @media (max-width: 575.98px) {
        .summary-panel,
        .disbursement-card,
        .stat-card,
        .chart-card {
            padding: 16px;
        }

        .summary-number {
            font-size: 38px;
        }

        .stat-number {
            font-size: 26px;
        }

        .disbursement-total {
            font-size: 30px;
        }

        .chart-wrap {
            height: 320px;
        }
    }
</style>

<div class="dashboard-wrapper">
    <div class="page-title-box">
        <div class="page-title-right">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Tomi</a>
                </li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
        <h4 class="page-title">Dashboard</h4>
    </div>

    <div class="dashboard-heading">
        <div>
            <h4>Dashboard BTA</h4>
            <p>Pantau posisi nasabah dari proses analyst, revisi, credam, finance, sampai pencairan.</p>
        </div>
        <div class="dashboard-date">{{ $today }}</div>
    </div>

    <div class="disbursement-card">
        <div class="disbursement-card-inner">
            <div>
                <div class="disbursement-title">Total Plafond Sudah Dicairkan</div>
                <p class="disbursement-subtitle">Total plafond dari nasabah yang sudah memiliki bukti disbursement.</p>
            </div>
            <div class="disbursement-total">
                <div class="summary-icon soft-success">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <span>Rp {{ number_format($totalPlafondDicairkan, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-lg-4 mb-3 mb-lg-0">
            <div class="summary-panel">
                <div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="summary-label">Jumlah Nasabah</div>
                        <div class="summary-icon soft-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="summary-number">{{ $totalNasabah }}</div>
                    <div class="summary-caption">Total seluruh data nasabah yang tercatat di sistem BTA.</div>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-8">
            <div class="row">
                <div class="col-md-4 col-sm-6 mb-3">
                    <a href="{{ route('bta.nasabah.index') }}#revisi" class="stat-card-link">
                        <div class="stat-card">
                            <div class="stat-card-inner">
                                <div>
                                    <div class="stat-label">Revisi</div>
                                    <div class="stat-number">{{ $totalRevisi }}</div>
                                </div>
                                <div class="stat-icon soft-warning">
                                    <i class="fas fa-undo"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6 mb-3">
                    <a href="{{ route('bta.nasabah.index') }}#analyst" class="stat-card-link">
                        <div class="stat-card">
                            <div class="stat-card-inner">
                                <div>
                                    <div class="stat-label">Analyst</div>
                                    <div class="stat-number">{{ $totalAnalyst }}</div>
                                </div>
                                <div class="stat-icon soft-info">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6 mb-3">
                    <a href="{{ route('bta.credam.index') }}" class="stat-card-link">
                        <div class="stat-card">
                            <div class="stat-card-inner">
                                <div>
                                    <div class="stat-label">Credam</div>
                                    <div class="stat-number">{{ $totalCredam }}</div>
                                </div>
                                <div class="stat-icon soft-primary">
                                    <i class="fas fa-tasks"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
                    <a href="{{ route('bta.nasabah.index') }}#ditolak" class="stat-card-link">
                        <div class="stat-card">
                            <div class="stat-card-inner">
                                <div>
                                    <div class="stat-label">Ditolak</div>
                                    <div class="stat-number">{{ $totalDitolak }}</div>
                                </div>
                                <div class="stat-icon soft-danger">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6 mb-3 mb-sm-0">
                    <a href="{{ route('bta.finance.uploaded.index') }}" class="stat-card-link">
                        <div class="stat-card">
                            <div class="stat-card-inner">
                                <div>
                                    <div class="stat-label">Selesai</div>
                                    <div class="stat-number">{{ $totalSelesai }}</div>
                                </div>
                                <div class="stat-icon soft-success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('bta.finance.index') }}" class="stat-card-link">
                        <div class="stat-card">
                            <div class="stat-card-inner">
                                <div>
                                    <div class="stat-label">Finance</div>
                                    <div class="stat-number">{{ $totalFinance }}</div>
                                </div>
                                <div class="stat-icon soft-info">
                                    <i class="fas fa-money-check-alt"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Diagram Nasabah per Bulan {{ $chartYear }}</div>
                <p class="chart-subtitle">Perbandingan jumlah nasabah, proses, pencairan, dan penolakan berdasarkan bulan pengajuan.</p>
            </div>
            <div class="chart-legend">
                <span class="legend-item"><span class="legend-dot" style="background:#2563eb"></span>Jumlah Nasabah</span>
                <span class="legend-item"><span class="legend-dot" style="background:#d97706"></span>Masih Diproses</span>
                <span class="legend-item"><span class="legend-dot" style="background:#16a34a"></span>Sudah Cair</span>
                <span class="legend-item"><span class="legend-dot" style="background:#dc2626"></span>Ditolak</span>
            </div>
        </div>
        <div class="chart-wrap">
            <canvas id="nasabahMonthlyChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function() {
        var chartElement = document.getElementById('nasabahMonthlyChart');

        if (!chartElement) {
            return;
        }

        new Chart(chartElement.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($monthLabels),
                datasets: [{
                        label: 'Jumlah Nasabah',
                        data: @json($chartNasabah),
                        backgroundColor: 'rgba(37, 99, 235, 0.78)',
                        borderColor: '#2563eb',
                        borderWidth: 1
                    },
                    {
                        label: 'Masih Diproses',
                        data: @json($chartProses),
                        backgroundColor: 'rgba(217, 119, 6, 0.78)',
                        borderColor: '#d97706',
                        borderWidth: 1
                    },
                    {
                        label: 'Sudah Cair',
                        data: @json($chartCair),
                        backgroundColor: 'rgba(22, 163, 74, 0.78)',
                        borderColor: '#16a34a',
                        borderWidth: 1
                    },
                    {
                        label: 'Ditolak',
                        data: @json($chartDitolak),
                        backgroundColor: 'rgba(220, 38, 38, 0.78)',
                        borderColor: '#dc2626',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            precision: 0
                        },
                        gridLines: {
                            color: 'rgba(148, 163, 184, 0.18)'
                        }
                    }]
                }
            }
        });
    });
</script>
@endpush
