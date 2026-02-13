<x-app-layout>
    @section('title', 'Business Details - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => route('businesses.index'), 'label' => 'Businesses'],
        ['url' => '#', 'label' => 'View Business'],
    ]" />

    @if(Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="py-6">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                        <div class="flex items-center gap-6">
                            <div class="flex-shrink-0">
                                <div class="h-20 w-20 rounded-xl bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-700 text-2xl font-bold">{{ Str::substr($business->business_name, 0, 2) }}</span>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $business->business_name }}</h1>
                                <div class="mt-2 flex flex-wrap items-center gap-3">
                                    <span class="text-sm text-gray-500">Owner: {{ $business->owner_name ?? 'N/A' }}</span>
                                    <span class="text-sm text-gray-500">CNIC: {{ $business->cnic ?? 'N/A' }}</span>
                                    
                                    @if($business->is_suspended)
                                    <span class="inline-flex items-center p-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Suspended
                                    </span>
                                    @else   
                                    <span class="inline-flex items-center p-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM3.707 9.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Active
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            @can('edit businesses')
                            <a href="{{ route('businesses.edit', $business) }}"
                                class="flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Business
                            </a>
                            @endcan
                            <a href="{{ route('businesses.index') }}"
                                class="flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    @if(isset($isAdmin) && $isAdmin)
                    <!-- Basic Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                            <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Business Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->business_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Owner Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->owner_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">CNIC</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->cnic ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date Format</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->date_format ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Timezone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->timezone->timezone_name ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    @endif

                    <!-- Store Information (always visible) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                            <h2 class="text-lg font-medium text-gray-900">Store Information</h2>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Store Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->store_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Store License Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->store_license_number ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">License Expiry Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->license_expiry_date ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Issuing Authority</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->issuing_authority ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Store Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->store_type ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">NTN</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->ntn ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">STRN</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->strn ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Store Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->store_phone ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Store Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->store_email ?? '-' }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Store Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->store_address ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Store City</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ optional($business->storeCity)->name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Store Country</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ optional($business->storeCountry)->country_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Store Postal Code</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->store_postal_code ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                @if(isset($isAdmin) && $isAdmin)
                <div class="space-y-6 lg:col-span-3">
                    <!-- Contact Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                            <h2 class="text-lg font-medium text-gray-900">Contact Information</h2>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->email ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Contact No</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->contact_no ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Country</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->country->country_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $business->address ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Package Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                            <h2 class="text-lg font-medium text-gray-900">Package & Settings</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Package</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ optional($business->package)->package_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Currency</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $business->currency->currency_name ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            @php
                $systemSeededBusinessEmails = ['ahmed@techsolutions.com', 'fatima@globaltrading.com'];
                $isSystemSeededBusiness = in_array($business->email, $systemSeededBusinessEmails);
            @endphp

            @if(auth()->user()->hasRole('Super Admin'))
            <!-- Danger Zone - Last Section -->
            <div class="mt-6 space-y-4">
                

                @if (!$isSystemSeededBusiness)
                <!-- Delete Business Section -->
                <div class="bg-red-50 border-2 border-red-200 rounded-xl shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-lg bg-red-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-red-900 mb-2">Delete Business</h3>
                            <p class="text-sm text-red-700 mb-4">
                                Deleting the business will permanently delete the business record and ALL associated data including transactions, inventory, customers, banks, and everything else. 
                                <strong>This action is IRREVERSIBLE and cannot be undone!</strong>
                            </p>
                            <button type="button"
                                    onclick="openDeleteBusinessModal()"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Business
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Clear All Data Confirmation Modal (same style as index) -->
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
                    <p class="text-sm text-gray-600">This will permanently delete ALL data for <strong>{{ $business->business_name }}</strong>.</p>
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

            <form method="POST" action="{{ route('businesses.clear-all-data', $business) }}" id="clearDataForm">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-900 mb-3">
                        To confirm, type the business name:
                    </label>
                    <div class="bg-gray-100 border-2 border-gray-300 rounded-lg p-3 mb-3">
                        <code class="text-base font-mono font-bold text-gray-900">{{ $business->business_name }}</code>
                    </div>
                    <input type="text" 
                           name="confirmation_text" 
                           id="confirmation_text"
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
                            id="confirmClearButton"
                            disabled
                            class="flex-1 px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Clear All Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Business Confirmation Modal -->
    <div id="deleteBusinessModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative mx-auto p-8 border-2 w-full max-w-2xl shadow-2xl rounded-2xl bg-white">
            <div class="flex items-start gap-4 mb-6">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">🗑️ Delete Business</h3>
                    <p class="text-sm text-gray-600">This will permanently delete <strong>{{ $business->business_name }}</strong> and ALL its data.</p>
                </div>
                <button onclick="closeDeleteBusinessModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4 mb-6">
                <p class="text-sm text-red-800 font-medium">
                    <strong>⚠️ WARNING: This action is IRREVERSIBLE!</strong><br>
                    This will permanently delete the business record and ALL associated data including:
                    sales, purchases, inventory, parties, banks, expenses, transactions, and master data.
                    <strong>This cannot be undone!</strong>
                </p>
            </div>

            <form method="POST" action="{{ route('businesses.destroy', $business) }}" id="deleteBusinessForm">
                @csrf
                @method('DELETE')
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-900 mb-3">
                        To confirm deletion, type the business name:
                    </label>
                    <div class="bg-gray-100 border-2 border-gray-300 rounded-lg p-3 mb-3">
                        <code class="text-base font-mono font-bold text-gray-900">{{ $business->business_name }}</code>
                    </div>
                    <input type="text" 
                           name="confirmation_text" 
                           id="delete_business_confirmation_text"
                           class="block w-full border-2 border-gray-300 rounded-lg px-4 py-3"
                           placeholder="Type business name here..."
                           autocomplete="off"
                           required>
                </div>

                <div class="flex gap-3">
                    <button type="button" 
                            onclick="closeDeleteBusinessModal()" 
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            id="confirmDeleteBusinessButton"
                            disabled
                            class="flex-1 px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Delete Business
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const businessName = "{{ addslashes($business->business_name) }}";

        function openClearDataModal() {
            document.getElementById('clearDataModal').classList.remove('hidden');
            document.getElementById('confirmation_text').value = '';
            document.getElementById('confirmClearButton').disabled = true;
        }

        function closeClearDataModal() {
            document.getElementById('clearDataModal').classList.add('hidden');
            document.getElementById('confirmation_text').value = '';
            document.getElementById('confirmClearButton').disabled = true;
        }

        function openDeleteBusinessModal() {
            document.getElementById('deleteBusinessModal').classList.remove('hidden');
            document.getElementById('delete_business_confirmation_text').value = '';
            document.getElementById('confirmDeleteBusinessButton').disabled = true;
        }

        function closeDeleteBusinessModal() {
            document.getElementById('deleteBusinessModal').classList.add('hidden');
            document.getElementById('delete_business_confirmation_text').value = '';
            document.getElementById('confirmDeleteBusinessButton').disabled = true;
        }

        document.getElementById('confirmation_text')?.addEventListener('input', function(e) {
            const confirmButton = document.getElementById('confirmClearButton');
            confirmButton.disabled = e.target.value !== businessName;
        });

        document.getElementById('delete_business_confirmation_text')?.addEventListener('input', function(e) {
            const confirmButton = document.getElementById('confirmDeleteBusinessButton');
            confirmButton.disabled = e.target.value !== businessName;
        });

        document.getElementById('clearDataModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeClearDataModal();
            }
        });

        document.getElementById('deleteBusinessModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteBusinessModal();
            }
        });

        document.getElementById('clearDataForm')?.addEventListener('submit', function(e) {
            const confirmText = document.getElementById('confirmation_text').value;
            if (confirmText !== businessName) {
                e.preventDefault();
                alert('Business name confirmation does not match!');
                return false;
            }

            if (!confirm('⚠️ FINAL WARNING: This will permanently delete ALL data for this business. Are you absolutely sure?')) {
                e.preventDefault();
                return false;
            }
        });

        document.getElementById('deleteBusinessForm')?.addEventListener('submit', function(e) {
            const confirmText = document.getElementById('delete_business_confirmation_text').value;
            if (confirmText !== businessName) {
                e.preventDefault();
                alert('Business name confirmation does not match!');
                return false;
            }

            if (!confirm('⚠️ FINAL WARNING: This will permanently delete the business and ALL its data. This cannot be undone! Are you absolutely sure?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</x-app-layout> 