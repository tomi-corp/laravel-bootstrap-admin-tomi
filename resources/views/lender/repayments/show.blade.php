@extends('lender.layouts.app')

@section('title', 'Detail Repayment')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Detail Repayment</h1>
                <p class="mt-1 text-sm text-white/90">{{ $repayment->invoice?->no_invoice ?? '#'.$repayment->id_invoicing }}</p>
            </div>
            <a href="{{ route('lender.repayments.edit', $repayment) }}" class="rounded-md bg-yellow-500 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-600">Edit</a>
        </div>
        <section class="rounded-lg border border-primary-100 bg-white p-5 text-primary-950 shadow-sm shadow-primary-950/10">
            <dl class="grid gap-4 md:grid-cols-2">
                @foreach ([
                    'Invoice' => $repayment->invoice?->no_invoice ?? '#'.$repayment->id_invoicing,
                    'Admin' => $repayment->admin?->nama ?? '-',
                    'Total Pembayaran' => 'Rp '.number_format($repayment->amount_total, 0, ',', '.'),
                    'Tanggal Pembayaran' => $repayment->tanggal_pembayaran ? \Carbon\Carbon::parse($repayment->tanggal_pembayaran)->format('d/m/Y H:i') : '-',
                    'Tanggal Bukti Bayar' => $repayment->payment_proof_date ? \Carbon\Carbon::parse($repayment->payment_proof_date)->format('d/m/Y') : '-',
                    'File Bukti Bayar' => $repayment->payment_proof_file ?? '-',
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
