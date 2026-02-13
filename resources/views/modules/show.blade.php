<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/modules', 'label' => 'Modules'], ['url' => '#', 'label' => $module->name]]" />

    <x-dynamic-heading title="Module Details" />

    <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
        <div class="pb-10 mb-10 border-b border-gray-150 my-8">
            <h2 class="text-lg font-semibold text-gray-900">{{ $module->name }}</h2>
            <p class="mt-1 text-sm text-gray-600">Here are the details of the selected module.</p>

            <div class="mt-8">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="name">Module Name</x-input-label>
                        <p>{{ $module->name }}</p>
                    </div>

                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="guard_name">Guard Name</x-input-label>
                        <p>{{ $module->guard_name }}</p>
                    </div>

                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="code">Module Code</x-input-label>
                        <p>{{ $module->code }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('modules.edit', $module->id) }}" class="text-blue-600 hover:underline">Edit Module</a>
        </div>
    </div>
</x-app-layout>
