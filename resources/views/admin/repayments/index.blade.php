@extends('layout.app')

@section('title', 'Admin Repayment')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Repayment</li>
                </ol>
            </div>
            <h4 class="page-title">Repayment</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Repayment
                    </button>
                </div>

                <table id="repaymentsTable" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Invoice</th>
                            <th>Perusahaan</th>
                            <th>Total Pembayaran</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Tanggal Bukti</th>
                            <th>Bukti</th>
                            <th>Admin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($repayments as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->invoice?->no_invoice ?? 'INV-'.$item->id_invoicing }}</td>
                                <td>{{ $item->invoice?->perusahaan?->nama ?? '-' }}</td>
                                <td>Rp {{ number_format($item->amount_total, 0, ',', '.') }}</td>
                                <td>{{ $item->tanggal_pembayaran ? $item->tanggal_pembayaran->format('d/m/Y H:i') : '-' }}</td>
                                <td>{{ $item->payment_proof_date ? $item->payment_proof_date->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if ($item->payment_proof_file)
                                        <a class="btn btn-sm btn-info" href="{{ asset('storage/'.$item->payment_proof_file) }}" target="_blank">
                                            <i class="fa-solid fa-file"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->admin?->nama ?? '-' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info" onclick="showRepayment({{ $item->id }})">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="editRepayment({{ $item->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteRepayment({{ $item->id }})">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="repaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repaymentModalTitle">Tambah Repayment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="repaymentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="repaymentId">

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Invoice</label>
                            <select class="form-control" name="id_invoicing" id="id_invoicing">
                                <option value="">Pilih Invoice</option>
                                @foreach ($invoices as $invoice)
                                    <option value="{{ $invoice->id }}">
                                        {{ $invoice->no_invoice ?? 'INV-'.$invoice->id }} - {{ $invoice->perusahaan?->nama ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-id_invoicing"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Total Pembayaran</label>
                            <input class="form-control" name="amount_total" id="amount_total" placeholder="5000000">
                            <small class="text-danger error-amount_total"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tanggal Pembayaran</label>
                            <input type="datetime-local" class="form-control" name="tanggal_pembayaran" id="tanggal_pembayaran">
                            <small class="text-danger error-tanggal_pembayaran"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tanggal Bukti Pembayaran</label>
                            <input type="date" class="form-control" name="payment_proof_date" id="payment_proof_date">
                            <small class="text-danger error-payment_proof_date"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Bukti Pembayaran</label>
                            <input type="file" class="form-control" name="payment_proof_file" id="payment_proof_file" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-muted d-block">Format jpg, jpeg, png, atau pdf. Maksimal 5 MB.</small>
                            <small class="text-danger error-payment_proof_file"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveRepaymentButton">
                        <span id="saveSpinner" role="status" aria-hidden="true"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Repayment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0" id="detailList"></dl>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $('#repaymentsTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        language: {
            emptyTable: 'Belum ada repayment.',
            zeroRecords: 'Repayment tidak ditemukan.',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
        }
    });

    function openCreateModal() {
        clearErrors();
        $('#repaymentForm')[0].reset();
        $('#repaymentId').val('');
        $('#repaymentModalTitle').text('Tambah Repayment');
        $('#repaymentModal').modal('show');
    }

    function editRepayment(id) {
        clearErrors();
        $('#repaymentForm')[0].reset();
        $('#repaymentId').val(id);
        $('#repaymentModalTitle').text('Ubah Repayment');

        $.get('{{ route('admin.repayments.show', ':id') }}'.replace(':id', id))
            .done(function (response) {
                var item = response.data;
                $('#id_invoicing').val(item.id_invoicing);
                $('#amount_total').val(item.amount_total);
                $('#tanggal_pembayaran').val(item.tanggal_pembayaran);
                $('#payment_proof_date').val(item.payment_proof_date);
                $('#repaymentModal').modal('show');
            })
            .fail(function () {
                toastr.error('Gagal mengambil detail repayment.');
            });
    }

    function showRepayment(id) {
        $.get('{{ route('admin.repayments.show', ':id') }}'.replace(':id', id))
            .done(function (response) {
                var item = response.data;
                var proof = item.payment_proof_url ? '<a href="' + item.payment_proof_url + '" target="_blank">Lihat bukti</a>' : '-';
                var rows = {
                    'No Invoice': item.invoice && item.invoice.no_invoice ? item.invoice.no_invoice : 'INV-' + item.id_invoicing,
                    'Perusahaan': item.invoice && item.invoice.perusahaan ? item.invoice.perusahaan.nama : '-',
                    'Admin': item.admin && item.admin.nama ? item.admin.nama : '-',
                    'Total Pembayaran': formatRupiah(item.amount_total),
                    'Tanggal Pembayaran': item.tanggal_pembayaran || '-',
                    'Tanggal Bukti': item.payment_proof_date || '-',
                    'Bukti Pembayaran': proof
                };

                var html = '';
                Object.keys(rows).forEach(function (label) {
                    html += '<dt class="col-sm-4">' + label + '</dt><dd class="col-sm-8">' + rows[label] + '</dd>';
                });

                $('#detailList').html(html);
                $('#detailModal').modal('show');
            })
            .fail(function () {
                toastr.error('Gagal mengambil detail repayment.');
            });
    }

    function deleteRepayment(id) {
        if (!confirm('Hapus repayment ini?')) {
            return;
        }

        $.ajax({
            url: '{{ route('admin.repayments.destroy', ':id') }}'.replace(':id', id),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                toastr.success(response.message);
                window.location.reload();
            },
            error: function () {
                toastr.error('Gagal menghapus repayment.');
            }
        });
    }

    $('#repaymentForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#repaymentId').val();
        var url = id ? '{{ route('admin.repayments.update', ':id') }}'.replace(':id', id) : '{{ route('admin.repayments.store') }}';
        var formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');

        if (id) {
            formData.append('_method', 'PUT');
        }

        $('#saveRepaymentButton').attr('disabled', true);
        $('#saveSpinner').addClass('spinner-border spinner-border-sm');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success(response.message);
                window.location.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                        $('.error-' + key).text(xhr.responseJSON.errors[key][0]);
                    });
                    return;
                }

                toastr.error('Gagal menyimpan repayment.');
            },
            complete: function () {
                $('#saveRepaymentButton').attr('disabled', false);
                $('#saveSpinner').removeClass('spinner-border spinner-border-sm');
            }
        });
    });

    function clearErrors() {
        $('[class*="error-"]').text('');
    }

    function formatRupiah(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
    }
</script>
@endpush
