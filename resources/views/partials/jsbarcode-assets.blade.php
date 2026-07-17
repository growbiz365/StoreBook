@once
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
(function () {
    if (window.renderBarcode) {
        return;
    }

    window.normalizeBarcodeText = function (code) {
        return String(code || '')
            .replace(/[\x00-\x1f\x7f]/g, '')
            .trim();
    };

    window.renderBarcode = function (target, code, options) {
        const el = typeof target === 'string' ? document.querySelector(target) : target;
        const normalized = window.normalizeBarcodeText(code);

        if (!el || !normalized || typeof JsBarcode === 'undefined') {
            return false;
        }

        const datasetWidth = parseFloat(el.dataset.barcodeWidth);
        const datasetHeight = parseFloat(el.dataset.barcodeHeight);
        const datasetMargin = parseFloat(el.dataset.barcodeMargin);

        const settings = Object.assign({
            format: 'CODE128',
            displayValue: false,
            margin: Number.isFinite(datasetMargin) ? datasetMargin : 10,
            height: Number.isFinite(datasetHeight) ? datasetHeight : 44,
            width: Number.isFinite(datasetWidth) ? datasetWidth : 2,
            lineColor: '#000000',
            background: '#ffffff',
        }, options || {});

        try {
            JsBarcode(el, normalized, settings);
            el.dataset.barcodeReady = '1';
            return true;
        } catch (error) {
            console.error('Barcode render failed for code:', normalized, error);
            el.dataset.barcodeReady = '0';
            return false;
        }
    };

    window.renderBarcodesIn = function (root, options) {
        let rendered = 0;
        (root || document).querySelectorAll('[data-barcode-code]').forEach(function (el) {
            if (window.renderBarcode(el, el.getAttribute('data-barcode-code'), options)) {
                rendered++;
            }
        });
        return rendered;
    };
})();
</script>
@endonce
