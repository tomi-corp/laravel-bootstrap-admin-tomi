@extends('layout.app')
@section('title', 'Pengurus Lender')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                    <li class="breadcrumb-item active">Pengurus Lender</li>
                </ol>
            </div>
            <h4 class="page-title">Pengurus Lender</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">List Pengurus Lender</h4>
                    <a href="{{ route('admin.lender-officers.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Tambah Pengurus
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table id="tableLenderOfficers" class="table table-hover table-bordered nowrap text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Lender</th>
                                <th>Jabatan</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Telepon</th>
                                <th>Email</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($officers as $officer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $officer->lender?->nama ?? '-' }}</td>
                                    <td>{{ $positions[$officer->position] ?? $officer->position }}</td>
                                    <td>{{ trim($officer->first_name.' '.$officer->last_name) }}</td>
                                    <td>{{ $officer->nik }}</td>
                                    <td>{{ $officer->phone_number ?? '-' }}</td>
                                    <td>{{ $officer->email ?? '-' }}</td>
                                    <td>{{ optional($officer->started_at)->format('Y-m-d') ?? '-' }}</td>
                                    <td>{{ optional($officer->ended_at)->format('Y-m-d') ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.lender-officers.show', $officer) }}" class="btn btn-sm btn-info">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.lender-officers.edit', $officer) }}" class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.lender-officers.destroy', $officer) }}" method="POST" class="d-inline js-delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $('#tableLenderOfficers').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [{
            extend: 'excel',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<i class="fa-solid fa-file-excel"></i><span class="pl-1">Excel</span>',
            title: 'List Pengurus Lender',
            exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
        }, {
            extend: 'copy',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<i class="fa-solid fa-copy"></i><span class="pl-1">Copy</span>',
            title: 'List Pengurus Lender',
            exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
        }, {
            extend: 'print',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<i class="fa-solid fa-print"></i><span class="pl-1">Print</span>',
            title: 'List Pengurus Lender',
            exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
        }],
        lengthMenu: [10, 50, 100, 500, 1000],
        scrollX: true,
        responsive: false,
    });

    $('.js-delete-form').on('submit', function (event) {
        if (!confirm('Apakah anda yakin menghapus pengurus lender ini?')) {
            event.preventDefault();
        }
    });
</script>
@endpush
