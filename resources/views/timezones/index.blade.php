<x-app-layout>
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '#', 'label' => 'Timezones'],
    ]" />

    <x-dynamic-heading title="Timezones" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('timezones.index') }}" placeholder="Search by timezone name or offset..." />
            @can('create timezones')
                <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                    <x-button href="{{ route('timezones.create') }}">Add Timezone</x-button>
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
                <x-table-header>Timezone Name</x-table-header>
                <x-table-header>UTC Offset</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @foreach ($timezones as $timezone)
                <tr>
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>{{ $timezone->timezone_name }}</x-table-cell>
                    <x-table-cell>{{ $timezone->utc_offset }}</x-table-cell>
                    <x-table-cell>
                        @can('edit timezones')
                            <a href="{{ route('timezones.edit', $timezone->id) }}"
                                class="text-blue-600 hover:underline">Edit</a>
                        @endcan
                        @can('delete timezones')
                            <form action="{{ route('timezones.destroy', $timezone->id) }}" method="POST"
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
        {{ $timezones->links() }}
    </div>
</x-app-layout>
