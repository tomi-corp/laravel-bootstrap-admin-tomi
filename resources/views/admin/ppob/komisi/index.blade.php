@extends('layout.app')
@section('title', 'PPOB')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">PPOB</li>
                        <li class="breadcrumb-item active">Komisi</li>
                    </ol>
                </div>
                <h4 class="page-title">Komisi PPOB</h4>
            </div>
        </div>


        <div class="col-12">
            <div class=" " id="contentMaster">
                <div class="row">
                    <div class="col-md-2">
                        <div class="card">
                            <div class="card-body">
                                <label>Filter :</label>
                                <input type="text" name="daterange" class="form-control" value=""
                                    id="filterRangePicker" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card ">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1"> Komisi Prabayar tanggal :
                                    </div>
                                    <div class="text-xs font-weight-bold text-uppercase mb-1" id="tanggalKomisiPrabayar">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between pt-4">
                                    <p>Total Penjualan :</p>
                                    <p> <span id="memberSalesPrabayarTanggal">Rp 0</span></p>
                                </div>
                                <hr class="grid">

                                <div class="d-flex justify-content-between ">
                                    <p>Komisi Member :</p>
                                    <p> <span id="commisionMemberPrabayarTanggal">Rp 0</span></p>
                                </div>

                                <div class="d-flex justify-content-between ">
                                    <p>Komisi Leader :</p>
                                    <p> <span id="leaderCommisionPrabayarTanggal">Rp 0</span></p>
                                </div>

                                <div class="d-flex justify-content-between ">
                                    <p>Total Komisi :</p>
                                    <p> <span id="totalCommisionPrabayar">Rp 0</span></p>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-body">

                                <div class="d-flex justify-content-between">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Komisi pascabayar tanggal :
                                    </div>
                                    <div class="text-xs font-weight-bold text-uppercase mb-1" id="tanggalKomisiPascabayar">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between pt-4">
                                    <p>Total Penjualan :</p>
                                    <p> <span id="memberSalesPascabayarTanggal">Rp 0</span></p>
                                </div>
                                <hr class="grid">

                                <div class="d-flex justify-content-between ">
                                    <p>Komisi Member :</p>
                                    <p> <span id="commisionMemberPascabayarTanggal">Rp 0</span></p>
                                </div>

                                <div class="d-flex justify-content-between ">
                                    <p>Komisi Leader :</p>
                                    <p> <span id="leaderCommisionPascabayarTanggal">Rp 0</span></p>
                                </div>

                                <div class="d-flex justify-content-between ">
                                    <p>Total Komisi :</p>
                                    <p> <span id="totalCommisionPascabayar">Rp 0</span></p>
                                </div>


                            </div>
                        </div>



                        <div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    @endsection
    @push('js')
        <script>
            $(document).ready(function() {
                let range;
                $('input[name="daterange"]').daterangepicker({
                    opens: 'left'
                }, function(start, end, label) {});

                $(document).on('change', '#filterRangePicker', function(e) {
                    filterRangePicker = $('#filterRangePicker').val();
                    callDataSummaryKomisi();
                });

                const callDataSummaryKomisi = () => {
                    $.ajax({
                        url: '{!! route('admin.ppob.komisi.summary.all') !!}',
                        data: {
                            '_token': "{{ csrf_token() }}",
                            'filterRangePicker': $('#filterRangePicker').val()
                        },
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function() {
                            $("#contentMaster").addClass('loading-skeleton');
                        },
                        success: function(summaryKomisi) {
                            console.log(summaryKomisi);
                            $("#contentMaster").removeClass('loading-skeleton');
                            // tanggal
                            $('#tanggalKomisiPrabayar').text(summaryKomisi.date);
                            $('#tanggalKomisiPascabayar').text(summaryKomisi.date);
                            // prabayar
                            $('#memberSalesPrabayarTanggal').text(summaryKomisi.dataPrepaid.data
                                .member_sales);
                            $('#commisionMemberPrabayarTanggal').text(summaryKomisi.dataPrepaid.data
                                .member_commission);
                            $('#leaderCommisionPrabayarTanggal').text(summaryKomisi.dataPrepaid.data
                                .leader_commission);

                            const leaderCommissionNominal = parseFloat(summaryKomisi.dataPrepaid.data
                                .leader_commission.replace(
                                    'Rp', '').replace('.', ''));
                            const memberCommissionNominal = parseFloat(summaryKomisi.dataPrepaid.data
                                .member_commission.replace(
                                    'Rp', '').replace('.', ''));

                            $('#totalCommisionPrabayar').text("Rp" +
                                (leaderCommissionNominal +
                                    memberCommissionNominal));
                            // pascabayar
                            $('#memberSalesPascabayarTanggal').text(summaryKomisi.dataPostPaid.data
                                .member_sales);
                            $('#commisionMemberPascabayarTanggal').text(summaryKomisi.dataPostPaid.data
                                .member_commission);
                            $('#leaderCommisionPascabayarTanggal').text(summaryKomisi.dataPostPaid.data
                                .leader_commission);

                            const leaderCommissionNominalPasca = parseFloat(summaryKomisi.dataPostPaid
                                .data
                                .leader_commission.replace(
                                    'Rp', '').replace('.', ''));
                            const memberCommissionNominalPasca = parseFloat(summaryKomisi.dataPostPaid
                                .data
                                .member_commission.replace(
                                    'Rp', '').replace('.', ''));

                            $('#totalCommisionPascabayar').text("Rp" +
                                (leaderCommissionNominalPasca +
                                    memberCommissionNominalPasca));
                        }
                    });
                }
                callDataSummaryKomisi();
            })
        </script>
    @endpush
