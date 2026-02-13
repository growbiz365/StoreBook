<x-app-layout>
    @section('title', 'Countries List - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],     ['url' => '/settings', 'label' => 'Settings'], ['url' => '#', 'label' => 'Countries']]" />

    <x-dynamic-heading title="Countries" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('countries.index') }}" placeholder="Search by country name or code..." />
            @can('create countries')
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                <x-button href="{{ route('countries.create') }}">Add Country</x-button>
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
                <x-table-header>Country Name</x-table-header>
                <x-table-header>Country Code</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @foreach ($countries as $country)
                <tr>
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>{{ $country->country_name }}</x-table-cell>
                    <x-table-cell>{{ $country->country_code }}</x-table-cell>

                    <x-table-cell>
                        @can('edit countries')
                        <a href="{{ route('countries.edit', $country->id) }}" class="text-blue-600 hover:underline">Edit</a>
                        @endcan
                        @can('delete countries')
                        <form action="{{ route('countries.destroy', $country->id) }}" method="POST" style="display:inline;">
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
        {{ $countries->links() }}
    </div>
</x-app-layout>
