    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.manage.admin.index') }}">Admin Management</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $title }}</h4>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="header-title mb-1">{{ $title }}</h4>
                            <p class="text-muted mb-0">{{ $description }}</p>
                        </div>
                        <span class="badge badge-primary">{{ number_format($admins->count()) }} administrators</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover dt-responsive nowrap" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Organization</th>
                                    <th>Entity</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($admins as $admin)
                                    @php
                                        $entity = $admin->lender?->nama
                                            ?? $admin->company?->nama
                                            ?? $admin->referral?->name
                                            ?? '-';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ strtoupper($admin->name ?? '-') }}</td>
                                        <td>{{ strtoupper($admin->username ?? '-') }}</td>
                                        <td>{{ $admin->email ?? '-' }}</td>
                                        <td>{{ strtoupper($admin->role?->name ?? '-') }}</td>
                                        <td>{{ strtoupper($admin->instansi?->name ?? '-') }}</td>
                                        <td>{{ strtoupper($entity) }}</td>
                                        <td>{{ optional($admin->created_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No administrators found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
