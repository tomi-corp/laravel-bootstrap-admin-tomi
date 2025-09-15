<!DOCTYPE html>
<html lang="en">
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tagihan Pinjaman</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>

    <!-- Begin page -->
    <div id="wrapper mt-2">
        <!-- Start Content-->
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="card-box">

                        <!-- Logo & title -->
                        <div class="clearfix">
                            <div class="float-left">
                                <img src="{{ asset('assets/images/logo.jpg') }}" alt="" height="40">
                            </div>
                            <div class="float-right">
                                <h4 class="m-0 d-print-none">Tagihan</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <p><b>Hallo, KSPDDT</b></p>
                                    <p class="text-muted">Berikut rincian tagihan layanan TOMI pada
                                        <b>{{ $perusahaan->nama }}</b> untuk periode
                                        <b>{{ Carbon::createFromDate($tahun, $bulan, 1)->format('Y-m-d') }}</b>
                                        hingga
                                        <b>{{ Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d') }}</b>
                                    </p>
                                </div>

                            </div><!-- end col -->
                        </div>
                        <!-- end row -->



                        <div class="row">
                            <div class="col-12">
                                <h4 class="heading-title">Lampiran Pinjaman</h4>
                                <h5 class="heading-title">ID Perusahaan :
                                    <b>{{ $perusahaan->id_code_perusahaan }}</b>
                                </h5>

                                <div class="table-responsive">
                                    <table class="table mt-4 table-centered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Pinjaman</th>
                                                <th>NIP</th>
                                                <th>Nominal</th>
                                                <th>Fee TOMI</th>
                                                <th>Biaya Layanan</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($pinjaman as $item)
                                                <tr>
                                                    <td>
                                                        <b>{{ $item->created_at }}</b><br />
                                                        {{ $item->id_pinjaman }}
                                                    </td>

                                                    <td>
                                                        <b> {{ $item->data_karyawan->nama_karyawan }}</b></br>
                                                        {{ $item->data_karyawan->nip }}
                                                    </td>

                                                    <td>
                                                        Rp {{ number_format($item->nominal) }}
                                                    </td>

                                                    <td>
                                                        Rp
                                                        {{ number_format($item->biaya_admin_ksp) }}
                                                    </td>

                                                    <td>
                                                        Rp
                                                        {{ number_format($item->biaya_layanan) }}
                                                    </td>
                                                </tr>
                                            @endforeach

                                            {{-- @foreach ($invoice->perusahaan->pinjamanBerjalan as $pinjama)
                                                <tr>

                                                    <td>
                                                        <b>{{ $pinjama->created_at }}</b>
                                                        <br />
                                                        {{ $pinjama->id_pinjaman }}
                                                    </td>

                                                    <td>
                                                        <b> {{ $pinjama->data_karyawan->nama_karyawan }}</b></br>
                                                        {{ $pinjama->data_karyawan->nip }}
                                                    </td>

                                                    <td>
                                                        Rp {{ number_format($pinjama->nominal) }}
                                                    </td>

                                                    <td>
                                                        Rp
                                                        {{ number_format($pinjama->total_angsuran_perbulan) }}
                                                    </td>

                                                    <td>
                                                        Rp
                                                        {{ number_format($pinjama->jumlah_yang_diterima) }}
                                                    </td>


                                                </tr>
                                            @endforeach --}}


                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive -->
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
                        <div class="float-right pt-4">
                            <p><b>Total Biaya Fee TOMI + Biaya layanan :</b> </p>
                            <h3>Rp
                                {{ number_format($pinjaman->sum('biaya_admin_pandu') + $pinjaman->sum('biaya_layanan')) }}
                            </h3>
                        </div>
                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="clearfix pt-5">
                                    <h6>Noted:</h6>

                                    {{-- <small class="text-muted">
                                        Harap segera dibayarkan tagihan sesuai invoice terlampir maksimum tanggal
                                        {{ $invoice->perusahaan->payment_date + 3 }} ( Payment date + H+3) ke
                                        rekening VA sebagai
                                        berikut: </br>
                                        BANK : {{ $invoice->perusahaan->virtual_account_channel_code }}
                                        </br>
                                        Atas Nama :
                                        {{ $invoice->perusahaan->virtual_account_channel_properties_customer_name }}
                                        </br>
                                        No Rekening :
                                        {{ $invoice->perusahaan->virtual_account_channel_properties_virtual_account_number }}
                                        </br>
                                    </small> --}}
                                </div>
                            </div>


                        </div>
                        <div class="btn-group btn-group-sm d-print-none mt-3">
                            <a href="#" onclick="print()" class="btn btn-light border text-black-50 shadow-none">
                                <i class="fa fa-print"></i>
                                Print & Download
                            </a>
                        </div>


                    </div>

                </div>

            </div>
            <!-- end row -->

        </div> <!-- container-fluid -->

    </div>

    <script>
        var css = '@page { size: landscape; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);
        window.print();
        print = () => {}
    </script>

</body>

</html>
