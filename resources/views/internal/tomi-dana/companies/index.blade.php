@extends('internal.layouts.app')

@section('title', 'Perusahaan '.$feature->name)

@push('css')
    <style>
        #featureCompanyTable th,
        #featureCompanyTable td { white-space: nowrap; vertical-align: middle; }

        #companyDetailModal .modal-dialog { max-width: min(1400px, calc(100vw - 2rem)); }
        #companyDetailModal .modal-content { height: calc(100vh - 3.5rem); overflow: hidden; }
        #companyDetailModal .modal-body { position: relative; flex: 1 1 auto; min-height: 0; padding: 0; }
        #companyDetailModal iframe { display: block; width: 100%; height: 100%; border: 0; }
        #companyDetailModal .modal-loading { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title mb-1">Perusahaan {{ $feature->name }}</h4>
                <p class="text-muted mb-0">Perusahaan yang memiliki PKS fitur {{ $feature->name }}. PKS dikelola dari menu Perusahaan.</p>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="featureCompanyTable" class="table table-hover nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Perusahaan</th>
                                    <th>Nama Perusahaan</th>
                                    <th>PIC Perusahaan</th>
                                    <th>PIC Internal</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Lender</th>
                                    <th>Nomor PKS</th>
                                    <th>Masa Berlaku PKS</th>
                                    <th>Status PKS</th>
                                    <th>Status Perusahaan</th>
                                    <th class="text-center">Konfigurasi</th>
                                    <th class="text-center">Proses Pengajuan</th>
                                    <th class="text-center">Daftar Karyawan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal konfigurasi / proses pengajuan / daftar karyawan: halaman dimuat lewat iframe mode embed --}}
    <div class="modal fade" id="companyDetailModal" tabindex="-1" role="dialog" aria-labelledby="companyDetailModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="companyDetailModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="modal-loading text-muted" id="companyDetailLoading">Memuat...</div>
                    <iframe id="companyDetailFrame" title="Detail perusahaan"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function () {
            const modal = $('#companyDetailModal');
            const frame = $('#companyDetailFrame');

            $('#featureCompanyTable').on('click', '.js-company-modal', function () {
                $('#companyDetailModalTitle').text(this.dataset.title);
                $('#companyDetailLoading').show();
                frame.attr('src', this.dataset.url);
                modal.modal('show');
            });
            frame.on('load', () => $('#companyDetailLoading').hide());
            // Kosongkan iframe saat ditutup, lalu muat ulang tabel karena jumlah karyawan/pengajuan bisa berubah.
            modal.on('hidden.bs.modal', () => {
                frame.attr('src', 'about:blank');
                companyTable.ajax.reload(null, false);
            });

            const companyTable = $('#featureCompanyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: { url: '{{ route('internal.tomi-dana.companies.table') }}', type: 'POST', data: { _token: '{{ csrf_token() }}' } },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'id_code_perusahaan', name: 'id_code_perusahaan' },
                    { data: 'nama', name: 'nama' },
                    { data: 'pic_perusahaan', name: 'pic_perusahaan' },
                    { data: 'pic_internal', name: 'pic_internal' },
                    { data: 'email_perusahaan', name: 'email_perusahaan' },
                    { data: 'telp', name: 'telp' },
                    { data: 'lender', name: 'lender', orderable: false },
                    { data: 'pks_number', name: 'pks_number', orderable: false },
                    { data: 'pks_period', name: 'pks_period', orderable: false, searchable: false },
                    { data: 'pks_status', name: 'pks_status', orderable: false, searchable: false },
                    { data: 'status', name: 'status' },
                    { data: 'configuration', name: 'configuration', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'applications', name: 'applications', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'employees', name: 'employees', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[2, 'asc']],
                language: {
                    processing: 'Memuat...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 - 0 dari 0 data',
                    infoFiltered: '(disaring dari _MAX_ total data)',
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Belum ada perusahaan dengan PKS {{ $feature->name }}',
                    paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
                }
            });
        });
    </script>
@endpush
