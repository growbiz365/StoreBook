<x-app-layout>
    @section('title', 'Parties List - Party Management - Arms Portal')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/party-management', 'label' => 'Party Management'],['url' => '/parties', 'label' => 'Parties']]" />

    <x-dynamic-heading title="Parties" />

    <div class="flex flex-nowrap items-center justify-between gap-3 pb-8">
        <form action="{{ route('parties.index') }}" method="GET" class="flex flex-nowrap items-center gap-2 min-w-0 flex-1">
            <div class="relative shrink-0 w-52">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search name, phone, NTN..."
                    class="w-full rounded-md border-gray-300 shadow-sm pl-9 pr-2 py-1.5 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                >
                <svg class="absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
                </svg>
            </div>
            <input
                type="text"
                name="pcode"
                value="{{ request('pcode') }}"
                placeholder="PCode"
                class="shrink-0 w-28 rounded-md border-gray-300 shadow-sm px-2 py-1.5 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
            >
            <select name="status" class="shrink-0 w-28 rounded-md border-gray-300 shadow-sm px-2 py-1.5 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="shrink-0 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md whitespace-nowrap">
                Filter
            </button>
            @if(request()->hasAny(['search', 'pcode', 'status']))
                <a href="{{ route('parties.index') }}" class="shrink-0 text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">Clear</a>
            @endif
        </form>

        <div class="shrink-0">
            <x-button href="{{ route('parties.create') }}">Add Party</x-button>
        </div>
    </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if (Session::has('error'))
        <x-error-alert message="{{ Session::get('error') }}" />
    @endif

    <x-table-wrapper>
        <thead class="bg-gray-50">
            <tr>
                <x-table-header>#</x-table-header>
                <x-table-header>Name</x-table-header>
                <x-table-header>Opening Balance</x-table-header>
                <x-table-header>Opening Type</x-table-header>
                <x-table-header>Status</x-table-header>
            </tr>
        </thead>
        <tbody>
            @forelse($parties as $party)
                <tr
                    onclick="window.location.href='{{ route('parties.edit', $party) }}'"
                    class="cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out"
                    title="Click to edit party"
                >
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>
                        <div>
                            <div class="font-medium text-gray-900">{{ $party->name }}</div>
                            <div class="text-sm text-gray-500">Phone: {{ $party->phone_no ?? '-' }}</div>
                            <div class="text-sm text-gray-500">PCode: {{ $party->pcode ?? '-' }}</div>
                        </div>
                    </x-table-cell>
                    <x-table-cell>{{ number_format($party->opening_balance, 2) }}</x-table-cell>
                    <x-table-cell>{{ ucfirst($party->opening_type ?? '-') }}</x-table-cell>
                    <x-table-cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $party->status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $party->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </x-table-cell>
                </tr>
            @empty
                <tr>
                    <x-table-cell colspan="6" class="text-center text-gray-500">No parties found</x-table-cell>
                </tr>
            @endforelse
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $parties->links() }}
    </div>
</x-app-layout> 