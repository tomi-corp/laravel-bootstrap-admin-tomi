@extends('internal.layouts.shell')
@section('title', 'Dashboard')
@section('crumbs', 'Internal')
@section('page-title', 'Dashboard')

{{--
    Dashboard internal. Data dihitung di App\Http\Controllers\Internal\DashboardController
    dan setiap kartu/baris tertaut ke halaman fiturnya.
--}}
@php
    $num = fn ($value) => number_format((float) $value, 0, ',', '.');
    $initials = fn ($name) => collect(explode(' ', trim($name)))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') ?: '-';
@endphp

@section('content')
    <div class="-mt-1 mb-5 flex flex-wrap items-center gap-3">
        <p class="section-sub">Ringkasan aktivitas dan performa seluruh produk TOMI dalam satu tampilan · data per {{ now()->format('d-m-Y H:i') }}</p>
        <a href="{{ route('admin.dashboard.export-report') }}" class="ui-btn primary ml-auto" aria-label="Export report dashboard">
            <svg class="i !h-4 !w-4"><use href="#i-table"/></svg>Export Report
        </a>
    </div>

    {{-- ===================== Statistik utama ===================== --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $i => $stat)
            <a href="{{ $stat['url'] }}" class="panel panel-pad fade-in flex items-start gap-4 transition hover:border-[color:var(--brand)]" style="--i: {{ $i }}">
                <span class="grid h-12 w-12 flex-none place-items-center rounded-[12px]"
                      style="color: {{ $stat['tone'] }}; background: color-mix(in srgb, {{ $stat['tone'] }} 12%, transparent)">
                    <svg class="i !h-6 !w-6"><use href="#i-{{ $stat['icon'] }}"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="kpi-label">{{ $stat['label'] }}</div>
                    <div class="figure-lg num mt-1 truncate">{{ $stat['value'] }}</div>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[12px]">
                        @if ($stat['delta'])
                            @php $up = $stat['delta']['up']; @endphp
                            <span class="badge {{ $up === null ? 'info' : ($up ? 'good' : 'crit') }}">
                                {!! $up === null ? '&rarr;' : ($up ? '&uarr;' : '&darr;') !!} {{ $stat['delta']['text'] }}
                            </span>
                        @endif
                        <span class="text-muted">{{ $stat['sub'] }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- ===================== Perlu perhatian ===================== --}}
    <section id="perlu-perhatian" class="panel mt-4 grid gap-3 p-4 fade-in lg:grid-cols-[minmax(0,1.1fr)_repeat(3,minmax(0,1fr))] lg:items-center"
             style="--i: 4; border-color: color-mix(in srgb, var(--crit-fill) 25%, var(--line)); background: color-mix(in srgb, var(--crit-bg) 55%, var(--surface))">
        <div class="flex items-center gap-3">
            <span class="grid h-11 w-11 flex-none place-items-center rounded-full" style="background: var(--crit-fill); color: #fff">
                <svg class="i !h-5 !w-5"><use href="#i-alert"/></svg>
            </span>
            <div class="min-w-0">
                <div class="text-[16px] font-semibold" style="color: var(--crit)">Perlu Perhatian</div>
                <div class="text-[12.5px] text-ink-2">Hal yang memerlukan tindakan di TomiDana</div>
            </div>
        </div>
        @foreach ($alerts as $alert)
            <a href="{{ $alert['url'] }}" class="panel flex items-center gap-3 px-4 py-3 transition hover:border-[color:var(--brand)]">
                <span class="badge {{ $alert['cls'] }} !h-9 !w-9 flex-none justify-center !rounded-[10px] !p-0">
                    <svg class="i !h-[18px] !w-[18px]"><use href="#i-{{ $alert['icon'] }}"/></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="num text-[18px] font-semibold leading-tight">{{ $num($alert['value']) }}</div>
                    <div class="truncate text-[12.5px] text-ink-2">{{ $alert['label'] }}</div>
                </div>
                <svg class="i !h-4 !w-4 text-muted"><use href="#i-chevron"/></svg>
            </a>
        @endforeach
    </section>

    {{-- ===================== Ringkasan produk ===================== --}}
    <section class="panel panel-pad mt-4 fade-in" style="--i: 5">
        <div class="panel-title">Ringkasan Produk</div>
        <div class="panel-sub">Perusahaan ber-PKS dan pengajuan per produk TOMI</div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($products as $product)
                <a href="{{ $product['url'] }}" class="tile panel block">
                    <div class="flex items-center gap-2.5">
                        <span class="grid h-9 w-9 place-items-center rounded-[10px] text-[13px] font-bold text-white" style="background: var(--s{{ $product['slot'] }})">{{ mb_substr($product['name'], 4, 1) }}</span>
                        <span class="text-[14px] font-semibold">{{ $product['name'] }}</span>
                        <svg class="i ml-auto !h-4 !w-4" style="color: var(--brand-ink)"><use href="#i-chevron"/></svg>
                    </div>
                    @if ($product['has_applications'])
                        <div class="mt-4 grid grid-cols-3 divide-x divide-[color:var(--line)] text-center">
                            @foreach (['total' => 'Pengajuan', 'process' => 'Diproses', 'active' => 'Aktif'] as $key => $label)
                                <div class="px-1">
                                    <div class="num text-[17px] font-semibold">{{ $num($product[$key]) }}</div>
                                    <div class="text-[11.5px] text-muted">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 text-[11.5px] text-muted">{{ $num($product['companies']) }} perusahaan ber-PKS</div>
                    @else
                        <div class="mt-4 text-center">
                            <div class="num text-[17px] font-semibold">{{ $num($product['companies']) }}</div>
                            <div class="text-[11.5px] text-muted">Perusahaan ber-PKS</div>
                        </div>
                        <div class="mt-3 text-[11.5px] text-muted">Data pengajuan belum tersedia</div>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

    <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1.45fr)_minmax(0,1fr)]">
        {{-- ===================== Operasional hari ini ===================== --}}
        <section class="panel panel-pad fade-in" style="--i: 6">
            <div class="panel-title">Ringkasan Operasional Hari Ini</div>
            <div class="panel-sub">Aktivitas operasional TomiDana hari ini</div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 2xl:grid-cols-4">
                @foreach ($operations as $operation)
                    <a href="{{ $operation['url'] }}" class="block rounded-[12px] p-4 transition hover:opacity-80" style="background: var(--surface-2)">
                        <div class="flex items-center gap-2.5">
                            <span class="badge {{ $operation['cls'] }} !h-9 !w-9 flex-none justify-center !rounded-[10px] !p-0">
                                <svg class="i !h-[18px] !w-[18px]"><use href="#i-{{ $operation['icon'] }}"/></svg>
                            </span>
                            <span class="text-[12.5px] font-medium text-ink-2">{{ $operation['label'] }}</span>
                        </div>
                        <div class="figure-lg num mt-3">{{ $num($operation['value']) }}</div>
                        <div class="mt-0.5 text-[11.5px] text-muted">{{ $operation['sub'] }}</div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ===================== Distribusi produk ===================== --}}
        <section class="panel panel-pad fade-in" style="--i: 7">
            <div class="flex items-start gap-3">
                <div>
                    <div class="panel-title">Distribusi Produk</div>
                    <div class="panel-sub">Jumlah perusahaan ber-PKS per produk</div>
                </div>
                <a href="{{ route('internal.companies') }}" class="link ml-auto inline-flex items-center gap-1 text-[12.5px] font-medium">Lihat Semua<svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></a>
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="tbl">
                    <thead><tr><th>No</th><th>Produk</th><th>Perusahaan</th><th class="r">PKS Aktif</th></tr></thead>
                    <tbody>
                        @foreach ($products as $i => $item)
                            <tr>
                                <td class="num text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <a href="{{ $item['url'] }}" class="flex items-center gap-2 hover:underline">
                                        <span class="h-2.5 w-2.5 rounded-[3px]" style="background: var(--s{{ $item['slot'] }})"></span>
                                        <span class="whitespace-nowrap">{{ $item['name'] }}</span>
                                    </a>
                                </td>
                                <td class="min-w-[140px]">
                                    <div class="flex items-center gap-2.5">
                                        <span class="num w-8 font-semibold">{{ $num($item['companies']) }}</span>
                                        <div class="track flex-1"><span style="width: {{ round($item['companies'] / $maxCompanies * 100) }}%; background: var(--s{{ $item['slot'] }})"></span></div>
                                    </div>
                                </td>
                                <td class="r">
                                    <span class="badge {{ $item['active_pks'] > 0 ? 'good' : 'info' }}">{{ $num($item['active_pks']) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- ===================== Aktivitas terbaru ===================== --}}
    <section class="panel panel-pad mt-4 fade-in" style="--i: 8">
        <div class="flex items-start gap-3">
            <div>
                <div class="panel-title">Aktivitas Pengajuan Terbaru</div>
                <div class="panel-sub">Pengajuan pinjaman yang terakhir diperbarui</div>
            </div>
            <a href="{{ route('internal.tomi-dana.application-history') }}" class="link ml-auto inline-flex items-center gap-1 text-[12.5px] font-medium">Lihat Semua<svg class="i !h-3.5 !w-3.5"><use href="#i-arrow"/></svg></a>
        </div>
        <div class="mt-3 overflow-x-auto">
            <table class="tbl">
                <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Produk</th><th>Status</th><th class="r">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr>
                            <td class="num whitespace-nowrap text-ink-2">{{ $activity['time'] }}</td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <span class="avatar !h-7 !w-7 !rounded-full !text-[10.5px]" style="--h: {{ $activity['hue'] }}">{{ mb_strtoupper($initials($activity['user'])) }}</span>
                                    <span class="whitespace-nowrap">{{ mb_strtoupper($activity['user']) }}</span>
                                </div>
                            </td>
                            <td class="min-w-[200px]">{{ mb_strtoupper($activity['activity']) }}</td>
                            <td class="whitespace-nowrap">{{ $activity['product'] }}</td>
                            <td><span class="badge {{ $activity['cls'] }}">{{ $activity['status'] }}</span></td>
                            <td class="r"><a href="{{ $activity['url'] }}" class="ui-btn !h-7 !px-3 !text-[12px]">Lihat</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Belum ada aktivitas pengajuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
