<x-app-layout>
    @section('title', 'Approvals - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        
        ['url' => '#', 'label' => 'Approvals']
    ]" />

    <div class="bg-gradient-to-r from-blue-50 via-white to-white rounded-xl shadow-sm border border-blue-100 p-6 mb-6 mt-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Approvals</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage items and arms on approval</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('approvals.report') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    View Report
                </a>
                <a href="{{ route('approvals.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Approval
                </a>
            </div>
        </div>
    </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif
    
    @if (request('success'))
        <x-success-alert message="{{ request('success') }}" />
    @endif

    @if (Session::has('error'))
        <x-error-alert message="{{ Session::get('error') }}" />
    @endif
    
    @if (request('error'))
        <x-error-alert message="{{ request('error') }}" />
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-3 mb-4">
        <form method="GET" action="{{ route('approvals.index') }}">
            <div class="flex flex-col lg:flex-row lg:items-end lg:space-x-4 space-y-2 lg:space-y-0">
                <div class="flex-1 min-w-[150px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm"
                        placeholder="Search...">
                </div>
                <div class="min-w-[120px]">
                    <select name="status" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="min-w-[120px]">
                    <select name="party_id" id="party_id" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm chosen-select">
                        <option value="">All Parties</option>
                        @foreach($parties ?? [] as $party)
                            <option value="{{ $party->id }}" {{ request('party_id') == $party->id ? 'selected' : '' }}>
                                {{ $party->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[120px]">
                    <input type="date" name="approval_date" value="{{ request('approval_date') }}"
                        class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm"
                        placeholder="Select Date">
                </div>
                <div class="flex items-center space-x-2">
                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md">
                        Filter
                    </button>
                    <a href="{{ route('approvals.index') }}" class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1">Clear</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Approvals List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approval #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Party</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Arms</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($approvals as $approval)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $approval->approval_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $approval->party->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @businessDate($approval->approval_date)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $approval->status === 'open' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($approval->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $approval->generalItems->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $approval->arms->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('approvals.show', $approval) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('approvals.edit', $approval) }}" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                @if($approval->status === 'open')
                                    <a href="{{ route('approvals.process', $approval) }}" class="text-purple-600 hover:text-purple-900">Process</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No approvals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $approvals->links() }}
        </div>
    </div>

    <!-- jQuery Chosen for Party Dropdown -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    <style>
    /* Make Chosen match Tailwind input styles exactly */
    .chosen-container { width: 100% !important; }
    .chosen-container-single .chosen-single {
        height: auto;
        min-height: 31px; /* Match py-1 (0.25rem top + 0.25rem bottom = 8px) + text line height */
        line-height: 1.5; /* Match default line-height */
        border: 1px solid #d1d5db; /* border-gray-300 */
        border-radius: 0.375rem; /* rounded-md */
        padding: 0.25rem 2rem 0.25rem 0.5rem; /* py-1 px-2 equivalent, with space for arrow */
        background: #fff;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); /* shadow-sm */
        font-size: 0.875rem; /* text-sm */
        color: #111827; /* text-gray-900 */
    }
    .chosen-container-single .chosen-single span { 
        margin-right: 0;
        display: block;
    }
    .chosen-container-single .chosen-single div { 
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
    }
    .chosen-container-active .chosen-single,
    .chosen-container .chosen-single:focus {
        border-color: #3b82f6; /* blue-500 */
        box-shadow: 0 0 0 1px #3b82f6 inset, 0 0 0 1px rgba(59, 130, 246, 0.2);
    }
    .chosen-container .chosen-search input {
        height: 31px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem; /* py-1 px-2 */
        font-size: 0.875rem; /* text-sm */
    }
    .chosen-container .chosen-results li.highlighted {
        background-color: #3b82f6;
        background-image: none;
    }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize jQuery Chosen for party dropdown
            $('#party_id').chosen({
                width: '100%',
                search_contains: true,
                allow_single_deselect: true,
                placeholder_text_single: 'All Parties'
            });
        });
    </script>
</x-app-layout>

