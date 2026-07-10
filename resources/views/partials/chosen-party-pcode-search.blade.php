<script>
    (function () {
        if (!window.jQuery) {
            return;
        }

        const $ = window.jQuery;

        function getOptionPcode($select, optionIndex) {
            const $option = $select.find('option').eq(optionIndex);
            const dataPcode = ($option.attr('data-pcode') || '').trim();
            if (dataPcode !== '') {
                return dataPcode;
            }

            const text = $option.text().trim();
            const match = text.match(/\(([^)]+)\)\s*$/);
            return match ? match[1].trim() : '';
        }

        function reorderPartyChosenResults($select, term) {
            term = (term || '').trim();
            if (term === '') {
                return;
            }

            const $container = $select.next('.chosen-container');
            const $results = $container.find('.chosen-results');
            const $items = $results.find('li.active-result');

            if ($items.length < 2) {
                return;
            }

            const upper = term.toUpperCase();
            const ranked = $items.get().map((element) => {
                const index = parseInt(element.getAttribute('data-option-array-index'), 10);
                const pcode = getOptionPcode($select, index).toUpperCase();
                const text = $(element).text().trim();
                let score = 4;

                if (pcode !== '' && pcode === upper) {
                    score = 0;
                } else if (pcode !== '' && pcode.startsWith(upper)) {
                    score = 1;
                } else if (pcode !== '' && pcode.includes(upper)) {
                    score = 2;
                } else if (text.toUpperCase().includes(upper)) {
                    score = 3;
                }

                return { element, score, text };
            });

            ranked.sort((a, b) => a.score - b.score || a.text.localeCompare(b.text));
            ranked.forEach(({ element }) => $results.append(element));

            $items.removeClass('highlighted');
            if (ranked.length > 0) {
                $(ranked[0].element).addClass('highlighted');
            }
        }

        window.attachPartyChosenPcodeSort = function ($select) {
            if (!$select || !$select.length || $select.data('partyPcodeSortAttached')) {
                return $select;
            }

            const bindSort = () => {
                const $container = $select.next('.chosen-container');
                if (!$container.length) {
                    return false;
                }

                $select.data('partyPcodeSortAttached', true);
                $container.on('keyup.partyPcodeSort input.partyPcodeSort', '.chosen-search input', function () {
                    const term = this.value;
                    window.requestAnimationFrame(() => reorderPartyChosenResults($select, term));
                });

                return true;
            };

            if (!bindSort()) {
                $select.one('chosen:ready', bindSort);
            }

            return $select;
        };

        window.initPartyChosen = function (selectorOrElement, options) {
            const $select = typeof selectorOrElement === 'string'
                ? $(selectorOrElement)
                : selectorOrElement;

            if (!$select || !$select.length || !$.fn.chosen) {
                return $select;
            }

            const defaults = {
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
            };

            $select.chosen($.extend({}, defaults, options || {}));
            window.attachPartyChosenPcodeSort($select);

            return $select;
        };
    })();
</script>
