@extends('layout.app')
@section('title', 'Fitur')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Tomi</a></li>
                    <li class="breadcrumb-item active">Fitur</li>
                </ol>
            </div>
            <h4 class="page-title">Fitur</h4>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">List Fitur</h4>
                    <a href="{{ route('admin.features.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Tambah Fitur
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table id="tableFeatures" class="table table-hover table-bordered nowrap text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($features as $feature)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ strtoupper($feature->code) }}</td>
                                    <td>{{ strtoupper($feature->name) }}</td>
                                    <td>{{ $feature->description ? strtoupper($feature->description) : '-' }}</td>
                                    <td>{{ strtoupper($statuses[$feature->status] ?? $feature->status) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.features.show', $feature) }}" class="btn btn-sm btn-info">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.features.edit', $feature) }}" class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.features.destroy', $feature) }}" method="POST" class="d-inline js-delete-form">
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
    $('#tableFeatures').DataTable({
        dom: 'B<"clear">lfrtip',
        buttons: [{
            extend: 'excel',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<i class="fa-solid fa-file-excel"></i><span class="pl-1">Excel</span>',
            title: 'List Fitur',
            exportOptions: { columns: [0, 1, 2, 3, 4] },
        }, {
            extend: 'copy',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<i class="fa-solid fa-copy"></i><span class="pl-1">Copy</span>',
            title: 'List Fitur',
            exportOptions: { columns: [0, 1, 2, 3, 4] },
        }, {
            extend: 'print',
            className: 'btn btn-sm btn-purple mb-2 ml-1 mr-1',
            text: '<i class="fa-solid fa-print"></i><span class="pl-1">Print</span>',
            title: 'List Fitur',
            exportOptions: { columns: [0, 1, 2, 3, 4] },
        }],
        lengthMenu: [10, 50, 100, 500, 1000],
        scrollX: true,
        responsive: false,
    });

    $('.js-delete-form').on('submit', function (event) {
        if (!confirm('Apakah anda yakin menghapus fitur ini?')) {
            event.preventDefault();
        }
    });
</script>
@endpush
