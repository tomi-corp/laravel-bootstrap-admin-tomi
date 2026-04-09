@extends('bta.layout.app')
@section('title', 'Profile Saya')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center my-3">
        <h4 class="page-title">Profile Saya</h4>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" value="{{ $user->username }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="{{ $user->email }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" value="********" readonly>
            </div>
        </div>
    </div>
</div>
@endsection