<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {
                        DEFAULT: '#0B8FE8',
                        50: '#EFF8FF',
                        100: '#DFF1FF',
                        200: '#B9E4FF',
                        300: '#7DCEFF',
                        400: '#38B4F8',
                        500: '#0B8FE8',
                        600: '#0878C4',
                        700: '#06619F',
                        800: '#075283',
                        900: '#0A456D',
                        950: '#062B49',
                    },
                },
            },
        },
    };
</script>
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
    :root {
        --lender-blue: #0B8FE8;
        --lender-blue-hover: #0878C4;
        --lender-blue-page: #38B4F8;
        --lender-blue-text: #062B49;
    }

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

    .lender-btn-primary {
        background-color: var(--lender-blue);
        box-shadow: 0 1px 2px 0 rgba(6, 43, 73, 0.12);
        color: #ffffff;
    }

    .lender-btn-primary:hover {
        background-color: var(--lender-blue-hover);
        color: #ffffff;
    }

    .lender-btn-primary:focus {
        outline: 2px solid rgba(11, 143, 232, 0.25);
        outline-offset: 2px;
    }

    .lender-btn-soft {
        background-color: #EFF8FF;
        color: var(--lender-blue-text);
    }

    .lender-btn-soft:hover {
        background-color: #DFF1FF;
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

    input[type="file"]::file-selector-button {
        margin-right: 0.75rem;
        border: 0;
        border-radius: 0.375rem;
        background-color: var(--lender-blue);
        color: #ffffff;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        transition: background-color 150ms ease;
    }

    input[type="file"]::file-selector-button:hover {
        background-color: var(--lender-blue-hover);
    }

    [id*="flasher"],
    [class*="flasher"],
    .fl-wrapper,
    .fl-container {
        display: none !important;
    }
</style>
