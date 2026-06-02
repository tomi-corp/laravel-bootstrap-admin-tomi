@extends('layout.app')

@section('title', 'Layanan Website')

@section('content')

<div class="container-fluid mt-4">

    <div class="card shadow-sm border-0">

        <div class="card-header py-3">
            <h4 class="mb-0">Daftar Invoice Website</h4>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Nomor Invoice</th>
                            <th>Bulan</th>
                            <th>Total Nasabah</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($invoices as $item)
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
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                Data invoice belum tersedia
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

@endsection