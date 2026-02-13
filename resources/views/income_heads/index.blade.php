<x-app-layout>
    @section('title', 'Income Heads List - Income Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/other-incomes', 'label' => 'Other Incomes'], ['url' => '#', 'label' => 'Income Heads']]" />

    <x-dynamic-heading title="Income Heads" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('income-heads.index') }}" placeholder="Search by income head name..." />
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                @can('create income heads')
                <x-button href="{{ route('income-heads.create') }}">Add Income Head</x-button>
                @endcan
            </div>
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
                <x-table-header>Income Head</x-table-header>
                <x-table-header>Chart Account</x-table-header>
                <x-table-header>Created At</x-table-header>
                
            </tr>
        </thead>
        <tbody>
            @forelse($incomeHeads as $incomeHead)
                <tr
                    onclick="window.location.href='{{ route('income-heads.edit', $incomeHead) }}'"
                    class="cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out"
                >
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell class="font-medium text-gray-900">{{ $incomeHead->name }}</x-table-cell>
                    <x-table-cell>
                        @if($incomeHead->chartOfAccount)
                            {{ $incomeHead->chartOfAccount->code }} - {{ $incomeHead->chartOfAccount->name }}
                        @else
                            <span class="text-gray-500">Not linked</span>
                        @endif
                    </x-table-cell>
                    <x-table-cell>{{ $incomeHead->created_at->format('d-m-Y') }}</x-table-cell>
                    
                </tr>
            @empty
                <tr>
                    <x-table-cell colspan="4" class="text-center text-gray-500 py-6">
                        No income heads found.
                    </x-table-cell>
                </tr>
            @endforelse
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $incomeHeads->links() }}
    </div>
</x-app-layout>
