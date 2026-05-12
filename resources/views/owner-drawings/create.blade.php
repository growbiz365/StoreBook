<x-app-layout>
    @section('title', 'New Owner Drawing | ' . config('app.name'))
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Dashboard'],
        ['url' => route('settings'), 'label' => 'Settings'],
        ['url' => route('owner-drawings.index'), 'label' => 'Owner Drawings'],
        ['url' => '#', 'label' => 'New Drawing'],
    ]" />

    <x-dynamic-heading title="New Owner Drawing" />

    @php
        $via = old('drawing_via', 'cash');
        $equityAccountsJson = $equityAccounts->map(fn($a) => ['id' => $a->id, 'label' => $a->name . ($a->code ? ' (' . $a->code . ')' : '')])->values()->toJson();
        $oldTo = old('to_account_id');
        $oldToLabel = $oldTo ? $equityAccounts->firstWhere('id', $oldTo)?->name : '';
    @endphp

    <form method="POST" action="{{ route('owner-drawings.store') }}" enctype="multipart/form-data"
        x-data="{ drawingVia: '{{ $via }}', amount: '{{ old('amount', '') }}' }">
        @csrf

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="space-y-6 lg:col-span-8">

                <div class="rounded-lg border border-gray-200/80 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                            <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Withdrawal source
                        </h3>
                        <p class="mt-0.5 text-xs text-gray-500">Choose cash wallet or bank account the funds are paid from.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="drawing_via" class="mb-1.5 block text-sm font-semibold text-gray-700">Via <span class="text-red-500">*</span></label>
                            <select name="drawing_via" id="drawing_via" x-model="drawingVia"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                            </select>
                            @error('drawing_via')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <template x-if="drawingVia === 'bank'">
                            <div class="md:col-span-2">
                                <label for="bank_id" class="mb-1.5 block text-sm font-semibold text-gray-700">Bank account <span class="text-red-500">*</span></label>
                                <select name="bank_id" id="bank_id"
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none">
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" @selected(old('bank_id') == $bank->id)>
                                            {{ $bank->account_name }} @if($bank->bank_name) — {{ $bank->bank_name }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                @if($banks->isEmpty())
                                    <p class="mt-2 text-xs text-amber-600">No active banks under &quot;Bank Accounts&quot; in the chart. Add or fix banks under Bank management.</p>
                                @endif
                            </div>
                        </template>

                        <template x-if="drawingVia === 'cash'">
                            <div class="md:col-span-2">
                                <label for="bank_id" class="mb-1.5 block text-sm font-semibold text-gray-700">Cash wallet <span class="text-red-500">*</span></label>
                                <p class="mb-2 text-xs text-gray-400">Banks module — type Cash, linked to Cash in Hand</p>
                                <select name="bank_id" id="bank_id"
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none">
                                    <option value="">Select Cash Account</option>
                                    @foreach($cashBanks as $bank)
                                        <option value="{{ $bank->id }}" @selected(old('bank_id') == $bank->id)>{{ $bank->account_name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                @if($cashBanks->isEmpty())
                                    <p class="mt-2 text-xs text-amber-600">No cash wallets. Add a <strong>Cash</strong> account under Banks.</p>
                                @endif
                            </div>
                        </template>

                        <div class="md:col-span-2">
                            <div x-data="accountCombobox(@js(json_decode($equityAccountsJson, true)), @js($oldTo), @js($oldToLabel))" class="relative">
                                <label class="mb-1.5 block text-sm font-semibold text-gray-700">Equity account (to) <span class="text-red-500">*</span></label>
                                <p class="mb-2 text-xs text-gray-400">Owner equity debited for this withdrawal</p>
                                <input type="hidden" name="to_account_id" :value="selectedId" />
                                <div class="relative">
                                    <input type="text" x-model="query" @focus="open=true" @input="open=true; highlighted = 0"
                                        @keydown.escape="close()"
                                        @keydown.arrow-down.prevent="highlightNext()"
                                        @keydown.arrow-up.prevent="highlightPrev()"
                                        @keydown.enter.prevent="selectHighlighted()"
                                        placeholder="Search equity account..."
                                        autocomplete="off"
                                        :class="selectedId ? 'border-rose-400 bg-rose-50/40' : 'border-gray-300'"
                                        class="block w-full rounded-lg border px-3 py-2.5 pr-8 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none" />
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                </div>
                                <ul x-show="open && filtered.length > 0" x-transition
                                    class="absolute z-30 mt-1 max-h-52 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 text-sm shadow-lg">
                                    <template x-for="(item, idx) in filtered" :key="item.id">
                                        <li @click="select(item)" @mouseenter="highlighted=idx"
                                            :class="highlighted===idx ? 'bg-rose-50 text-rose-800' : 'text-gray-800'"
                                            class="cursor-pointer px-3 py-2 hover:bg-rose-50">
                                            <span x-text="item.label"></span>
                                        </li>
                                    </template>
                                </ul>
                                <p x-show="open && query.length > 0 && filtered.length === 0" class="mt-1 pl-1 text-xs text-gray-400">No accounts found.</p>
                                @error('to_account_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200/80 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                            <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Transaction details
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                        <div>
                            <label for="amount" class="mb-1.5 block text-sm font-semibold text-gray-700">Amount ({{ $businessCurrencySymbol }}) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><span class="text-sm text-gray-400">{{ $businessCurrencySymbol }}</span></div>
                                <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required placeholder="0.00"
                                    class="block w-full rounded-lg border border-gray-300 py-2.5 pl-8 pr-3 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none"
                                    x-model="amount" />
                            </div>
                            @error('amount')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="drawing_date" class="mb-1.5 block text-sm font-semibold text-gray-700">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="drawing_date" id="drawing_date" value="{{ old('drawing_date', date('Y-m-d')) }}" required
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none" />
                            @error('drawing_date')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Paid via <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(['cash' => ['label' => 'Cash', 'icon' => '💵'], 'bank_transfer' => ['label' => 'Bank Transfer', 'icon' => '🏦'], 'cheque' => ['label' => 'Cheque', 'icon' => '📝'], 'online' => ['label' => 'Online', 'icon' => '💳']] as $value => $opt)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="paid_via" value="{{ $value }}" class="peer sr-only" {{ old('paid_via', 'cash') === $value ? 'checked' : '' }}>
                                        <div class="rounded-lg border-2 border-gray-200 p-2.5 text-center text-xs font-medium transition-all duration-150 hover:border-gray-300 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700">
                                            {{ $opt['icon'] }} {{ $opt['label'] }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('paid_via')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="reference_number" class="mb-1.5 block text-sm font-semibold text-gray-700">Reference #</label>
                            <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" placeholder="Cheque #, transfer ref…"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none" />
                            @error('reference_number')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="mb-1.5 block text-sm font-semibold text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3" placeholder="Notes about this drawing…"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none">{{ old('description') }}</textarea>
                            @error('description')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200/80 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('owner-drawings.index') }}" class="text-sm text-gray-500 transition-colors hover:text-gray-800">← Back to list</a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Save drawing
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-6 lg:col-span-4">
                <div class="rounded-lg border border-gray-200/80 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h3 class="text-sm font-semibold text-gray-900">Journal preview</h3>
                    </div>
                    <div class="p-5">
                        <p class="mb-3 text-xs leading-relaxed text-gray-500">On save: debit equity, credit asset; bank/cash ledger shows a withdrawal.</p>
                        <div class="space-y-2">
                            <div class="flex items-start justify-between rounded-lg bg-rose-50 p-3 text-xs">
                                <div><span class="font-semibold text-rose-700">DEBIT</span><p class="mt-0.5 text-gray-600">Equity (to)</p></div>
                                <span class="font-semibold text-rose-800" x-text="'{{ $businessCurrencySymbol }}' + parseFloat(amount || 0).toFixed(2)">{{ $businessCurrencySymbol }}0.00</span>
                            </div>
                            <div class="flex items-start justify-between rounded-lg bg-amber-50 p-3 text-xs">
                                <div><span class="font-semibold text-amber-800">CREDIT</span><p class="mt-0.5 text-gray-600">Cash / bank asset</p></div>
                                <span class="font-semibold text-amber-900" x-text="'{{ $businessCurrencySymbol }}' + parseFloat(amount || 0).toFixed(2)">{{ $businessCurrencySymbol }}0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200/80 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                            Attachments
                        </h3>
                    </div>
                    <div class="p-5" x-data="{ files: [0] }">
                        <div class="space-y-2.5">
                            <template x-for="(idx, i) in files" :key="i">
                                <div class="flex items-center gap-2">
                                    <input name="attachments[]" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                                        class="block w-full text-xs text-gray-500 file:mr-3 file:rounded-md file:border-0 file:bg-rose-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-rose-700 hover:file:bg-rose-100" />
                                    <button type="button" @click="files.splice(i, 1)" x-show="files.length > 1" class="flex-shrink-0 rounded-md bg-red-50 p-1.5 text-red-600 hover:bg-red-100">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="files.push(files.length)" class="mt-3 flex items-center gap-1.5 text-xs font-medium text-rose-600 hover:text-rose-800">+ Add file</button>
                        <p class="mt-2 text-xs text-gray-400">Max 10MB each</p>
                        @error('attachments.*')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
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
    </script>
</x-app-layout>
