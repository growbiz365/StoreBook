<x-app-layout>
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '/timezones', 'label' => 'Timezones'],
        ['url' => '#', 'label' => 'Add Timezone'],
    ]" />

    <x-dynamic-heading title="Add Timezone" />

    <form action="{{ route('timezones.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
            <div class="pb-10 mb-10 border-b border-gray-150 my-8">
                <h2 class="text-lg font-semibold text-gray-900">Timezone Information</h2>
                <p class="mt-1 text-sm text-gray-600">Please provide details of the timezone.</p>

                <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="timezone_name">Timezone Name <span
                                class="text-red-500">*</span></x-input-label>
                        <x-text-input name="timezone_name" value="{{ old('timezone_name') }}" required />
                    </div>

                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="utc_offset">UTC Offset <span class="text-red-500">*</span></x-input-label>
                        <x-text-input name="utc_offset" value="{{ old('utc_offset') }}" required />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('timezones.index') }}"
                    class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 ml-2">Save</button>
            </div>
        </div>
    </form>
</x-app-layout>
