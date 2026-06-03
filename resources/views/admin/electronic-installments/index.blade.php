@extends('layout.app')

@section('title', 'Cicilan Gadget')

@section('content')

@php
$installmentValue = function ($item, array $keys, $default = '-') {
    foreach ($keys as $key) {
        $value = data_get($item, $key);

        if ($value !== null && $value !== '') {
            return $value;
        }
    }

    return $default;
};

$installmentMoney = function ($value) {
    return 'Rp ' . number_format((float) ($value ?? 0), 0, ',', '.');
};

$installmentDate = function ($value) {
    if (empty($value) || $value === '-') {
        return '-';
    }

    try {
        return \Carbon\Carbon::parse($value)->translatedFormat('d F Y');
    } catch (\Throwable $e) {
        return $value;
    }
};
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">
            Data Nasabah Cicilan Gadget
        </h2>

        <p class="text-muted mb-0">
            Daftar nasabah yang mengambil kredit cicilan HP.
        </p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@unless($tableAvailable)
<div class="alert alert-warning">
    Tabel gadget_installments belum tersedia.
</div>
@endunless

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Nasabah</th>
                        <th class="py-3">Produk HP</th>
                        <th class="py-3">Harga</th>
                        <th class="py-3">DP</th>
                        <th class="py-3">Tenor</th>
                        <th class="py-3">Cicilan / Bulan</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">SN / IMEI</th>
                        <th class="py-3">Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($installments as $item)
                    @php
                    $customerName = $installmentValue($item, [
                        'user_nama_lengkap',
                        'user_name',
                        'nama_lengkap',
                        'nama_nasabah',
                        'customer_name',
                        'name',
                    ]);

                    $customerPhone = $installmentValue($item, [
                        'user_nohp',
                        'nohp',
                        'phone',
                        'customer_phone',
                        'nomor_hp',
                    ]);

                    $productName = $installmentValue($item, [
                        'product_name',
                        'nama_produk',
                        'product',
                        'gadget_name',
                        'name_product',
                    ]);

                    $variantSku = $installmentValue($item, ['variant_sku', 'sku'], null);
                    $variantRam = $installmentValue($item, ['variant_ram_gb', 'ram_gb'], null);
                    $variantStorage = $installmentValue($item, ['variant_storage_gb', 'storage_gb'], null);
                    $variantColor = $installmentValue($item, ['variant_color', 'color', 'warna'], null);

                    $price = $installmentValue($item, [
                        'price',
                        'harga',
                        'harga_produk',
                        'variant_price',
                        'nominal',
                    ], 0);

                    $downPayment = $installmentValue($item, [
                        'down_payment',
                        'dp',
                        'uang_muka',
                    ], 0);

                    $tenor = $installmentValue($item, [
                        'installment_month',
                        'tenor',
                        'tenor_bulan',
                    ], 0);

                    $monthly = $installmentValue($item, [
                        'monthly_installment',
                        'cicilan_bulanan',
                        'angsuran_bulanan',
                    ], 0);

                    $status = strtolower((string) $installmentValue($item, ['status'], 'pending'));
                    $badgeClass = match ($status) {
                        'approved', 'approve', 'diterima', 'active', 'aktif' => 'bg-success',
                        'rejected', 'reject', 'ditolak', 'cancelled', 'canceled' => 'bg-danger',
                        'done', 'completed', 'lunas' => 'bg-primary',
                        default => 'bg-warning text-dark',
                    };
                    @endphp

                    <tr>
                        <td class="px-4">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $customerName }}
                            </div>

                            <small class="text-muted">
                                {{ $customerPhone }}
                            </small>
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $productName }}
                            </div>

                            <small class="text-muted">
                                @if($variantSku)
                                {{ $variantSku }}
                                @endif

                                @if($variantRam || $variantStorage || $variantColor)
                                {{ $variantSku ? '-' : '' }}
                                RAM: {{ $variantRam ?? '-' }} GB,
                                Storage: {{ $variantStorage ?? '-' }} GB,
                                Warna: {{ $variantColor ?? '-' }}
                                @endif
                            </small>
                        </td>

                        <td class="fw-bold text-primary">
                            {{ $installmentMoney($price) }}
                        </td>

                        <td>
                            {{ $installmentMoney($downPayment) }}
                        </td>

                        <td>
                            {{ (int) $tenor }} Bulan
                        </td>

                        <td class="fw-semibold text-success">
                            {{ $installmentMoney($monthly) }}
                        </td>

                        <td>
                            <span class="badge {{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        </td>

                        <td>
                            <div>
                                SN: {{ $installmentValue($item, ['serial_number', 'sn'], '-') }}
                            </div>

                            <small class="text-muted">
                                IMEI: {{ $installmentValue($item, ['imei'], '-') }}
                            </small>
                        </td>

                        <td>
                            {{ $installmentDate($installmentValue($item, ['created_at', 'tanggal_pengajuan', 'tanggal_order'], '-')) }}
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <h5 class="fw-bold">
                                Data Masih Kosong
                            </h5>

                            <p class="text-muted mb-0">
                                Belum ada nasabah cicilan gadget.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
