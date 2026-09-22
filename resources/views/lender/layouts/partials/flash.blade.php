@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AppToast.success(@json(session('success')));
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AppToast.error(@json(session('error')));
        });
    </script>
@endif
