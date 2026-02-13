<x-app-layout>
    @section('title', 'Expense Details - Expense Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/expenses/dashboard', 'label' => 'Expense dashboard'],
        ['url' => route('expenses.index'), 'label' => 'Expenses'],
        ['url' => '#', 'label' => 'View Expense']
    ]" />

    <div class="flex justify-between items-center">
        <x-dynamic-heading title="Expense Details" />
        
        <div class="flex gap-3">
            <a href="{{ route('expenses.edit', $expense) }}" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @can('delete expenses')
            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" 
                onsubmit="return confirm('Are you sure you want to delete this expense? This will reverse all ledger entries and cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </form>
            @endcan
            <a href="{{ route('expenses.index') }}" 
                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Expense Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Expense Information</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Date</label>
                        <p class="mt-1 text-sm text-gray-900">@businessDate($expense->date_added)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Amount</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ number_format(round($expense->amount), 0) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Expense Head</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $expense->expenseHead->expense_head }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Bank Account</label>
                        <p class="mt-1 text-sm text-gray-900">{{ strtoupper($expense->bank->chartOfAccount->name ?? $expense->bank->account_name) }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-600">Details</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $expense->details ?: 'No details provided' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Account Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Chart Account Details</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Expense Account</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($expense->expenseHead->chartOfAccount)
                                {{ $expense->expenseHead->chartOfAccount->code }} - {{ $expense->expenseHead->chartOfAccount->name }}
                            @else
                                <span class="text-gray-500">Not linked</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Bank Account</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($expense->bank->chartOfAccount)
                                {{ $expense->bank->chartOfAccount->code }} - {{ $expense->bank->chartOfAccount->name }}
                            @else
                                <span class="text-gray-500">Not linked</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attachments -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2" x-data="{ attachmentsOpen: false }">
            <div class="p-4">
                <button @click="attachmentsOpen = !attachmentsOpen" class="flex items-center justify-between w-full text-left mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Attachments</h3>
                    <svg class="w-4 h-4 text-indigo-600 transition-transform duration-200" :class="{ 'rotate-180': attachmentsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="attachmentsOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2">
                    @if($expense->attachments->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($expense->attachments as $attachment)
                                <div class="bg-gray-50 rounded-lg p-3 flex flex-col">
                                    <div class="flex items-center mb-1">
                                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900 truncate flex-1" title="{{ $attachment->original_name }}">
                                            {{ $attachment->original_name }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        {{ number_format($attachment->file_size / 1024, 2) }} KB
                                    </div>
                                    <div class="flex items-center justify-between mt-auto">
                                        <a href="{{ route('files.expense-attachments.download', $attachment) }}" target="_blank"
                                            class="text-sm text-indigo-600 hover:text-indigo-900">
                                            View
                                        </a>
                                        <button onclick="deleteAttachment({{ $attachment->id }})" class="text-sm text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No attachments found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Journal Entries -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2" x-data="{ journalEntriesOpen: false }">
            <div class="p-4">
                <button @click="journalEntriesOpen = !journalEntriesOpen" class="flex items-center justify-between w-full text-left mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Journal Entries</h3>
                    <svg class="w-4 h-4 text-indigo-600 transition-transform duration-200" :class="{ 'rotate-180': journalEntriesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="journalEntriesOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2">
                    @php
                        $journalEntries = \App\Models\JournalEntry::where('voucher_id', $expense->id)
                            ->where('voucher_type', 'Expense')
                            ->with('account')
                            ->orderBy('debit_amount', 'desc')
                            ->get();
                    @endphp
                    
                    @if($journalEntries->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Account
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Debit
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Credit
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($journalEntries as $entry)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $entry->account->code }} - {{ $entry->account->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if($entry->debit_amount > 0)
                                                        {{ number_format(round($entry->debit_amount), 0) }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if($entry->credit_amount > 0)
                                                        {{ number_format(round($entry->credit_amount), 0) }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @businessDate($entry->date_added)
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No journal entries found for this expense.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Meta Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2" x-data="{ additionalInfoOpen: false }">
            <div class="p-4">
                <button @click="additionalInfoOpen = !additionalInfoOpen" class="flex items-center justify-between w-full text-left mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Additional Information</h3>
                    <svg class="w-4 h-4 text-indigo-600 transition-transform duration-200" :class="{ 'rotate-180': additionalInfoOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="additionalInfoOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Created By</label>
                            <p class="mt-1 text-gray-900">{{ $expense->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Created At</label>
                            <p class="mt-1 text-gray-900">@businessDateTime($expense->created_at)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Last Updated</label>
                            <p class="mt-1 text-gray-900">@businessDateTime($expense->updated_at)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteAttachment(id) {
            if (confirm('Are you sure you want to delete this attachment?')) {
                fetch(`/expenses/attachments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error deleting attachment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting attachment');
                });
            }
        }
    </script>
</x-app-layout> 