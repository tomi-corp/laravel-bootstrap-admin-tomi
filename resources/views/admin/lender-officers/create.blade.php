@extends('layout.app')
@section('title', 'Tambah Pengurus Lender')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.lender-officers.index') }}">Pengurus Lender</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>
            <h4 class="page-title">Tambah Pengurus Lender</h4>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.lender-officers.store') }}" method="POST" enctype="multipart/form-data">
                    @include('admin.lender-officers._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
