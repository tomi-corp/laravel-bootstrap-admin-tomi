<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" crossorigin="anonymous"></script>
<script>
    window.AppToast = toastr;
    window.AppAlert = Swal;

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3500,
    };

    window.formatRupiahInput = function (value) {
        const digits = String(value || '').replace(/\D/g, '');

        if (!digits) {
            return '';
        }

        return 'Rp. ' + digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    window.normalizeCurrencyInput = function (value) {
        return String(value || '').replace(/\D/g, '');
    };

    function initializeCurrencyInputs(scope) {
        $(scope || document).find('.js-rupiah-input').each(function () {
            this.value = window.formatRupiahInput(this.value);
        });
    }

    $(document).on('input', '.js-rupiah-input', function () {
        const cursorAtEnd = this.selectionStart === this.value.length;
        this.value = window.formatRupiahInput(this.value);

        if (cursorAtEnd) {
            this.setSelectionRange(this.value.length, this.value.length);
        }
    });

    $(document).on('submit', 'form', function () {
        $(this).find('.js-rupiah-input').each(function () {
            this.value = window.normalizeCurrencyInput(this.value);
        });
    });

    $(function () {
        initializeCurrencyInputs(document);
    });
</script>

