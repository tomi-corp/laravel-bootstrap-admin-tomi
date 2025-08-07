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
            <div class="card">
                <div class="card-body">
                    <h4>Cost</h4>
                    <div class="form-group">
                        <label>Identity Verification</label>
                        <input class="form-control format-currency" id="identityVerification" type="number"
                            value="{{ $data['identity_verifikasi'] }}"></input>
                        <small id="erridentityVerification"></small>
                    </div>

                    <div class="form-group">
                        <label>Disbursement Peminjam + VAT</label>
                        <input class="form-control" id="disbursementPeminjam" type="number"
                            value="{{ $data['disbursement_peminjam'] }}"></input>
                        <small id="errdisbursementPeminjam"></small>
                    </div>

                    <div class="form-group">
                        <label>Disbursement Tomi + VAT</label>
                        <input class="form-control" id="disbursementTomi" type="number"
                            value="{{ $data['disbursement_tomi'] }}"></input>
                        <small id="errdisbursementTomi"></small>
                    </div>

                    <button class="btn btn-primary float-right" type="button" id="buttonSubmit"
                        onclick="buttonSubmit()">Save
                        <div id="loadingText">
                        </div>
                    </button>
                </div>


            </div>
        </div>

        <div class="col-6">
            <div class="card">
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
                        {{-- <input class="form-control format-currency" id="identityVerification" type="number"
                            value="{{ $data['identity_verifikasi'] }}"></input>
                        <small id="erridentityVerification"></small> --}}
                    </div>

                    <button class="btn btn-primary float-right" type="button" id="buttonSubmitRekening"
                        onclick="buttonSubmitSaveRekening()">Save
                        <div id="loadingTextRekening">
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
                    'identity_verification': $("#identityVerification").val(),
                    'disbursement_peminjam': $("#disbursementPeminjam").val(),
                    'disbursement_tomi': $("#disbursementTomi").val(),
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
                    $('#erridentityVerification').text();
                    $('#errdisbursementPeminjam').text();
                    $('#errdisbursementTomi').text();
                },
                error: function(saveConfig) {
                    $('#erridentityVerification').text(saveConfig.responseJSON.errors
                        .identity_verification);
                    $('#errdisbursementPeminjam').text(saveConfig.responseJSON.errors
                        .disbursement_peminjam);
                    $('#errdisbursementTomi').text(saveConfig.responseJSON.errors
                        .disbursement_tomi);
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
    </script>
@endpush
