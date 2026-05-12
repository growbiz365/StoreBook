<x-app-layout>
    @section('title', 'Edit Owner Drawing | ' . config('app.name'))
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Dashboard'],
        ['url' => route('settings'), 'label' => 'Settings'],
        ['url' => route('owner-drawings.index'), 'label' => 'Owner Drawings'],
        ['url' => route('owner-drawings.show', $ownerDrawing), 'label' => '#' . $ownerDrawing->id],
        ['url' => '#', 'label' => 'Edit'],
    ]" />

    <x-dynamic-heading title="Edit Owner Drawing" />

    @php
        $via = old('drawing_via', $ownerDrawing->drawing_via);
        $equityAccountsJson = $equityAccounts->map(fn($a) => ['id' => $a->id, 'label' => $a->name . ($a->code ? ' (' . $a->code . ')' : '')])->values()->toJson();
        $toId = old('to_account_id', $ownerDrawing->to_account_id);
        $toLabel = $equityAccounts->firstWhere('id', $toId)?->name ?? '';
    @endphp

    <form method="POST" action="{{ route('owner-drawings.update', $ownerDrawing) }}" enctype="multipart/form-data"
        x-data="{ drawingVia: '{{ $via }}', amount: '{{ old('amount', $ownerDrawing->amount) }}' }">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="space-y-6 lg:col-span-8">

                <div class="rounded-lg border border-gray-200/80 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                            <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            Withdrawal source
                        </h3>
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
                                        <option value="{{ $bank->id }}" @selected(old('bank_id', $defaultBankId) == $bank->id)>
                                            {{ $bank->account_name }} @if($bank->bank_name) — {{ $bank->bank_name }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </template>

                        <template x-if="drawingVia === 'cash'">
                            <div class="md:col-span-2">
                                <label for="bank_id" class="mb-1.5 block text-sm font-semibold text-gray-700">Cash wallet <span class="text-red-500">*</span></label>
                                <select name="bank_id" id="bank_id"
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none">
                                    <option value="">Select Cash Account</option>
                                    @foreach($cashBanks as $bank)
                                        <option value="{{ $bank->id }}" @selected(old('bank_id', $defaultBankId) == $bank->id)>{{ $bank->account_name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </template>

                        <div class="md:col-span-2">
                            <div x-data="accountCombobox(@js(json_decode($equityAccountsJson, true)), @js($toId), @js($toLabel))" class="relative">
                                <label class="mb-1.5 block text-sm font-semibold text-gray-700">Equity account (to) <span class="text-red-500">*</span></label>
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
                                <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                                    value="{{ old('amount', $ownerDrawing->amount) }}" required placeholder="0.00"
                                    class="block w-full rounded-lg border border-gray-300 py-2.5 pl-8 pr-3 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none"
                                    x-model="amount" />
                            </div>
                            @error('amount')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="drawing_date" class="mb-1.5 block text-sm font-semibold text-gray-700">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="drawing_date" id="drawing_date"
                                value="{{ old('drawing_date', $ownerDrawing->drawing_date->format('Y-m-d')) }}" required
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none" />
                            @error('drawing_date')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Paid via <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(['cash' => ['label' => 'Cash', 'icon' => '💵'], 'bank_transfer' => ['label' => 'Bank Transfer', 'icon' => '🏦'], 'cheque' => ['label' => 'Cheque', 'icon' => '📝'], 'online' => ['label' => 'Online', 'icon' => '💳']] as $value => $opt)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="paid_via" value="{{ $value }}" class="peer sr-only" {{ old('paid_via', $ownerDrawing->paid_via) === $value ? 'checked' : '' }}>
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
                            <input type="text" name="reference_number" id="reference_number"
                                value="{{ old('reference_number', $ownerDrawing->reference_number) }}"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none" />
                            @error('reference_number')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="mb-1.5 block text-sm font-semibold text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition-colors focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none">{{ old('description', $ownerDrawing->description) }}</textarea>
                            @error('description')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200/80 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('owner-drawings.show', $ownerDrawing) }}" class="text-sm text-gray-500 transition-colors hover:text-gray-800">← Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-rose-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Update drawing
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-6 lg:col-span-4">
                <div class="rounded-lg border border-gray-200/80 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-4"><h3 class="text-sm font-semibold text-gray-900">Journal preview</h3></div>
                    <div class="space-y-2 p-5">
                        <div class="flex items-start justify-between rounded-lg bg-rose-50 p-3 text-xs">
                            <div><span class="font-semibold text-rose-700">DEBIT</span><p class="mt-0.5 text-gray-600">Equity</p></div>
                            <span class="font-semibold text-rose-800" x-text="'{{ $businessCurrencySymbol }}' + parseFloat(amount || 0).toFixed(2)"></span>
                        </div>
                        <div class="flex items-start justify-between rounded-lg bg-amber-50 p-3 text-xs">
                            <div><span class="font-semibold text-amber-800">CREDIT</span><p class="mt-0.5 text-gray-600">Asset</p></div>
                            <span class="font-semibold text-amber-900" x-text="'{{ $businessCurrencySymbol }}' + parseFloat(amount || 0).toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                @if($ownerDrawing->attachments->count() > 0)
                    <div class="rounded-lg border border-gray-200/80 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4"><h3 class="text-sm font-semibold text-gray-900">Existing attachments</h3></div>
                        <ul class="divide-y divide-gray-100 p-2">
                            @foreach($ownerDrawing->attachments as $attachment)
                                <li class="flex items-center justify-between rounded-lg p-2.5 hover:bg-gray-50">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <svg class="h-4 w-4 flex-shrink-0 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                        <span class="truncate text-xs text-gray-700">{{ $attachment->original_name }}</span>
                                    </div>
                                    <div class="flex flex-shrink-0 items-center gap-2">
                                        <a href="{{ route('files.owner-drawing-attachments.download', $attachment) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Download</a>
                                        <button type="button"
                                            onclick="deleteOwnerDrawingAttachment('{{ route('owner-drawings.attachments.delete', $attachment) }}', this)"
                                            class="text-xs text-red-500 hover:text-red-700">Remove</button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="rounded-lg border border-gray-200/80 bg-white shadow-sm">
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
                                    <input name="attachments[]" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                                        class="block w-full text-xs text-gray-500 file:mr-3 file:rounded-md file:border-0 file:bg-rose-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-rose-700 hover:file:bg-rose-100" />
                                    <button type="button" @click="files.splice(i, 1)" x-show="files.length > 1" class="flex-shrink-0 rounded-md bg-red-50 p-1.5 text-red-600 hover:bg-red-100">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="files.push(files.length)" class="mt-3 flex items-center gap-1.5 text-xs font-medium text-rose-600 hover:text-rose-800">+ Add file</button>
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

        function deleteOwnerDrawingAttachment(url, btn) {
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
