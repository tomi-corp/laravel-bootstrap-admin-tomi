@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-primary-800">Pinjaman</label>
        <select name="pinjaman_id" class="js-select2 w-full" required>
            <option value="">Pilih pinjaman</option>
            @foreach ($loans as $loan)
                <option value="{{ $loan->id }}" @selected(old('pinjaman_id', $disbursement->pinjaman_id) == $loan->id)>{{ $loan->id_pinjaman }} - Rp {{ number_format($loan->nominal, 0, ',', '.') }}</option>
            @endforeach
        </select>
        @error('pinjaman_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-primary-800">Bank</label>
        <select name="bank_id" class="js-select2 w-full" required>
            <option value="">Pilih bank</option>
            @foreach ($banks as $bank)
                <option value="{{ $bank->id }}" @selected(old('bank_id', $disbursement->bank_id) == $bank->id)>{{ $bank->code_bank }} - {{ $bank->description }}</option>
            @endforeach
        </select>
        @error('bank_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-primary-800">Dana yang Dicairkan</label>
        <input type="number" step="any" name="dana_yang_dicairkan" value="{{ old('dana_yang_dicairkan', $disbursement->dana_yang_dicairkan) }}" class="w-full rounded-md border border-primary-100 px-3 py-2 text-primary-950 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" required>
        @error('dana_yang_dicairkan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-primary-800">Atas Nama</label>
        <input name="atas_nama" value="{{ old('atas_nama', $disbursement->atas_nama) }}" class="w-full rounded-md border border-primary-100 px-3 py-2 text-primary-950 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" required>
        @error('atas_nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-primary-800">Nomor Rekening</label>
        <input name="nomor_rekening" value="{{ old('nomor_rekening', $disbursement->nomor_rekening) }}" class="w-full rounded-md border border-primary-100 px-3 py-2 text-primary-950 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" required>
        @error('nomor_rekening') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-primary-800">Tanggal Transfer</label>
        <input type="date" name="transfer_date" value="{{ old('transfer_date', $disbursement->transfer_date) }}" class="w-full rounded-md border border-primary-100 px-3 py-2 text-primary-950 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
        @error('transfer_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-primary-800">Bukti Transfer</label>
        <input name="transfer_proof" value="{{ old('transfer_proof', $disbursement->transfer_proof) }}" class="w-full rounded-md border border-primary-100 px-3 py-2 text-primary-950 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Path file bukti transfer">
        @error('transfer_proof') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
<div class="mt-5 flex items-center gap-2">
    <button type="submit" class="rounded-md bg-[#0B8FE8] px-4 py-2 text-sm font-medium text-white hover:bg-[#0878C4]">Simpan</button>
    <a href="{{ route('lender.disbursements.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
</div>

@push('scripts')
    <script>
        $('.js-select2').select2({ width: '100%', dropdownParent: $('.js-select2').closest('form') });
    </script>
@endpush
