@extends('bta.layout.app')
@section('title', 'Credam')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">BTA</a></li>
                        <li class="breadcrumb-item active">Credam</li>
                    </ol>
                </div>
                <h4 class="page-title">Review Credam</h4>
            </div>
        </div>
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title mb-4">{{ $nasabah->nama_lengkap }}</h4>

                <ul class="nav nav-tabs nav-bordered nav-justified">
                    <li class="nav-item">
                        <a href="#home-b2" data-toggle="tab" aria-expanded="true" class="nav-link active">
                            Data Diri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#profile-b2" data-toggle="tab" aria-expanded="false" class="nav-link">
                            Pekerjaan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#messages-b2" data-toggle="tab" aria-expanded="false" class="nav-link">
                            Dokumen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#loan-b2" data-toggle="tab" aria-expanded="false" class="nav-link">
                            Pinjaman
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="home-b2">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Identitas Pribadi</h5>
                                <hr>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Nama Lengkap</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" value="{{ $nasabah->nama_lengkap }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Jenis Kelamin</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" value="{{ $nasabah->jenis_kelamin }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">TTL</label>
                                    <div class="col-sm-8">
                                        <input class="form-control"
                                            value="{{ $nasabah->tempat_lahir }}, {{ \Carbon\Carbon::parse($nasabah->tanggal_lahir)->format('d-m-Y') }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Agama</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" value="{{ $nasabah->agama }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Pendidikan</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" value="{{ $nasabah->pendidikan_terakhir }}" readonly>
                                    </div>
                                </div>

                                <h5 class="mt-4">Pasangan</h5>
                                <hr>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Nama Pasangan</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" value="{{ $nasabah->nama_pasangan ?? '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>Kontak & Alamat</h5>
                                <hr>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">No. Telepon</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" value="{{ $nasabah->no_telp }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Alamat Rumah</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" readonly>{{ $nasabah->alamat_rumah }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Kode Pos</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" value="{{ $nasabah->kode_pos }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Status Kawin</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" value="{{ $nasabah->status_perkawinan }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Nama Ibu Kandung</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" value="{{ $nasabah->nama_ibu }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="profile-b2">
                        <div class="tab-pane" id="profile-b2">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Informasi Perusahaan</h5>
                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Nama Perusahaan</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" value="{{ $nasabah->nama_perusahaan }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Bidang Usaha</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" value="{{ $nasabah->bidang_usaha }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Alamat Kantor</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" rows="3" readonly>{{ $nasabah->alamat_kerja }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Status Pekerjaan</h5>
                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">NIP</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" value="{{ $nasabah->nip ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Status Pegawai</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" value="{{ $nasabah->status_pegawai }}" readonly>
                                        </div>
                                    </div>
                                    @if ($nasabah->status_pegawai != 'Karyawan Tetap')
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Tgl Akhir Kontrak</label>
                                            <div class="col-sm-8">
                                                <input class="form-control"
                                                    value="{{ $nasabah->tgl_akhir_kontrak ?? '-' }}" readonly>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="messages-b2">
                        <h5 class="mb-3">Daftar Dokumen Pendukung</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-centered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nama Dokumen</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $docItems = [
                                            [
                                                'key' => 'ktp',
                                                'label' => 'KTP',
                                                'status' => $nasabah->status_ktp,
                                                'note' => $nasabah->note_ktp,
                                            ],
                                            [
                                                'key' => 'npwp',
                                                'label' => 'NPWP',
                                                'status' => $nasabah->status_npwp,
                                                'note' => $nasabah->note_npwp,
                                            ],
                                            [
                                                'key' => 'selfie',
                                                'label' => 'Selfie',
                                                'status' => $nasabah->status_selfie,
                                                'note' => $nasabah->note_selfie,
                                            ],
                                            [
                                                'key' => 'kk',
                                                'label' => 'Kartu Keluarga',
                                                'status' => $nasabah->status_kk,
                                                'note' => $nasabah->note_kk,
                                            ],
                                            [
                                                'key' => 'id_card',
                                                'label' => 'ID Card',
                                                'status' => $nasabah->status_id_card,
                                                'note' => $nasabah->note_id_card,
                                            ],
                                            [
                                                'key' => 'sk_karyawan',
                                                'label' => 'SK Karyawan',
                                                'status' => $nasabah->status_sk_karyawan,
                                                'note' => $nasabah->note_sk_karyawan,
                                            ],
                                            [
                                                'key' => 'slip_gaji',
                                                'label' => 'Slip Gaji',
                                                'status' => $nasabah->status_slip_gaji,
                                                'note' => $nasabah->note_slip_gaji,
                                            ],
                                            [
                                                'key' => 'buku_tabungan',
                                                'label' => 'Buku Tabungan',
                                                'status' => $nasabah->status_buku_tabungan,
                                                'note' => $nasabah->note_buku_tabungan,
                                            ],
                                            [
                                                'key' => 'mutasi_rekening',
                                                'label' => 'Mutasi Rekening',
                                                'status' => $nasabah->status_mutasi_rekening,
                                                'note' => $nasabah->note_mutasi_rekening,
                                            ],
                                            [
                                                'key' => 'form_slik',
                                                'label' => 'Form SLIK',
                                                'status' => $nasabah->status_form_slik,
                                                'note' => $nasabah->note_form_slik,
                                            ],
                                            [
                                                'key' => 'form_kredit',
                                                'label' => 'Form Kredit',
                                                'status' => $nasabah->status_form_kredit,
                                                'note' => $nasabah->note_form_kredit,
                                            ],
                                            [
                                                'key' => 'form_bta',
                                                'label' => 'Form BTA',
                                                'status' => $nasabah->status_form_bta,
                                                'note' => $nasabah->note_form_bta,
                                            ],
                                            [
                                                'key' => 'form_checklist',
                                                'label' => 'Form Checklist',
                                                'status' => $nasabah->status_form_checklist,
                                                'note' => $nasabah->note_form_checklist,
                                            ],
                                            [
                                                'key' => 'foto_akad_kredit',
                                                'label' => 'Foto Akad Kredit',
                                                'status' => 'approve',
                                                'note' => '',
                                            ], // Tidak ada status di migrate
                                            [
                                                'key' => 'pk_tanda_tangan',
                                                'label' => 'PK Tanda Tangan',
                                                'status' => 'approve',
                                                'note' => '',
                                            ], // Tidak ada status di migrate
                                        ];
                                    @endphp

                                    @foreach ($docItems as $item)
                                        <tr>
                                            <td><strong>{{ $item['label'] }}</strong></td>
                                            <td>
                                                @if (isset($item['status']))
                                                    <span
                                                        class="badge @if ($item['status'] == 'approve') badge-success @elseif($item['status'] == 'reject') badge-danger @else badge-warning @endif">
                                                        {{ ucfirst($item['status']) }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td><small class="text-muted">{{ $item['note'] ?? '-' }}</small></td>
                                            <td class="text-center">
                                                @if (isset($safeUrls[$item['key']]) && $safeUrls[$item['key']] != null)
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        onclick="previewFile('{{ $safeUrls[$item['key']] }}', '{{ $item['label'] }}')">
                                                        <i class="mdi mdi-eye"></i> Lihat
                                                    </button>
                                                @else
                                                    <span class="badge badge-soft-secondary">Belum Upload</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="loan-b2">
                        <div class="tab-pane" id="loan-b2">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Detail Pengajuan</h5>
                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Produk</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" value="{{ $nasabah->produk_kredit }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Plafond</label>
                                        <div class="col-sm-8">
                                            <input class="form-control"
                                                value="Rp {{ number_format($nasabah->plafond, 0, ',', '.') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Tenor (Bulan)</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" value="{{ $nasabah->jangka_waktu }} Bulan"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Tujuan</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" value="{{ $nasabah->tujuan_penggunaan }}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Analisa Finansial</h5>
                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Gaji Bersih</label>
                                        <div class="col-sm-8">
                                            <input class="form-control"
                                                value="Rp {{ number_format($nasabah->gaji_bersih, 0, ',', '.') }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Angsuran</label>
                                        <div class="col-sm-8">
                                            <input class="form-control"
                                                value="Rp {{ number_format($nasabah->nilai_angsuran, 0, ',', '.') }}"
                                                readonly style="background-color: #f8f9fa; font-weight: bold;">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">DBR </label>
                                        <div class="col-sm-8">
                                            <input class="form-control" value="{{ $nasabah->dbr }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Status Pengajuan</label>
                                        <div class="col-sm-8">
                                            <span
                                                class="badge @if ($nasabah->status == 'approve') badge-success @elseif($nasabah->status == 'reject') badge-danger @else badge-warning @endif">
                                                {{ strtoupper($nasabah->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="d-flex float-right">
                                        <a href="{{ route('bta.credam.index') }}"
                                            class="btn btn-secondary mr-2">Kembali</a>
                                        <button class="btn btn-primary" id="lanjutkanKredit">Lanjutkan Kredit
                                            Berjalan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalPreview" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="labelModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="labelModal">Preview Dokumen</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body text-center">
                    <div id="previewContent">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <a id="btnDownload" href="#" class="btn btn-info" download>Download</a>
                </div>
            </div>
        </div>
    </div>



@endsection

@push('js')
    <script>
        function previewFile(url, title) {
            const previewContent = document.getElementById('previewContent');
            const labelModal = document.getElementById('labelModal');
            const btnDownload = document.getElementById('btnDownload');

            labelModal.innerText = 'Preview: ' + title;
            btnDownload.href = url;
            previewContent.innerHTML = `<div class="p-3"><i class="mdi mdi-spin mdi-loading"></i> Loading Dokumen...</div>`;

            // Karena URL adalah route terenkripsi, kita tidak bisa cek ekstensi via string URL.
            // Solusi terbaik adalah mencoba memuatnya di iframe atau memberikan pilihan preview universal.

            // Gunakan Iframe untuk preview (mendukung Image dan PDF di browser modern)
            previewContent.innerHTML = `
        <iframe src="${url}" 
                style="width: 100%; height: 500px; border: none;" 
                onload="this.style.background='none'">
        </iframe>`;

            $('#modalPreview').modal('show');
        }

        $('#lanjutkanKredit').on('click', function(e) {
            e.preventDefault(); // Mencegah reload halaman

            let $btn = $(this); // Menyimpan context tombol

            // Cegah double click (spam klik)
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '{!! route('bta.credam.approve') !!}',
                type: 'POST',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'no_ktp': '{{ $nasabah->no_ktp }}',
                },
                dataType: 'json',
                success: function(response) {
                    toastr.success(response.message || 'Dokumen berhasil diupload');

                    // Redirect ke route index setelah jeda singkat (agar user sempat baca toastr)
                    setTimeout(function() {
                        window.location.href = "{{ route('bta.credam.index') }}";
                    }, 1500);
                },
                error: function(xhr) {
                    // Mengambil pesan error dari server jika ada, jika tidak pakai pesan default
                    let errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                        'Ada kesalahan pada server';
                    toastr.error(errorMsg);

                    // Kembalikan tombol jika error agar user bisa coba lagi
                    $btn.prop('disabled', false).html('Lanjutkan');
                },
                complete: function() {
                    console.log('Upload process completed');
                }
            });
        });
    </script>
@endpush
