@csrf

@if ($officer->exists)
    @method('PUT')
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Lender <span class="text-danger">*</span></label>
            <select name="lender_id" class="form-control" required>
                <option value="">Pilih Lender</option>
                @foreach ($lenders as $lender)
                    <option value="{{ $lender->id }}" @selected((string) old('lender_id', $officer->lender_id) === (string) $lender->id)>
                        {{ $lender->nama }}
                    </option>
                @endforeach
            </select>
            @error('lender_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Jabatan <span class="text-danger">*</span></label>
            <select name="position" class="form-control" required>
                <option value="">Pilih Jabatan</option>
                @foreach ($positions as $value => $label)
                    <option value="{{ $value }}" @selected(old('position', $officer->position) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('position') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Nama Depan/Nama Tunggal <span class="text-danger">*</span></label>
            <input name="first_name" class="form-control" type="text" value="{{ old('first_name', $officer->first_name) }}" required>
            @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Nama Belakang</label>
            <input name="last_name" class="form-control" type="text" value="{{ old('last_name', $officer->last_name) }}">
            @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>NIK <span class="text-danger">*</span></label>
            <input name="nik" class="form-control" type="text" maxlength="16" value="{{ old('nik', $officer->nik) }}" required>
            @error('nik') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Tempat Lahir</label>
            <input name="place_of_birth" class="form-control" type="text" value="{{ old('place_of_birth', $officer->place_of_birth) }}">
            @error('place_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Tanggal Lahir</label>
            <input name="date_of_birth" class="form-control" type="date" value="{{ old('date_of_birth', optional($officer->date_of_birth)->format('Y-m-d')) }}">
            @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Gender</label>
            <select name="gender" class="form-control">
                <option value="">Pilih Gender</option>
                <option value="male" @selected(old('gender', $officer->gender) === 'male')>Laki-laki</option>
                <option value="female" @selected(old('gender', $officer->gender) === 'female')>Perempuan</option>
            </select>
            @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Telepon</label>
            <input name="phone_number" class="form-control" type="text" value="{{ old('phone_number', $officer->phone_number) }}">
            @error('phone_number') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Email</label>
            <input name="email" class="form-control" type="email" value="{{ old('email', $officer->email) }}">
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>

<h5 class="mt-3 mb-3">Alamat KTP</h5>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>Alamat</label>
            <textarea name="ktp_address" class="form-control" rows="2">{{ old('ktp_address', $officer->ktp_address) }}</textarea>
            @error('ktp_address') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>RT</label>
            <input name="rt" class="form-control" type="text" maxlength="3" value="{{ old('rt', $officer->rt) }}">
            @error('rt') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>RW</label>
            <input name="rw" class="form-control" type="text" maxlength="3" value="{{ old('rw', $officer->rw) }}">
            @error('rw') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Provinsi</label>
            <select name="province_id" id="province_id" class="form-control">
                <option value="">Pilih Provinsi</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" @selected((string) old('province_id', $officer->province_id) === (string) $province->id)>{{ $province->provinsi }}</option>
                @endforeach
            </select>
            @error('province_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Kota/Kabupaten</label>
            <select name="city_id" id="city_id" class="form-control">
                <option value="">Pilih Kota/Kabupaten</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" @selected((string) old('city_id', $officer->city_id) === (string) $city->id)>{{ $city->kabupaten }}</option>
                @endforeach
            </select>
            @error('city_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Kecamatan</label>
            <select name="district_id" id="district_id" class="form-control">
                <option value="">Pilih Kecamatan</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}" @selected((string) old('district_id', $officer->district_id) === (string) $district->id)>{{ $district->kecamatan }}</option>
                @endforeach
            </select>
            @error('district_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Kelurahan/Desa</label>
            <select name="village_id" id="village_id" class="form-control">
                <option value="">Pilih Kelurahan/Desa</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}" data-postal-code="{{ $village->kodepos }}" @selected((string) old('village_id', $officer->village_id) === (string) $village->id)>{{ $village->desa }} {{ $village->kodepos }}</option>
                @endforeach
            </select>
            @error('village_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Kode Pos</label>
            <input name="postal_code" id="postal_code" class="form-control" type="text" maxlength="5" value="{{ old('postal_code', $officer->postal_code) }}">
            @error('postal_code') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>

<h5 class="mt-3 mb-3">Masa Jabatan dan Dokumen</h5>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Mulai Menjabat <span class="text-danger">*</span></label>
            <input name="started_at" class="form-control" type="date" value="{{ old('started_at', optional($officer->started_at)->format('Y-m-d')) }}" required>
            @error('started_at') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Akhir Menjabat</label>
            <input name="ended_at" class="form-control" type="date" value="{{ old('ended_at', optional($officer->ended_at)->format('Y-m-d')) }}">
            @error('ended_at') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>No. SK/Dokumen Pengangkatan</label>
            <input name="appointment_reference_number" class="form-control" type="text" value="{{ old('appointment_reference_number', $officer->appointment_reference_number) }}">
            @error('appointment_reference_number') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>File KTP</label>
            <input name="ktp_file" class="form-control" type="file" accept=".jpg,.jpeg,.png,.pdf">
            @if ($officer->ktp_file_path)
                <small class="form-text text-muted">
                    <a href="{{ Storage::disk('public')->url($officer->ktp_file_path) }}" target="_blank">Lihat file KTP saat ini</a>
                </small>
            @endif
            @error('ktp_file') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Catatan</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $officer->notes) }}</textarea>
            @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>

<div class="text-right">
    <a href="{{ route('admin.lender-officers.index') }}" class="btn btn-secondary">Cancel</a>
    <button class="btn btn-primary" type="submit">Save</button>
</div>

@push('js')
<script>
    const csrfToken = "{{ csrf_token() }}";

    const resetSelect = (selector, placeholder) => {
        $(selector).html(`<option value="">${placeholder}</option>`);
    };

    const fillSelect = (selector, rows, valueKey, textKey, placeholder) => {
        resetSelect(selector, placeholder);

        rows.forEach((row) => {
            const suffix = row.kodepos ? ` ${row.kodepos}` : '';
            $(selector).append($('<option>', {
                value: row[valueKey],
                text: `${row[textKey]}${suffix}`,
                'data-postal-code': row.kodepos || '',
            }));
        });
    };

    $('#province_id').on('change', function () {
        resetSelect('#city_id', 'Pilih Kota/Kabupaten');
        resetSelect('#district_id', 'Pilih Kecamatan');
        resetSelect('#village_id', 'Pilih Kelurahan/Desa');
        $('#postal_code').val('');

        if (!this.value) return;

        $.post('{{ route('admin.lender-officers.cities') }}', {
            _token: csrfToken,
            province_id: this.value,
        }).done((rows) => fillSelect('#city_id', rows, 'id', 'kabupaten', 'Pilih Kota/Kabupaten'));
    });

    $('#city_id').on('change', function () {
        resetSelect('#district_id', 'Pilih Kecamatan');
        resetSelect('#village_id', 'Pilih Kelurahan/Desa');
        $('#postal_code').val('');

        if (!this.value) return;

        $.post('{{ route('admin.lender-officers.districts') }}', {
            _token: csrfToken,
            city_id: this.value,
        }).done((rows) => fillSelect('#district_id', rows, 'id', 'kecamatan', 'Pilih Kecamatan'));
    });

    $('#district_id').on('change', function () {
        resetSelect('#village_id', 'Pilih Kelurahan/Desa');
        $('#postal_code').val('');

        if (!this.value) return;

        $.post('{{ route('admin.lender-officers.villages') }}', {
            _token: csrfToken,
            district_id: this.value,
        }).done((rows) => fillSelect('#village_id', rows, 'id', 'desa', 'Pilih Kelurahan/Desa'));
    });

    $('#village_id').on('change', function () {
        $('#postal_code').val($(this).find(':selected').data('postal-code') || '');
    });
</script>
@endpush
