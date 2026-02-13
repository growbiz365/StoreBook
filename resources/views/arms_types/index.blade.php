<x-app-layout>
    @section('title', 'Arms Types - Arms Management')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/arms-dashboard', 'label' => 'Arms Management'], ['url' => '#', 'label' => 'Arms Types']]" />

    <x-dynamic-heading title="Arms Types" />

    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div class="flex gap-4">
                <x-search-form action="{{ route('arms-types.index') }}" placeholder="Search by arm type..." />
                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                    onchange="window.location.href='{{ route('arms-types.index') }}?status='+this.value+'&search={{ request('search') }}'">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            @can('create arm_types')
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                <x-button href="{{ route('arms-types.create') }}">Add Arms Type</x-button>
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
                <x-table-header>Arm Type</x-table-header>
                <x-table-header>Status</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @foreach ($armsTypes as $armsType)
                <tr>
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>{{ $armsType->arm_type }}</x-table-cell>
                    <x-table-cell>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $armsType->status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $armsType->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </x-table-cell>
                    <x-table-cell>
                    @can('edit arm_types')
                        <a href="{{ route('arms-types.edit', $armsType->id) }}" class="text-blue-600 hover:underline">Edit</a> 
                        @endcan
                        
                        
                    </x-table-cell>
                </tr>
            @endforeach
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $armsTypes->links() }}
    </div>
</x-app-layout> 