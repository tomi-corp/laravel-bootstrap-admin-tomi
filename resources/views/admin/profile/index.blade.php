@extends('layout.app')
@section('title', 'Profile')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
                <h4 class="page-title">Profile</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Informasi Profil</h4>
                    <div id="success-message" class="alert alert-success d-none" role="alert">
                        Profil berhasil diperbarui!
                    </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-control" value="{{ $user->username }}" required>
                            <small class="text-danger" id="errUsername"></small>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" required>
                            <small class="text-danger" id="errEmail"></small>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru (opsional)</label>
                            <input type="password" id="password" name="password" class="form-control">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                            <small class="text-danger" id="errPassword"></small>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        </div>

                        <button class="btn btn-primary float-right" type="button" id="buttonSubmitEdit"
                            onClick="saveUpdateProfile()">Update Profile <div id="loadingTextEdit">
                            </div></button>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Aktivitas Terakhir</h4>
                    <ul class="list-unstyled activity-list">
                        <li class="mb-3">
                            <div class="d-flex">
                                <div>
                                    <h5 class="mt-0 mb-1">Dibuat Pada</h5>
                                    <p class="text-muted font-13">{{ $user->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex">
                                <div>
                                    <h5 class="mt-0 mb-1">Terakhir Diperbarui</h5>
                                    <p class="text-muted font-13">{{ $user->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5>Qr Code Google Authenticator :</h5>
                    <div id="qrcode">
                        @if ($enabled)
                            <button class="btn btn-info btn-sm">Active</button>
                        @else
                            <button class="btn btn-primary btn-sm" id="enabled2fa">Enabled</button>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('js')
<script>
   $('#enabled2fa').click(() => {
    $('#enabled2fa').attr('disabled', true);
    $.ajax({
        url: '{!! route('admin.profile.enableTwoFactor') !!}',
        data: {
            '_token': "{{ csrf_token() }}",
        },
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            const qrcodeContainer = $('#qrcode');
            qrcodeContainer.html(res.data);
        },
        error: function(err) {
            toastr.info(err.responseJSON.message);
        }
    });
});

    saveUpdateProfile = () =>  {
                $.ajax({
                    url: '{!! route('admin.profile.update') !!}',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'username' : $('#username').val(),
                        'email' : $('#email').val(),
                        'password' : $('#password').val(),
                        'password_confirmation' : $('#password_confirmation').val()
                    },
                    type: 'POST',
                    dataType: 'json',
                beforeSend: function() {
                    $('#buttonSubmitEdit').attr("disabled", true);
                    $('#loadingTextEdit').addClass("spinner-border");
                },
                success: function(res) {
                    toastr.success(res.message);
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                },
                error: function(err) {
                    $('#buttonSubmitEdit').attr("disabled", false);
                    $('#loadingTextEdit').removeClass("spinner-border");
                    $('#errUsername').text(err.responseJSON.errors.username);
                    $('#errEmail').text(err.responseJSON.errors.email);
                    $('#errPassword').text(err.responseJSON.errors.password);
                }
            });
    }
</script>
@endpush