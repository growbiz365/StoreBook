<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],     ['url' => '/settings', 'label' => 'Settings'], ['url' => '#', 'label' => 'Cities']]" />

    <x-dynamic-heading title="Cities" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('cities.index') }}" placeholder="Search by city or country name..." />
            @can('create cities')
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                <x-button href="{{ route('cities.create') }}">Add City</x-button>
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
                <x-table-header>City Name</x-table-header>
                <x-table-header>Country</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @foreach ($cities as $city)
                <tr>
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>{{ $city->name }}</x-table-cell>
                    <x-table-cell>{{ $city->country->country_name ?? 'N/A' }}</x-table-cell>
                    <x-table-cell>
                        @can('edit cities')
                        <a href="{{ route('cities.edit', $city->id) }}" class="text-blue-600 hover:underline">Edit</a> |
                        @endcan
                        @can('delete cities')
                        <form action="{{ route('cities.destroy', $city->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this city?')">Delete</button>
                        </form>
                        @endcan
                    </x-table-cell>
                </tr>
            @endforeach
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $cities->links() }}
    </div>
</x-app-layout>
