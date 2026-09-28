@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Pinjaman</label>
        <select name="pinjaman_id" class="js-select2 w-full" required>
            <option value="">Pilih pinjaman</option>
            @foreach ($loans as $loan)
                @php
                    $pkUrl = $loan->document_name ? URL::temporarySignedRoute('preview.pk', now()->addMinutes(10), ['document' => $loan->document_name]) : '';
                    $ktpUrl = $loan->kyc?->ktp ? secureFile($loan->kyc->ktp) : '';
                    $selfieUrl = $loan->kyc?->selfie ? secureFile($loan->kyc->selfie) : '';
                @endphp
                <option value="{{ $loan->id }}"
                    data-pk-file="{{ $loan->document_name }}"
                    data-ktp-file="{{ $loan->kyc?->ktp }}"
                    data-selfie-file="{{ $loan->kyc?->selfie }}"
                    data-pk-url="{{ $pkUrl }}"
                    data-ktp-url="{{ $ktpUrl }}"
                    data-selfie-url="{{ $selfieUrl }}"
                    @selected(old('pinjaman_id', $disbursement->pinjaman_id) == $loan->id)>{{ $loan->id_pinjaman }} - Rp. {{ number_format($loan->nominal, 0, ',', '.') }}</option>
            @endforeach
        </select>
        @error('pinjaman_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Bank</label>
        <select name="bank_id" class="js-select2 w-full" required>
            <option value="">Pilih bank</option>
            @foreach ($banks as $bank)
                <option value="{{ $bank->id }}" @selected(old('bank_id', $disbursement->bank_id) == $bank->id)>{{ $bank->code_bank }} - {{ $bank->description }}</option>
            @endforeach
        </select>
        @error('bank_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Dana yang Dicairkan</label>
        <input type="text" name="dana_yang_dicairkan" value="{{ old('dana_yang_dicairkan', $disbursement->dana_yang_dicairkan) }}" class="js-rupiah-input w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20" required>
        @error('dana_yang_dicairkan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Atas Nama</label>
        <input name="atas_nama" value="{{ old('atas_nama', $disbursement->atas_nama) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20" required>
        @error('atas_nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Nomor Rekening</label>
        <input name="nomor_rekening" value="{{ old('nomor_rekening', $disbursement->nomor_rekening) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20" required>
        @error('nomor_rekening') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Tanggal Transfer</label>
        <input type="date" name="transfer_date" value="{{ old('transfer_date', $disbursement->transfer_date) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20">
        @error('transfer_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-slate-900">Bukti Transfer</label>
        <input name="transfer_proof" value="{{ old('transfer_proof', $disbursement->transfer_proof) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20" placeholder="Path file bukti transfer">
        @error('transfer_proof') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-medium text-slate-900">Dokumen Pinjaman</label>
        <div class="grid gap-3 md:grid-cols-3">
            @foreach (['pk' => 'PK', 'ktp' => 'KTP', 'selfie' => 'Selfie'] as $key => $label)
                @php
                    $loan = $disbursement->loan;
                    $storedFile = match ($key) {
                        'pk' => $loan?->document_name,
                        'ktp' => $loan?->kyc?->ktp,
                        default => $loan?->kyc?->selfie,
                    };
                    $storedUrl = '';

                    if ($storedFile) {
                        $storedUrl = $key === 'pk'
                            ? URL::temporarySignedRoute('preview.pk', now()->addMinutes(10), ['document' => $storedFile])
                            : secureFile($storedFile);
                    }
                @endphp
                <div class="rounded-md border border-slate-200 bg-slate-50/50 p-3">
                    <div class="text-sm font-medium text-slate-900">{{ $label }}</div>
                    <a id="{{ $key }}_link" href="{{ $storedUrl }}" target="_blank" class="{{ $storedUrl ? '' : 'hidden' }} lender-btn lender-btn-primary shadow-sm lender-btn-sm mt-2">Lihat</a>
                    <span id="{{ $key }}_empty" class="{{ $storedUrl ? 'hidden' : '' }} mt-2 block text-sm text-slate-500">Belum ada file</span>
                </div>
            @endforeach
        </div>
        <p class="mt-2 text-xs text-slate-500">Dokumen otomatis mengikuti pinjaman yang dipilih.</p>
    </div>
</div>
<div class="mt-5 flex items-center gap-2">
    <button type="submit" class="lender-btn lender-btn-primary shadow-sm lender-btn-md">Simpan</button>
    <a href="{{ route('lender.disbursements.index') }}" class="lender-btn bg-slate-50 text-slate-950 hover:bg-slate-100 lender-btn-md">Batal</a>
</div>

@push('scripts')
    <script>
        $('.js-select2').select2({ width: '100%', dropdownParent: $('.js-select2').closest('form') });

        function syncLoanDocuments() {
            const option = $('select[name="pinjaman_id"] option:selected');
            const hasLoan = !!option.val();

            ['pk', 'ktp', 'selfie'].forEach(function (key) {
                const file = hasLoan ? (option.data(key + '-file') || '') : '';
                const url = hasLoan ? (option.data(key + '-url') || '') : '';

                $('#' + key + '_link').attr('href', url).toggleClass('hidden', !url);
                $('#' + key + '_empty').toggleClass('hidden', !!url);
            });
        }

        $('select[name="pinjaman_id"]').on('change', syncLoanDocuments);
        syncLoanDocuments();
    </script>
@endpush
