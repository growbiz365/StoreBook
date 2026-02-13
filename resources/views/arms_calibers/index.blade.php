<x-app-layout>
    @section('title', 'Arms Calibers - Arms Management')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/arms-dashboard', 'label' => 'Arms Management'],['url' => '#', 'label' => 'Arms Calibers']]" />

    <x-dynamic-heading title="Arms Calibers" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div class="flex gap-4">
            <x-search-form action="{{ route('arms-calibers.index') }}" placeholder="Search by arm caliber..." />
                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                    onchange="window.location.href='{{ route('arms-calibers.index') }}?status='+this.value+'&search={{ request('search') }}'">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            @can('create arm_calibers')
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                <x-button href="{{ route('arms-calibers.create') }}">Add Arms Caliber</x-button>
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
                <x-table-header>Arm Caliber</x-table-header>
                <x-table-header>Status</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @foreach ($armsCalibers as $armsCaliber)
                <tr>
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>{{ $armsCaliber->arm_caliber }}</x-table-cell>
                    <x-table-cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $armsCaliber->status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $armsCaliber->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </x-table-cell>

                    <x-table-cell>
                    @can('edit arm_calibers')
                        <a href="{{ route('arms-calibers.edit', $armsCaliber->id) }}" class="text-blue-600 hover:underline">Edit</a> 
                       @endcan

                       
                    </x-table-cell>
                </tr>
            @endforeach
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $armsCalibers->links() }}
    </div>
</x-app-layout> 