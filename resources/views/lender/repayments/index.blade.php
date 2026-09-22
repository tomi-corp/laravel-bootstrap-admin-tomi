@extends('lender.layouts.app')

@section('title', 'Repayment')

@push('styles')
    <style>
        #repaymentsTable th,
        #repaymentsTable td {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Repayment</h1>
                <p class="mt-1 text-sm text-white/90">Kelola pembayaran kembali pinjaman.</p>
            </div>
            <a href="{{ route('lender.repayments.create') }}" class="inline-flex items-center gap-2 rounded-md bg-[#0B8FE8] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#0878C4]">
                <i class="fa-solid fa-plus"></i>
                Tambah Repayment
            </a>
        </div>

        <section class="rounded-lg border border-primary-100 bg-white p-5 text-primary-950 shadow-sm shadow-primary-950/10">
            <table id="repaymentsTable" class="display nowrap w-full min-w-[1000px]">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Admin</th>
                        <th>Total</th>
                        <th>Tanggal Pembayaran</th>
                        <th>Tanggal Bukti</th>
                        <th>File Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($repayments as $repayment)
                        <tr>
                            <td>{{ $repayment->invoice?->no_invoice ?? '#'.$repayment->id_invoicing }}</td>
                            <td>{{ $repayment->admin?->nama ?? '-' }}</td>
                            <td>Rp {{ number_format($repayment->amount_total, 0, ',', '.') }}</td>
                            <td>{{ $repayment->tanggal_pembayaran ? \Carbon\Carbon::parse($repayment->tanggal_pembayaran)->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $repayment->payment_proof_date ? \Carbon\Carbon::parse($repayment->payment_proof_date)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $repayment->payment_proof_file ?? '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('lender.repayments.show', $repayment) }}" class="rounded-md bg-[#0B8FE8] px-3 py-1.5 text-sm font-medium text-white hover:bg-[#0878C4]">Detail</a>
                                    <a href="{{ route('lender.repayments.edit', $repayment) }}" class="rounded-md bg-yellow-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-yellow-600">Edit</a>
                                    <form action="{{ route('lender.repayments.destroy', $repayment) }}" method="POST" class="js-delete-row" data-title="Hapus repayment?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $('#repaymentsTable').DataTable({
            responsive: false,
            scrollX: true,
            scrollY: '420px',
            scrollCollapse: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
            language: {
                lengthMenu: 'Tampilkan _MENU_ data',
                search: 'Cari:',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data repayment',
                paginate: { next: 'Berikutnya', previous: 'Sebelumnya' },
            },
        });

        $('.js-delete-row').on('submit', function (event) {
            event.preventDefault();
            const form = this;
            AppAlert.fire({
                icon: 'warning',
                title: $(this).data('title'),
                text: 'Data akan dihapus dari daftar.',
                showCancelButton: true,
                confirmButtonColor: '#0B8FE8',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then((result) => result.isConfirmed && form.submit());
        });
    </script>
@endpush
