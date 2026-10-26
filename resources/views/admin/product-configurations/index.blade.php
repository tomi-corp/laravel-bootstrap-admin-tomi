@extends('layouts.admin')

@section('title', 'Konfigurasi ' . $productName)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">{{ $productName }}</li>
                        <li class="breadcrumb-item active">Konfigurasi</li>
                    </ol>
                </div>
                <h4 class="page-title">Konfigurasi {{ $productName }}</h4>
            </div>
        </div>
    </div>

    @php
        $companies = [
            ['name' => 'PT Contoh Sejahtera', 'pic' => 'PIC Perusahaan', 'lender' => 'Lender Demo', 'status' => 'AKTIF'],
            ['name' => 'PT Maju Bersama', 'pic' => 'PIC Perusahaan', 'lender' => 'Lender Demo', 'status' => 'AKTIF'],
        ];
        $configurations = [
            ['tenor' => '1 bulan', 'rate' => '12%', 'insurance' => '1%', 'provision' => '1%', 'admin' => '1%', 'tomi_fee' => '1%', 'service' => '0%', 'minimum' => 'Rp. 1.000.000', 'maximum' => 'Rp. 10.000.000'],
            ['tenor' => '3 bulan', 'rate' => '15%', 'insurance' => '1%', 'provision' => '1%', 'admin' => '1%', 'tomi_fee' => '1%', 'service' => '0%', 'minimum' => 'Rp. 1.000.000', 'maximum' => 'Rp. 25.000.000'],
            ['tenor' => '6 bulan', 'rate' => '18%', 'insurance' => '1%', 'provision' => '1%', 'admin' => '1%', 'tomi_fee' => '1%', 'service' => '0%', 'minimum' => 'Rp. 1.000.000', 'maximum' => 'Rp. 50.000.000'],
        ];
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="header-title mb-1">Daftar Perusahaan</h4>
                            <p class="text-muted mb-0">Pilih perusahaan untuk melihat konfigurasi tenor {{ $productName }}.</p>
                        </div>
                        <span class="badge badge-soft-warning">Mode tampilan sementara</span>
                    </div>

                    <div class="table-responsive">
                        <table id="tableProductConfiguration" class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Perusahaan</th>
                                    <th>PIC Perusahaan</th>
                                    <th>Lender</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($companies as $company)
                                <tr>
                                    <td>{{ strtoupper($company['name']) }}</td>
                                    <td>{{ strtoupper($company['pic']) }}</td>
                                    <td>{{ strtoupper($company['lender']) }}</td>
                                    <td><span class="badge badge-success">{{ $company['status'] }}</span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#productConfigurationModal">
                                            <i class="dripicons-gear mr-1"></i> Konfigurasi
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="productConfigurationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfigurasi Tenor {{ $productName }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">Data konfigurasi ini masih berupa tampilan sementara dan belum terhubung ke database.</div>
                <div class="table-responsive">
                    <table id="tableProductTenor" class="table table-bordered table-centered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Tenor</th>
                                <th>Rate</th>
                                <th>Asuransi</th>
                                <th>Provisi</th>
                                <th>Biaya Admin</th>
                                <th>Fee TOMI</th>
                                <th>Biaya Layanan</th>
                                <th>Minimal Nominal</th>
                                <th>Maximal Nominal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($configurations as $configuration)
                            <tr>
                                <td>{{ $configuration['tenor'] }}</td>
                                <td>{{ $configuration['rate'] }}</td>
                                <td>{{ $configuration['insurance'] }}</td>
                                <td>{{ $configuration['provision'] }}</td>
                                <td>{{ $configuration['admin'] }}</td>
                                <td>{{ $configuration['tomi_fee'] }}</td>
                                <td>{{ $configuration['service'] }}</td>
                                <td>{{ $configuration['minimum'] }}</td>
                                <td>{{ $configuration['maximum'] }}</td>
                                <td><button type="button" class="btn btn-sm btn-light" onclick="alert('Fitur edit akan tersedia setelah koneksi database diaktifkan.')">Edit</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('js')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(function () {
        $('#tableProductConfiguration').DataTable({
            pageLength: 10,
            order: [[0, 'asc']],
            language: { url: '{{ asset('assets/libs/datatables.net/i18n/Indonesian.json') }}' },
            columnDefs: [{ targets: 4, orderable: false, searchable: false }]
        });

        $('#tableProductTenor').DataTable({
            pageLength: 10,
            scrollX: true,
            language: { url: '{{ asset('assets/libs/datatables.net/i18n/Indonesian.json') }}' },
            columnDefs: [{ targets: 9, orderable: false, searchable: false }]
        });
    });
</script>
@endpush
