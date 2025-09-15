@extends('layout.auth')

@section('title', 'Verifikasi Dua Faktor')

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

                            <div class="text-center">
                                <h4 class="text-uppercase mt-0">Verifikasi Dua Faktor</h4>
                                <p class="text-muted">
                                    Masukkan kode dari aplikasi autentikator Anda.
                                </p>
                            </div>

                            <form action="{{ route('admin.two-factor-auth.confirm.store') }}" method="POST" class="pt-2">
                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-group mb-3">
                                    <label for="code">Kode Verifikasi</label>
                                    <input class="form-control" type="text" id="code" name="code" required=""
                                        placeholder="Masukkan kode 6 digit">
                                </div>

                                <div class="form-group mb-0 text-center">
                                    <button class="btn btn-primary btn-block" type="submit">Konfirmasi</button>
                                </div>
                            </form>

                            {{-- <div class="text-center mt-4">
                                <p>
                                    <a href="#">Kehilangan perangkat Anda?</a>
                                </p>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection