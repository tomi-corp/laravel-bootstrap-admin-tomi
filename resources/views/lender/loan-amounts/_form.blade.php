@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-slate-900">Perusahaan</label>
        <select name="id_perusahaan" class="js-select2 w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            <option value="">Pilih perusahaan</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected((string) old('id_perusahaan', $amount->id_perusahaan) === (string) $company->id)>
                    {{ $company->nama }}
                </option>
            @endforeach
        </select>
        @error('id_perusahaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    @foreach ([
        ['iuran_anggota', 'Iuran Anggota', false],
        ['minimal', 'Minimal', true],
        ['maximal_persentase', 'Maksimal Persentase', false],
    ] as [$field, $label, $required])
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-900">{{ $label }}</label>
            @php
                $isCurrency = in_array($field, ['iuran_anggota', 'minimal'], true);
            @endphp
            <input
                type="{{ $isCurrency ? 'text' : 'number' }}"
                name="{{ $field }}"
                @if (! $isCurrency) step="any" min="0" @endif
                value="{{ old($field, $amount->{$field}) }}"
                class="{{ $isCurrency ? 'js-rupiah-input ' : '' }}w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950"
                @if ($required) required @endif
            >
            @error($field) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    @endforeach

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Maksimal</label>
        <input type="hidden" name="maximal" value="{{ old('maximal', $amount->maximal) }}">
        <input
            type="text"
            value="{{ old('maximal', $amount->maximal) }}"
            class="js-rupiah-input w-full cursor-not-allowed rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-slate-500"
            disabled
        >
        <p class="mt-1 text-xs text-slate-500">Diatur otomatis dari gaji karyawan tertinggi dikali DSR.</p>
        @error('maximal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-2">
    <a href="{{ route('lender.loan-amounts.index') }}" class="lender-btn bg-slate-50 text-slate-950 hover:bg-slate-100 lender-btn-md">
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
