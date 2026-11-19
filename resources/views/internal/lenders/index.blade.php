@extends('layout.app')

@section('title', 'Lenders')

@push('css')
    <style>
        #lenderTable th,
        #lenderTable td { white-space: nowrap; vertical-align: middle; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title mb-0">Lenders</h4>
                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#lenderModal" onclick="openCreateLender()">
                    <i class="mdi mdi-plus-circle mr-1"></i>Add Lender
                </button>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="lenderTable" class="table table-hover nowrap" style="width:100%">
                            <thead><tr><th>#</th><th>Lender Code</th><th>Name</th><th>Lender PIC</th><th>Internal PIC</th><th>Email</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="lenderModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="lenderModalTitle">Add Lender</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button></div>
                <form id="lenderForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="lenderId">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Lender PIC</label><input class="form-control" id="picLender" name="pic_lender"><small class="text-danger" id="errorPicLender"></small></div>
                            <div class="col-md-6 form-group"><label>Internal PIC</label><input class="form-control" id="picInternal" name="pic_internal"><small class="text-danger" id="errorPicInternal"></small></div>
                            <div class="col-md-6 form-group"><label>Lender Name</label><input class="form-control" id="lenderName" name="nama_lender"><small class="text-danger" id="errorLenderName"></small></div>
                            <div class="col-md-6 form-group"><label>Email</label><input class="form-control" id="lenderEmail" name="email_lender" type="email"><small class="text-danger" id="errorLenderEmail"></small></div>
                            <div class="col-md-6 form-group"><label>Phone</label><input class="form-control" id="lenderPhone" name="telepon" type="number"><small class="text-danger" id="errorLenderPhone"></small></div>
                            <div class="col-md-6 form-group"><label>Agreement Number</label><input class="form-control" id="agreementNumber" name="nomor_pks"><small class="text-danger" id="errorAgreementNumber"></small></div>
                            <div class="col-md-6 form-group"><label>Province</label><select class="form-control" id="province" name="provinsi"><option value="">Select province</option>@foreach ($provinces as $province)<option value="{{ $province->provinsi }}">{{ $province->provinsi }}</option>@endforeach</select></div>
                            <div class="col-md-6 form-group"><label>City / Regency</label><select class="form-control" id="city" name="kabupaten"><option value="">Select city / regency</option></select></div>
                            <div class="col-md-6 form-group"><label>District</label><select class="form-control" id="district" name="kecamatan"><option value="">Select district</option></select></div>
                            <div class="col-md-6 form-group"><label>Village</label><select class="form-control" id="village" name="desa"><option value="">Select village</option></select></div>
                            <div class="col-md-12 form-group"><label>Full Address</label><textarea class="form-control" id="address" name="alamat_lengkap" rows="3"></textarea></div>
                            <div class="col-md-6 form-group"><label>Bank</label><select class="form-control" id="bank" name="bank"><option value="">Select bank</option>@foreach ($banks as $item)<option value="{{ $item->id }}">{{ $item->code_bank }}</option>@endforeach</select></div>
                            <div class="col-md-6 form-group"><label>Account Name</label><input class="form-control" id="accountName" name="an_rekening"></div>
                            <div class="col-md-6 form-group"><label>Account Number</label><input class="form-control" id="accountNumber" name="no_rekening" type="number"></div>
                            <div class="col-md-6 form-group"><label>Brand Color</label><input class="form-control" id="brandColor" name="colorLender" type="color" value="#188ae2"></div>
                            <div class="col-md-6 form-group"><label>PKS File (PDF)</label><input class="form-control" id="pksFile" name="file_pks" type="file" accept="application/pdf"></div>
                            <div class="col-md-6 form-group"><label>Lender Logo</label><input class="form-control" id="lenderLogo" name="logoLender" type="file" accept="image/jpeg,image/png"></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit" id="saveLenderButton">Save Lender</button></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let lenderTable;
        let editingLender = false;

        $(function () {
            lenderTable = $('#lenderTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: '{{ route('internal.lenders.table') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' }, { data: 'id_code_lender', name: 'id_code_lender' },
                    { data: 'nama', name: 'nama' }, { data: 'pic_lender', name: 'pic_lender' }, { data: 'pic_internal', name: 'pic_internal' },
                    { data: 'email_lender', name: 'email_lender' }, { data: 'telp', name: 'telp' }, { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
            $('#province').on('change', function () { loadOptions('{{ route('internal.lenders.locations.cities') }}', { provinsi: this.value }, '#city', 'kabupaten', 'Select city / regency'); $('#district, #village').html('<option value="">Select an option</option>'); });
            $('#city').on('change', function () { loadOptions('{{ route('internal.lenders.locations.districts') }}', { kabupaten: this.value }, '#district', 'kecamatan', 'Select district'); $('#village').html('<option value="">Select village</option>'); });
            $('#district').on('change', function () { loadOptions('{{ route('internal.lenders.locations.villages') }}', { kecamatan: this.value }, '#village', 'desa', 'Select village', true); });
            $('#lenderForm').on('submit', submitLender);
        });

        function loadOptions(url, data, selector, label, placeholder, includePostalCode = false) {
            $.post(url, { ...data, _token: '{{ csrf_token() }}' }, function (response) {
                const options = [`<option value="">${placeholder}</option>`];
                (response.data || response).forEach(item => { const value = includePostalCode ? `${item.desa}-${item.kodepos}` : item[label]; const text = includePostalCode ? `${item.desa} - ${item.kodepos}` : item[label]; options.push(`<option value="${value}">${text}</option>`); });
                $(selector).html(options.join(''));
            });
        }

        function openCreateLender() {
            editingLender = false; $('#lenderForm')[0].reset(); $('#lenderId').val(''); $('#lenderModalTitle').text('Add Lender'); $('#saveLenderButton').text('Save Lender'); $('#city, #district, #village').html('<option value="">Select an option</option>');
        }

        function editLender(id) {
            editingLender = true;
            $.post('{{ route('internal.lenders.detail') }}', { id, _token: '{{ csrf_token() }}' }, function (response) {
                const item = response.data; $('#lenderId').val(id); $('#picLender').val(item.pic_lender); $('#picInternal').val(item.pic_internal); $('#lenderName').val(item.nama); $('#lenderEmail').val(item.email_lender); $('#lenderPhone').val(item.telp); $('#agreementNumber').val(item.nomor_pks); $('#address').val(item.alamat_lengkap); $('#brandColor').val(item.color || '#188ae2'); $('#bank').val(item.id_bank); $('#accountName').val(item.nama_rekening); $('#accountNumber').val(item.no_rekening); $('#province').val(item.provinsi).trigger('change');
                setTimeout(() => { $('#city').html(`<option value="${item.kabupaten}">${item.kabupaten}</option>`).val(item.kabupaten).trigger('change'); setTimeout(() => { $('#district').html(`<option value="${item.kecamatan}">${item.kecamatan}</option>`).val(item.kecamatan).trigger('change'); setTimeout(() => $('#village').html(`<option value="${item.desa}">${item.desa}</option>`).val(item.desa), 500); }, 500); }, 500);
                $('#lenderModalTitle').text('Edit Lender'); $('#saveLenderButton').text('Update Lender'); $('#lenderModal').modal('show');
            });
        }

        function submitLender(event) {
            event.preventDefault(); const data = new FormData(this); if (editingLender) data.append('id', $('#lenderId').val()); data.append('_token', '{{ csrf_token() }}');
            const url = editingLender ? '{{ route('internal.lenders.update') }}' : '{{ route('internal.lenders.add') }}';
            $.ajax({ url, method: 'POST', data, processData: false, contentType: false }).done(() => { $('#lenderModal').modal('hide'); lenderTable.ajax.reload(null, false); toastr.success(editingLender ? 'Lender update request submitted.' : 'Lender added successfully.'); }).fail(error => toastr.error(error.responseJSON?.message || 'Unable to save lender.'));
        }

        function deleteLender(id) {
            if (!confirm('Are you sure you want to delete this lender?')) return;
            $.post('{{ route('internal.lenders.delete') }}', { id, _token: '{{ csrf_token() }}' }).done(() => { lenderTable.ajax.reload(null, false); toastr.success('Lender deletion request submitted.'); }).fail(error => toastr.error(error.responseJSON?.message || 'Unable to delete lender.'));
        }
    </script>
@endpush
