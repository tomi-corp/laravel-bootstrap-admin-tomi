@extends('layout.app')
@section('title', 'Tambah Fitur')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.features.index') }}">Fitur</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>
            <h4 class="page-title">Tambah Fitur</h4>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.features.store') }}" method="POST">
                    @include('admin.features._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
