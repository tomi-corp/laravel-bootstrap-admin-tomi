@extends('layout.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>

        <div class="col-12">

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Chart</h4>
                            <div class="chartjs-chart-example chartjs-chart">
                                <canvas id="filled-line-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-custom-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <p>Total Transaction </p>
                                        </div>
                                        <div>
                                            <div class="bg-clear">
                                                <i class="mdi mdi-cart"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h5>1555 <i class="mdi mdi-trending-down"></i></h5>
                                    </div>
                                    <small>-15% more earning usualy from tommorow</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-custom-danger">
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
                                    <h5>1555 <i class="mdi mdi-trending-up"></i></h5>
                                    <small>-15% more earning usualy from tommorow</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-custom-info">
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
                                    <h5>1555 <i class="mdi mdi-trending-up"></i></h5>
                                    <small>-15% more earning usualy from tommorow</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-custom-success">
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
                                    <div>
                                        <h5>1555 <i class="mdi mdi-trending-up"></i></h5>
                                    </div>

                                    <small>-15% more earning usualy from tommorow</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                <h5>Last Activity</h5>
                                <p><a href="#">See All</a></p>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-2" style="">
                                <div class="d-flex align-items-center">
                                    <div class="bg-icon-primary mr-2">
                                        <i class="mdi mdi-car" style="font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">New Vehicle Lead</h5>
                                        <smal>Toyota Avanza 2023</smal>
                                    </div>
                                </div>
                                <div>
                                    <p>2 Seconds ago</p>
                                </div>
                            </div>



                            <div class="d-flex justify-content-between align-items-center py-2" style="">
                                <div class="d-flex align-items-center">
                                    <div class="bg-icon-primary mr-2">
                                        <i class="mdi mdi-car" style="font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">New Vehicle Lead</h5>
                                        <smal>Toyota Avanza 2023</smal>
                                    </div>
                                </div>
                                <div>
                                    <p>30 Minutes ago</p>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-2"
                                style="border-bottom:solid; border-width: thin;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-icon-primary mr-2">
                                        <i class="mdi mdi-car" style="font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">New Vehicle Lead</h5>
                                        <smal>Toyota Avanza 2023</smal>
                                    </div>
                                </div>
                                <div>
                                    <p>1 Hours ago</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



@endsection
