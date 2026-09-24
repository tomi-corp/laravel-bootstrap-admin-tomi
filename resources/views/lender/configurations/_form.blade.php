@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-slate-900">Perusahaan</label>
        <select name="id_perusahaan" class="js-select2 w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            <option value="">Pilih perusahaan</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected((string) old('id_perusahaan', $tenor->id_perusahaan) === (string) $company->id)>
                    {{ $company->nama }}
                </option>
            @endforeach
        </select>
        @error('id_perusahaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    @foreach ([
        ['tenor', 'Tenor (bulan)', 'number', '1', '1'],
        ['rate', 'Rate', 'number', '0.01', '0'],
        ['asuransi', 'Asuransi', 'number', '0.01', '0'],
        ['provisi', 'Provisi', 'number', '0.01', '0'],
        ['biaya_admin', 'Biaya Admin', 'number', '0.01', '0'],
        ['fee_tomi', 'Fee TOMI', 'number', '0.01', '0'],
        ['biaya_layanan', 'Biaya Layanan', 'number', '1', '0'],
        ['minimal_nominal', 'Minimal Nominal', 'number', '1', '0'],
        ['maximal_nominal', 'Maksimal Nominal', 'number', '1', '0'],
        ['profesional_fee_1', 'Profesional Fee 1', 'number', '0.01', '0'],
        ['profesional_fee_2', 'Profesional Fee 2', 'number', '0.01', '0'],
        ['ksp', 'KSP', 'number', '0.01', '0'],
    ] as [$field, $label, $type, $step, $min])
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-900">{{ $label }}</label>
            <input
                type="{{ $type }}"
                name="{{ $field }}"
                step="{{ $step }}"
                min="{{ $min }}"
                value="{{ old($field, $tenor->{$field}) }}"
                class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950"
                @if (! in_array($field, ['biaya_layanan', 'minimal_nominal', 'maximal_nominal'], true)) required @endif
            >
            @error($field) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    @endforeach
</div>

<div class="mt-6 flex items-center justify-end gap-2">
    <a href="{{ route('lender.configurations.index') }}" class="lender-btn bg-slate-50 text-slate-950 hover:bg-slate-100 lender-btn-md">
        Batal
    </a>
    <button type="submit" class="lender-btn bg-black text-white shadow-sm hover:bg-slate-800 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-slate-300 lender-btn-md">
        Simpan
    </button>
</div>

@push('scripts')
    <script>
        $('.js-select2').each(function () {
            $(this).select2({
                width: '100%',
                dropdownParent: $(this).closest('form'),
            });
        });
    </script>
@endpush
