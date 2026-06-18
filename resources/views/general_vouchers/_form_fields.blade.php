@php
    $voucher = $voucher ?? null;
    $entryDate = old('entry_date', $voucher ? $voucher->entry_date->format('Y-m-d') : date('Y-m-d'));
    $amount = old('amount', $voucher ? round($voucher->amount) : null);
    $details = old('details', $voucher?->details);
    $selectedEntryType = old('entry_type', $voucher?->entry_type ?? 'credit');
    $defaultCashBankId = $banks->firstWhere('account_type', 'cash')?->id;
    $selectedBankId = old('bank_id', $voucher?->bank_id ?? $defaultCashBankId);
    $selectedPartyId = old('party_id', $voucher?->party_id);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-x-4 gap-y-3 lg:items-start">
    {{-- 1. Entry Date --}}
    <div class="lg:col-span-3">
        <x-input-label for="entry_date">Entry Date <span class="text-red-600">*</span></x-input-label>
        <x-text-input id="entry_date" name="entry_date" type="date" class="mt-1 block w-full text-sm"
            :value="$entryDate" required />
        <x-input-error :messages="$errors->get('entry_date')" class="mt-1" />
    </div>

    {{-- 2. Entry Type --}}
    <div class="lg:col-span-9">
        <x-input-label>Entry Type <span class="text-red-600">*</span></x-input-label>
        @include('general_vouchers._entry_type_fields', ['selected' => $selectedEntryType])
        <x-input-error :messages="$errors->get('entry_type')" class="mt-1" />
    </div>

    {{-- 3. Bank Account --}}
    <div class="lg:col-span-4">
        <x-input-label for="bank_id">Bank Account <span class="text-red-600">*</span></x-input-label>
        <select id="bank_id" name="bank_id" required
            class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">Select Bank Account</option>
            @foreach($banks as $bank)
                <option value="{{ $bank->id }}" {{ $selectedBankId == $bank->id ? 'selected' : '' }}>
                    {{ strtoupper($bank->chartOfAccount->name ?? $bank->account_name) }}
                </option>
            @endforeach
        </select>
        <div id="bank_balance" class="mt-0.5 text-xs hidden">
            <span class="font-medium">Balance:</span>
            <span id="balance_amount" class="ml-1"></span>
        </div>
        <x-input-error :messages="$errors->get('bank_id')" class="mt-1" />
    </div>

    {{-- 4. Party --}}
    <div class="lg:col-span-4">
        <x-input-label for="party_id">Party <span class="text-red-600">*</span></x-input-label>
        <select id="party_id" name="party_id" required
            class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">Select Party</option>
            @foreach($parties as $party)
                <option value="{{ $party->id }}" {{ $selectedPartyId == $party->id ? 'selected' : '' }}>
                    {{ $party->name }}
                </option>
            @endforeach
        </select>
        <div id="party_balance" class="mt-0.5 text-xs hidden">
            <span class="font-medium">Balance:</span>
            <span id="party_balance_amount" class="ml-1"></span>
        </div>
        <x-input-error :messages="$errors->get('party_id')" class="mt-1" />
    </div>

    {{-- 5. Amount --}}
    <div class="lg:col-span-4">
        <x-input-label for="amount">Amount <span class="text-red-600">*</span></x-input-label>
        <x-text-input id="amount" name="amount" type="number" step="1" class="mt-1 block w-full text-sm"
            :value="$amount" required placeholder="0" />
        <x-input-error :messages="$errors->get('amount')" class="mt-1" />
    </div>

    {{-- 6. Details --}}
    <div class="lg:col-span-6 flex flex-col">
        <x-input-label for="details">Details</x-input-label>
        <textarea id="details" name="details" rows="4"
            class="mt-1 block w-full min-h-[8.5rem] rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm resize-none"
            placeholder="Optional voucher details...">{{ $details }}</textarea>
        <x-input-error :messages="$errors->get('details')" class="mt-1" />
    </div>

    {{-- 7. Attachments --}}
    <div class="lg:col-span-6 flex flex-col">
        <x-input-label>Attachments</x-input-label>
        <div class="mt-1 min-h-[8.5rem] rounded-md border border-gray-200 bg-gray-50 p-2.5 flex flex-col">
            <p class="text-[10px] text-gray-500 mb-2">PDF, Word, Images</p>

            @if($voucher && $voucher->attachments->isNotEmpty())
                <div class="mb-2 space-y-1 max-h-16 overflow-y-auto">
                    @foreach($voucher->attachments as $attachment)
                        <div class="flex items-center justify-between gap-2 bg-white rounded border px-2 py-1 text-xs" data-attachment-id="{{ $attachment->id }}">
                            <span class="truncate text-gray-900" title="{{ $attachment->original_name }}">{{ $attachment->original_name }}</span>
                            <div class="shrink-0 flex items-center gap-2">
                                <a href="{{ route('files.general-voucher-attachments.download', $attachment) }}" target="_blank"
                                    class="text-indigo-600 hover:text-indigo-900">View</a>
                                <button type="button" onclick="deleteAttachment({{ $attachment->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div id="attachments-container" class="space-y-2">
                <div class="attachment-group">
                    @include('general_vouchers._attachment_fields')
                </div>
            </div>

            <button type="button" onclick="addAttachmentField()"
                class="mt-auto pt-2 self-start inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500">
                <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add More
            </button>
        </div>
    </div>
</div>
