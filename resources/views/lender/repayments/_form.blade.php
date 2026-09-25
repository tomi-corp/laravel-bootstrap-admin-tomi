@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Invoice</label>
        <select name="id_invoicing" class="js-select2 w-full" required>
            <option value="">Pilih invoice</option>
            @foreach ($invoices as $invoice)
                <option value="{{ $invoice->id }}" @selected(old('id_invoicing', $repayment->id_invoicing) == $invoice->id)>{{ $invoice->no_invoice ?? 'Invoice #'.$invoice->id }}</option>
            @endforeach
        </select>
        @error('id_invoicing') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Total Pembayaran</label>
        <input type="number" step="any" name="amount_total" value="{{ old('amount_total', $repayment->amount_total) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20" required>
        @error('amount_total') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Tanggal Pembayaran</label>
        <input type="datetime-local" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $repayment->tanggal_pembayaran ? \Carbon\Carbon::parse($repayment->tanggal_pembayaran)->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20">
        @error('tanggal_pembayaran') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-900">Tanggal Bukti Bayar</label>
        <input type="date" name="payment_proof_date" value="{{ old('payment_proof_date', $repayment->payment_proof_date) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20">
        @error('payment_proof_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-slate-900">File Bukti Bayar</label>
        <input name="payment_proof_file" value="{{ old('payment_proof_file', $repayment->payment_proof_file) }}" class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-950 focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20" placeholder="Path file bukti pembayaran">
        @error('payment_proof_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
<div class="mt-5 flex items-center gap-2">
    <button type="submit" class="lender-btn lender-btn-primary shadow-sm lender-btn-md">Simpan</button>
    <a href="{{ route('lender.repayments.index') }}" class="lender-btn bg-slate-50 text-slate-950 hover:bg-slate-100 lender-btn-md">Batal</a>
</div>

@push('scripts')
    <script>
        $('.js-select2').select2({ width: '100%', dropdownParent: $('.js-select2').closest('form') });
    </script>
@endpush
