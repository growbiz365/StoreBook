<x-app-layout>
    @section('title', 'Purchase #' . $purchase->id . ' - Audit Log - Purchases Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'], 
        ['url' => '/purchases-dashboard', 'label' => 'Purchases Dashboard'],
        ['url' => route('purchases.index'), 'label' => 'Purchases'], 
        ['url' => route('purchases.show', $purchase), 'label' => 'Purchase #' . $purchase->id], 
        ['url' => '#', 'label' => 'Audit Log']
    ]" />

    <x-dynamic-heading title="Purchase #{{ $purchase->id }} - Audit Log" />

    <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-6">
        <!-- Purchase Summary -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Purchase Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700">Status:</span>
                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full 
                        @if($purchase->status === 'draft') bg-gray-100 text-gray-800
                        @elseif($purchase->status === 'posted') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($purchase->status) }}
                    </span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Total Amount:</span>
                    <span class="ml-2">${{ number_format($purchase->total_amount, 2) }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Invoice Date:</span>
                    <span class="ml-2">{{ $purchase->invoice_date->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Audit Log Entries -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Audit Trail</h3>
            
            @if($auditLogs->count() > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($auditLogs as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white 
                                                @if($log->action === 'edit_started') bg-yellow-500
                                                @elseif($log->action === 'line_added') bg-green-500
                                                @elseif($log->action === 'line_removed') bg-red-500
                                                @elseif($log->action === 'line_modified') bg-blue-500
                                                @else bg-gray-500 @endif">
                                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    @if($log->action === 'edit_started')
                                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                                    @elseif($log->action === 'line_added')
                                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                    @elseif($log->action === 'line_removed')
                                                        <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                    @elseif($log->action === 'line_modified')
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    @else
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                    @endif
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    <span class="font-medium text-gray-900">{{ $log->user->name ?? 'Unknown User' }}</span>
                                                    {{ $log->description }}
                                                </p>
                                                @if($log->changes)
                                                    <div class="mt-2 text-xs text-gray-600">
                                                        @foreach($log->changes as $key => $change)
                                                            @if(is_array($change) && isset($change['old']) && isset($change['new']))
                                                                <div class="mb-1">
                                                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                                    <span class="text-red-600">{{ $change['old'] }}</span>
                                                                    <span class="mx-1">→</span>
                                                                    <span class="text-green-600">{{ $change['new'] }}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                                    {{ $log->created_at->format('M d, Y H:i') }}
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No audit entries</h3>
                    <p class="mt-1 text-sm text-gray-500">No changes have been made to this purchase yet.</p>
                </div>
            @endif
        </div>

        <!-- Back Button -->
        <div class="mt-8 flex justify-end">
            <a href="{{ route('purchases.show', $purchase) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Purchase Details
            </a>
        </div>
    </div>
</x-app-layout>
