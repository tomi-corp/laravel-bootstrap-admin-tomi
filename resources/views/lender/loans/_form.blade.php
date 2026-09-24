@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-sky-800">User</label>
        <select name="user_id" class="js-select2 w-full" required>
            <option value="">Pilih user</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(old('user_id', $loan->user_id) == $user->id)>{{ $user->name ?? $user->nama_lengkap ?? 'User #'.$user->id }} - {{ $user->email }}</option>
            @endforeach
        </select>
        @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-sky-800">Perusahaan</label>
        <select name="id_perusahaan" class="js-select2 w-full" required>
            <option value="">Pilih perusahaan</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected(old('id_perusahaan', $loan->id_perusahaan) == $company->id)>{{ $company->nama }}</option>
            @endforeach
        </select>
        @error('id_perusahaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-sky-800">Karyawan</label>
        <select name="id_karyawan" class="js-select2 w-full" required>
            <option value="">Pilih karyawan</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" @selected(old('id_karyawan', $loan->id_karyawan) == $employee->id)>{{ $employee->nama_karyawan ?? 'Karyawan #'.$employee->id }} - {{ $employee->nik }}</option>
            @endforeach
        </select>
        @error('id_karyawan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-sky-800">Lender</label>
        <select name="id_lender" class="js-select2 w-full" required>
            <option value="">Pilih lender</option>
            @foreach ($lenders as $lender)
                <option value="{{ $lender->id }}" @selected(old('id_lender', $loan->id_lender) == $lender->id)>{{ $lender->nama }}</option>
            @endforeach
        </select>
        @error('id_lender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-sky-800">KYC</label>
        <select name="id_kyc" class="js-select2 w-full" required>
            <option value="">Pilih KYC</option>
            @foreach ($kycs as $kyc)
                <option value="{{ $kyc->id }}" @selected(old('id_kyc', $loan->id_kyc) == $kyc->id)>KYC #{{ $kyc->id }} - User #{{ $kyc->user_id }}</option>
            @endforeach
        </select>
        @error('id_kyc') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-sky-800">ID Pinjaman</label>
        <input name="id_pinjaman" value="{{ old('id_pinjaman', $loan->id_pinjaman) }}" class="w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20" required>
        @error('id_pinjaman') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    @foreach ([
        'nominal' => 'Nominal',
        'tenor' => 'Tenor',
        'rate' => 'Rate',
        'biaya_admin' => 'Biaya Admin',
        'iuran_anggota' => 'Iuran Anggota',
        'biaya_layanan' => 'Biaya Layanan',
        'total_angsuran_perbulan' => 'Angsuran Per Bulan',
        'jumlah_yang_diterima' => 'Jumlah Diterima',
    ] as $field => $label)
        <div>
            <label class="mb-1 block text-sm font-medium text-sky-800">{{ $label }}</label>
            <input type="number" step="any" name="{{ $field }}" value="{{ old($field, $loan->{$field}) }}" class="w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20" {{ in_array($field, ['nominal', 'tenor', 'rate']) ? 'required' : '' }}>
            @error($field) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    @endforeach
    <div>
        <label class="mb-1 block text-sm font-medium text-sky-800">Status</label>
        <select name="status" class="w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20" required>
            @foreach (['on_proses', 'pending', 'approve', 'done'] as $status)
                <option value="{{ $status }}" @selected(old('status', $loan->status ?? 'on_proses') === $status)>{{ str_replace('_', ' ', $status) }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    @foreach (['ttd' => 'TTD', 'document_privy' => 'Dokumen Privy'] as $field => $label)
        <div>
            <label class="mb-1 block text-sm font-medium text-sky-800">{{ $label }}</label>
            <select name="{{ $field }}" class="w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20" required>
                <option value="n" @selected(old($field, $loan->{$field} ?? 'n') === 'n')>Tidak</option>
                <option value="y" @selected(old($field, $loan->{$field}) === 'y')>Ya</option>
            </select>
        </div>
    @endforeach
    <div>
        <label class="mb-1 block text-sm font-medium text-sky-800">Status OTP</label>
        <select name="status_otp" class="w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20" required>
            <option value="pending" @selected(old('status_otp', $loan->status_otp ?? 'pending') === 'pending')>Pending</option>
            <option value="success" @selected(old('status_otp', $loan->status_otp) === 'success')>Success</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-sky-800">Catatan</label>
        <textarea name="note" rows="3" class="w-full rounded-md border border-sky-100 px-3 py-2 text-slate-950 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20">{{ old('note', $loan->note) }}</textarea>
    </div>
</div>
<div class="mt-5 flex items-center gap-2">
    <button type="submit" class="lender-btn bg-sky-500 text-white shadow-sm hover:bg-sky-600 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-sky-200 lender-btn-md">Simpan</button>
    <a href="{{ route('lender.loans.index') }}" class="lender-btn bg-sky-50 text-slate-950 hover:bg-sky-100 lender-btn-md">Batal</a>
</div>

@push('scripts')
    <script>
        $('.js-select2').select2({ width: '100%', dropdownParent: $('.js-select2').closest('form') });
    </script>
@endpush
