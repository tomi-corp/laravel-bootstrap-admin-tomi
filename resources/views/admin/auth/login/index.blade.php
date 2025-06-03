@extends('layout.auth')
@section('title', 'Login BackOffice')
@section('content')
    <div class="account-pages w-100 mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">

                        <div class="card-body p-4">

                            <div class="text-center mb-4">
                                <span><img src="{{ asset('assets/images/logo.jpg') }}" alt="" height="100"></span>
                            </div>

                            <form action="{{ route('admin.login.action') }}" method="POST" class="pt-2">
                                @csrf

                                <div class="form-group mb-3">
                                    <label for="username">Username or Email</label>
                                    <input class="form-control" type="text" id="username" name="identity" required=""
                                        placeholder="Username or Email">
                                </div>

                                <div class="form-group mb-3">
                                    <a href="{{ route('admin.forgot') }}" class=" float-right">
                                        <small>Forgot your password?</small></a>
                                    <label for="password">Password</label>
                                    <input class="form-control" type="password" required="" id="password"
                                        name="password" placeholder="password">
                                </div>


                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" name="remember" id="remember">
                                    <label class="custom-control-label" for="remember">Remember me</label>
                                </div>

                                <div class="form-group mb-0 text-center">
                                    <button class="btn btn-primary btn-block" type="submit"> Log In </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
