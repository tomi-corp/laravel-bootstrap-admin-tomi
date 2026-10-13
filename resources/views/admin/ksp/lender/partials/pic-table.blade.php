<div class="form-group">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="mb-0">{{ $title }}</label>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openLenderPicFormModal('{{ $prefix }}')">
            <i class="fa-solid fa-pen"></i> Isi {{ $title }}
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered text-center mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="{{ $prefix }}_table_body">
                <tr><td colspan="5">Belum ada {{ $title }}</td></tr>
            </tbody>
        </table>
    </div>
    <div class="d-none lender-pic-form-storage" data-prefix="{{ $prefix }}">
        @include('admin.ksp.lender.partials.pic-form', ['prefix' => $prefix, 'title' => $title])
    </div>
</div>
