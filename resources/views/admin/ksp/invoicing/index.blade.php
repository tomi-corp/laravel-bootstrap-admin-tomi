<!DOCTYPE html>
<html lang="en">
@php
    use Carbon\Carbon;
@endphp

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tagihan Fee Base</title>
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
                                <img src="{{ asset('assets/images/logo.jpg') }}" alt="" height="20">
                            </div>
                            <div class="float-right">
                                <h4 class="m-0 d-print-none">Tagihan</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <p><b>Hallo, {{ $invoice->perusahaan->nama }}</b></p>
                                    <p class="text-muted">Bersama ini kami sampaikan tagihan atas Fee Base yang telah
                                        kami berikan kepada Koperasi {{ $invoice->perusahaan->nama }} pada periode
                                        {{ Carbon::parse($invoice->created_at)->startOfMonth()->format('d-M-Y') }}
                                        hingga
                                        {{ Carbon::parse($invoice->created_at)->endOfMonth()->format('d-M-Y') }} </p>
                                </div>

                            </div><!-- end col -->
                            <div class="col-md-6">
                                <div class="mt-3 float-right">
                                    <p><strong>Periode : </strong> <span class="float-right">
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            {{ Carbon::parse($invoice->created_at)->startOfMonth()->format('d-M-Y') }} -
                                            {{ Carbon::parse($invoice->created_at)->startOfMonth()->format('d-M-Y') }}</span>
                                    </p>
                                </div>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-12">
                                <h4 class="heading-title">Lampiran Pinjaman</h4>
                                <div class="table-responsive">
                                    <table class="table mt-4 table-centered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Pinjaman</th>
                                                <th>NIP</th>
                                                <th>Nominal</th>
                                                <th>Rate</th>
                                                <th>Biaya Admin</th>
                                                <th>Iuran Anggota</th>
                                                <th>Angsuran Perbulan</th>
                                                <th>Nominal Yg Diterima</th>
                                                <th>Fee Base TOMI</th>
                                                <th>Income KSP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $grandTotal = 0;
                                            @endphp
                                            @foreach ($pinjaman as $pinjama)
                                                <tr>
                                                    <td>
                                                        <b>{{ $pinjama->created_at }}</b>
                                                        <br />
                                                        {{ $pinjama->id_pinjaman }}
                                                    </td>

                                                    <td>
                                                        {{ $pinjama->data_karyawan->nama_karyawan }}
                                                        <br />{{ $pinjama->data_karyawan->nip }}
                                                    </td>
                                                    <td class="text-right">Rp.{{ number_format($pinjama->nominal) }}
                                                        <br />
                                                        {{ $pinjama->tenor }} Bulan
                                                    </td>
                                                    <td class="text-right">{{ $pinjama->rate }}%</td>
                                                    <td class="text-right">{{ $pinjama->biaya_admin }}%</td>
                                                    <td class="text-right">
                                                        Rp.{{ number_format($pinjama->iuran_anggota) }}</td>
                                                    <td class="text-right">
                                                        Rp.{{ number_format($pinjama->total_angsuran_perbulan) }}
                                                    </td>
                                                    <td class="text-right">
                                                        Rp.{{ number_format($pinjama->jumlah_yang_diterima) }}
                                                    </td>

                                                    <td class="text-right">
                                                        @php
                                                            $feeBase =
                                                                ($pinjama->biaya_admin / 100 / 2) * $pinjama->nominal;
                                                            $grandTotal = $grandTotal + $feeBase;
                                                        @endphp
                                                        Rp.{{ number_format($feeBase) }}
                                                    </td>
                                                    <td class="text-right">
                                                        @php
                                                            $income =
                                                                $pinjama->nominal -
                                                                $pinjama->jumlah_yang_diterima -
                                                                $feeBase;
                                                        @endphp
                                                        Rp.{{ number_format($income) }}
                                                </tr>
                                            @endforeach


                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive -->
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-md-6">
                                <div class="clearfix pt-5">
                                    <h6>Notes:</h6>

                                    <small class="text-muted">
                                        Semua tagihan harus dibayar dalam waktu 7 hari sejak diterimanya faktur.
                                        Pembayaran dapat
                                        dilakukan dengan metode transfer bank ke rekening bank yang tertera dalam
                                        perjanjian kerjasama.
                                        Terima kasih.
                                    </small>
                                </div>
                            </div> <!-- end col -->
                            <div class="col-md-6">
                                <div class="float-right pt-4">
                                    <p><b>Sub-total Fee Base Tomi:</b> </p>
                                    {{-- <p><b>Discount (10%):</b> <span class="float-right"> &nbsp;&nbsp;&nbsp;
                                            $459.75</span></p> --}}
                                    <h3>Rp. {{ number_format($grandTotal) }}</h3>
                                </div>
                                <div class="clearfix"></div>
                            </div> <!-- end col -->

                            <div class="btn-group btn-group-sm d-print-none mt-3">
                                <a href="#" onclick="print()"
                                    class="btn btn-light border text-black-50 shadow-none">
                                    <i class="fa fa-print"></i>
                                    Print & Download
                                </a>
                            </div>

                        </div>
                        <!-- end row -->

                        {{-- <div class="mt-4 mb-1">
                            <div class="text-right d-print-none">
                                <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light"><i
                                        class="mdi mdi-printer mr-1"></i> Print</a>
                                <a href="#" class="btn btn-info waves-effect waves-light">Submit</a>
                            </div>
                        </div> --}}
                    </div>

                </div>

            </div>
            <!-- end row -->

        </div> <!-- container-fluid -->

    </div>
    <!-- END wrapper -->


    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

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
        print = () => {



        }
    </script>

</body>

</html>
