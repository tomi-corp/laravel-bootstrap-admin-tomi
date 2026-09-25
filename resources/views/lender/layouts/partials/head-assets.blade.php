<script src="https://cdn.tailwindcss.com"></script>
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
>
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css"
    crossorigin="anonymous"
>
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css"
    crossorigin="anonymous"
>
<link
    rel="stylesheet"
    href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css"
    crossorigin="anonymous"
>
<link
    rel="stylesheet"
    href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css"
    crossorigin="anonymous"
>
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    crossorigin="anonymous"
>
<style>
    .lender-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border: 0;
        border-radius: 0.375rem;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.25rem;
        text-decoration: none;
        transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease, box-shadow 150ms ease;
    }

    .lender-btn-sm {
        padding: 0.375rem 0.75rem;
    }

    .lender-btn-md {
        padding: 0.5rem 1rem;
    }

    .lender-btn-block {
        width: 100%;
    }

    .bg-primary,
    .file\:bg-primary::file-selector-button {
        background-color: #188ae2;
    }

    .hover\:bg-primary-dark:hover {
        background-color: #11609d;
    }

    .focus\:ring-primary:focus {
        --tw-ring-color: #188ae2;
    }

    .lender-btn-primary {
        background-color: #188ae2;
        color: #fff;
    }

    .lender-btn-primary:hover,
    .lender-btn-primary:focus {
        background-color: #11609d;
        color: #fff;
    }

    .lender-btn-primary:focus {
        outline: 2px solid #188ae2;
        outline-offset: 2px;
    }

    input[type="file"]::file-selector-button {
        margin-right: 0.75rem;
        border: 0;
        border-radius: 0.375rem;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        transition: background-color 150ms ease;
    }

    [id*="flasher"],
    [class*="flasher"],
    .fl-wrapper,
    .fl-container {
        display: none !important;
    }
</style>
