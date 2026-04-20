@extends('bta.layout.app')
@section('title', 'Dashboard')

@section('content')
<style>
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --success: #16a34a;
        --warning: #d97706;
        --danger: #dc2626;
        --info: #0891b2;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border-soft: #e2e8f0;
        --bg-soft: #f8fafc;
        --shadow-soft: 0 10px 30px rgba(15, 23, 42, 0.06);
        --radius-lg: 20px;
        --radius-md: 16px;
        --radius-sm: 12px;
    }

    .dashboard-wrapper {
        padding-bottom: 10px;
    }

    .dashboard-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: var(--radius-lg);
        padding: 28px;
        color: #fff;
        box-shadow: 0 16px 36px rgba(37, 99, 235, 0.20);
        overflow: hidden;
        position: relative;
    }

    .dashboard-header::after {
        content: "";
        position: absolute;
        top: -30px;
        right: -30px;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
    }

    .dashboard-header h2,
    .dashboard-header p,
    .dashboard-header small {
        color: #fff !important;
        position: relative;
        z-index: 2;
    }

    .dashboard-header .summary-box {
        position: relative;
        z-index: 2;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 18px;
        padding: 18px 22px;
        min-width: 210px;
        text-align: center;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.10);
    }

    .summary-box .summary-number {
        font-size: 30px;
        font-weight: 700;
        color: var(--primary);
        line-height: 1.1;
    }

    .summary-box .summary-label {
        margin-top: 6px;
        color: var(--text-muted);
        font-size: 14px;
    }

    .stat-card,
    .section-card {
        border: none;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-soft);
        background: #fff;
        height: 100%;
    }

    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.10);
    }

    .stat-card .card-body,
    .section-card .card-body {
        padding: 22px;
    }

    .stat-label {
        font-size: 14px;
        color: var(--text-muted);
        margin-bottom: 6px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.1;
        margin-bottom: 10px;
    }

    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .soft-primary {
        background: rgba(37, 99, 235, 0.12);
        color: var(--primary);
    }

    .soft-success {
        background: rgba(22, 163, 74, 0.12);
        color: var(--success);
    }

    .soft-warning {
        background: rgba(217, 119, 6, 0.12);
        color: var(--warning);
    }

    .soft-danger {
        background: rgba(220, 38, 38, 0.12);
        color: var(--danger);
    }

    .soft-info {
        background: rgba(8, 145, 178, 0.12);
        color: var(--info);
    }

    .badge-soft {
        display: inline-block;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 10px;
        border-radius: 999px;
    }

    .badge-soft-primary {
        background: rgba(37, 99, 235, 0.10);
        color: var(--primary);
    }

    .badge-soft-success {
        background: rgba(22, 163, 74, 0.10);
        color: var(--success);
    }

    .badge-soft-warning {
        background: rgba(217, 119, 6, 0.10);
        color: var(--warning);
    }

    .badge-soft-danger {
        background: rgba(220, 38, 38, 0.10);
        color: var(--danger);
    }

    .badge-soft-info {
        background: rgba(8, 145, 178, 0.10);
        color: var(--info);
    }

    .section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 20px;
    }

    .progress-item {
        margin-bottom: 18px;
    }

    .progress-item:last-child {
        margin-bottom: 0;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
        font-size: 14px;
        color: var(--text-main);
    }

    .progress {
        height: 10px;
        border-radius: 999px;
        background-color: #eef2f7;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 999px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-item:first-child {
        padding-top: 0;
    }

    .info-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 2px;
    }

    .info-desc {
        font-size: 12px;
        color: var(--text-muted);
    }

    .info-value {
        font-size: 16px;
        font-weight: 700;
        white-space: nowrap;
    }

    .insight-box {
        background: var(--bg-soft);
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 18px;
        height: 100%;
    }

    .insight-box h6 {
        margin-bottom: 8px;
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
    }

    .insight-box p {
        margin-bottom: 0;
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    @media (max-width: 767.98px) {
        .dashboard-header {
            padding: 22px;
        }

        .dashboard-header .summary-box {
            width: 100%;
            margin-top: 16px;
        }

        .stat-number {
            font-size: 24px;
        }
    }
</style>

<div class="dashboard-wrapper">
    <div class="row mb-4">
        <div class="col-12">
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
        </div>
    </div>

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-header">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="mb-2">Selamat Datang di Dashboard BTA</h2>
                        <p class="mb-2">
                            Pantau seluruh proses pengajuan nasabah, mulai dari analisa,
                            revisi, hingga tahap realisasi kredit dalam satu tampilan.
                        </p>
                        <small>{{ $today }}</small>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="summary-box d-inline-block">
                            <div class="summary-number">{{ $totalPengajuan }}</div>
                            <div class="summary-label">Total Data Pengajuan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik utama --}}
    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="stat-label">Sudah Dianalisa</div>
                            <div class="stat-number">{{ $totalSudahAnalisa }}</div>
                        </div>
                        <div class="stat-icon soft-info">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="stat-label">Dikembalikan</div>
                            <div class="stat-number">{{ $totalDikembalikan }}</div>
                        </div>
                        <div class="stat-icon soft-warning">
                            <i class="fas fa-undo"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="stat-label">Proses Realisasi</div>
                            <div class="stat-number">{{ $totalProsesRealisasi }}</div>
                        </div>
                        <div class="stat-icon soft-success">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="stat-label">Sudah Direalisasi</div>
                            <div class="stat-number">{{ $totalSudahRealisasi }}</div>
                        </div>
                        <div class="stat-icon soft-danger">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Komposisi + ringkasan --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card section-card">
                <div class="card-body">
                    <h5 class="section-title">Komposisi Status Pengajuan</h5>

                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Pending / Sudah Dianalisa</span>
                            <span>{{ $totalPending }} data ({{ $persenPending }}%)</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $persenPending }}%">
                            </div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Revisi / Dikembalikan</span>
                            <span>{{ $totalRevisi }} data ({{ $persenRevisi }}%)</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $persenRevisi }}%">
                            </div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Approve / Proses Realisasi</span>
                            <span>{{ $totalApprove }} data ({{ $persenApprove }}%)</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ $persenApprove }}%"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Reject / Sudah Direalisasi</span>
                            <span>{{ $totalReject }} data ({{ $persenReject }}%)</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $persenReject }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card section-card">
                <div class="card-body">
                    <h5 class="section-title">Ringkasan Hari Ini</h5>

                    <div class="info-item">
                        <div>
                            <div class="info-title">Total Pengajuan</div>
                            <div class="info-desc">Jumlah seluruh nasabah yang tercatat</div>
                        </div>
                        <div class="info-value text-primary">{{ $totalPengajuan }}</div>
                    </div>

                    <div class="info-item">
                        <div>
                            <div class="info-title">Butuh Tindak Lanjut</div>
                            <div class="info-desc">Data revisi yang perlu diperiksa kembali</div>
                        </div>
                        <div class="info-value text-warning">{{ $totalRevisi }}</div>
                    </div>

                    <div class="info-item">
                        <div>
                            <div class="info-title">Siap Diproses</div>
                            <div class="info-desc">Data approve untuk tahap berikutnya</div>
                        </div>
                        <div class="info-value text-success">{{ $totalApprove }}</div>
                    </div>

                    <div class="info-item">
                        <div>
                            <div class="info-title">Masih Pending</div>
                            <div class="info-desc">Data yang masih menunggu antrean analisa</div>
                        </div>
                        <div class="info-value text-info">{{ $totalPending }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection