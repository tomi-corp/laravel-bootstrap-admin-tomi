@extends('bta.layout.auth')
@section('title', 'Login BackOffice BTA')

@section('content')
<div class="account-pages w-100 mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">

                <div class="card">
                    <div class="card-body p-4">

                        {{-- Logo --}}
                        <div class="text-center mb-4">
                            <img src="{{ asset('assets/images/bprtataasia.jpg') }}" alt="" height="100">
                        </div>

                        {{-- Form Login --}}
                        <form action="{{ route('bta.login.action') }}" method="POST" class="pt-2">
                            @csrf

                            {{-- Username --}}
                            <div class="form-group mb-3">
                                <label for="username">Username</label>
                                <input class="form-control" type="text" id="username" name="username"
                                    placeholder="Username" required>
                            </div>

                            {{-- Password --}}
                            <div class="form-group mb-3">
                                <a href="{{ route('admin.forgot') }}" class="float-right">
                                    <small>Forgot your password?</small>
                                </a>

                                <label for="password">Password</label>

                                <div class="input-group">
                                    <input class="form-control" type="password" id="password" name="password"
                                        placeholder="Password" required>

                                    <div class="input-group-append">
                                        <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                                            <i class="fa fa-eye" id="eyeIcon"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Remember Me --}}
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" name="remember" id="remember">
                                <label class="custom-control-label" for="remember">Remember me</label>
                            </div>

                            {{-- Button --}}
                            <div class="form-group mb-0 text-center">
                                <button class="btn btn-primary btn-block" type="submit">
                                    Log In
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

{{-- JS --}}
@push('js')
<script>
    $(document).ready(function () {

        $('#togglePassword').on('click', function () {

            let input = $('#password');
            let icon = $('#eyeIcon');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }

        });

    });
</script>
@endpush