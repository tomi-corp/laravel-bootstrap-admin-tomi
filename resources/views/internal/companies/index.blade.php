@extends('layout.app')

@section('title', 'Perusahaan')

@push('css')
    <style>
        #companyTable th,
        #companyTable td { white-space: nowrap; vertical-align: middle; }
        #companyModal .modal-dialog,
        #companyModal .modal-content { max-height: calc(100vh - 2rem); }
        #companyModal .modal-body { overflow-y: auto; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box"><h4 class="page-title">Perusahaan</h4></div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#companyModal" onclick="openCreateCompany()">
                            <i class="mdi mdi-plus-circle mr-1"></i>Tambah Perusahaan
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="companyTable" class="table table-hover nowrap" style="width:100%">
                            <thead><tr><th>#</th><th>Kode Perusahaan</th><th>Nama Perusahaan</th><th>PIC Perusahaan</th><th>PIC Internal</th><th>Email</th><th>Telepon</th><th>Lender</th><th>Status</th><th>Tanggal Pembayaran</th><th>Aksi</th></tr></thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="companyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="companyModalTitle">Tambah Perusahaan</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button></div>
                <form id="companyForm">
                    <div class="modal-body">
                        <input type="hidden" id="companyId">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>PIC Perusahaan</label><input class="form-control" id="companyPic" name="pic_perusahaan"></div>
                            <div class="col-md-6 form-group"><label>PIC Internal</label><select class="form-control" id="internalAdmin" name="admin_id"><option value="">Pilih PIC internal</option>@foreach ($internalAdmins as $admin)<option value="{{ $admin->id }}">{{ $admin->username }} ({{ $admin->email }})</option>@endforeach</select></div>
                            <div class="col-md-6 form-group"><label>Nama Perusahaan</label><input class="form-control" id="companyName" name="nama_perusahaan"></div>
                            <div class="col-md-6 form-group"><label>Email</label><input class="form-control" id="companyEmail" name="email_perusahaan" type="email"></div>
                            <div class="col-md-6 form-group"><label>Telepon</label><input class="form-control" id="companyPhone" name="telepon" type="number"></div>
                            <div class="col-md-6 form-group"><label>Nomor Perjanjian</label><input class="form-control" id="agreementNumber" name="nomor_pks"></div>
                            <div class="col-md-6 form-group"><label>Provinsi</label><select class="form-control" id="province" name="provinsi"><option value="">Pilih provinsi</option>@foreach ($provinces as $province)<option value="{{ $province->provinsi }}">{{ $province->provinsi }}</option>@endforeach</select></div>
                            <div class="col-md-6 form-group"><label>Kabupaten / Kota</label><select class="form-control" id="city" name="kabupaten"><option value="">Pilih kabupaten / kota</option></select></div>
                            <div class="col-md-6 form-group"><label>Kecamatan</label><select class="form-control" id="district" name="kecamatan"><option value="">Pilih kecamatan</option></select></div>
                            <div class="col-md-6 form-group"><label>Desa</label><select class="form-control" id="village" name="desa"><option value="">Pilih desa</option></select></div>
                            <div class="col-md-12 form-group"><label>Alamat Lengkap</label><textarea class="form-control" id="address" name="alamat_lengkap" rows="3"></textarea></div>
                            <div class="col-md-6 form-group"><label>Tanggal Pembayaran</label><input class="form-control" id="paymentDate" name="payment_date" type="number"></div>
                            <div class="col-md-6 form-group d-none" id="statusGroup"><label>Status</label><select class="form-control" id="status" name="status"><option value="active">Aktif</option><option value="nonactive">Tidak Aktif</option></select></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit" id="saveCompanyButton">Simpan Perusahaan</button></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let companyTable;
        let editingCompany = false;

        $(function () {
            companyTable = $('#companyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: '{{ route('internal.companies.table') }}', type: 'POST', data: { _token: '{{ csrf_token() }}' } },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'id_code_perusahaan', name: 'id_code_perusahaan' }, { data: 'nama', name: 'nama' },
                    { data: 'pic_perusahaan', name: 'pic_perusahaan' }, { data: 'pic_internal', name: 'pic_internal' },
                    { data: 'email_perusahaan', name: 'email_perusahaan' }, { data: 'telp', name: 'telp' },
                    { data: 'lender', name: 'lender' }, { data: 'status', name: 'status' }, { data: 'payment_date', name: 'payment_date' },
                    { data: null, name: 'actions', orderable: false, searchable: false, render: row => `<button class="btn btn-sm btn-primary mr-1" onclick="editCompany(${row.id})"><i class="mdi mdi-pencil"></i></button><button class="btn btn-sm btn-danger" onclick="deleteCompany(${row.id})"><i class="mdi mdi-trash-can"></i></button>` }
                ]
            });
            $('#province').on('change', function () { loadOptions('{{ route('internal.companies.locations.cities') }}', { provinsi: this.value }, '#city', 'kabupaten', 'Pilih kabupaten / kota'); $('#district, #village').html('<option value="">Pilih pilihan</option>'); });
            $('#city').on('change', function () { loadOptions('{{ route('internal.companies.locations.districts') }}', { kabupaten: this.value }, '#district', 'kecamatan', 'Pilih kecamatan'); $('#village').html('<option value="">Pilih desa</option>'); });
            $('#district').on('change', function () { loadOptions('{{ route('internal.companies.locations.villages') }}', { kecamatan: this.value }, '#village', 'desa', 'Pilih desa', true); });
            $('#companyForm').on('submit', submitCompany);
        });

        function loadOptions(url, data, selector, label, placeholder, includePostalCode = false) {
            $.post(url, { ...data, _token: '{{ csrf_token() }}' }, function (response) {
                const options = [`<option value="">${placeholder}</option>`];
                (response.data || response).forEach(item => { const value = includePostalCode ? `${item.desa}-${item.kodepos}` : item[label]; const text = includePostalCode ? `${item.desa} - ${item.kodepos}` : item[label]; options.push(`<option value="${value}">${text}</option>`); });
                $(selector).html(options.join(''));
            });
        }

        function openCreateCompany() {
            editingCompany = false; $('#companyForm')[0].reset(); $('#companyId').val(''); $('#companyModalTitle').text('Tambah Perusahaan'); $('#saveCompanyButton').text('Simpan Perusahaan'); $('#statusGroup').addClass('d-none'); $('#city, #district, #village').html('<option value="">Pilih pilihan</option>');
        }

        function editCompany(id) {
            editingCompany = true;
            $.post('{{ route('internal.companies.detail') }}', { id, _token: '{{ csrf_token() }}' }, function (response) {
                const item = response.data; $('#companyId').val(id); $('#companyPic').val(item.pic_perusahaan); $('#internalAdmin').val(item.admin_id); $('#companyName').val(item.nama); $('#companyPhone').val(item.telp); $('#agreementNumber').val(item.nomor_pks); $('#address').val(item.alamat_lengkap); $('#paymentDate').val(item.payment_date); $('#status').val(item.status); $('#statusGroup').removeClass('d-none'); $('#province').val(item.provinsi).trigger('change');
                setTimeout(() => { $('#city').html(`<option value="${item.kabupaten}">${item.kabupaten}</option>`).val(item.kabupaten).trigger('change'); setTimeout(() => { $('#district').html(`<option value="${item.kecamatan}">${item.kecamatan}</option>`).val(item.kecamatan).trigger('change'); setTimeout(() => $('#village').html(`<option value="${item.desa}">${item.desa}</option>`).val(item.desa), 500); }, 500); }, 500);
                $('#companyModalTitle').text('Edit Perusahaan'); $('#saveCompanyButton').text('Perbarui Perusahaan'); $('#companyModal').modal('show');
            });
        }

        function submitCompany(event) {
            event.preventDefault(); const data = new FormData(this); if (editingCompany) data.append('id', $('#companyId').val()); data.append('_token', '{{ csrf_token() }}');
            const url = editingCompany ? '{{ route('internal.companies.update') }}' : '{{ route('internal.companies.add') }}';
            $.ajax({ url, method: 'POST', data, processData: false, contentType: false }).done(() => { $('#companyModal').modal('hide'); companyTable.ajax.reload(null, false); toastr.success(editingCompany ? 'Perusahaan berhasil diperbarui.' : 'Perusahaan berhasil ditambahkan.'); }).fail(error => toastr.error(error.responseJSON?.message || 'Perusahaan gagal disimpan.'));
        }

        function deleteCompany(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus perusahaan dan data terkait?')) return;
            $.post('{{ route('internal.companies.delete') }}', { id, _token: '{{ csrf_token() }}' }).done(() => { companyTable.ajax.reload(null, false); toastr.success('Perusahaan berhasil dihapus.'); }).fail(error => toastr.error(error.responseJSON?.message || 'Perusahaan gagal dihapus.'));
        }
    </script>
@endpush
