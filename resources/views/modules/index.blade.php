<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '#', 'label' => 'Modules']]" />

    <x-dynamic-heading title="Modules" />

    <!-- Filter Form -->
    <div class="space-y-4 pb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <x-search-form action="{{ route('modules.index') }}" placeholder="Search by name, code..." />
            
            <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                <x-button href="{{ route('modules.create') }}">Add Module</x-button>
            </div>
            
        </div>
    </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    <x-table-wrapper>
        <thead class="bg-gray-50">
            <tr>
                <x-table-header>#</x-table-header>
                <x-table-header>Module Name</x-table-header>
                <x-table-header>Code</x-table-header>
                <x-table-header>Actions</x-table-header>
            </tr>
        </thead>
        <tbody>
            @foreach ($modules as $module)
                <tr>
                    <x-table-cell>{{ $loop->iteration }}</x-table-cell>
                    <x-table-cell>{{ $module->name }}</x-table-cell>
                    <x-table-cell>{{ $module->code }}</x-table-cell>
                    <x-table-cell>
                        
                        <a href="{{ route('modules.edit', $module->id) }}" class="text-blue-600 hover:underline">Edit</a>
                        
                        
                        <form action="{{ route('modules.destroy', $module->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                        
                    </x-table-cell>
                </tr>
            @endforeach
        </tbody>
    </x-table-wrapper>

    <div class="mt-4">
        {{ $modules->links() }}
    </div>
</x-app-layout>
