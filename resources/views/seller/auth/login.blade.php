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
                            <img src="{{ asset('assets/images/logo.jpg') }}" alt="TOMI" height="100">
                            <h1 class="h4 mt-3 mb-1">Seller Login</h1>
                            <p class="text-muted mb-0">Electronic & Gadget Installment</p>
                        </div>

                        {{-- GLOBAL ERROR --}}
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                        @endif

                        <form action="{{ route('seller.login.action') }}" method="POST">
                            @csrf

                            {{-- IDENTITY --}}
                            <div class="mb-3">
                                <label for="identity" class="form-label">Username or Email</label>

                                <input type="text" id="identity" name="identity" value="{{ old('identity') }}"
                                    class="form-control @error('identity') is-invalid @enderror"
                                    placeholder="Username or Email" required>

                                @error('identity')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            {{-- PASSWORD --}}
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>

                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                                    required>

                                @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            {{-- REMEMBER --}}
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" {{
                                    old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            {{-- SUBMIT --}}
                            <div class="d-grid">
                                <button class="btn btn-primary" type="submit">
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