@once
<script>
(function () {
    if (window.BarcodeScan) {
        return;
    }

    function normalizeScanInput(value) {
        return String(value || '')
            .replace(/[\x00-\x1f\x7f]/g, '')
            .trim();
    }

    window.BarcodeScan = {
        init(options) {
            const inputId = options.inputId || 'pos_barcode';
            const input = document.getElementById(inputId);
            if (!input) {
                return;
            }

            const itemTypeSelectId = options.itemTypeSelectId || 'pos_item_type_id';
            const lookupUrl = options.lookupUrl || '/api/general-items/lookup-by-code';
            let lookupInFlight = false;

            async function runLookup() {
                if (lookupInFlight) {
                    return;
                }

                const raw = normalizeScanInput(input.value);
                if (!raw) {
                    return;
                }

                lookupInFlight = true;

                const params = new URLSearchParams({ code: raw });
                const typeSelect = document.getElementById(itemTypeSelectId);
                if (typeSelect && typeSelect.value) {
                    params.set('item_type_id', typeSelect.value);
                }

                try {
                    const response = await fetch(`${lookupUrl}?${params.toString()}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        },
                        credentials: 'same-origin',
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const message = data.message || 'No item matches this code.';
                        if (typeof options.onNotFound === 'function') {
                            options.onNotFound(message, raw);
                        } else {
                            alert(message);
                        }
                        return;
                    }

                    if (typeof options.onItemFound === 'function') {
                        options.onItemFound(data);
                    }

                    input.value = '';
                    if (options.refocus !== false) {
                        input.focus();
                    }
                } catch (error) {
                    console.error('Barcode lookup failed:', error);
                    const message = 'Barcode lookup failed. Please try again.';
                    if (typeof options.onError === 'function') {
                        options.onError(message, error);
                    } else {
                        alert(message);
                    }
                } finally {
                    lookupInFlight = false;
                }
            }

            input.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter') {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();
                runLookup();
            });

            if (options.enableF8 !== false) {
                document.addEventListener('keydown', function (event) {
                    if (event.key !== 'F8' || event.altKey || event.ctrlKey || event.metaKey) {
                        return;
                    }
                    event.preventDefault();
                    input.focus();
                    input.select();
                });
            }
        },
    };
})();
</script>
@endonce
