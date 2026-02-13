<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],     ['url' => '/settings', 'label' => 'Settings'], ['url' => '#', 'label' => 'Packages']]" />

    <x-dynamic-heading title="Packages" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('packages.index') }}" placeholder="Search by package name..." />
            @can('create packages')
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                <x-button href="{{ route('packages.create') }}">Add Package</x-button>
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
                <x-table-header>Package Name</x-table-header>
                <x-table-header>Price</x-table-header>
                <x-table-header>Currency</x-table-header>
                <x-table-header>Duration (Months)</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @foreach ($packages as $package)
                <tr>
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>{{ $package->package_name }}</x-table-cell>
                    <x-table-cell>{{ $package->price }}</x-table-cell>
                    <x-table-cell>{{ $package->currency->currency_name ?? 'N/A' }}</x-table-cell>
                    <x-table-cell>{{ $package->duration_months }}</x-table-cell>
                    <x-table-cell>
                         @can('edit packages')
                        <a href="{{ route('packages.edit', $package->id) }}" class="text-blue-600 hover:underline">Edit</a> |
                        @endcan
                        @can('delete packages')
                        <form action="{{ route('packages.destroy', $package->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form> |
                        @endcan
                        @can('assign modules')
                        <a href="{{ route('packages.modules', $package->id) }}" class="text-green-600 hover:underline">Modules</a>
                        @endcan
                    </x-table-cell>
                </tr>
            @endforeach
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $packages->links() }}
    </div>
</x-app-layout>
