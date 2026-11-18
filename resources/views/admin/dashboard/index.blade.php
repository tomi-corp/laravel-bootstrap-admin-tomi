@extends('layout.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.dashboard.export-report') }}" class="btn btn-success mr-2">
                        <i class="mdi mdi-file-excel-outline mr-1"></i> Export Report
                    </a>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between pb-2">
                        <h5>Aktivitas Terakhir</h5>
                    </div>

                    @foreach ($register['activiti'] as $activity)
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-icon-primary rounded p-2 me-2 mr-2"
                                    style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                    @if ($activity['type'] == 'Registrasi')
                                        <i class="mdi mdi-account-plus-outline" style="font-size: 1.25rem;"></i>
                                    @else
                                        <i class="mdi mdi-receipt" style="font-size: 1.25rem;"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0" style="font-weight: 500;">{{ $activity['name'] }}</h6>
                                    <small class="text-muted">{{ $activity['type'] }}</small>
                                </div>
                            </div>
                            <div>
                                <span class="text-muted">{{ $activity['created_at'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p>Jumlah Unduhan Bulan Ini</p>
                                </div>
                                <div>
                                    <div class="bg-clear">
                                        <i class="mdi mdi-transition-masked"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5>{{ $register['downloadBulanIni'] }} <i class="mdi mdi-trending-up"></i>
                                </h5>
                            </div>
                            <small>{{ $register['persentaseDonwnload'] }}% perbandingan registrasi dari
                                bulan
                                kemarin</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p>Jumlah Unduhan</p>
                                </div>
                                <div>
                                    <div class="bg-clear">
                                        <i class="mdi mdi-transition-masked"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5>{{ $register['jumlahDownload'] }} <i class="mdi mdi-trending-up"></i>
                                </h5>
                            </div>
                            <small>Unduhan</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p>Total Register Bulan Ini</p>
                                </div>
                                <div>
                                    <div class="bg-clear">
                                        <i class="mdi mdi-transition-masked"></i>
                                    </div>
                                </div>
                            </div>
                            <h5>{{ $register['registerBulanIni'] }} <i class="mdi mdi-trending-up"></i></h5>
                            <small>{{ $register['persentaseRegister'] }}% perbandingan registrasi dari bulan
                                kemarin</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p>Total Register</p>
                                </div>
                                <div>
                                    <div class="bg-clear">
                                        <i class="mdi mdi-transition-masked"></i>
                                    </div>
                                </div>
                            </div>
                            <h5>{{ $register['totalRegister'] }} <i class="mdi mdi-trending-up"></i></h5>
                            <small>Pengguna</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>



    </div>



@endsection
