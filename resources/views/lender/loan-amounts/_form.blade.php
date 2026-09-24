@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-sky-800">Perusahaan</label>
        <select name="id_perusahaan" class="js-select2 w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950" required>
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
        ['maximal', 'Maksimal', true],
        ['maximal_persentase', 'Maksimal Persentase', false],
    ] as [$field, $label, $required])
        <div>
            <label class="mb-1 block text-sm font-medium text-sky-800">{{ $label }}</label>
            <input
                type="number"
                name="{{ $field }}"
                step="any"
                min="0"
                value="{{ old($field, $amount->{$field}) }}"
                class="w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950"
                @if ($required) required @endif
            >
            @error($field) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    @endforeach
</div>

<div class="mt-6 flex items-center justify-end gap-2">
    <a href="{{ route('lender.loan-amounts.index') }}" class="lender-btn bg-sky-50 text-slate-950 hover:bg-sky-100 lender-btn-md">
        Batal
    </a>
    <button type="submit" class="lender-btn bg-sky-500 text-white shadow-sm hover:bg-sky-600 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-sky-200 lender-btn-md">
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
