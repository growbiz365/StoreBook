<x-app-layout>
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '/packages', 'label' => 'Packages'],
        ['url' => '#', 'label' => 'Modules'],
    ]" />

    <x-dynamic-heading title="Assign Modules to {{ $package->package_name }}" />

    @if (session('success'))
        <x-success-alert message="{{ session('success') }}" />
    @endif

    <!-- Package Info Section -->
    <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8 mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Package Information</h2>
        <p class="mt-1 text-sm text-gray-600">Below are the details for the selected package.</p>

        <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
            <div class="sm:col-span-1">
                <x-input-label for="package_name">Package Name</x-input-label>
                <p class="mt-1 text-gray-900">{{ $package->package_name }}</p>
            </div>
            <div class="sm:col-span-1">
                <x-input-label for="price">Price</x-input-label>
                <p class="mt-1 text-gray-900">{{ $package->price }} {{ $package->currency->currency_code }}</p>
            </div>
            <div class="sm:col-span-1">
                <x-input-label for="duration">Duration</x-input-label>
                <p class="mt-1 text-gray-900">{{ $package->duration_months }} months</p>
            </div>
            <div class="sm:col-span-1">
                <x-input-label for="currency">Currency</x-input-label>
                <p class="mt-1 text-gray-900">{{ $package->currency->currency_name }}
                    ({{ $package->currency->currency_code }})</p>
            </div>
        </div>
    </div>

    <!-- Assign Modules Form -->
    <form action="{{ route('packages.storeModules', $package->id) }}" method="POST">
        @csrf
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Select Modules</h2>
                    <p class="mt-1 text-sm text-gray-600">Assign the modules to this package by selecting from the list
                        below.</p>
                </div>
                <button type="button" id="checkAll"
                    class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Check All
                </button>
            </div>

            <div class="mt-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($modules as $module)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                                id="module-{{ $module->id }}" class="module-checkbox w-4 h-4 text-indigo-600"
                                @if ($assignedModules->contains($module)) checked @endif />
                            <label for="module-{{ $module->id }}"
                                class="ml-2 text-sm text-gray-700">{{ $module->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('packages.index') }}"
                    class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 ml-2">Save</button>
            </div>
        </div>
    </form>

    <!-- Add this script section at the bottom of your layout or page -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAllButton = document.getElementById('checkAll');
            const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
            let isAllChecked = false;

            checkAllButton.addEventListener('click', function() {
                isAllChecked = !isAllChecked;
                moduleCheckboxes.forEach(checkbox => {
                    checkbox.checked = isAllChecked;
                });

                // Update button text
                checkAllButton.innerHTML = `
                     <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    ${isAllChecked ? 'Uncheck All' : 'Check All'}
                `;
            });
        });
    </script>
</x-app-layout>
