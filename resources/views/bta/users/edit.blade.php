@extends('bta.layout.app')
@section('title', 'Edit User BTA')

@section('content')
<div class="container">
    <h2>Edit User</h2>

    {{-- Session success message --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('bta.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Method PUT untuk update --}}

                {{-- Username --}}
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username', $user->username) }}">
                    @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password dan Konfirmasi Password (1 row) --}}
                <div class="mb-3 row">
                    <div class="col">
                        <label for="password" class="form-label">Password <small>(kosongkan jika tidak ingin
                                diganti)</small></label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror">
                        @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Type --}}
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" class="form-control @error('type') is-invalid @enderror">
                        <option value="">-- Pilih Type --</option>
                        <option value="master" {{ old('type', $user->type)=='master' ? 'selected' : '' }}>Master
                        </option>
                        <option value="admin" {{ old('type', $user->type)=='admin' ? 'selected' : '' }}>Admin</option>
                        <option value="analyst" {{ old('type', $user->type)=='analyst' ? 'selected' : '' }}>Analyst
                        </option>
                        <option value="credam" {{ old('type', $user->type)=='credam' ? 'selected' : '' }}>Credit Admin
                        </option>
                    </select>
                    @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update User</button>
            </form>
        </div>
    </div>
</div>
@endsection