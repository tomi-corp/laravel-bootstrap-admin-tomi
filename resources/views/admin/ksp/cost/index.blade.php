@extends('layout.app')
@section('title', 'Cost')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">Cost</li>
                    </ol>
                </div>
                <h4 class="page-title">Cost</h4>
            </div>
        </div>


        <div class="col-6">
            {{-- <div class="card">
                <div class="card-body">
                    <h4>Rekening Repayment</h4>
                    <div class="form-group">
                        <label>Nama Bank</label>
                        <div class="form-group">
                            <select class="form-control" id="bank">
                                @foreach ($data['bank'] as $bank)
                                    <option value="{{ $bank->id }}"
                                        {{ $data['bankTomi']['bank_id'] == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->code_bank }}
                                    </option>
                                @endforeach
                                <small id="errBank"></small>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>A.N Rekening</label>
                            <input class="form-control" type="text" id="an_rekening"
                                value="{{ $data['bankTomi']['atas_nama'] }}"></input>
                            <small id="errANRekening"></small>
                        </div>

                        <div class="form-group">
                            <label>No Rekening</label>
                            <input class="form-control" type="number" id="no_rekening"
                                value="{{ $data['bankTomi']['nomor_rekening'] }}"></input>
                            <small id="errNoRekening"></small>
                        </div>
                    </div>

                    <button class="btn btn-primary float-right" type="button" id="buttonSubmitRekening"
                        onclick="buttonSubmitSaveRekening()">Save
                        <div id="loadingTextRekening">
                        </div>
                    </button>
                </div>


            </div> --}}

            <div class="card">
                <div class="card-body">
                    <h4>Rekening Fee Agregator Tomi (PANDU)</h4>
                    <div class="form-group">
                        <label>Nama Bank</label>
                        <div class="form-group">
                            <select class="form-control" id="bankPandu">
                                @foreach ($data['bank'] as $bank)
                                    <option value="{{ $bank->id }}"
                                        {{ $data['bankPandu']['bank_id'] == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->code_bank }}
                                    </option>
                                @endforeach
                                <small id="errBankPandu"></small>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>A.N Rekening</label>
                            <input class="form-control" type="text" id="an_rekeningPandu"
                                value="{{ $data['bankPandu']['atas_nama'] }}"></input>
                            <small id="errANRekeningPandu"></small>
                        </div>

                        <div class="form-group">
                            <label>No Rekening</label>
                            <input class="form-control" type="number" id="no_rekeningPandu"
                                value="{{ $data['bankPandu']['nomor_rekening'] }}"></input>
                            <small id="errNoRekeningPandu"></small>
                        </div>
                        {{-- <input class="form-control format-currency" id="penerbitan_sertifikat_elektronik" type="number"
                            value="{{ $data['identity_verifikasi'] }}"></input>
                        <small id="errpenerbitan_sertifikat_elektronik"></small> --}}
                    </div>

                    <button class="btn btn-primary float-right" type="button" id="buttonSubmitRekeningPandu"
                        onclick="buttonSubmitSaveRekeningPandu()">Save
                        <div id="loadingTextRekeningPandu">
                        </div>
                    </button>
                </div>
            </div>

        </div>


        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <h4>Cost</h4>
                    <div class="form-group">
                        <label>Penerbitan Sertifikat Elektronik</label>
                        <input class="form-control format-currency" id="penerbitan_sertifikat_elektronik" type="number"
                            value="{{ $data['penerbitan_sertifikat_elektronik'] }}"></input>
                        <small id="errpenerbitan_sertifikat_elektronik"></small>
                    </div>

                    <div class="form-group">
                        <label>PK Automation</label>
                        <input class="form-control" id="pkAutomation" type="number"
                            value="{{ $data['pk_automation'] }}"></input>
                        <small id="errpkAutomation"></small>
                    </div>

                    <div class="form-group">
                        <label>Wa OTP</label>
                        <input class="form-control" id="waotp" type="number" value="{{ $data['wa_otp'] }}"></input>
                        <small id="errwaotp"></small>
                    </div>

                    <div class="form-group">
                        <label>Disbursement</label>
                        <input class="form-control" id="disbursement" type="number"
                            value="{{ $data['disbursement'] }}"></input>
                        <small id="errdisbursement"></small>
                    </div>

                    <button class="btn btn-primary float-right" type="button" id="buttonSubmit"
                        onclick="buttonSubmit()">Save
                        <div id="loadingText">
                        </div>
                    </button>
                </div>


            </div>
        </div>

    </div>







@endsection
@push('js')
    <script>
        var dataTable = {
            "_token": "{{ csrf_token() }}",
        };


        buttonSubmit = () => {
            $.ajax({
                url: '{!! route('admin.cost.add') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'penerbitan_sertifikat_elektronik': $("#penerbitan_sertifikat_elektronik").val(),
                    'pk_automation': $("#pkAutomation").val(),
                    'wa_otp': $("#waotp").val(),
                    'disbursement': $("#disbursement").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmit').attr("disabled", true);
                    $('#loadingText').addClass("spinner-border");
                },
                success: function(saveBiaya) {
                    toastr.success(saveBiaya.message)
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                    $('#errpenerbitan_sertifikat_elektronik').text();
                    $('#errpkAutomation').text();
                    $('#errwaotp').text();
                    $('#errdisbursement').text();
                },
                error: function(saveConfig) {
                    $('#errpenerbitan_sertifikat_elektronik').text(saveConfig.responseJSON.errors
                        .penerbitan_sertifikat_elektronik);
                    $('#errpkAutomation').text(saveConfig.responseJSON.errors
                        .pk_automation);
                    $('#errwaotp').text(saveConfig.responseJSON.errors
                        .wa_otp);
                    $('#errdisbursement').text(saveConfig.responseJSON.errors
                        .disbursement);
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                }
            });
        }


        buttonSubmitSaveRekening = () => {
            $.ajax({
                url: '{!! route('admin.cost.save.rekening') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'bank': $("#bank").val(),
                    'atasNamaRekening': $("#an_rekening").val(),
                    'noRekening': $("#no_rekening").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmitRekening').attr("disabled", true);
                    $('#loadingTextRekening').addClass("spinner-border");
                },
                success: function(saveBiaya) {
                    toastr.success(saveBiaya.message)
                    $('#buttonSubmitRekening').attr("disabled", false);
                    $('#loadingTextRekening').removeClass("spinner-border");
                    $('#errBank').text();
                    $('#errANRekening').text();
                    $('#errNoRekening').text();
                },
                error: function(saveConfig) {
                    $('#errBank').text(saveConfig.responseJSON.errors
                        .bank);
                    $('#errANRekening').text(saveConfig.responseJSON.errors
                        .atasNamaRekening);
                    $('#errNoRekening').text(saveConfig.responseJSON.errors
                        .noRekening);
                    $('#buttonSubmitRekening').attr("disabled", false);
                    $('#loadingTextRekening').removeClass("spinner-border");
                }
            });
        }



        buttonSubmitSaveRekeningPandu = () => {
            $.ajax({
                url: '{!! route('admin.cost.save.rekeningPandu') !!}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'bank': $("#bankPandu").val(),
                    'atasNamaRekening': $("#an_rekeningPandu").val(),
                    'noRekening': $("#no_rekeningPandu").val(),
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmitRekeningPandu').attr("disabled", true);
                    $('#loadingTextRekeningPandu').addClass("spinner-border");
                },
                success: function(saveBiaya) {
                    toastr.success(saveBiaya.message)
                    $('#buttonSubmitRekeningPandu').attr("disabled", false);
                    $('#loadingTextRekeningPandu').removeClass("spinner-border");
                    $('#errBankPandu').text();
                    $('#errANRekeningPandu').text();
                    $('#errNoRekeningPandu').text();
                },
                error: function(saveConfig) {
                    $('#errBankPandu').text(saveConfig.responseJSON.errors
                        .bank);
                    $('#errANRekeningPandu').text(saveConfig.responseJSON.errors
                        .atasNamaRekening);
                    $('#errNoRekeningPandu').text(saveConfig.responseJSON.errors
                        .noRekening);
                    $('#buttonSubmitRekeningPandu').attr("disabled", false);
                    $('#loadingTextRekeningPandu').removeClass("spinner-border");
                }
            });
        }
    </script>
@endpush
