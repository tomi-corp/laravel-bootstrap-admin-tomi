<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>BTA - @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('assets/js/jquery.js') }}"></script>

</head>

<body class="authentication-bg authentication-bg-pattern-bta d-flex align-items-center">

    <div class="home-btn d-none d-sm-block">
        {{-- <a href="{{ route('bpr.login') }}"><i class="fas fa-home h2 text-white"></i></a> --}}
    </div>

    @yield('content')

    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    @stack('js')

</body>

</html>