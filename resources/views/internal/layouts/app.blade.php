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

        .container-fluid {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        .d-flex {
            display: flex !important;
        }

        .d-inline-flex {
            display: inline-flex !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .justify-content-center {
            justify-content: center !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .text-decoration-none {
            text-decoration: none !important;
        }

        .text-dark {
            color: #111827 !important;
        }

        .text-white {
            color: #ffffff !important;
        }

        .text-muted {
            color: #6b7280 !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-danger {
            color: #dc2626 !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .small {
            font-size: 0.875rem !important;
        }

        .h4 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -12px;
            margin-left: -12px;
        }

        .col-12,
        .col-md-3,
        .col-md-4,
        .col-md-6,
        .col-md-12 {
            position: relative;
            width: 100%;
            padding-right: 12px;
            padding-left: 12px;
        }

        .col-12,
        .col-md-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        @media (min-width: 768px) {
            .col-md-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }

            .col-md-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }

            .col-md-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        .page-title-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .page-title {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .page-title-right {
            order: 2;
        }

        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 0;
            margin: 0;
            list-style: none;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "/";
            padding-right: 6px;
            color: #9ca3af;
        }

        .breadcrumb a {
            color: #2563eb;
            text-decoration: none;
        }

        .card {
            margin-bottom: 24px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
        }

        .card-body {
            padding: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            color: #374151;
        }

        .table th,
        .table td {
            padding: 12px;
            border-top: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .table thead th {
            border-top: 0;
            border-bottom: 1px solid #d1d5db;
            font-weight: 700;
            color: #111827;
        }

        .table-hover tbody tr:hover {
            background: #f9fafb;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: inline-block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #374151;
        }

        .form-control {
            display: block;
            width: 100%;
            height: 38px;
            padding: 8px 12px;
            font-size: 0.95rem;
            line-height: 1.5;
            color: #111827;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        textarea.form-control {
            height: auto;
        }

        .form-control:focus {
            border-color: #2563eb;
            outline: 0;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 14px;
            border: 1px solid transparent;
            border-radius: 6px;
            font-weight: 600;
            line-height: 1.25;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }

        .btn-outline-primary {
            background: #ffffff;
            border-color: #2563eb;
            color: #2563eb;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .btn-secondary {
            background: #6b7280;
            border-color: #6b7280;
            color: #ffffff;
        }

        .btn-danger {
            background: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            line-height: 1;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .float-right {
            float: right !important;
        }

        .rounded {
            border-radius: 6px !important;
        }

        .bg-primary {
            background: #2563eb !important;
        }

        .m-0 {
            margin: 0 !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mb-1 {
            margin-bottom: 4px !important;
        }

        .mb-2 {
            margin-bottom: 8px !important;
        }

        .mb-3 {
            margin-bottom: 16px !important;
        }

        .ml-1 {
            margin-left: 4px !important;
        }

        .mr-1 {
            margin-right: 4px !important;
        }

        .mr-2 {
            margin-right: 8px !important;
        }

        .pl-1 {
            padding-left: 4px !important;
        }

        .py-3 {
            padding-top: 16px !important;
            padding-bottom: 16px !important;
        }

        .modal {
            position: fixed;
            inset: 0;
            z-index: 1050;
            display: none;
            overflow: hidden;
            background: rgba(15, 23, 42, 0.5);
        }

        .modal.show {
            display: block;
        }

        .modal-dialog {
            position: relative;
            width: auto;
            margin: 28px auto;
            pointer-events: none;
        }

        .modal-dialog-centered {
            min-height: calc(100% - 56px);
            display: flex;
            align-items: center;
        }

        .modal-dialog-scrollable {
            max-height: calc(100% - 56px);
        }

        .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 56px);
            overflow: hidden;
        }

        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
        }

        .modal-xl {
            max-width: min(1140px, calc(100% - 32px));
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 24px 72px rgba(15, 23, 42, 0.25);
        }

        .modal-header,
        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-footer {
            justify-content: flex-end;
            border-top: 1px solid #e5e7eb;
            border-bottom: 0;
        }

        .modal-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .modal-body {
            padding: 20px;
        }

        .close {
            padding: 0;
            border: 0;
            background: transparent;
            color: #6b7280;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
        }

        .modal-open {
            overflow: hidden;
        }

        .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 0.16em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
        }

        .spinner-border-sm {
            width: 0.875rem;
            height: 0.875rem;
            border-width: 0.14em;
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" rel="stylesheet" />
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

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/keytable/2.12.1/js/dataTables.keyTable.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script>
        (function ($) {
            $.fn.modal = function (action) {
                return this.each(function () {
                    var $modal = $(this);

                    if (action === 'hide') {
                        $modal.removeClass('show').hide();
                        $('body').removeClass('modal-open');
                        return;
                    }

                    $modal.show().addClass('show');
                    $('body').addClass('modal-open');
                });
            };

            $(document).on('click', '[data-dismiss="modal"]', function () {
                $(this).closest('.modal').modal('hide');
            });

            $(document).on('click', '.modal', function (event) {
                if (event.target === this) {
                    $(this).modal('hide');
                }
            });
        })(jQuery);
    </script>

    @stack('js')
</body>

</html>
