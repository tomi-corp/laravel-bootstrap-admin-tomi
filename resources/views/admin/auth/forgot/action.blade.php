@extends('layout.auth')
@section('title', 'Reset Password')
@section('content')
    <div class="account-pages w-100 mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">

                        <div class="card-body p-4">

                            <div class="text-center mb-4">
                                <a href="{{ route('admin.login') }}">
                                    <span><img src="{{ asset('assets/images/logo.jpg') }}" alt=""
                                            height="92"></span>
                                </a>
                                <p class="text-muted mt-3">Silahkan perbarui password anda.</p>
                            </div>

                            <form action="{{ route('admin.do.forgot.action', $token) }}" class="pt-2" method="POST">
                                @method('POST')
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="emailaddress">Password</label>
                                    <input class="form-control" type="password" id="emailaddress" required=""
                                        placeholder="Enter new password" name="password">
                                    @if ($errors->has('password'))
                                        <div class="text-danger">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <label for="emailaddress">Password Confirmation</label>
                                    <input class="form-control" type="password" id="emailaddress" required=""
                                        placeholder="" name="password_confirmation">
                                </div>

                                <div class="form-group mb-0 text-center">
                                    <button class="btn btn-primary btn-block" type="submit"> Reset Password </button>
                                </div>
                            </form>

                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <p class="text-muted mb-0">Kembali ke
                                        <a href="{{ route('admin.login') }}" class=" ml-1"><b>Log in</b></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
