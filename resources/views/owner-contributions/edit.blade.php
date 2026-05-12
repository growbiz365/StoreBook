<x-app-layout>
    @section('title', 'Edit Owner Contribution | ' . config('app.name'))
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Dashboard'],
        ['url' => route('settings'), 'label' => 'Settings'],
        ['url' => route('owner-contributions.index'), 'label' => 'Owner Contributions'],
        ['url' => route('owner-contributions.show', $ownerContribution), 'label' => '#' . $ownerContribution->id],
        ['url' => '#', 'label' => 'Edit'],
    ]" />

    <x-dynamic-heading title="Edit Owner Contribution" />

    @php
        $via = old('contribution_via', $ownerContribution->contribution_via);
        $equityAccountsJson  = $equityAccounts->map(fn($a) => ['id' => $a->id, 'label' => $a->name . ($a->code ? ' (' . $a->code . ')' : '')])->values()->toJson();

        $fromId = old('from_account_id', $ownerContribution->from_account_id);
        $fromLabel = $equityAccounts->firstWhere('id', $fromId)?->name ?? '';
    @endphp

    <form method="POST" action="{{ route('owner-contributions.update', $ownerContribution) }}" enctype="multipart/form-data"
        x-data="{ contributionVia: '{{ $via }}', amount: '{{ old('amount', $ownerContribution->amount) }}' }">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="space-y-6 lg:col-span-8">

                <div class="rounded-lg bg-white shadow-sm border border-gray-200/80">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            Contribution source
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="contribution_via" class="block text-sm font-semibold text-gray-700 mb-1.5">Via <span class="text-red-500">*</span></label>
                            <select name="contribution_via" id="contribution_via" x-model="contributionVia"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-colors">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                            </select>
                            @error('contribution_via')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <template x-if="contributionVia === 'bank'">
                            <div class="md:col-span-2">
                                <label for="bank_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Bank account <span class="text-red-500">*</span></label>
                                <select name="bank_id" id="bank_id"
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-colors">
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" @selected(old('bank_id', $defaultBankId) == $bank->id)>
                                            {{ $bank->account_name }} @if($bank->bank_name) — {{ $bank->bank_name }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                @if($banks->isEmpty())
                                    <p class="mt-2 text-xs text-amber-600">No active banks linked to an account under &quot;Bank Accounts&quot; in the chart. Fix the bank&apos;s chart account or add a bank under Bank management.</p>
                                @endif
                            </div>
                        </template>

                        <template x-if="contributionVia === 'cash'">
                            <div class="md:col-span-2">
                                <label for="bank_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Cash wallet <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-400 mb-2">Cash accounts from Banks (type Cash)</p>
                                <select name="bank_id" id="bank_id"
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-colors">
                                    <option value="">Select Cash Account</option>
                                    @foreach($cashBanks as $bank)
                                        <option value="{{ $bank->id }}" @selected(old('bank_id', $defaultBankId) == $bank->id)>
                                            {{ $bank->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                @if($cashBanks->isEmpty())
                                    <p class="mt-2 text-xs text-amber-600">No active cash wallets. Add a <strong>Cash</strong> account under Banks.</p>
                                @endif
                                <p class="mt-3 text-xs text-gray-500 leading-relaxed">
                                    Updates this wallet&apos;s ledger balance (Banks module) and the chart via journal entries.
                                </p>
                            </div>
                        </template>

                        <div class="md:col-span-2">
                            <div x-data="accountCombobox(@js(json_decode($equityAccountsJson, true)), @js($fromId), @js($fromLabel))" class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Equity account (from) <span class="text-red-500">*</span></label>
                                <input type="hidden" name="from_account_id" :value="selectedId" />
                                <div class="relative">
                                    <input type="text" x-model="query" @focus="open=true" @input="open=true; highlighted = 0"
                                        @keydown.escape="close()"
                                        @keydown.arrow-down.prevent="highlightNext()"
                                        @keydown.arrow-up.prevent="highlightPrev()"
                                        @keydown.enter.prevent="selectHighlighted()"
                                        placeholder="Search equity account..."
                                        autocomplete="off"
                                        :class="selectedId ? 'border-emerald-400 bg-emerald-50/40' : 'border-gray-300'"
                                        class="block w-full rounded-lg border px-3 py-2.5 pr-8 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-colors" />
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                </div>
                                <ul x-show="open && filtered.length > 0" x-transition
                                    class="absolute z-30 mt-1 max-h-52 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg text-sm">
                                    <template x-for="(item, idx) in filtered" :key="item.id">
                                        <li @click="select(item)" @mouseenter="highlighted=idx"
                                            :class="highlighted===idx ? 'bg-emerald-50 text-emerald-800' : 'text-gray-800'"
                                            class="cursor-pointer px-3 py-2 hover:bg-emerald-50">
                                            <span x-text="item.label"></span>
                                        </li>
                                    </template>
                                </ul>
                                @error('from_account_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white shadow-sm border border-gray-200/80">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Transaction details
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="amount" class="block text-sm font-semibold text-gray-700 mb-1.5">Amount ({{ $businessCurrencySymbol }}) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><span class="text-gray-400 text-sm">{{ $businessCurrencySymbol }}</span></div>
                                <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                                    value="{{ old('amount', $ownerContribution->amount) }}" required placeholder="0.00"
                                    class="block w-full rounded-lg border border-gray-300 pl-8 pr-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-colors"
                                    x-model="amount" />
                            </div>
                            @error('amount')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="contribution_date" class="block text-sm font-semibold text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="contribution_date" id="contribution_date"
                                value="{{ old('contribution_date', $ownerContribution->contribution_date->format('Y-m-d')) }}" required
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-colors" />
                            @error('contribution_date')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Received via <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(['cash' => ['label' => 'Cash', 'icon' => '💵'], 'bank_transfer' => ['label' => 'Bank Transfer', 'icon' => '🏦'], 'cheque' => ['label' => 'Cheque', 'icon' => '📝'], 'online' => ['label' => 'Online', 'icon' => '💳']] as $value => $opt)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="received_via" value="{{ $value }}"
                                            class="sr-only peer" {{ old('received_via', $ownerContribution->received_via) === $value ? 'checked' : '' }}>
                                        <div class="rounded-lg border-2 border-gray-200 p-2.5 text-center text-xs font-medium peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-all duration-150 hover:border-gray-300">
                                            {{ $opt['icon'] }} {{ $opt['label'] }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('received_via')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="reference_number" class="block text-sm font-semibold text-gray-700 mb-1.5">Reference #</label>
                            <input type="text" name="reference_number" id="reference_number"
                                value="{{ old('reference_number', $ownerContribution->reference_number) }}"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-colors" />
                            @error('reference_number')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-colors">{{ old('description', $ownerContribution->description) }}</textarea>
                            @error('description')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white border border-gray-200/80 shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('owner-contributions.show', $ownerContribution) }}" class="text-sm text-gray-500 hover:text-gray-800 transition-colors">← Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-all duration-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Update contribution
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-6 lg:col-span-4">
                <div class="rounded-lg bg-white border border-gray-200/80 shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-4"><h3 class="text-sm font-semibold text-gray-900">Journal entry preview</h3></div>
                    <div class="p-5 space-y-2">
                        <div class="flex items-start justify-between rounded-lg bg-green-50 p-3 text-xs">
                            <div><span class="font-semibold text-green-700">DEBIT</span><p class="mt-0.5 text-gray-600">Deposit (asset)</p></div>
                            <span class="font-semibold text-green-800" x-text="'{{ $businessCurrencySymbol }}' + parseFloat(amount || 0).toFixed(2)"></span>
                        </div>
                        <div class="flex items-start justify-between rounded-lg bg-blue-50 p-3 text-xs">
                            <div><span class="font-semibold text-blue-700">CREDIT</span><p class="mt-0.5 text-gray-600">Equity</p></div>
                            <span class="font-semibold text-blue-800" x-text="'{{ $businessCurrencySymbol }}' + parseFloat(amount || 0).toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                @if($ownerContribution->attachments->count() > 0)
                    <div class="rounded-lg bg-white border border-gray-200/80 shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4"><h3 class="text-sm font-semibold text-gray-900">Existing attachments</h3></div>
                        <ul class="divide-y divide-gray-100 p-2">
                            @foreach($ownerContribution->attachments as $attachment)
                                <li class="flex items-center justify-between p-2.5 rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="h-4 w-4 flex-shrink-0 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                        <span class="truncate text-xs text-gray-700">{{ $attachment->original_name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <a href="{{ route('files.owner-contribution-attachments.download', $attachment) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Download</a>
                                        <button type="button"
                                            onclick="deleteOwnerContributionAttachment('{{ route('owner-contributions.attachments.delete', $attachment) }}', this)"
                                            class="text-xs text-red-500 hover:text-red-700">Remove</button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="rounded-lg bg-white border border-gray-200/80 shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                            Add attachments
                        </h3>
                    </div>
                    <div class="p-5" x-data="{ files: [0] }">
                        <div class="space-y-2.5">
                            <template x-for="(idx, i) in files" :key="i">
                                <div class="flex items-center gap-2">
                                    <input name="attachments[]" type="file"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                                        class="block w-full text-xs text-gray-500 file:mr-3 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-emerald-700 hover:file:bg-emerald-100" />
                                    <button type="button" @click="files.splice(i, 1)" x-show="files.length > 1"
                                        class="flex-shrink-0 rounded-md bg-red-50 p-1.5 text-red-600 hover:bg-red-100">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="files.push(files.length)"
                            class="mt-3 flex items-center gap-1.5 text-xs font-medium text-emerald-600 hover:text-emerald-800">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add another file
                        </button>
                        <p class="mt-2 text-xs text-gray-400">PDF, DOC, XLS, JPG, PNG — max 10MB each</p>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        function accountCombobox(items, initialId, initialLabel) {
            return {
                items: items || [],
                query: initialLabel || '',
                selectedId: initialId || null,
                open: false,
                highlighted: 0,
                get filtered() {
                    if (!this.query) return this.items;
                    const q = this.query.toLowerCase();
                    return this.items.filter(i => i.label.toLowerCase().includes(q));
                },
                select(item) {
                    this.selectedId = item.id;
                    this.query = item.label;
                    this.open = false;
                },
                selectHighlighted() {
                    if (this.filtered[this.highlighted]) this.select(this.filtered[this.highlighted]);
                },
                highlightNext() {
                    if (this.highlighted < this.filtered.length - 1) this.highlighted++;
                },
                highlightPrev() {
                    if (this.highlighted > 0) this.highlighted--;
                },
                close() {
                    this.open = false;
                    if (this.selectedId) {
                        const match = this.items.find(i => i.id == this.selectedId);
                        if (match && this.query !== match.label) {
                            this.selectedId = null;
                            this.query = '';
                        }
                    }
                }
            };
        }

        function deleteOwnerContributionAttachment(url, btn) {
            if (!confirm('Remove this attachment?')) return;
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(data => {
                if (data.success) btn.closest('li').remove();
                else alert('Could not remove attachment.');
            }).catch(() => alert('Could not remove attachment.'));
        }
    </script>
</x-app-layout>
