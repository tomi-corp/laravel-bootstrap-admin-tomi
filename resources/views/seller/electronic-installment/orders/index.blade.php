@extends('seller.layout.app')

@section('title', 'Electronic Installment Orders')

@section('content')

<div class="container">

    <h3 class="mb-4">
        Daftar Order
    </h3>

    @if (session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

    @endif

    <div class="card">

        <div class="card-body table-responsive">

            <table id="orders-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pelanggan</th>
                        <th>Produk</th>
                        <th>Varian</th>
                        <th>Harga</th>
                        <th>Cicilan / Bulan</th>
                        <th>Status</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($orders as $order)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>

                            <strong>
                                {{ $order->user->name ?? '-' }}
                            </strong>

                            <br>

                            <small>
                                {{ $order->user->phone ?? '-' }}
                            </small>

                            <br>

                            <small>
                                {{ $order->user->address ?? '-' }}
                            </small>

                        </td>

                        <td>
                            {{ $order->product->name ?? '-' }}
                        </td>

                        <td>

                            @if ($order->variant)

                            {{ $order->variant->sku ?? '-' }}

                            <br>

                            <small>

                                RAM:
                                {{ $order->variant->ram_gb ?? '-' }} GB,

                                Storage:
                                {{ $order->variant->storage_gb ?? '-' }} GB,

                                Warna:
                                {{ $order->variant->color ?? '-' }}

                            </small>

                            @else

                            -

                            @endif

                        </td>

                        <td>
                            Rp {{ number_format($order->price, 0, ',', '.') }}
                        </td>


                        <td>
                            {{ $order->installment_month }} bulan
                        </td>

                        <td>
                            Rp {{ number_format($order->monthly_installment, 0, ',', '.') }}
                        </td>

                        <td>

                            <span class="badge badge-{{
                                    $order->status == 'approved'
                                    ? 'success'
                                    : ($order->status == 'rejected'
                                    ? 'danger'
                                    : 'warning')
                                }}">

                                {{ ucfirst($order->status) }}

                            </span>

                        </td>

                        <td>

                            @if ($order->status == 'pending')

                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                data-target="#approveModal{{ $order->id }}">

                                Terima

                            </button>

                            @else

                            <span class="text-muted">
                                -
                            </span>

                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="10" class="text-center">

                            Belum ada order.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- MODAL APPROVE ORDER --}}
@foreach ($orders as $order)

<div class="modal fade" id="approveModal{{ $order->id }}" tabindex="-1" role="dialog"
    aria-labelledby="approveModalTitle{{ $order->id }}" aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable" role="document">

        <form action="{{ route('seller.electronic-installment.orders.approve', $order->id) }}" method="POST"
            class="w-100">

            @csrf

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="approveModalTitle{{ $order->id }}">

                        Approve Order

                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">

                        ×

                    </button>

                </div>

                <div class="modal-body">

                    <div class="form-group">

                        <label>
                            Serial Number
                        </label>

                        <input type="text" name="serial_number" class="form-control"
                            placeholder="Masukkan serial number" required>

                    </div>

                    <div class="form-group">

                        <label>
                            IMEI
                        </label>

                        <input type="text" name="imei" class="form-control" placeholder="Masukkan IMEI" required>

                    </div>


                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit" class="btn btn-primary">

                        Save

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@endforeach

@endsection

@push('js')

<script>

    $(document).ready(function () {

        $('#orders-table').DataTable({

            responsive: true,
            autoWidth: false,
            pageLength: 10,
            ordering: true,

            language: {

                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",

                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                },

                emptyTable: "Belum ada order",
                zeroRecords: "Data tidak ditemukan"

            }

        });

    });

</script>

@endpush