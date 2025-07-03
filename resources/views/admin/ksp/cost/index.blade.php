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

        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="form-group">
                        <label>Identity Verification</label>
                        <input class="form-control" id="identityVerification" type="number"
                            value="{{ $data['identity_verifikasi'] }}"></input>
                        <small id="erridentityVerification"></small>
                    </div>

                    <div class="form-group">
                        <label>Disbursement Peminjam (VAT)</label>
                        <input class="form-control" id="disbursementPeminjam" type="number"
                            value="{{ $data['disbursement_peminjam'] }}"></input>
                        <small id="errdisbursementPeminjam"></small>
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
                    'identity_verification': $("#identityVerification").val(),
                    'disbursement_peminjam': $("#disbursementPeminjam").val(),
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
                },
                error: function(saveConfig) {
                    $('#erridentityVerification').text(saveConfig.responseJSON.errors
                        .identity_verification);
                    $('#errdisbursementPeminjam').text(saveConfig.responseJSON.errors
                        .disbursement_peminjam);
                    $('#buttonSubmit').attr("disabled", false);
                    $('#loadingText').removeClass("spinner-border");
                }
            });
        }
    </script>
@endpush
