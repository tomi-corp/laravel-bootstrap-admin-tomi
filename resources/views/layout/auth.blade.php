<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TOMI - @yield('title')</title>
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

</head>

<body class="authentication-bg authentication-bg-pattern d-flex align-items-center">

    <div class="home-btn d-none d-sm-block">
        {{-- <a href="{{ route('bpr.login') }}"><i class="fas fa-home h2 text-white"></i></a> --}}
    </div>

    @yield('content')

    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script>
        (function () {
            function enablePasswordToggles() {
                document.querySelectorAll('input[type="password"]').forEach(function (input) {
                    if (input.dataset.passwordToggleReady === '1' || input.closest('.input-group')) {
                        return;
                    }

                    var wrapper = document.createElement('div');
                    wrapper.className = 'input-group';
                    input.parentNode.insertBefore(wrapper, input);
                    wrapper.appendChild(input);

                    var append = document.createElement('div');
                    append.className = 'input-group-append';

                    var button = document.createElement('button');
                    button.className = 'btn btn-outline-secondary';
                    button.type = 'button';
                    button.setAttribute('aria-label', 'Lihat password');
                    button.innerHTML = '<i class="mdi mdi-eye-off"></i>';

                    button.addEventListener('click', function () {
                        var icon = button.querySelector('i');
                        var isPassword = input.type === 'password';

                        input.type = isPassword ? 'text' : 'password';
                        button.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
                        icon.classList.toggle('mdi-eye-off', !isPassword);
                        icon.classList.toggle('mdi-eye', isPassword);
                    });

                    append.appendChild(button);
                    wrapper.appendChild(append);
                    input.dataset.passwordToggleReady = '1';
                });
            }

            document.addEventListener('DOMContentLoaded', enablePasswordToggles);
        })();
    </script>

</body>

</html>
