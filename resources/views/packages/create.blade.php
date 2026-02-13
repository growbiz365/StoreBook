<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],     ['url' => '/settings', 'label' => 'Settings'], ['url' => '/packages', 'label' => 'Packages'], ['url' => '#', 'label' => 'Add Package']]" />

    <x-dynamic-heading title="Add Package" />

    <form action="{{ route('packages.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
            <div class="pb-10 mb-10 border-b border-gray-150 my-8">
                <h2 class="text-lg font-semibold text-gray-900">Package Information</h2>
                <p class="mt-1 text-sm text-gray-600">Please provide details of the package.</p>

                <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                    <!-- Package Name -->
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="package_name">Package Name <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                            <x-text-input name="package_name" value="{{ old('package_name') }}" required />
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="price">Price <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                            <x-text-input name="price" value="{{ old('price') }}" required />
                        </div>
                    </div>

                    <!-- Currency Search -->
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="currency_id">Currency <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                            <x-dynamic-combobox label="Select Currency" id="currency_id"
                                fetchUrl="{{ route('currencies.search') }}" placeholder="Search for a Currency..."
                                :defaultValue="old('currency_id')" required />
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="duration_months">Duration (Months) <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                            <x-text-input name="duration_months" value="{{ old('duration_months') }}" required />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="description">Description</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="description" value="{{ old('description') }}" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('packages.index') }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 ml-2">Save</button>
            </div>
        </div>
    </form>
</x-app-layout>
