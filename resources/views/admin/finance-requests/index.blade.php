@extends('layout.app')

@section('title', 'Finance Request')

@section('content')

@php
$totalPerusahaan = count($perusahaans ?? []);
@endphp

{{-- =========================================================
PAGE TITLE
========================================================= --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box">

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0);">Tomi</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Finance Request
                    </li>
                </ol>
            </div>

            <h4 class="page-title">
                Finance Request
            </h4>

        </div>
    </div>
</div>


{{-- =========================================================
SUMMARY
========================================================= --}}
<div class="row">

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <div class="media">

                    <div class="media-body">

                        <span class="text-muted text-uppercase font-size-12 font-weight-bold">
                            Total Perusahaan
                        </span>

                        <h3 class="mb-0 mt-1">
                            {{ $totalPerusahaan }}
                        </h3>

                    </div>

                    <div class="avatar-sm rounded-circle bg-primary align-self-center">

                        <span class="avatar-title rounded-circle bg-primary">

                            <i class="mdi mdi-office-building font-20"></i>

                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
FINANCE REQUEST TABLE
========================================================= --}}
<div class="row">

    <div class="col-12">

        <div class="card">

            <div class="card-body">

                <h4 class="header-title mb-3">
                    Daftar Finance Request
                </h4>

                <table class="table table-bordered table-hover dt-responsive nowrap" id="financeRequestTable"
                    style="width: 100%;">

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Max Plafon</th>
                            <th class="text-center">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($perusahaans ?? [] as $perusahaan)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $perusahaan['nama'] }}
                            </td>

                            <td>
                                Rp {{ number_format(
                                $perusahaan['max_plafon'] ?? 0,
                                0,
                                ',',
                                '.'
                                ) }}
                            </td>

                            <td class="text-center">

                                <a href="{{ route(
                                            'admin.finance-request.show',
                                            $perusahaan['id']
                                        ) }}" class="btn btn-sm btn-outline-info" title="Lihat Finance Request">

                                    <i class="fas fa-eye"></i>

                                </a>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="4" class="text-center text-muted">
                                Belum ada data finance request.
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


@push('js')

<script>

    $(document).ready(function () {

        $('#financeRequestTable').DataTable({

            responsive: true,

            pageLength: 10,

            ordering: true,

            language: {

                emptyTable:
                    'Belum ada data finance request.',

                search:
                    'Cari:',

                lengthMenu:
                    'Tampilkan _MENU_ data',

                info:
                    'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                infoEmpty:
                    'Menampilkan 0 data',

                zeroRecords:
                    'Data finance request tidak ditemukan.',

                paginate: {

                    previous:
                        'Sebelumnya',

                    next:
                        'Berikutnya'

                }

            }

        });

    });

</script>

@endpush