<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TOMI - @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Superapp All-in-One Backend Aplikasi Cepat" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <script>
        window.tailwind = {
            config: {
                corePlugins: {
                    preflight: false
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #f5f7fb;
            color: #1f2937;
        }

        .fl-wrapper {
            z-index: 99999 !important;
            position: fixed !important;
        }

        .internal-shell {
            min-height: 100vh;
        }

        .internal-topbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        .internal-page {
            padding: 24px;
        }

        .btn-purple {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        .btn-purple:hover,
        .btn-purple:focus {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }
    </style>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" />
    @stack('css')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
    <div class="internal-shell">
        <header class="internal-topbar">
            <div class="container-fluid d-flex align-items-center justify-content-between py-3">
                <a href="{{ route('internal.companies.index') }}" class="d-flex align-items-center text-decoration-none text-dark">
                    <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary text-white mr-2" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-building"></i>
                    </span>
                    <span class="font-weight-bold">TOMI Internal</span>
                </a>

                <nav class="d-flex align-items-center">
                    <a href="{{ route('internal.companies.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-building mr-1"></i> Perusahaan
                    </a>
                </nav>
            </div>
        </header>

        <main class="internal-page">
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>

        <footer class="py-3 text-center text-muted small">
            TOMI Internal
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/keytable/2.12.1/js/dataTables.keyTable.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>

    @stack('js')
</body>

</html>
