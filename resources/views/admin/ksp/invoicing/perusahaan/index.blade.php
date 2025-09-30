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
                                <img src="{{ asset('assets/images/ksp.png') }}" alt="" height="40">
                            </div>
                            <div class="float-right">
                                <h4 class="m-0 d-print-none">Tagihan</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <p><b>Hallo, {{ $invoice->perusahaan->nama }}</b></p>
                                    <p class="text-muted">Berikut rincian tagihan angsuran atas pinjaman payroll
                                        karyawan
                                        <b>{{ $invoice->perusahaan->nama }}</b> untuk periode
                                        {{-- <b>{{ Carbon::parse($invoice->period)->subMonth(1)->format('Y-m-d') }}</b>
                                        hingga --}}
                                        <b>{{ Carbon::parse($invoice->period)->format('M-Y') }}</b>
                                    </p>
                                </div>

                            </div><!-- end col -->
                        </div>
                        <!-- end row -->



                        <div class="row">
                            <div class="col-12">
                                <h4 class="heading-title">No. Invoice : {{ $noInv }}</h4>
                                <h4 class="heading-title">Lampiran Pinjaman</h4>
                                <h5 class="heading-title">ID Perusahaan :
                                    <b>{{ $invoice->perusahaan->id_code_perusahaan }}</b>
                                </h5>

                                <div class="table-responsive">
                                    <table class="table mt-4 table-centered">
                                        <thead>
                                            <tr>

                                                <th>Tanggal Pinjaman</th>
                                                <th>NIP</th>
                                                <th>Jumlah Pinjaman</th>
                                                <th>Jumlah Angsuran</th>
                                                <th>Jumlah Yg Diterima</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total = 0;
                                            @endphp
                                            @foreach ($pinjaman as $pinjama)
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
                                                        @php
                                                            $total = $total + $pinjama->total_angsuran_perbulan;
                                                        @endphp
                                                        <br />
                                                        (Angs. ke
                                                        {{ $pinjama->angsuranPeriod->where('status', 'belum')->first()->pembayaran }}/{{ count($pinjama->angsuran) }})
                                                    </td>

                                                    <td>
                                                        Rp
                                                        {{ number_format($pinjama->jumlah_yang_diterima) }}
                                                    </td>


                                                </tr>
                                            @endforeach


                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive -->
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
                        <div class="float-right pt-4">
                            <p><b>Total Angsuran :</b> </p>
                            <h3>Rp
                                {{ number_format($total) }}
                            </h3>
                        </div>
                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="clearfix pt-5">
                                    <h6>Noted:</h6>

                                    <ke>
                                        Harap segera dibayarkan tagihan sesuai invoice terlampir maksimum tanggal
                                        {{ $invoice->perusahaan->payment_date + 3 }}
                                        {{ Carbon::parse($invoice->period)->format('M-Y') }} ke rekening
                                        berikut: </br>
                                        BANK : {{ $lender->bank->code_bank }}
                                        </br>
                                        Atas Nama :
                                        {{ $lender->atas_nama }}
                                        </br>
                                        No Rekening :
                                        {{ $lender->nomor_rekening }}
                                        </br>
                                        </b>
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
        </div>
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
