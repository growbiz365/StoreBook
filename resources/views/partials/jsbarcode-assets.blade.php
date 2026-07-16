@once
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
(function () {
    if (window.renderBarcode) {
        return;
    }

    window.renderBarcode = function (target, code, options) {
        const el = typeof target === 'string' ? document.querySelector(target) : target;
        if (!el || !code || typeof JsBarcode === 'undefined') {
            return;
        }

        const datasetWidth = parseFloat(el.dataset.barcodeWidth);
        const datasetHeight = parseFloat(el.dataset.barcodeHeight);

        JsBarcode(el, String(code), Object.assign({
            format: 'CODE128',
            displayValue: false,
            margin: 2,
            height: Number.isFinite(datasetHeight) ? datasetHeight : 38,
            width: Number.isFinite(datasetWidth) ? datasetWidth : 1.75,
            lineColor: '#111827',
        }, options || {}));
    };

    window.renderBarcodesIn = function (root) {
        (root || document).querySelectorAll('[data-barcode-code]').forEach(function (el) {
            window.renderBarcode(el, el.getAttribute('data-barcode-code'));
        });
    };
})();
</script>
@endonce
