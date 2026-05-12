<x-app-layout>
    @section('title', 'Owner Drawing #{{ $ownerDrawing->id }} | ' . config('app.name'))
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Dashboard'],
        ['url' => route('settings'), 'label' => 'Settings'],
        ['url' => route('owner-drawings.index'), 'label' => 'Owner Drawings'],
        ['url' => '#', 'label' => 'Drawing #' . $ownerDrawing->id],
    ]" />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Owner drawing <span class="text-gray-400">#{{ $ownerDrawing->id }}</span></h1>
            <p class="mt-1 text-sm text-gray-500">Recorded on {{ $ownerDrawing->created_at->format('d M Y, h:i A') }}
                by {{ $ownerDrawing->createdBy->name }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @can('edit owner drawings')
            <a href="{{ route('owner-drawings.edit', $ownerDrawing) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Edit
            </a>
            @endcan
            @can('delete owner drawings')
            <form action="{{ route('owner-drawings.destroy', $ownerDrawing) }}" method="POST"
                onsubmit="return confirm('Delete this drawing? Journal entries and bank ledger will be reversed.')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Delete
                </button>
            </form>
            @endcan
        </div>
    </div>

    <x-message />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">

            <div class="overflow-hidden rounded-xl border border-gray-200/90 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gray-50/60 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Transaction details</h2>
                </div>
                <dl class="divide-y divide-gray-100">
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Via</dt>
                        <dd class="col-span-2 text-sm text-gray-900">
                            @if($ownerDrawing->drawing_via === 'bank' && $ownerDrawing->bank)
                                <span class="font-medium text-blue-700">Bank</span>
                                <span class="text-gray-700"> — {{ $ownerDrawing->bank->account_name }}</span>
                            @elseif($ownerDrawing->drawing_via === 'cash' && $ownerDrawing->bank)
                                <span class="font-medium text-green-700">Cash</span>
                                <span class="text-gray-700"> — {{ $ownerDrawing->bank->account_name }}</span>
                            @else
                                <span class="font-medium text-green-700">Cash</span>
                                @if($ownerDrawing->fromAccount)
                                    <span class="text-gray-700"> — {{ $ownerDrawing->fromAccount->name }} (chart only)</span>
                                @endif
                            @endif
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="col-span-2 text-sm text-gray-900">{{ $ownerDrawing->drawing_date->format('d M Y') }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="col-span-2 text-xl font-bold text-rose-600">{{ $businessCurrencySymbol }}{{ number_format($ownerDrawing->amount, 2) }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Withdraw (chart)</dt>
                        <dd class="col-span-2 text-sm font-medium text-gray-900">
                            {{ $ownerDrawing->fromAccount->name }}
                            @if($ownerDrawing->fromAccount->code)
                                <span class="ml-1 text-xs text-gray-400">({{ $ownerDrawing->fromAccount->code }})</span>
                            @endif
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Equity (to)</dt>
                        <dd class="col-span-2 text-sm font-medium text-gray-900">
                            {{ $ownerDrawing->toAccount->name }}
                            @if($ownerDrawing->toAccount->code)
                                <span class="ml-1 text-xs text-gray-400">({{ $ownerDrawing->toAccount->code }})</span>
                            @endif
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Paid via</dt>
                        <dd class="col-span-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($ownerDrawing->paid_via === 'cash') bg-green-100 text-green-700
                                @elseif($ownerDrawing->paid_via === 'bank_transfer') bg-blue-100 text-blue-700
                                @elseif($ownerDrawing->paid_via === 'cheque') bg-amber-100 text-amber-700
                                @else bg-purple-100 text-purple-700
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $ownerDrawing->paid_via)) }}
                            </span>
                        </dd>
                    </div>
                    @if($ownerDrawing->reference_number)
                        <div class="grid grid-cols-3 gap-4 px-6 py-4">
                            <dt class="text-sm font-medium text-gray-500">Reference #</dt>
                            <dd class="col-span-2 text-sm text-gray-900">{{ $ownerDrawing->reference_number }}</dd>
                        </div>
                    @endif
                    @if($ownerDrawing->description)
                        <div class="grid grid-cols-3 gap-4 px-6 py-4">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="col-span-2 text-sm leading-relaxed text-gray-700">{{ $ownerDrawing->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200/90 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gray-50/60 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Journal entry</h2>
                </div>
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/40">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Account</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Debit</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="px-6 py-3 text-sm text-gray-900">{{ $ownerDrawing->toAccount->name }} <span class="text-xs text-gray-400">(Equity)</span></td>
                            <td class="px-6 py-3 text-right text-sm font-medium text-rose-700">{{ $businessCurrencySymbol }}{{ number_format($ownerDrawing->amount, 2) }}</td>
                            <td class="px-6 py-3 text-right text-sm text-gray-400">—</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 text-sm text-gray-900">{{ $ownerDrawing->fromAccount->name }} <span class="text-xs text-gray-400">(Asset)</span></td>
                            <td class="px-6 py-3 text-right text-sm text-gray-400">—</td>
                            <td class="px-6 py-3 text-right text-sm font-medium text-amber-700">{{ $businessCurrencySymbol }}{{ number_format($ownerDrawing->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($ownerDrawing->attachments->count() > 0)
                <div class="overflow-hidden rounded-xl border border-gray-200/90 bg-white shadow-sm">
                    <div class="border-b border-gray-100 bg-gray-50/60 px-6 py-4">
                        <h2 class="text-sm font-semibold text-gray-700">Attachments ({{ $ownerDrawing->attachments->count() }})</h2>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @foreach($ownerDrawing->attachments as $attachment)
                            <li class="flex items-center justify-between px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $attachment->original_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $attachment->file_size_formatted }} • {{ $attachment->uploadedBy->name }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('files.owner-drawing-attachments.download', $attachment) }}"
                                    class="text-xs font-medium text-indigo-600 transition-colors hover:text-indigo-800">Download</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-5">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-rose-600">Amount</p>
                <p class="text-3xl font-bold text-rose-700">{{ $businessCurrencySymbol }}{{ number_format($ownerDrawing->amount, 2) }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $ownerDrawing->drawing_date->format('d M Y') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200/90 bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-sm font-semibold text-gray-700">Quick actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('owner-drawings.index') }}"
                        class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2.5 text-sm text-gray-600 transition-colors hover:bg-gray-50">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        All drawings
                    </a>
                    @can('create owner drawings')
                    <a href="{{ route('owner-drawings.create') }}"
                        class="flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2.5 text-sm text-rose-700 transition-colors hover:bg-rose-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        New drawing
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
