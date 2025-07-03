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
                <h4 class="page-title">PPOB</h4>
            </div>
        </div>


        <div class="col-12">
            <div class="col-md-2 pb-4">
                <label>Filter :</label>
                <input type="text" name="daterange" class="form-control" value="" id="filterRangePicker" />
            </div>


            <div class="row " id="contentMaster">
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <span>Komisi prabayar tanggal :</br>
                                    <span id="tanggalKomisiPrabayar"></span>
                                </span>
                            </div>


                            <div class="d-flex justify-content-between pt-4">
                                <h5><span id="memberSalesPrabayarTanggal">Rp 0</span> </br>Member sales</h5>
                                <h5><span id="commisionMemberPrabayarTanggal">Rp 0</span> </br>Komisi member</h5>
                                <h5><span id="leaderCommisionPrabayarTanggal">Rp 0</span> </br>Komisi leader</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between">
                                <span>Komisi pascabayar tanggal :</br>
                                    <span id="tanggalKomisiPascabayar"></span>
                                </span>

                            </div>

                            <div class="d-flex justify-content-between pt-4">
                                <h5><span id="memberSalesPascabayarTanggal">Rp 0</span> </br>Member sales</h5>
                                <h5><span id="commisionMemberPascabayarTanggal">Rp 0</span> </br>Komisi member</h5>
                                <h5><span id="leaderCommisionPascabayarTanggal">Rp 0</span> </br>Komisi leader</h5>
                            </div>
                        </div>
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
                        // pascabayar
                        $('#memberSalesPascabayarTanggal').text(summaryKomisi.dataPostPaid.data
                            .member_sales);
                        $('#commisionMemberPascabayarTanggal').text(summaryKomisi.dataPostPaid.data
                            .member_commission);
                        $('#leaderCommisionPascabayarTanggal').text(summaryKomisi.dataPostPaid.data
                            .leader_commission);
                    }
                });
            }
            callDataSummaryKomisi();
        })
    </script>
@endpush
