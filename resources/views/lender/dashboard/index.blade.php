@extends('lender.layouts.app')
@section('title', 'Dasbor Lender')
@section('content')
<div class="row"><div class="col-12"><div class="page-title-box"><h4 class="page-title">Dasbor Lender</h4></div></div></div>
<div class="row">
    @foreach (['TomiDana', 'TomiDana Plus', 'TomiGawai', 'TomiGaji', 'TomiInvoice'] as $product)
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>{{ $product }}</h5><p class="text-muted mb-0">Ringkasan {{ strtolower($product) }} lender.</p></div></div></div>
    @endforeach
</div>
@endsection
