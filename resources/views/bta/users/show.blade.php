@extends('bta.layout.app')
@section('title', 'Detail User BTA')

@section('content')
<div class="container">
    <h2>Detail User</h2>

    <div class="card">
        <div class="card-body">
            {{-- Username --}}
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" class="form-control" value="{{ $user->username }}" readonly>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" value="{{ $user->email }}" readonly>
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" class="form-control" value="********" readonly>
            </div>

            {{-- Type --}}
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select id="type" class="form-control" readonly disabled>
                    <option value="master" {{ $user->type=='master' ? 'selected' : '' }}>Master</option>
                    <option value="admin" {{ $user->type=='admin' ? 'selected' : '' }}>Admin</option>
                    <option value="analyst" {{ $user->type=='analyst' ? 'selected' : '' }}>Analyst</option>
                    <option value="credam" {{ $user->type=='credam' ? 'selected' : '' }}>Credit Admin</option>
                </select>
            </div>

            {{-- Timestamps --}}
            <div class="mb-3">
                <label class="form-label">Dibuat pada</label>
                <input type="text" class="form-control" value="{{ $user->created_at }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Diperbarui pada</label>
                <input type="text" class="form-control" value="{{ $user->updated_at }}" readonly>
            </div>

            {{-- Aksi --}}
            <a href="{{ route('bta.users.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('bta.users.edit', $user->id) }}" class="btn btn-primary">Edit User</a>
            <form action="{{ route('bta.users.destroy', $user->id) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Hapus User</button>
            </form>
        </div>
    </div>
</div>
@endsection