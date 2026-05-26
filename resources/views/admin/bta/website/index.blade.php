@extends('layout.app')

@section('title', 'Layanan Website')

@section('content')

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h4>Daftar Invoice Website</h4>
        </div>

        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Invoice</th>
                        <th>Bulan</th>
                        <th>Total Nasabah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($invoices as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['invoice'] }}</td>
                        <td>{{ $item['bulan'] }}</td>
                        <td>{{ $item['total_nasabah'] }}</td>
                        <td>
                            <a href="{{ route('admin.website.invoice.generate', [
                    'bulan' => $item['bulan_angka'],
                    'tahun' => $item['tahun']
                ]) }}" target="_blank" class="btn btn-primary btn-sm">
                                Generate PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection