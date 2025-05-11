@extends('layout.app')
@section('title', 'PPOB')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">PPOB</li>
                    </ol>
                </div>
                <h4 class="page-title">PPOB</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="col-md-4 pb-2">
                    <div class="d-flex">
                        <div class="input-group mr-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class=" mdi mdi-magnify"></i></div>
                            </div>
                            <input type="text" class="form-control" placeholder="" />
                        </div>
                        <select class="form-control">
                            <option> -- All Categories -- </option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-8">
                    <div class="float-right">
                        <button class="btn btn-primary"><i class="mdi mdi-plus"></i> New Product</button>
                    </div>
                </div>
            </div>


            <div class="row pt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 style="margin: 2px">Pulsa Reguler</h5>
                                    <small>All Operator</small>
                                </div>
                                <div>
                                    <i class=" mdi mdi-plus-circle text-primary btn-action-size mr-1" type="button"
                                        style="font-size: "></i>
                                    <i class=" mdi mdi-trash-can text-danger btn-action-size" type="button"></i>
                                </div>
                            </div>

                            <div class="pt-2">
                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Status</span>
                                    </div>
                                    <div>
                                        <span class="text-success">Active</span>
                                    </div>

                                </div>


                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Products</span>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">24 variants</h5>
                                    </div>

                                </div>


                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Last Updated</span>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">2025-04-23</h5>
                                    </div>
                                </div>
                            </div>






                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 style="margin: 2px">Data Package</h5>
                                    <small>Internet & Data</small>
                                </div>
                                <div>
                                    <i class=" mdi mdi-plus-circle text-primary btn-action-size mr-1" type="button"
                                        style="font-size: "></i>
                                    <i class=" mdi mdi-trash-can text-danger btn-action-size" type="button"></i>
                                </div>
                            </div>

                            <div class="pt-2">
                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Status</span>
                                    </div>
                                    <div>
                                        <span class="text-success">Active</span>
                                    </div>

                                </div>


                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Products</span>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">24 variants</h5>
                                    </div>

                                </div>


                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Last Updated</span>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">2025-04-23</h5>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 style="margin: 2px">Electricity Token</h5>
                                    <small>PLN Prepaid</small>
                                </div>
                                <div>
                                    <i class=" mdi mdi-plus-circle text-primary btn-action-size mr-1" type="button"
                                        style="font-size: "></i>
                                    <i class=" mdi mdi-trash-can text-danger btn-action-size" type="button"></i>
                                </div>
                            </div>

                            <div class="pt-2">
                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Status</span>
                                    </div>
                                    <div>
                                        <span class="text-warning">Maintenance</span>
                                    </div>

                                </div>


                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Products</span>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">24 variants</h5>
                                    </div>

                                </div>


                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Last Updated</span>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">2025-04-23</h5>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 style="margin: 2px">Water Bill</h5>
                                    <small>PDAM Payment</small>
                                </div>
                                <div>
                                    <i class=" mdi mdi-plus-circle text-primary btn-action-size mr-1" type="button"
                                        style="font-size: "></i>
                                    <i class=" mdi mdi-trash-can text-danger btn-action-size" type="button"></i>
                                </div>
                            </div>

                            <div class="pt-2">
                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Status</span>
                                    </div>
                                    <div>
                                        <span class="text-danger">Inactive</span>
                                    </div>

                                </div>


                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Products</span>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">24 variants</h5>
                                    </div>

                                </div>


                                <div class="d-flex justify-content-between pb-1">
                                    <div>
                                        <span>Last Updated</span>
                                    </div>
                                    <div>
                                        <h5 style="margin:2px;">2025-04-23</h5>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>

                </div>
            </div>

            <div style="bottom:0; display:block;">
                <div class="d-flex justify-content-between">
                    <div>
                        <small>Showing 1 to 4 of 12 products</small>
                    </div>
                    <div>
                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


        </div>


    </div>



@endsection
