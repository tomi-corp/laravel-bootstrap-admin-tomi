@extends('layout.auth')

@section('title', 'Seller Login')

@section('content')
    <div class="account-pages w-100 mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <span><img src="{{ asset('assets/images/logo.jpg') }}" alt="TOMI" height="100"></span>
                                <h1 class="h4 mt-3 mb-1">Seller Login</h1>
                                <p class="text-muted mb-0">Electronic & Gadget Installment</p>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form action="{{ route('seller.login.action') }}" method="POST" class="pt-2">
                                @csrf

                                <div class="form-group mb-3">
                                    <label for="identity">Username or Email</label>
                                    <input class="form-control @error('identity') is-invalid @enderror" type="text"
                                        id="identity" name="identity" value="{{ old('identity') }}" required
                                        placeholder="Username or Email">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password">Password</label>
                                    <input class="form-control @error('password') is-invalid @enderror" type="password"
                                        required id="password" name="password" placeholder="Password">
                                </div>

                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" name="remember" id="remember"
                                        @checked(old('remember'))>
                                    <label class="custom-control-label" for="remember">Remember me</label>
                                </div>

                                <div class="form-group mb-0 text-center">
                                    <button class="btn btn-primary btn-block" type="submit">Log In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
