<x-app-layout>
    @section('title', 'Other Income Details - Income Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => route('other-incomes.index'), 'label' => 'Other Incomes'],
        ['url' => '#', 'label' => 'Income #' . $otherIncome->id]
    ]" />

    <div class="flex justify-between items-center">
        <x-dynamic-heading title="Income Details" />
        
        <div class="flex gap-3">
            @can('edit other incomes')
            <a href="{{ route('other-incomes.edit', $otherIncome) }}" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @endcan
            @can('delete other incomes')
            <form action="{{ route('other-incomes.destroy', $otherIncome) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this income record? This will also reverse the journal entries.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </form>
            @endcan
            @can('view other incomes')
            <a href="{{ route('other-incomes.index') }}" 
                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            @endcan
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Income Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Income Information</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Income Date</label>
                        <p class="mt-1 text-sm text-gray-900">@businessDate($otherIncome->income_date)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Amount</label>
                        <p class="mt-1 text-sm font-semibold text-green-600">{{ number_format(round($otherIncome->amount), 0) }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-600">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $otherIncome->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Account Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Bank Account</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ strtoupper($otherIncome->bank->chartOfAccount->name ?? $otherIncome->bank->account_name) }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Income Account</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $otherIncome->chartOfAccount->name }} ({{ $otherIncome->chartOfAccount->code }})
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
                    @if($otherIncome->attachments->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($otherIncome->attachments as $attachment)
                                <div class="bg-gray-50 rounded-lg p-3 flex flex-col">
                                    <div class="flex items-center mb-1">
                                        @if($attachment->isImage())
                                            <svg class="h-4 w-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                        @endif
                                        <span class="text-sm font-medium text-gray-900 truncate flex-1" title="{{ $attachment->file_name }}">
                                            {{ $attachment->file_name }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        {{ $attachment->file_size_formatted }}
                                    </div>
                                    <div class="flex items-center justify-between mt-auto">
                                        <a href="{{ route('other-incomes.attachments.download', $attachment) }}" 
                                            class="text-sm text-indigo-600 hover:text-indigo-900">
                                            Download
                                        </a>
                                        @if($attachment->isImage())
                                            <button onclick="viewImage('{{ $attachment->file_url }}')" 
                                                class="text-sm text-green-600 hover:text-green-900">
                                                View
                                            </button>
                                        @endif
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
                    @if($otherIncome->journalEntries->count() > 0)
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
                                    @foreach($otherIncome->journalEntries as $entry)
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
                        <p class="text-sm text-gray-500">No journal entries found for this income.</p>
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
                            <p class="mt-1 text-gray-900">{{ $otherIncome->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Created At</label>
                            <p class="mt-1 text-gray-900">@businessDateTime($otherIncome->created_at)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Last Updated</label>
                            <p class="mt-1 text-gray-900">@businessDateTime($otherIncome->updated_at)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="max-w-4xl max-h-full p-4">
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl">&times;</button>
        </div>
    </div>

    <script>
        function viewImage(url) {
            document.getElementById('modalImage').src = url;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
    </script>
</x-app-layout>