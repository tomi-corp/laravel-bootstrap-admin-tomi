@extends('bta.layout.app')
@section('title', 'Daftar User BTA')

@section('content')

<div class="container mt-3">

    {{-- Header + Action --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Daftar User</h2>

        <div>
            <button type="button" class="btn btn-info rounded-1" id="btnShow" disabled>Show</button>
            <button type="button" class="btn btn-warning rounded-1" id="btnEdit" disabled>Edit</button>
            <button type="submit" form="bulkDeleteForm" class="btn btn-danger rounded-1" id="btnDelete" disabled>
                Delete
            </button>
            <a href="{{ route('bta.users.create') }}" class="btn btn-primary rounded-1">Tambah</a>
        </div>
    </div>

    {{-- Session success --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">

            @if($users->count())
            <form id="bulkDeleteForm" action="{{ route('bta.users.bulkDelete') }}" method="POST">
                @csrf

                <table id="usersTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="userCheckbox">
                            </td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->type) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
            @else
            <p class="text-center">Belum ada user yang ditambahkan.</p>
            @endif

        </div>
    </div>
</div>

{{-- JS --}}
@push('js')
<script>
    $(document).ready(function () {
        // Inisialisasi DataTables dengan pagination dan search
        const table = $('#usersTable').DataTable({
            paging: true,
            pageLength: 10, // default 10 rows per page
            lengthMenu: [5, 10, 25, 50], // pilihan rows per page
            searching: true,
            ordering: true,
            columnDefs: [
                { orderable: false, targets: 0 } // checkbox tidak bisa di-sort
            ]
        });

        const btnShow = document.getElementById('btnShow');
        const btnEdit = document.getElementById('btnEdit');
        const btnDelete = document.getElementById('btnDelete');

        function updateButtons() {
            const checked = document.querySelectorAll('.userCheckbox:checked');
            btnDelete.disabled = checked.length === 0;
            btnShow.disabled = checked.length !== 1;
            btnEdit.disabled = checked.length !== 1;
        }

        // Checkbox event
        $('#usersTable').on('change', '.userCheckbox', updateButtons);

        // CheckAll
        $('#checkAll').on('click', function () {
            const isChecked = this.checked;
            $('.userCheckbox').prop('checked', isChecked);
            updateButtons();
        });

        // Tombol Show
        btnShow.addEventListener('click', function () {
            const checked = document.querySelectorAll('.userCheckbox:checked');
            if (checked.length === 1) {
                const url = "{{ route('bta.users.show', ':id') }}".replace(':id', checked[0].value);
                window.location.href = url;
            }
        });

        // Tombol Edit
        btnEdit.addEventListener('click', function () {
            const checked = document.querySelectorAll('.userCheckbox:checked');
            if (checked.length === 1) {
                const url = "{{ route('bta.users.edit', ':id') }}".replace(':id', checked[0].value);
                window.location.href = url;
            }
        });
    });
</script>
@endpush

@endsection