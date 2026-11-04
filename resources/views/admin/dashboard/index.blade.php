{{--
    Dashboard utama menggunakan tampilan invoice lender yang sama dengan
    halaman dashboard tagihan. Route admin.dashboard.index mengarah langsung
    ke TomiDanaController agar filter dan ringkasan memakai data yang sama.
--}}
@include('admin.tomi-dana.lender.invoice.index')
