@extends('lender.layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Detail Karyawan</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $employee->nama_karyawan }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('lender.employees.index') }}" class="lender-btn bg-slate-50 text-slate-950 hover:bg-slate-100 lender-btn-md">
                    Kembali
                </a>
                <a href="{{ route('lender.employees.edit', $employee) }}" class="lender-btn lender-btn-primary shadow-sm lender-btn-md">
                    Edit
                </a>
            </div>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm shadow-slate-950/10">
            <dl class="grid gap-4 md:grid-cols-2">
                @foreach ([
                    'Perusahaan' => $employee->company?->nama ?? '-',
                    'Nama Karyawan' => $employee->nama_karyawan,
                    'NIK' => $employee->nik,
                    'NIP' => $employee->nip,
                    'WhatsApp' => $employee->whatsapp,
                    'Divisi' => $employee->divisi ?? '-',
                    'Departement' => $employee->departement,
                    'Jabatan' => $employee->jabatan,
                    'Max Plafon' => 'Rp '.number_format((float) $employee->max_plafon, 0, ',', '.'),
                    'Tanggal Bergabung' => optional($employee->tanggal_bergabung)->format('d/m/Y') ?? '-',
                    'Status' => ucfirst($employee->status),
                    'Status SP' => 'SP '.$employee->status_sp,
                    'Bank' => $employee->bank?->code_bank ?? '-',
                    'Nama Rekening' => $employee->nama_rekening ?? '-',
                    'Nomor Bank' => $employee->nomor_bank ?? '-',
                    'Approve' => ucfirst($employee->approve_satu).' / '.ucfirst($employee->approve_dua),
                ] as $label => $value)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950">{{ $value }}</dd>
                    </div>
                @endforeach
                <div class="md:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Alamat</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-950">{{ $employee->alamat }}</dd>
                </div>
            </dl>
        </section>
    </div>
@endsection
