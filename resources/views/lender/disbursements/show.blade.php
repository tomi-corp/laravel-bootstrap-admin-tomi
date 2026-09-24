@extends('lender.layouts.app')

@section('title', 'Detail Disbursement')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Detail Disbursement</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $disbursement->loan?->id_pinjaman ?? '#'.$disbursement->pinjaman_id }}</p>
            </div>
            <a href="{{ route('lender.disbursements.edit', $disbursement) }}" class="rounded-md bg-yellow-500 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-600">Edit</a>
        </div>
        <section class="rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <dl class="grid gap-4 md:grid-cols-2">
                @foreach ([
                    'ID Pinjaman' => $disbursement->loan?->id_pinjaman ?? '#'.$disbursement->pinjaman_id,
                    'Admin' => $disbursement->admin?->nama ?? '-',
                    'Bank' => trim(($disbursement->bank?->code_bank ?? '-').' '.($disbursement->bank?->description ?? '')),
                    'Dana Dicairkan' => 'Rp '.number_format($disbursement->dana_yang_dicairkan, 0, ',', '.'),
                    'Atas Nama' => $disbursement->atas_nama,
                    'Nomor Rekening' => $disbursement->nomor_rekening,
                    'Tanggal Transfer' => $disbursement->transfer_date ? \Carbon\Carbon::parse($disbursement->transfer_date)->format('d/m/Y') : '-',
                    'Bukti Transfer' => $disbursement->transfer_proof ?? '-',
                ] as $label => $value)
                    <div>
                        <dt class="text-sm font-medium text-slate-700">{{ $label }}</dt>
                        <dd class="mt-1 text-sm text-slate-950">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
        <section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <h2 class="mb-4 text-lg font-semibold">Dokumen Pinjaman</h2>
            <div class="grid gap-3 md:grid-cols-3">
                @foreach ([
                    'pk' => ['label' => 'PK', 'file' => $disbursement->loan?->document_name],
                    'ktp' => ['label' => 'KTP', 'file' => $disbursement->loan?->kyc?->ktp],
                    'selfie' => ['label' => 'Selfie', 'file' => $disbursement->loan?->kyc?->selfie],
                ] as $key => $document)
                    @php
                        $url = '';

                        if ($document['file']) {
                            $url = $key === 'pk'
                                ? URL::temporarySignedRoute('preview.pk', now()->addMinutes(10), ['document' => $document['file']])
                                : secureFile($document['file']);
                        }
                    @endphp
                    <div class="rounded-md border border-slate-200 bg-slate-50/50 p-3">
                        <div class="text-sm font-medium text-slate-900">{{ $document['label'] }}</div>
                        @if ($url)
                            <a href="{{ $url }}" target="_blank" class="lender-btn bg-black text-white shadow-sm hover:bg-slate-800 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-slate-300 lender-btn-sm mt-2">Lihat</a>
                            <div class="mt-2 break-all text-xs text-slate-500">{{ $document['file'] }}</div>
                        @else
                            <div class="mt-2 text-sm text-slate-500">Belum ada file</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
