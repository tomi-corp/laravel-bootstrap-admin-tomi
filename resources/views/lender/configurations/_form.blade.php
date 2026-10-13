@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-slate-900">Perusahaan</label>
        <select name="id_perusahaan"
            class="js-select2 w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            <option value="">Pilih perusahaan</option>
            @foreach ($companies as $company)
            <option value="{{ $company->id }}" @selected((string) old('id_perusahaan', $tenor->id_perusahaan) ===
                (string) $company->id)>
                {{ $company->nama }}
            </option>
            @endforeach
        </select>
        @error('id_perusahaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    @foreach ([
    ['tenor', 'Tenor (bulan)', 'number', '1', '1'],
    ['rate', 'Biaya Bunga', 'number', '0.01', '0'],
    ['asuransi', 'Biaya Asuransi', 'number', '0.01', '0'],
    ['provisi', 'Biaya Provisi', 'number', '0.01', '0'],
    ['biaya_layanan', 'Biaya Layanan', 'number', '1', '0'],
    ['biaya_admin', 'Biaya Admin', 'number', '0.01', '0'],
    ['ksp', 'Lender', 'number', '0.01', '0'],
    ['fee_tomi', 'Tomi', 'number', '0.01', '0'],
    ['profesional_fee_1', 'Ref 1', 'number', '0.01', '0'],
    ['profesional_fee_2', 'Ref 2', 'number', '0.01', '0'],
    ['maximal_nominal', 'Maximal Nominal', 'number', '1', '0'],
    ['minimal_nominal', 'Minimal Nominal', 'number', '1', '0'],
    ] as [$field, $label, $type, $step, $min])
    <div @class(['md:col-span-2' => ! in_array($field, ['maximal_nominal', 'minimal_nominal'], true)])>
        <label class="mb-1 block text-sm font-medium text-slate-900">{{ $label }}</label>
        @php
        $isCurrency = in_array($field, ['biaya_layanan', 'minimal_nominal', 'maximal_nominal'], true);
        @endphp
        <input type="{{ $isCurrency ? 'text' : $type }}" name="{{ $field }}" @if (! $isCurrency) step="{{ $step }}"
            min="{{ $min }}" @endif value="{{ old($field, $tenor->{$field}) }}"
            class="{{ $isCurrency ? 'js-rupiah-input ' : '' }}w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950"
            @if (! in_array($field, ['biaya_layanan', 'minimal_nominal' , 'maximal_nominal' ], true)) required @endif>
        @error($field) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    @endforeach
</div>

<div class="mt-6 flex items-center justify-end gap-2">
    <a href="{{ route('lender.configurations.index') }}"
        class="lender-btn bg-slate-50 text-slate-950 hover:bg-slate-100 lender-btn-md">
        Batal
    </a>
    <button type="submit" class="lender-btn lender-btn-primary shadow-sm lender-btn-md">
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
