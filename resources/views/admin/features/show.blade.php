@extends('layout.app')
@section('title', 'Detail Fitur')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.features.index') }}">Fitur</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
            <h4 class="page-title">Detail Fitur</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="text-right mb-3">
                    <a href="{{ route('admin.features.edit', $feature) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('admin.features.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <table class="table table-bordered">
                    <tr><th style="width: 25%">Kode</th><td>{{ strtoupper($feature->code) }}</td></tr>
                    <tr><th>Nama</th><td>{{ strtoupper($feature->name) }}</td></tr>
                    <tr><th>Deskripsi</th><td>{{ $feature->description ? strtoupper($feature->description) : '-' }}</td></tr>
                    <tr><th>Status</th><td>{{ strtoupper($statuses[$feature->status] ?? $feature->status) }}</td></tr>
                    <tr><th>Dibuat</th><td>{{ optional($feature->created_at)->format('Y-m-d H:i:s') ?? '-' }}</td></tr>
                    <tr><th>Diperbarui</th><td>{{ optional($feature->updated_at)->format('Y-m-d H:i:s') ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
