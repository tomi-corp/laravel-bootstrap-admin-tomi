@extends('internal.layouts.app')
@section('title', 'Dashboard Tomi Dana Plus')

@push('css')
    <style>
        .stat-icon { width: 48px; height: 48px; flex: none; font-size: 24px; }
        .stat-label { font-size: 12px; }
        a.stat-link { color: inherit; display: block; }
        a.stat-link:hover .card { border-color: #6658dd; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi Dana Plus</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">Dashboard Tomi Dana Plus</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-inline" style="gap: .5rem;">
                        <label class="mr-1" for="periode">PERIODE</label>
                        <select class="form-control form-control-sm" id="periode" name="periode" onchange="togglePeriode()">
                            <option value="bulan" @selected($filter['periode'] === 'bulan')>BULAN</option>
                            <option value="tahun" @selected($filter['periode'] === 'tahun')>TAHUN</option>
                            <option value="semua" @selected($filter['periode'] === 'semua')>SEMUA WAKTU</option>
                        </select>
                        <input class="form-control form-control-sm" type="month" id="inputBulan" name="bulan" value="{{ $filter['bulan'] }}">
                        <input class="form-control form-control-sm" type="number" id="inputTahun" name="tahun" min="2020" max="2100" value="{{ $filter['tahun'] }}" style="width: 100px;">
                        <button class="btn btn-sm btn-primary" type="submit">TAMPILKAN</button>
                        <span class="text-muted ml-2">Data: <b>{{ strtoupper($periodLabel) }}</b></span>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mt-0 mb-2">RINGKASAN PINJAMAN</h5>
    <div class="row">
        @foreach ($kpis as $card)
            <div class="col-sm-6 col-xl">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon rounded d-flex align-items-center justify-content-center mr-3 bg-soft-{{ $card['color'] }} text-{{ $card['color'] }}">
                            <i class="mdi {{ $card['icon'] }}"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="stat-label text-muted mb-1 text-truncate">{{ $card['label'] }}</p>
                            <h4 class="my-0 text-nowrap">{{ $card['value'] }}</h4>
                            <small class="text-muted">{{ $card['sub'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <h5 class="mt-1 mb-2">PERJANJIAN KERJA SAMA (PKS)</h5>
    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('internal.tomi-dana-plus.companies') }}" class="stat-link">
                <div class="card"><div class="card-body d-flex align-items-center">
                    <div class="stat-icon rounded d-flex align-items-center justify-content-center mr-3 bg-soft-primary text-primary"><i class="mdi mdi-domain"></i></div>
                    <div>
                        <p class="stat-label text-muted mb-1">PERUSAHAAN BER-PKS</p>
                        <h4 class="my-0">{{ number_format($pks['companies'], 0, ',', '.') }}</h4>
                        <small class="text-muted">{{ number_format($pks['active_companies'], 0, ',', '.') }} perusahaan berstatus aktif</small>
                    </div>
                </div></div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card"><div class="card-body d-flex align-items-center">
                <div class="stat-icon rounded d-flex align-items-center justify-content-center mr-3 bg-soft-success text-success"><i class="mdi mdi-file-check-outline"></i></div>
                <div>
                    <p class="stat-label text-muted mb-1">PKS AKTIF</p>
                    <h4 class="my-0">{{ number_format($pks['active'], 0, ',', '.') }}</h4>
                    <small class="text-muted">masih berlaku hari ini</small>
                </div>
            </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card"><div class="card-body d-flex align-items-center">
                <div class="stat-icon rounded d-flex align-items-center justify-content-center mr-3 bg-soft-warning text-warning"><i class="mdi mdi-clock-alert-outline"></i></div>
                <div>
                    <p class="stat-label text-muted mb-1">PKS AKAN BERAKHIR</p>
                    <h4 class="my-0">{{ number_format($pks['expiring'], 0, ',', '.') }}</h4>
                    <small class="text-muted">dalam 30 hari ke depan</small>
                </div>
            </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card"><div class="card-body d-flex align-items-center">
                <div class="stat-icon rounded d-flex align-items-center justify-content-center mr-3 bg-soft-danger text-danger"><i class="mdi mdi-file-cancel-outline"></i></div>
                <div>
                    <p class="stat-label text-muted mb-1">PKS BERAKHIR</p>
                    <h4 class="my-0">{{ number_format($pks['expired'], 0, ',', '.') }}</h4>
                    <small class="text-muted">perlu diperpanjang</small>
                </div>
            </div></div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function togglePeriode() {
            const periode = document.getElementById('periode').value;
            document.getElementById('inputBulan').style.display = periode === 'bulan' ? '' : 'none';
            document.getElementById('inputTahun').style.display = periode === 'tahun' ? '' : 'none';
        }
        togglePeriode();
    </script>
@endpush
