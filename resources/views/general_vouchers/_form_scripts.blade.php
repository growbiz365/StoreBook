<script>
    const attachmentFieldHtml = @json(view('general_vouchers._attachment_fields')->render());

    function addAttachmentField() {
        const container = document.getElementById('attachments-container');
        const newGroup = document.createElement('div');
        newGroup.className = 'attachment-group border-t border-gray-200 pt-2';
        newGroup.innerHTML = attachmentFieldHtml + `
            <button type="button" onclick="this.parentElement.remove()" class="mt-1 text-[10px] text-red-600 hover:text-red-800">Remove</button>
        `;
        container.appendChild(newGroup);
        container.scrollTop = container.scrollHeight;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const bankSelect = document.getElementById('bank_id');
        const amountInput = document.getElementById('amount');
        const entryTypeRadios = document.querySelectorAll('input[name="entry_type"]');
        const $partySelect = window.jQuery ? $('#party_id') : null;

        function getPartyId() {
            return $partySelect ? $partySelect.val() : document.getElementById('party_id')?.value;
        }

        if ($partySelect && $partySelect.length) {
            $partySelect.chosen({
                placeholder_text_single: 'Select Party',
                search_contains: true,
                width: '100%',
                allow_single_deselect: true
            });

            if ($partySelect.parent().find('.chosen-error-container .text-red-600').length) {
                $partySelect.next('.chosen-container').find('.chosen-single').addClass('border-red-500');
            }

            $partySelect.on('change', function() {
                const partyId = $(this).val();
                if (partyId) {
                    fetchPartyBalance(partyId);
                } else {
                    hidePartyBalance();
                }
            });
        }

        bankSelect.addEventListener('change', function() {
            if (this.value) {
                fetchBankBalance(this.value);
            } else {
                hideBankBalance();
            }
        });

        entryTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (bankSelect.value) {
                    fetchBankBalance(bankSelect.value);
                }
                const partyId = getPartyId();
                if (partyId) {
                    fetchPartyBalance(partyId);
                }
            });
        });

        function fetchPartyBalance(partyId) {
            if (!partyId) {
                hidePartyBalance();
                return;
            }

            fetch(`/parties/${partyId}/balance`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.balance !== undefined) {
                        showPartyBalance(data.formatted_balance ?? data.balance, data.status);
                    } else {
                        hidePartyBalance();
                    }
                })
                .catch(error => {
                    console.error('Error fetching party balance:', error);
                    hidePartyBalance();
                });
        }

        function showPartyBalance(balance, status) {
            const balanceDiv = document.getElementById('party_balance');
            const balanceSpan = document.getElementById('party_balance_amount');

            balanceSpan.textContent = balance;
            balanceSpan.className = '';
            if (status === 'positive') {
                balanceSpan.classList.add('text-green-600');
            } else if (status === 'negative') {
                balanceSpan.classList.add('text-red-600');
            } else {
                balanceSpan.classList.add('text-gray-600');
            }

            balanceDiv.classList.remove('hidden');
        }

        function hidePartyBalance() {
            document.getElementById('party_balance').classList.add('hidden');
        }

        function fetchBankBalance(bankId) {
            fetch(`/banks/${bankId}/balance`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.balance !== undefined) {
                        showBankBalance(data.balance, data.status);
                    } else {
                        hideBankBalance();
                    }
                })
                .catch(error => {
                    console.error('Error fetching bank balance:', error);
                    hideBankBalance();
                });
        }

        function showBankBalance(balance, status) {
            const balanceDiv = document.getElementById('bank_balance');
            const balanceSpan = document.getElementById('balance_amount');

            balanceSpan.textContent = Math.round(parseFloat(balance)).toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });

            balanceSpan.className = '';
            if (status === 'positive') {
                balanceSpan.classList.add('text-green-600');
            } else if (status === 'negative') {
                balanceSpan.classList.add('text-red-600');
            } else {
                balanceSpan.classList.add('text-gray-600');
            }

            balanceDiv.classList.remove('hidden');
        }

        function hideBankBalance() {
            document.getElementById('bank_balance').classList.add('hidden');
        }

        if (bankSelect.value) {
            fetchBankBalance(bankSelect.value);
        }

        const initialPartyId = getPartyId();
        if (initialPartyId) {
            fetchPartyBalance(initialPartyId);
        }

        form.addEventListener('submit', function(e) {
            if (!bankSelect.value || !getPartyId()) {
                e.preventDefault();
                alert('Please select both Bank Account and Party.');
                return;
            }

            const entryTypeSelected = Array.from(entryTypeRadios).some(radio => radio.checked);
            if (!entryTypeSelected) {
                e.preventDefault();
                alert('Please select an entry type.');
                return;
            }

            const amount = parseFloat(amountInput.value) || 0;
            if (amount <= 0) {
                e.preventDefault();
                alert('Please enter a valid amount.');
                return;
            }
        });
    });
</script>
