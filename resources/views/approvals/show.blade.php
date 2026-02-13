<x-app-layout>
    @section('title', 'Approval #' . $approval->id . ' - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => route('approvals.index'), 'label' => 'Approvals'],
        ['url' => '#', 'label' => 'Approval #' . $approval->id]
    ]" />

    <x-dynamic-heading 
        :title="'Approval ' . $approval->approval_number" 
        :subtitle="'Approval Details'"
        :icon="'fas fa-file-invoice'"
    />

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if (Session::has('error'))
        <x-error-alert message="{{ Session::get('error') }}" />
    @endif

    <!-- Actions Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1.5 rounded-full text-sm font-semibold 
                {{ $approval->status === 'open' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($approval->status) }}
            </span>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('approvals.edit', $approval) }}" 
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @if($approval->status === 'open')
                <a href="{{ route('approvals.process', $approval) }}" 
                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Process
                </a>
            @endif
            <a href="{{ route('approvals.index') }}" 
                class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="space-y-4">
        <!-- Approval Details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Approval Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Party</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $approval->party->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Approval Date</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $approval->approval_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Created By</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $approval->createdBy->name ?? 'N/A' }}</p>
                    </div>
                    @if($approval->notes)
                    <div class="md:col-span-3">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Notes</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $approval->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- General Items -->
        @if($approval->generalItems->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">General Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Sale Price</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Line Total</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-blue-600 uppercase tracking-wider">Returned</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-green-600 uppercase tracking-wider">Sold</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-yellow-600 uppercase tracking-wider">Remaining</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($approval->generalItems as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item->generalItem->item_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 text-right">{{ number_format(round($item->quantity), 0) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 text-right">{{ number_format($item->sale_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">{{ number_format($item->line_total, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-blue-600 text-right">{{ number_format(round($item->returned_quantity), 0) }}</td>
                                <td class="px-4 py-3 text-sm text-green-600 text-right">{{ number_format(round($item->sold_quantity), 0) }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-yellow-600 text-right">{{ number_format(round($item->remaining_quantity), 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Arms -->
        @if($approval->arms->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Arms</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Serial No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Arm Title</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Sale Price</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Returned Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Sold Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($approval->arms as $approvalArm)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $approvalArm->arm->serial_no }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $approvalArm->arm->arm_title }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">{{ number_format($approvalArm->sale_price, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $approvalArm->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($approvalArm->status === 'sold' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($approvalArm->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $approvalArm->returned_date ? $approvalArm->returned_date->format('d M Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $approvalArm->sold_date ? $approvalArm->sold_date->format('d M Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Summary -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Summary</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center md:text-left">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Approved</label>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($approval->total_approved_value ?? 0, 2) }}</p>
                    </div>
                    <div class="text-center md:text-left">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Sold</label>
                        <p class="mt-2 text-2xl font-bold text-green-600">{{ number_format($approval->total_sold_value ?? 0, 2) }}</p>
                    </div>
                    <div class="text-center md:text-left">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Returned</label>
                        <p class="mt-2 text-2xl font-bold text-blue-600">{{ number_format($approval->total_returned_value ?? 0, 2) }}</p>
                    </div>
                    <div class="text-center md:text-left md:border-l md:border-gray-300 md:pl-6">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Remaining</label>
                        <p class="mt-2 text-2xl font-bold text-yellow-600">{{ number_format($approval->remaining_value ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
