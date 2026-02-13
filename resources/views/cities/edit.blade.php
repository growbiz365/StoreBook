<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],     ['url' => '/settings', 'label' => 'Settings'], ['url' => '/cities', 'label' => 'Cities'], ['url' => '#', 'label' => 'Edit City']]" />

    <x-dynamic-heading title="Edit City" />

    <form action="{{ route('cities.update', $city) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
            <div class="pb-10 mb-10 border-b border-gray-150 my-8">
                <h2 class="text-lg font-semibold text-gray-900">City Information</h2>
                <p class="mt-1 text-sm text-gray-600">Please update the details of the city.</p>

                <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="name">City Name <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                        <x-text-input name="name" value="{{ old('name', $city->name) }}" required />
                        </div>
                    </div>

                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="country_id">Country <span class="text-red-500">*</span></x-input-label>
                        <x-dynamic-combobox label="Select Country" id="country_id" fetchUrl="{{ route('countries.search') }}" placeholder="Search for a Country..." :defaultValue="old('country_id', $city->country_id)" required />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('cities.index') }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 ml-2">Update</button>
            </div>
        </div>
    </form>
</x-app-layout>
