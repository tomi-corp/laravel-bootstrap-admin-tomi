@extends('lender.layouts.app')

@section('title', 'Detail Disbursement')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Detail Disbursement</h1>
                <p class="mt-1 text-sm text-white/90">{{ $disbursement->loan?->id_pinjaman ?? '#'.$disbursement->pinjaman_id }}</p>
            </div>
            <a href="{{ route('lender.disbursements.edit', $disbursement) }}" class="rounded-md bg-yellow-500 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-600">Edit</a>
        </div>
        <section class="rounded-lg border border-primary-100 bg-white p-5 text-primary-950 shadow-sm shadow-primary-950/10">
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
                        <dt class="text-sm font-medium text-primary-700">{{ $label }}</dt>
                        <dd class="mt-1 text-sm text-primary-950">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    </div>
@endsection
