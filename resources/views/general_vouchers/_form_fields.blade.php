@php
    $voucher = $voucher ?? null;
    $entryDate = old('entry_date', $voucher ? $voucher->entry_date->format('Y-m-d') : date('Y-m-d'));
    $amount = old('amount', $voucher ? round($voucher->amount) : null);
    $details = old('details', $voucher?->details);
    $selectedEntryType = old('entry_type', $voucher?->entry_type ?? 'credit');
    $defaultCashBankId = $banks->firstWhere('account_type', 'cash')?->id;
    $selectedBankId = old('bank_id', $voucher?->bank_id ?? $defaultCashBankId);
    $selectedPartyId = old('party_id', $voucher?->party_id);

    $fieldClass = 'mt-0.5 block w-full h-9 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm';
    $labelClass = 'text-xs font-medium text-gray-700';
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 items-stretch">
    {{-- Left: form fields --}}
    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-2 content-start">
        <div>
            <label for="entry_date" class="{{ $labelClass }}">Date <span class="text-red-600">*</span></label>
            <x-text-input id="entry_date" name="entry_date" type="date" class="{{ $fieldClass }}"
                :value="$entryDate" required />
            <x-input-error :messages="$errors->get('entry_date')" class="mt-0.5" />
        </div>

        <div>
            <span class="{{ $labelClass }}">Entry Type <span class="text-red-600">*</span></span>
            @include('general_vouchers._entry_type_fields', ['selected' => $selectedEntryType])
            <x-input-error :messages="$errors->get('entry_type')" class="mt-0.5" />
        </div>

        <div>
            <label for="bank_id" class="{{ $labelClass }}">Bank A/C <span class="text-red-600">*</span></label>
            <select id="bank_id" name="bank_id" required class="{{ $fieldClass }}">
                <option value="">Select Bank Account</option>
                @foreach($banks as $bank)
                    <option value="{{ $bank->id }}" {{ $selectedBankId == $bank->id ? 'selected' : '' }}>
                        {{ strtoupper($bank->chartOfAccount->name ?? $bank->account_name) }}
                    </option>
                @endforeach
            </select>
            <div id="bank_balance" class="text-[10px] hidden">
                <span class="font-medium">Bal:</span>
                <span id="balance_amount"></span>
            </div>
            <x-input-error :messages="$errors->get('bank_id')" class="mt-0.5" />
        </div>

        <div>
            <label for="party_id" class="{{ $labelClass }}">Party <span class="text-red-600">*</span></label>
            <select id="party_id" name="party_id" required class="{{ $fieldClass }} chosen-select">
                <option value="">Select Party</option>
                @foreach($parties as $party)
                    <option value="{{ $party->id }}" {{ $selectedPartyId == $party->id ? 'selected' : '' }}>
                        {{ $party->name }}@if($party->pcode) ({{ $party->pcode }})@endif
                    </option>
                @endforeach
            </select>
            <div class="chosen-error-container">
                <x-input-error :messages="$errors->get('party_id')" class="mt-0.5" />
            </div>
            <div id="party_balance" class="text-[10px] hidden">
                <span class="font-medium">Bal:</span>
                <span id="party_balance_amount"></span>
            </div>
        </div>

        <div>
            <label for="amount" class="{{ $labelClass }}">Amount <span class="text-red-600">*</span></label>
            <x-text-input id="amount" name="amount" type="number" step="1" class="{{ $fieldClass }}"
                :value="$amount" required placeholder="0" />
            <x-input-error :messages="$errors->get('amount')" class="mt-0.5" />
        </div>

        <div>
            <label for="details" class="{{ $labelClass }}">Details</label>
            <x-text-input id="details" name="details" type="text" class="{{ $fieldClass }}"
                :value="$details" placeholder="Optional..." />
            <x-input-error :messages="$errors->get('details')" class="mt-0.5" />
        </div>
    </div>

    {{-- Right: attachments --}}
    <div class="lg:col-span-1 flex flex-col min-h-[10rem] lg:min-h-0">
        @include('general_vouchers._attachments_section', ['voucher' => $voucher])
    </div>
</div>
