<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],     ['url' => '/settings', 'label' => 'Settings'], ['url' => '#', 'label' => 'Currencies']]" />

    <x-dynamic-heading title="Currencies" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('currencies.index') }}" placeholder="Search by currency name or code..." />
            @can('create currencies')
                <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                    <x-button href="{{ route('currencies.create') }}">Add Currency</x-button>
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
                <x-table-header>Currency Code</x-table-header>
                <x-table-header>Currency Name</x-table-header>
                <x-table-header>Symbol</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @foreach ($currencies as $currency)
                <tr>
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>{{ $currency->currency_code }}</x-table-cell>
                    <x-table-cell>{{ $currency->currency_name }}</x-table-cell>
                    <x-table-cell>{{ $currency->symbol ?? 'N/A' }}</x-table-cell>
                    <x-table-cell>
                        @can('edit currencies')
                            <a href="{{ route('currencies.edit', $currency->id) }}"
                                class="text-blue-600 hover:underline">Edit</a>
                        @endcan
                        @can('delete currencies')
                            <form action="{{ route('currencies.destroy', $currency->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        @endcan
                    </x-table-cell>
                </tr>
            @endforeach
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $currencies->links() }}
    </div>
</x-app-layout>
