<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/modules', 'label' => 'Modules'], ['url' => '#', 'label' => 'Edit Module']]" />

    <x-dynamic-heading title="Edit Module" />

    <form action="{{ route('modules.update', $module->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
            <div class="pb-10 mb-10 border-b border-gray-150 my-8">
                <h2 class="text-lg font-semibold text-gray-900">Module Information</h2>
                <p class="mt-1 text-sm text-gray-600">Please update the details of the module.</p>

                <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="name">Module Name <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="name" value="{{ old('name', $module->name) }}" required />
                    </div>

                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="code">Module Code <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="code" value="{{ old('code', $module->code) }}" required />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('modules.index') }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 ml-2">Update</button>
            </div>
        </div>
    </form>
</x-app-layout>
