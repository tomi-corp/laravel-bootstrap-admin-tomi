@extends('layout.app')

@section('title', $sectionLabel . ' ' . $productLabel)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">{{ $productLabel }}</li>
                    <li class="breadcrumb-item active">{{ $sectionLabel }}</li>
                </ol>
            </div>
            <h4 class="page-title">{{ $sectionLabel }} {{ $productLabel }}</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="header-title mb-2">{{ $sectionLabel }} {{ $productLabel }}</h4>
        <p class="text-muted mb-0">Kelola {{ strtolower($sectionLabel) }} untuk {{ $productLabel }}.</p>
    </div>
</div>
@endsection
