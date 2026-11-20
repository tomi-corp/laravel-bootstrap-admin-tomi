@extends('internal.layouts.app')
@section('title', 'Riwayat Pengajuan')

@push('css')
    <style>
        #tableApplicationHistory th,
        #tableApplicationHistory td { white-space: nowrap; vertical-align: middle; }
        .table-scroll { clear: both; overflow-x: auto; margin-top: 1rem; }
        .stat-card { cursor: pointer; border: 2px solid transparent; transition: border-color .15s; }
        .stat-card.active { border-color: #6658dd; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi Dana</a></li>
                        <li class="breadcrumb-item active">Riwayat Pengajuan</li>
                    </ol>
                </div>
                <h4 class="page-title">Riwayat Pengajuan</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card stat-card active" data-filter="">
                <div class="card-body">
                    <span>PENGAJUAN</span>
                    <h4 id="statPengajuanTotal">0</h4>
                    <small class="text-muted" id="statPengajuanNominal">Rp. 0</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card" data-filter="aktif">
                <div class="card-body">
                    <span>AKTIF</span>
                    <h4 id="statAktifTotal">0</h4>
                    <small class="text-muted" id="statAktifNominal">Rp. 0</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card" data-filter="lunas">
                <div class="card-body">
                    <span>LUNAS</span>
                    <h4 id="statLunasTotal">0</h4>
                    <small class="text-muted" id="statLunasNominal">Rp. 0</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <h4 class="m-0">RIWAYAT PENGAJUAN</h4>
                        <div class="form-inline">
                            <label for="filterStatus" class="mr-2">STATUS</label>
                            <select id="filterStatus" class="form-control form-control-sm">
                                <option value="">SEMUA</option>
                                <option value="diproses">DIPROSES</option>
                                <option value="aktif">AKTIF</option>
                                <option value="lunas">LUNAS</option>
                                <option value="ditolak">DITOLAK</option>
                            </select>
                        </div>
                    </div>

                    <table id="tableApplicationHistory" class="table table-hover nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>TANGGAL</th>
                                <th>ID PINJAMAN</th>
                                <th>KARYAWAN</th>
                                <th>PERUSAHAAN</th>
                                <th>LENDER</th>
                                <th>NOMINAL</th>
                                <th>TENOR</th>
                                <th>ANGSURAN / BULAN</th>
                                <th>DITERIMA</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const csrfToken = "{{ csrf_token() }}";

        const loadStatistic = () => {
            $.post('{!! route('internal.tomi-dana.application-history.statistic') !!}', { _token: csrfToken }, function (res) {
                $('#statPengajuanTotal').text(res.pengajuan.total);
                $('#statPengajuanNominal').text(res.pengajuan.nominal);
                $('#statAktifTotal').text(res.aktif.total);
                $('#statAktifNominal').text(res.aktif.nominal);
                $('#statLunasTotal').text(res.lunas.total);
                $('#statLunasNominal').text(res.lunas.nominal);
            });
        };

        const exportButtons = ['excel', 'copy', 'print'].map((type) => ({
            extend: type,
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: {
                excel: '<span class="mdi mdi-file-excel"><span class="pl-1">Excel</span></span>',
                copy: '<span class="mdi mdi-content-copy"><span class="pl-1">Copy</span></span>',
                print: '<span class="mdi mdi-printer"><span class="pl-1">Print</span></span>',
            }[type],
            header: true,
            title: 'Riwayat Pengajuan Tomi Dana',
        }));

        const table = $('#tableApplicationHistory').DataTable({
            dom: 'B<"clear">lf<"table-scroll"t>rip',
            buttons: exportButtons,
            lengthMenu: [10, 50, 100, 500, 1000],
            processing: true,
            serverSide: true,
            order: [[1, 'desc']],
            language: {
                processing: '<i class="dripicons-loading">autorenew</i>'
            },
            ajax: {
                type: 'POST',
                url: '{!! route('internal.tomi-dana.application-history.table') !!}',
                data: (d) => $.extend(d, { _token: csrfToken, filter: $('#filterStatus').val() }),
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal', searchable: false },
                { data: 'id_pinjaman' },
                { data: 'nama_karyawan', orderable: false },
                { data: 'perusahaan', orderable: false },
                { data: 'lender', orderable: false },
                { data: 'nominal', searchable: false },
                { data: 'tenor', orderable: false, searchable: false },
                { data: 'angsuran_perbulan', orderable: false, searchable: false },
                { data: 'jumlah_yang_diterima', orderable: false, searchable: false },
                { data: 'status_badge', orderable: false, searchable: false },
            ],
        });

        const setFilter = (filter) => {
            $('#filterStatus').val(filter);
            $('.stat-card').removeClass('active');
            $('.stat-card[data-filter="' + filter + '"]').addClass('active');
            table.ajax.reload();
        };

        $('.stat-card').on('click', function () {
            setFilter($(this).data('filter') || '');
        });

        $('#filterStatus').on('change', function () {
            setFilter($(this).val());
        });

        loadStatistic();
    </script>
@endpush
