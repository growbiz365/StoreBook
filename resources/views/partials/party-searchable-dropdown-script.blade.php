@once
<script>
(function () {
    if (window.PartySearchableDropdown) {
        return;
    }

    window.formatPartyDisplayText = function (party) {
        if (!party || !party.name) {
            return '';
        }
        let text = party.name;
        if (party.pcode) {
            text += ` (${party.pcode})`;
        }
        return text;
    };

    window.PartySearchableDropdown = class PartySearchableDropdown {
        constructor(container, options = {}) {
            this.container = container;
            this.input = container.querySelector('.searchable-input');
            this.hiddenInput = container.querySelector('.selected-item-id');
            this.dropdown = container.querySelector('.searchable-dropdown');
            this.resultsContainer = container.querySelector('.search-results-container');
            this.paginationContainer = container.querySelector('.pagination-container');
            this.loadingIndicator = container.querySelector('.loading-indicator');

            this.searchTimeout = null;
            this.currentPage = 1;
            this.searchTerm = '';
            this.selectedItem = null;
            this.itemsPerPage = options.itemsPerPage || 25;
            this.debounceDelay = options.debounceDelay || 250;
            this.minSearchLength = options.minSearchLength ?? 1;
            this.excludePartyId = options.excludePartyId || null;

            this.init();
        }

        init() {
            this.bindEvents();
            this.setupGlobalClickHandler();
        }

        setExcludePartyId(partyId) {
            this.excludePartyId = partyId ? String(partyId) : null;
        }

        bindEvents() {
            this.input.addEventListener('focus', () => {
                this.showDropdown();
                if ((this.searchTerm || this.input.value).trim().length >= this.minSearchLength) {
                    this.searchTerm = this.input.value.trim();
                    this.performSearch();
                } else {
                    this.showHint();
                }
            });

            this.input.addEventListener('input', (event) => {
                this.searchTerm = event.target.value.trim();
                this.currentPage = 1;
                this.showDropdown();

                if (!this.searchTerm && this.selectedItem) {
                    this.clearSelection(false);
                }

                this.debounceSearch();
            });

            this.input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    this.selectHighlightedResult();
                } else if (event.key === 'Escape') {
                    this.hideDropdown();
                } else if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    this.navigateResults('down');
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    this.navigateResults('up');
                }
            });

            this.hiddenInput.addEventListener('change', () => {
                this.container.dispatchEvent(new CustomEvent('party-selected', {
                    bubbles: true,
                    detail: { partyId: this.hiddenInput.value || null },
                }));
            });
        }

        setupGlobalClickHandler() {
            document.addEventListener('click', (event) => {
                if (!this.container.contains(event.target)) {
                    this.hideDropdown();
                }
            });
        }

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.performSearch(), this.debounceDelay);
        }

        async performSearch() {
            if (this.searchTerm.length < this.minSearchLength) {
                this.showHint();
                return;
            }

            this.showLoading();

            try {
                const response = await fetch(`/api/parties/search?q=${encodeURIComponent(this.searchTerm)}&page=${this.currentPage}&limit=${this.itemsPerPage}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                if (data.error) {
                    throw new Error(data.message || 'Search failed');
                }

                this.displayResults(data.data || [], data.meta || {});
            } catch (error) {
                console.error('Party search error:', error);
                this.showError('Search failed. Please try again.');
            } finally {
                this.hideLoading();
            }
        }

        displayResults(parties, meta) {
            this.resultsContainer.innerHTML = '';

            const filtered = (parties || []).filter((party) => {
                return party && party.id && party.name && String(party.id) !== String(this.excludePartyId);
            });

            if (filtered.length === 0) {
                this.resultsContainer.innerHTML = `
                    <div class="px-4 py-3 text-sm text-gray-500 text-center">
                        No parties found.
                    </div>
                `;
                this.paginationContainer.classList.add('hidden');
                return;
            }

            filtered.forEach((party) => {
                const resultItem = document.createElement('div');
                resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer result-item';
                resultItem.dataset.partyId = party.id;
                resultItem.dataset.partyName = party.name;
                resultItem.dataset.partyPcode = party.pcode || '';
                resultItem.dataset.partyCnic = party.cnic || '';

                resultItem.innerHTML = `
                    <div class="font-medium text-gray-900">${window.formatPartyDisplayText(party)}</div>
                    <div class="text-sm text-gray-500">${party.phone_no ? `Phone: ${party.phone_no}` : ''}</div>
                `;

                resultItem.addEventListener('click', () => this.selectParty(party));
                this.resultsContainer.appendChild(resultItem);
            });

            if (meta && meta.last_page > 1) {
                this.showPagination(meta);
            } else {
                this.paginationContainer.classList.add('hidden');
            }
        }

        showPagination(meta) {
            this.paginationContainer.classList.remove('hidden');

            const pageInfo = this.paginationContainer.querySelector('.page-info');
            const prevBtn = this.paginationContainer.querySelector('.prev-page');
            const nextBtn = this.paginationContainer.querySelector('.next-page');

            pageInfo.textContent = `Page ${meta.current_page} of ${meta.last_page}`;
            prevBtn.disabled = meta.current_page <= 1;
            nextBtn.disabled = meta.current_page >= meta.last_page;

            prevBtn.onclick = () => {
                if (meta.current_page > 1) {
                    this.currentPage = meta.current_page - 1;
                    this.performSearch();
                }
            };

            nextBtn.onclick = () => {
                if (meta.current_page < meta.last_page) {
                    this.currentPage = meta.current_page + 1;
                    this.performSearch();
                }
            };
        }

        selectParty(party) {
            this.selectedItem = party;
            this.input.value = window.formatPartyDisplayText(party);
            this.hiddenInput.value = party.id;
            this.hiddenInput.dispatchEvent(new Event('change'));
            this.hideDropdown();
        }

        clearSelection(dispatchChange = true) {
            this.selectedItem = null;
            this.hiddenInput.value = '';
            if (dispatchChange) {
                this.hiddenInput.dispatchEvent(new Event('change'));
            }
        }

        selectFirstResult() {
            const firstResult = this.resultsContainer.querySelector('.result-item');
            if (!firstResult) {
                return;
            }

            this.selectParty({
                id: firstResult.dataset.partyId,
                name: firstResult.dataset.partyName,
                pcode: firstResult.dataset.partyPcode || '',
                cnic: firstResult.dataset.partyCnic || '',
            });
        }

        selectHighlightedResult() {
            const highlightedResult = this.resultsContainer.querySelector('.result-item.selected');
            if (highlightedResult) {
                this.selectParty({
                    id: highlightedResult.dataset.partyId,
                    name: highlightedResult.dataset.partyName,
                    pcode: highlightedResult.dataset.partyPcode || '',
                    cnic: highlightedResult.dataset.partyCnic || '',
                });
                return;
            }

            this.selectFirstResult();
        }

        navigateResults(direction) {
            const results = this.resultsContainer.querySelectorAll('.result-item');
            const currentIndex = Array.from(results).findIndex((item) => item.classList.contains('selected'));

            let newIndex;
            if (direction === 'down') {
                newIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
            } else {
                newIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
            }

            results.forEach((item) => item.classList.remove('selected', 'bg-indigo-50'));

            if (results[newIndex]) {
                results[newIndex].classList.add('selected', 'bg-indigo-50');
                results[newIndex].scrollIntoView({ block: 'nearest' });
            }
        }

        showHint() {
            this.resultsContainer.innerHTML = `
                <div class="px-4 py-3 text-sm text-gray-500 text-center">
                    Type party name or pcode to search...
                </div>
            `;
            this.paginationContainer.classList.add('hidden');
        }

        showError(message) {
            this.resultsContainer.innerHTML = `
                <div class="px-4 py-3 text-sm text-red-600 text-center">${message}</div>
            `;
            this.paginationContainer.classList.add('hidden');
        }

        showDropdown() {
            this.dropdown.classList.remove('hidden');
        }

        hideDropdown() {
            this.dropdown.classList.add('hidden');
        }

        showLoading() {
            this.loadingIndicator?.classList.remove('hidden');
        }

        hideLoading() {
            this.loadingIndicator?.classList.add('hidden');
        }
    };

    window.initAjaxPartySelect = function (container, options = {}) {
        if (!container || container._partyDropdown) {
            return container?._partyDropdown || null;
        }

        container._partyDropdown = new window.PartySearchableDropdown(container, options);
        return container._partyDropdown;
    };

    window.restoreAjaxPartySelect = function (container, partyId) {
        if (!container || !partyId) {
            return Promise.resolve();
        }

        return fetch(`/api/parties/${partyId}`, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        })
            .then((response) => response.json())
            .then((party) => {
                if (!party || party.error) {
                    return;
                }

                const instance = container._partyDropdown || window.initAjaxPartySelect(container);
                instance.selectParty(party);
            })
            .catch((error) => console.error('Failed to restore party selection', error));
    };

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-ajax-party-select]').forEach((container) => {
            const excludePartyId = container.dataset.excludePartyId || null;
            window.initAjaxPartySelect(container, { excludePartyId });

            const selectedId = container.querySelector('.selected-item-id')?.value;
            if (selectedId) {
                window.restoreAjaxPartySelect(container, selectedId);
            }
        });
    });
})();
</script>
@endonce
