@extends('bta.layout.app')
@section('title', 'Profile Saya')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center my-3">
        <h4 class="page-title">Profile Saya</h4>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" value="{{ $user->username }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="{{ $user->email }}" readonly>
            </div>
        </div>
    </div>

    <!-- Form Ganti Password -->
    <div class="card">
        <div class="card-header">Ganti Password</div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('profile.updatePassword') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection