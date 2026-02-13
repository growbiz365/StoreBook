<x-app-layout>
    @section('title', 'Businesses List - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/settings', 'label' => 'Settings'], ['url' => '#', 'label' => 'Businesses']]" />

    <x-dynamic-heading title="Businesses" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('businesses.index') }}" placeholder="Search by name, owner, email, country, or timezone..." />
            @can('module','create businesses')
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                <x-button href="{{ route('businesses.create') }}">Add Business</x-button>
            </div>
            @endcan
        </div>
    </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    <x-table-wrapper>
        <thead class="bg-gray-50">
            <tr>
                <x-table-header>#</x-table-header>
                <x-table-header>Business Name</x-table-header>
                <x-table-header>Owner Name</x-table-header>
                <x-table-header>Country</x-table-header>
                <x-table-header>Timezone</x-table-header>
                <x-table-header>Currency</x-table-header>
                <x-table-header>Package</x-table-header>
                <x-table-header>Status</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @foreach ($businesses as $business)
                <tr class="{{ $business->is_suspended ? 'bg-red-50' : '' }}">
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>{{ $business->business_name }}</x-table-cell>
                    <x-table-cell>{{ $business->owner_name }}</x-table-cell>
                    <x-table-cell>{{ $business->country->country_name ?? '-' }}</x-table-cell>
                    <x-table-cell>{{ $business->timezone->timezone_name ?? '-' }}</x-table-cell>
                    <x-table-cell>{{ $business->currency->currency_name ?? '-' }}</x-table-cell>
                    <x-table-cell>{{ $business->package->package_name ?? '-' }}</x-table-cell>
                    <x-table-cell>
                        @if($business->is_suspended)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Suspended
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Active
                            </span>
                        @endif
                    </x-table-cell>

                    <x-table-cell>
                        @can('module','view businesses')
                        <a href="{{ route('businesses.show', $business->id) }}" class="text-green-600 hover:underline mr-3">View</a>
                        @endcan
                        
                        @if(auth()->user()->hasRole('Super Admin'))
                            @if($business->is_suspended)
                                <form method="POST" action="{{ route('businesses.unsuspend', $business) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:underline mr-3" 
                                            onclick="return confirm('Are you sure you want to unsuspend this business?')">
                                        Unsuspend
                                    </button>
                                </form>
                            @else
                                <button onclick="openSuspendModal({{ $business->id }}, '{{ $business->business_name }}')" 
                                        class="text-red-600 hover:underline mr-3">
                                    Suspend
                                </button>
                            @endif
                        @endif
                    </x-table-cell>
                </tr>
            @endforeach
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $businesses->links() }}
    </div>

    <!-- Suspension Modal -->
    <div id="suspendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white z-50">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="mt-2 px-7 py-3">
                    <h3 class="text-lg font-medium text-gray-900">Suspend Business</h3>
                    <div class="mt-2 px-1 py-3">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to suspend <span id="businessName" class="font-semibold"></span>?
                        </p>
                        <form id="suspendForm" method="POST" class="mt-4">
                            @csrf
                            <div>
                                <label for="suspension_reason" class="block text-sm font-medium text-gray-700">Reason (Optional)</label>
                                <textarea id="suspension_reason" name="suspension_reason" rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                          placeholder="Enter reason for suspension..."></textarea>
                            </div>
                            <div class="mt-4 flex justify-end space-x-3">
                                <button type="button" onclick="closeSuspendModal()" 
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    Suspend Business
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Data Modal -->
    <div id="clearDataModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative mx-auto p-8 border-2 w-full max-w-2xl shadow-2xl rounded-2xl bg-white">
            <div class="flex items-start gap-4 mb-6">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">⚠️ Clear All Business Data</h3>
                    <p class="text-sm text-gray-600">This will permanently delete ALL data for <strong id="clearBusinessName"></strong></p>
                </div>
                <button onclick="closeClearDataModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4 mb-6">
                <p class="text-sm text-red-800 font-medium">
                    This will delete ALL sales, purchases, inventory, parties, banks, expenses, and transaction history. 
                    <strong>This action CANNOT be undone!</strong>
                </p>
            </div>

            <form method="POST" action="" id="clearDataForm">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-900 mb-3">
                        To confirm, type the business name:
                    </label>
                    <div class="bg-gray-100 border-2 border-gray-300 rounded-lg p-3 mb-3">
                        <code class="text-base font-mono font-bold text-gray-900" id="clearBusinessNameDisplay"></code>
                    </div>
                    <input type="text" 
                           name="confirmation_text" 
                           id="clear_confirmation_text"
                           class="block w-full border-2 border-gray-300 rounded-lg px-4 py-3"
                           placeholder="Type business name here..."
                           autocomplete="off"
                           required>
                </div>

                <div class="flex gap-3">
                    <button type="button" 
                            onclick="closeClearDataModal()" 
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            id="confirmClearBtn"
                            disabled
                            class="flex-1 px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Clear All Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentBusinessName = '';

        function openSuspendModal(businessId, businessName) {
            document.getElementById('businessName').textContent = businessName;
            document.getElementById('suspendForm').action = `/businesses/${businessId}/suspend`;
            document.getElementById('suspendModal').classList.remove('hidden');
        }

        function closeSuspendModal() {
            document.getElementById('suspendModal').classList.add('hidden');
            document.getElementById('suspension_reason').value = '';
        }

        function openClearDataModal(businessId, businessName) {
            currentBusinessName = businessName;
            document.getElementById('clearBusinessName').textContent = businessName;
            document.getElementById('clearBusinessNameDisplay').textContent = businessName;
            document.getElementById('clearDataForm').action = `/businesses/${businessId}/clear-all-data`;
            document.getElementById('clearDataModal').classList.remove('hidden');
            document.getElementById('clear_confirmation_text').value = '';
            document.getElementById('confirmClearBtn').disabled = true;
        }

        function closeClearDataModal() {
            document.getElementById('clearDataModal').classList.add('hidden');
            document.getElementById('clear_confirmation_text').value = '';
            document.getElementById('confirmClearBtn').disabled = true;
        }

        // Enable/disable clear button based on confirmation
        document.getElementById('clear_confirmation_text')?.addEventListener('input', function(e) {
            const confirmButton = document.getElementById('confirmClearBtn');
            if (e.target.value === currentBusinessName) {
                confirmButton.disabled = false;
            } else {
                confirmButton.disabled = true;
            }
        });

        // Final confirmation before clearing
        document.getElementById('clearDataForm')?.addEventListener('submit', function(e) {
            const confirmText = document.getElementById('clear_confirmation_text').value;
            if (confirmText !== currentBusinessName) {
                e.preventDefault();
                alert('Business name confirmation does not match!');
                return false;
            }
            
            if (!confirm('⚠️ FINAL WARNING: This will permanently delete ALL data. Are you absolutely sure?')) {
                e.preventDefault();
                return false;
            }
        });

        // Close modals when clicking outside
        document.getElementById('suspendModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuspendModal();
            }
        });

        document.getElementById('clearDataModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeClearDataModal();
            }
        });
    </script>
</x-app-layout> 