@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-slate-900">Perusahaan</label>
        <select name="id_list_perusahaan" class="js-select2 w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            <option value="">Pilih perusahaan</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected((string) old('id_list_perusahaan', $employee->id_list_perusahaan) === (string) $company->id)>
                    {{ $company->nama }}
                </option>
            @endforeach
        </select>
        @error('id_list_perusahaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    @foreach ([
        ['nama_karyawan', 'Nama Karyawan', 'text', true],
        ['nik', 'NIK', 'text', true],
        ['nip', 'NIP', 'text', true],
        ['whatsapp', 'WhatsApp', 'text', true],
        ['divisi', 'Divisi', 'text', false],
        ['departement', 'Departement', 'text', true],
        ['jabatan', 'Jabatan', 'text', true],
        ['max_plafon', 'Max Plafon', 'number', true],
        ['tanggal_bergabung', 'Tanggal Bergabung', 'date', true],
    ] as [$field, $label, $type, $required])
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-900">{{ $label }}</label>
            <input
                type="{{ $type }}"
                name="{{ $field }}"
                value="{{ old($field, $type === 'date' && $employee->{$field} ? $employee->{$field}->format('Y-m-d') : $employee->{$field}) }}"
                class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950"
                @if ($type === 'number') min="0" step="1" @endif
                @if ($required) required @endif
            >
            @error($field) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    @endforeach

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-slate-900">Alamat</label>
        <textarea name="alamat" rows="3" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>{{ old('alamat', $employee->alamat) }}</textarea>
        @error('alamat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Status</label>
        <select name="status" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            @foreach (['tetap' => 'Tetap', 'percobaan' => 'Percobaan', 'phk' => 'PHK', 'resign' => 'Resign'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $employee->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Status SP</label>
        <select name="status_sp" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            @foreach (['0', '1', '2', '3'] as $value)
                <option value="{{ $value }}" @selected((string) old('status_sp', $employee->status_sp) === $value)>SP {{ $value }}</option>
            @endforeach
        </select>
        @error('status_sp') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Bank</label>
        <select name="id_nama_bank" class="js-select2 w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950">
            <option value="">Pilih bank</option>
            @foreach ($banks as $bank)
                <option value="{{ $bank->id }}" @selected((string) old('id_nama_bank', $employee->id_nama_bank) === (string) $bank->id)>
                    {{ $bank->code_bank }}
                </option>
            @endforeach
        </select>
        @error('id_nama_bank') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Nama Rekening</label>
        <input type="text" name="nama_rekening" value="{{ old('nama_rekening', $employee->nama_rekening) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950">
        @error('nama_rekening') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Nomor Bank</label>
        <input type="text" name="nomor_bank" value="{{ old('nomor_bank', $employee->nomor_bank) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950">
        @error('nomor_bank') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Approve Satu</label>
        <select name="approve_satu" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            @foreach (['pending' => 'Pending', 'approve' => 'Approve', 'reject' => 'Reject'] as $value => $label)
                <option value="{{ $value }}" @selected(old('approve_satu', $employee->approve_satu) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('approve_satu') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Approve Dua</label>
        <select name="approve_dua" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950" required>
            @foreach (['pending' => 'Pending', 'approve' => 'Approve', 'reject' => 'Reject'] as $value => $label)
                <option value="{{ $value }}" @selected(old('approve_dua', $employee->approve_dua) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('approve_dua') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-2">
    <a href="{{ route('lender.employees.index') }}" class="lender-btn bg-slate-50 text-slate-950 hover:bg-slate-100 lender-btn-md">
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
