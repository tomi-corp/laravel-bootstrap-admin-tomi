@extends('lender.layouts.app')

@section('title', 'Detail Pinjaman')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Detail Pinjaman</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $loan->id_pinjaman }}</p>
            </div>
            <a href="{{ route('lender.loans.edit', $loan) }}" class="rounded-md bg-yellow-500 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-600">Edit</a>
        </div>
        <section class="rounded-lg border border-slate-200 bg-white p-5 text-slate-950 shadow-sm shadow-slate-950/10">
            <dl class="grid gap-4 md:grid-cols-2">
                @foreach ([
                    'ID Pinjaman' => $loan->id_pinjaman,
                    'User' => $loan->user?->name ?? $loan->user?->nama_lengkap ?? '-',
                    'Perusahaan' => $loan->company?->nama ?? '-',
                    'Karyawan' => $loan->employee?->nama_karyawan ?? '-',
                    'Lender' => $loan->lender?->nama ?? '-',
                    'Nominal' => 'Rp. '.number_format($loan->nominal, 0, ',', '.'),
                    'Tenor' => $loan->tenor.' bulan',
                    'Rate' => $loan->rate,
                    'Angsuran Per Bulan' => 'Rp. '.number_format($loan->total_angsuran_perbulan, 0, ',', '.'),
                    'Jumlah Diterima' => 'Rp. '.number_format($loan->jumlah_yang_diterima, 0, ',', '.'),
                    'Status' => str_replace('_', ' ', $loan->status),
                    'Status OTP' => $loan->status_otp,
                    'TTD' => $loan->ttd === 'y' ? 'Ya' : 'Tidak',
                    'Dokumen Privy' => $loan->document_privy === 'y' ? 'Ya' : 'Tidak',
                    'Catatan' => $loan->note ?? '-',
                ] as $label => $value)
                    <div>
                        <dt class="text-sm font-medium text-slate-700">{{ $label }}</dt>
                        <dd class="mt-1 text-sm text-slate-950">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    </div>
@endsection
