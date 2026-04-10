@extends('bta.layout.app')
@section('title', 'Data OTP')

@section('content')

<div class="container mt-3">

    <h3 class="mb-3">Data OTP</h3>

    <div class="card">
        <div class="card-body">

            <table id="otpTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>User</th>
                        <th>Code OTP</th>
                        <th>Expired At</th>
                        <th>Created At</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($otps as $key => $otp)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $otp->user->name ?? '-' }}</td>
                        <td>{{ $otp->code_rand }}</td>
                        <td>{{ $otp->expired }}</td>
                        <td>{{ $otp->created_at }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data OTP</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>
@push('js')
<script>
    $(document).ready(function () {

        $('#otpTable').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            searching: true,
            ordering: true,
            order: [[4, 'desc']], // sort by Created At terbaru
            columnDefs: [
                { orderable: false, targets: 0 } // kolom No tidak bisa di-sort
            ]
        });

    });
</script>
@endpush

@endsection