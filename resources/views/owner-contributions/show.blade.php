<x-app-layout>
    @section('title', 'Owner Contribution #{{ $ownerContribution->id }} | ' . config('app.name'))
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Dashboard'],
        ['url' => route('settings'), 'label' => 'Settings'],
        ['url' => route('owner-contributions.index'), 'label' => 'Owner Contributions'],
        ['url' => '#', 'label' => 'Contribution #' . $ownerContribution->id],
    ]" />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Owner contribution <span class="text-gray-400">#{{ $ownerContribution->id }}</span></h1>
            <p class="mt-1 text-sm text-gray-500">Recorded on {{ $ownerContribution->created_at->format('d M Y, h:i A') }}
                by {{ $ownerContribution->createdBy->name }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @can('edit owner contributions')
            <a href="{{ route('owner-contributions.edit', $ownerContribution) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Edit
            </a>
            @endcan
            @can('delete owner contributions')
            <form action="{{ route('owner-contributions.destroy', $ownerContribution) }}" method="POST"
                onsubmit="return confirm('Delete this contribution? Journal entries and bank ledger will be reversed.')">
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
                            @if($ownerContribution->contribution_via === 'bank' && $ownerContribution->bank)
                                <span class="font-medium text-blue-700">Bank</span>
                                <span class="text-gray-700"> — {{ $ownerContribution->bank->account_name }}</span>
                            @elseif($ownerContribution->contribution_via === 'cash' && $ownerContribution->bank)
                                <span class="font-medium text-green-700">Cash</span>
                                <span class="text-gray-700"> — {{ $ownerContribution->bank->account_name }}</span>
                            @else
                                <span class="font-medium text-green-700">Cash</span>
                                @if($ownerContribution->depositAccount)
                                    <span class="text-gray-700"> — {{ $ownerContribution->depositAccount->name }} (chart only)</span>
                                @endif
                            @endif
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="col-span-2 text-sm text-gray-900">{{ $ownerContribution->contribution_date->format('d M Y') }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="col-span-2 text-xl font-bold text-emerald-600">{{ $businessCurrencySymbol }}{{ number_format($ownerContribution->amount, 2) }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Deposit (chart)</dt>
                        <dd class="col-span-2 text-sm text-gray-900 font-medium">
                            {{ $ownerContribution->depositAccount->name }}
                            @if($ownerContribution->depositAccount->code)
                                <span class="ml-1 text-xs text-gray-400">({{ $ownerContribution->depositAccount->code }})</span>
                            @endif
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Equity (from)</dt>
                        <dd class="col-span-2 text-sm text-gray-900 font-medium">
                            {{ $ownerContribution->fromAccount->name }}
                            @if($ownerContribution->fromAccount->code)
                                <span class="ml-1 text-xs text-gray-400">({{ $ownerContribution->fromAccount->code }})</span>
                            @endif
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 gap-4 px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Received via</dt>
                        <dd class="col-span-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($ownerContribution->received_via === 'cash') bg-green-100 text-green-700
                                @elseif($ownerContribution->received_via === 'bank_transfer') bg-blue-100 text-blue-700
                                @elseif($ownerContribution->received_via === 'cheque') bg-amber-100 text-amber-700
                                @else bg-purple-100 text-purple-700
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $ownerContribution->received_via)) }}
                            </span>
                        </dd>
                    </div>
                    @if($ownerContribution->reference_number)
                        <div class="grid grid-cols-3 gap-4 px-6 py-4">
                            <dt class="text-sm font-medium text-gray-500">Reference #</dt>
                            <dd class="col-span-2 text-sm text-gray-900">{{ $ownerContribution->reference_number }}</dd>
                        </div>
                    @endif
                    @if($ownerContribution->description)
                        <div class="grid grid-cols-3 gap-4 px-6 py-4">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="col-span-2 text-sm text-gray-700 leading-relaxed">{{ $ownerContribution->description }}</dd>
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
                            <td class="px-6 py-3 text-sm text-gray-900">{{ $ownerContribution->depositAccount->name }} <span class="text-xs text-gray-400">(Asset)</span></td>
                            <td class="px-6 py-3 text-right text-sm font-medium text-green-700">{{ $businessCurrencySymbol }}{{ number_format($ownerContribution->amount, 2) }}</td>
                            <td class="px-6 py-3 text-right text-sm text-gray-400">—</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 text-sm text-gray-900">{{ $ownerContribution->fromAccount->name }} <span class="text-xs text-gray-400">(Equity)</span></td>
                            <td class="px-6 py-3 text-right text-sm text-gray-400">—</td>
                            <td class="px-6 py-3 text-right text-sm font-medium text-blue-700">{{ $businessCurrencySymbol }}{{ number_format($ownerContribution->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($ownerContribution->attachments->count() > 0)
                <div class="overflow-hidden rounded-xl border border-gray-200/90 bg-white shadow-sm">
                    <div class="border-b border-gray-100 bg-gray-50/60 px-6 py-4">
                        <h2 class="text-sm font-semibold text-gray-700">Attachments ({{ $ownerContribution->attachments->count() }})</h2>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @foreach($ownerContribution->attachments as $attachment)
                            <li class="flex items-center justify-between px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $attachment->original_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $attachment->file_size_formatted }} • Uploaded by {{ $attachment->uploadedBy->name }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('files.owner-contribution-attachments.download', $attachment) }}"
                                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Download</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600 mb-2">Amount</p>
                <p class="text-3xl font-bold text-emerald-700">{{ $businessCurrencySymbol }}{{ number_format($ownerContribution->amount, 2) }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $ownerContribution->contribution_date->format('d M Y') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200/90 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Quick actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('owner-contributions.index') }}"
                        class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        All contributions
                    </a>
                    @can('create owner contributions')
                    <a href="{{ route('owner-contributions.create') }}"
                        class="flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-sm text-emerald-700 hover:bg-emerald-100 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        New contribution
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
