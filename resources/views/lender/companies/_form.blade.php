@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Nama Perusahaan</label>
        <input type="text" name="nama" value="{{ old('nama', $company->nama) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
        @error('nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">PIC Perusahaan</label>
        <input type="text" name="pic_perusahaan" value="{{ old('pic_perusahaan', $company->pic_perusahaan) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
        @error('pic_perusahaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Email Perusahaan</label>
        <input type="email" name="email_perusahaan" value="{{ old('email_perusahaan', $company->email_perusahaan) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950">
        @error('email_perusahaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Telepon</label>
        <input type="text" name="telp" value="{{ old('telp', $company->telp) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
        @error('telp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Lender</label>
        <select name="id_lender" class="js-select2 w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950">
            <option value="">Pilih lender</option>
            @foreach ($lenders as $lender)
                <option value="{{ $lender->id }}" @selected((string) old('id_lender', $company->id_lender ?: auth('lender')->user()?->id_lender) === (string) $lender->id)>
                    {{ $lender->nama }}
                </option>
            @endforeach
        </select>
        @error('id_lender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Provinsi</label>
        <select id="provinceSelect" name="id_provinsi" class="js-select2 w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            <option value="">Pilih provinsi</option>
            @foreach ($provinces as $province)
                <option value="{{ $province->id }}" @selected((string) old('id_provinsi', $company->id_provinsi) === (string) $province->id)>
                    {{ $province->provinsi }}
                </option>
            @endforeach
        </select>
        @error('id_provinsi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Kabupaten/Kota</label>
        <select id="citySelect" name="id_kabupaten" class="js-select2 w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            <option value="">Pilih kabupaten/kota</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}" @selected((string) old('id_kabupaten', $company->id_kabupaten) === (string) $city->id)>
                    {{ $city->kabupaten }}
                </option>
            @endforeach
        </select>
        @error('id_kabupaten') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Kecamatan</label>
        <select id="districtSelect" name="id_kecamatan" class="js-select2 w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            <option value="">Pilih kecamatan</option>
            @foreach ($districts as $district)
                <option value="{{ $district->id }}" @selected((string) old('id_kecamatan', $company->id_kecamatan) === (string) $district->id)>
                    {{ $district->kecamatan }}
                </option>
            @endforeach
        </select>
        @error('id_kecamatan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Desa/Kelurahan</label>
        <select id="villageSelect" name="id_desa" class="js-select2 w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            <option value="">Pilih desa/kelurahan</option>
            @foreach ($villages as $village)
                <option value="{{ $village->id }}" @selected((string) old('id_desa', $company->id_desa) === (string) $village->id)>
                    {{ $village->desa }}
                </option>
            @endforeach
        </select>
        @error('id_desa') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Nomor PKS</label>
        <input type="text" name="nomor_pks" value="{{ old('nomor_pks', $company->nomor_pks) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
        @error('nomor_pks') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Status</label>
        <select name="status" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950">
            <option value="active" @selected(old('status', $company->status ?: 'active') === 'active')>Aktif</option>
            <option value="nonactive" @selected(old('status', $company->status) === 'nonactive')>Nonaktif</option>
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Tanggal Pembayaran</label>
        <input type="number" name="payment_date" min="1" max="31" value="{{ old('payment_date', $company->payment_date ?: 25) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950">
        @error('payment_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">H- Jatuh Tempo</label>
        <input type="number" name="h_min_jatuh_tempo" min="0" max="31" value="{{ old('h_min_jatuh_tempo', $company->h_min_jatuh_tempo ?: 0) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950">
        @error('h_min_jatuh_tempo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Maksimal Plafon</label>
        <input type="number" name="max_plafon" min="0" value="{{ old('max_plafon', $company->max_plafon) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950">
        @error('max_plafon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-slate-900">Alamat Lengkap</label>
        <textarea name="alamat_lengkap" rows="3" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>{{ old('alamat_lengkap', $company->alamat_lengkap) }}</textarea>
        @error('alamat_lengkap') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-2">
    <a href="{{ route('lender.companies.index') }}" class="lender-btn bg-slate-50 text-slate-950 hover:bg-slate-100 lender-btn-md">
        Batal
    </a>
    <button type="submit" class="lender-btn bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500 lender-btn-md">
        Simpan
    </button>
</div>

@push('scripts')
    <script>
        const cityUrl = @json(route('lender.companies.cities'));
        const districtUrl = @json(route('lender.companies.districts'));
        const villageUrl = @json(route('lender.companies.villages'));

        $('.js-select2').each(function () {
            $(this).select2({
                width: '100%',
                dropdownParent: $(this).closest('form'),
            });
        });

        function resetSelect(selector, placeholder) {
            $(selector).empty().append(new Option(placeholder, '')).trigger('change');
        }

        function fillSelect(selector, rows, valueKey, textKey, placeholder) {
            resetSelect(selector, placeholder);

            rows.forEach((row) => {
                $(selector).append(new Option(row[textKey], row[valueKey]));
            });

            $(selector).trigger('change');
        }

        $('#provinceSelect').on('change', function () {
            resetSelect('#citySelect', 'Pilih kabupaten/kota');
            resetSelect('#districtSelect', 'Pilih kecamatan');
            resetSelect('#villageSelect', 'Pilih desa/kelurahan');

            if (!this.value) {
                return;
            }

            $.get(cityUrl, { province_id: this.value }, function (rows) {
                fillSelect('#citySelect', rows, 'id', 'kabupaten', 'Pilih kabupaten/kota');
            });
        });

        $('#citySelect').on('change', function () {
            resetSelect('#districtSelect', 'Pilih kecamatan');
            resetSelect('#villageSelect', 'Pilih desa/kelurahan');

            if (!this.value) {
                return;
            }

            $.get(districtUrl, { city_id: this.value }, function (rows) {
                fillSelect('#districtSelect', rows, 'id', 'kecamatan', 'Pilih kecamatan');
            });
        });

        $('#districtSelect').on('change', function () {
            resetSelect('#villageSelect', 'Pilih desa/kelurahan');

            if (!this.value) {
                return;
            }

            $.get(villageUrl, { district_id: this.value }, function (rows) {
                fillSelect('#villageSelect', rows, 'id', 'desa', 'Pilih desa/kelurahan');
            });
        });
    </script>
@endpush

