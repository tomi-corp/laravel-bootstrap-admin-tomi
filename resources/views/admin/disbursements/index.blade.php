@extends('layout.app')

@section('title', 'Admin Pencairan Dana')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Pencairan Dana</li>
                </ol>
            </div>
            <h4 class="page-title">Pencairan Dana</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Pencairan Dana
                    </button>
                </div>

                <table id="disbursementsTable" class="table dt-responsive table-hover text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pinjaman</th>
                            <th>Nama</th>
                            <th>Perusahaan</th>
                            <th>Bank</th>
                            <th>Dana Dicairkan</th>
                            <th>Tanggal Transfer</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($disbursements as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->pinjaman?->id_pinjaman ?? '-' }}</td>
                                <td>{{ $item->atas_nama }}</td>
                                <td>{{ $item->pinjaman?->perusahaan?->nama ?? '-' }}</td>
                                <td>{{ trim(($item->bank?->code_bank ?? '').' '.($item->bank?->description ?? '')) ?: '-' }}</td>
                                <td>Rp {{ number_format($item->dana_yang_dicairkan, 0, ',', '.') }}</td>
                                <td>{{ $item->transfer_date ? $item->transfer_date->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if ($item->transfer_proof)
                                        <a class="btn btn-sm btn-info" href="{{ asset('storage/'.$item->transfer_proof) }}" target="_blank">
                                            <i class="fa-solid fa-file"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info" onclick="showDisbursement({{ $item->id }})">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="editDisbursement({{ $item->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteDisbursement({{ $item->id }})">
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

<div class="modal fade" id="disbursementModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disbursementModalTitle">Tambah Pencairan Dana</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <form id="disbursementForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="disbursementId">

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Pinjaman</label>
                            <select class="form-control" name="pinjaman_id" id="pinjaman_id">
                                <option value="">Pilih Pinjaman</option>
                                @foreach ($pinjamanList as $pinjaman)
                                    <option value="{{ $pinjaman->id }}">
                                        {{ $pinjaman->id_pinjaman ?? 'PIN-'.$pinjaman->id }} - {{ $pinjaman->user?->name ?? $pinjaman->user?->nama_lengkap ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-pinjaman_id"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Bank</label>
                            <select class="form-control" name="bank_id" id="bank_id">
                                <option value="">Pilih Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->code_bank }} - {{ $bank->description }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-bank_id"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Dana Dicairkan</label>
                            <input class="form-control" name="dana_yang_dicairkan" id="dana_yang_dicairkan" placeholder="5000000">
                            <small class="text-danger error-dana_yang_dicairkan"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tanggal Transfer</label>
                            <input type="date" class="form-control" name="transfer_date" id="transfer_date">
                            <small class="text-danger error-transfer_date"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Atas Nama</label>
                            <input class="form-control" name="atas_nama" id="atas_nama">
                            <small class="text-danger error-atas_nama"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nomor Rekening</label>
                            <input class="form-control" name="nomor_rekening" id="nomor_rekening">
                            <small class="text-danger error-nomor_rekening"></small>
                        </div>
                        <div class="col-12 form-group">
                            <label>Bukti Transfer</label>
                            <input type="file" class="form-control" name="transfer_proof" id="transfer_proof" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-muted d-block">Format jpg, jpeg, png, atau pdf. Maksimal 5 MB.</small>
                            <small class="text-danger error-transfer_proof"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveDisbursementButton">
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
                <h5 class="modal-title">Detail Pencairan Dana</h5>
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
    $('#disbursementsTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        language: {
            emptyTable: 'Belum ada pencairan dana.',
            zeroRecords: 'Pencairan dana tidak ditemukan.',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
        }
    });

    function openCreateModal() {
        clearErrors();
        $('#disbursementForm')[0].reset();
        $('#disbursementId').val('');
        $('#disbursementModalTitle').text('Tambah Pencairan Dana');
        $('#disbursementModal').modal('show');
    }

    function editDisbursement(id) {
        clearErrors();
        $('#disbursementForm')[0].reset();
        $('#disbursementId').val(id);
        $('#disbursementModalTitle').text('Ubah Pencairan Dana');

        $.get('{{ route('admin.disbursements.show', ':id') }}'.replace(':id', id))
            .done(function (response) {
                var item = response.data;
                $('#pinjaman_id').val(item.pinjaman_id);
                $('#bank_id').val(item.bank_id);
                $('#dana_yang_dicairkan').val(item.dana_yang_dicairkan);
                $('#atas_nama').val(item.atas_nama);
                $('#nomor_rekening').val(item.nomor_rekening);
                $('#transfer_date').val(item.transfer_date);
                $('#disbursementModal').modal('show');
            })
            .fail(function () {
                toastr.error('Gagal mengambil detail pencairan dana.');
            });
    }

    function showDisbursement(id) {
        $.get('{{ route('admin.disbursements.show', ':id') }}'.replace(':id', id))
            .done(function (response) {
                var item = response.data;
                var proof = item.transfer_proof_url ? '<a href="' + item.transfer_proof_url + '" target="_blank">Lihat bukti</a>' : '-';
                var rows = {
                    'ID Pinjaman': item.pinjaman && item.pinjaman.id_pinjaman ? item.pinjaman.id_pinjaman : '-',
                    'Admin': item.admin && item.admin.nama ? item.admin.nama : '-',
                    'Bank': item.bank ? ((item.bank.code_bank || '') + ' ' + (item.bank.description || '')) : '-',
                    'Dana Dicairkan': formatRupiah(item.dana_yang_dicairkan),
                    'Atas Nama': item.atas_nama || '-',
                    'Nomor Rekening': item.nomor_rekening || '-',
                    'Tanggal Transfer': item.transfer_date || '-',
                    'Bukti Transfer': proof
                };

                var html = '';
                Object.keys(rows).forEach(function (label) {
                    html += '<dt class="col-sm-4">' + label + '</dt><dd class="col-sm-8">' + rows[label] + '</dd>';
                });

                $('#detailList').html(html);
                $('#detailModal').modal('show');
            })
            .fail(function () {
                toastr.error('Gagal mengambil detail pencairan dana.');
            });
    }

    function deleteDisbursement(id) {
        if (!confirm('Hapus pencairan dana ini?')) {
            return;
        }

        $.ajax({
            url: '{{ route('admin.disbursements.destroy', ':id') }}'.replace(':id', id),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                toastr.success(response.message);
                window.location.reload();
            },
            error: function () {
                toastr.error('Gagal menghapus pencairan dana.');
            }
        });
    }

    $('#disbursementForm').on('submit', function (event) {
        event.preventDefault();
        clearErrors();

        var id = $('#disbursementId').val();
        var url = id ? '{{ route('admin.disbursements.update', ':id') }}'.replace(':id', id) : '{{ route('admin.disbursements.store') }}';
        var formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');

        if (id) {
            formData.append('_method', 'PUT');
        }

        $('#saveDisbursementButton').attr('disabled', true);
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

                toastr.error('Gagal menyimpan pencairan dana.');
            },
            complete: function () {
                $('#saveDisbursementButton').attr('disabled', false);
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
