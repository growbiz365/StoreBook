<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],     ['url' => '/settings', 'label' => 'Settings'], ['url' => '/packages', 'label' => 'Packages'], ['url' => '#', 'label' => $package->package_name]]" />

    <x-dynamic-heading title="Package Details" />

    <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
        <div class="pb-10 mb-10 border-b border-gray-150 my-8">
            <h2 class="text-lg font-semibold text-gray-900">Package Information</h2>
            <p class="mt-1 text-sm text-gray-600">Details of the selected package.</p>

            <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-1 mb-4">
                    <x-input-label for="package_name">Package Name</x-input-label>
                    <div class="mt-2">
                        <x-text-input name="package_name" value="{{ $package->package_name }}" disabled />
                    </div>
                </div>

                <div class="sm:col-span-1 mb-4 ml-4">
                    <x-input-label for="price">Price</x-input-label>
                    <div class="mt-2">
                        <x-text-input name="price" value="{{ $package->price }}" disabled />
                    </div>
                </div>

                <div class="sm:col-span-1 mb-4">
                    <x-input-label for="currency_id">Currency</x-input-label>
                    <div class="mt-2">
                        <x-text-input name="currency_id" value="{{ $package->currency->currency_name ?? 'N/A' }}" disabled />
                    </div>
                </div>

                <div class="sm:col-span-1 mb-4 ml-4">
                    <x-input-label for="duration_months">Duration (Months)</x-input-label>
                    <div class="mt-2">
                        <x-text-input name="duration_months" value="{{ $package->duration_months }}" disabled />
                    </div>
                </div>

                <div class="sm:col-span-1 mb-4">
                    <x-input-label for="description">Description</x-input-label>
                    <div class="mt-2">
                        <textarea name="description" rows="4" class="form-textarea w-full" disabled>{{ $package->description }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{ route('packages.index') }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Back to List</a>
        </div>
    </div>
</x-app-layout>
