<x-app-layout>
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'], ['url' => '/item-types', 'label' => 'Item Types'], ['url' => '#', 'label' => 'Edit Item Type']]" />

    <x-dynamic-heading title="Edit Item Type" />

    <form action="{{ route('item-types.update', $itemType) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
            <div class="pb-10 mb-10 border-b border-gray-150 my-8">
                <h2 class="text-lg font-semibold text-gray-900">Item Type Information</h2>
                <p class="mt-1 text-sm text-gray-600">Please update the details of the item type.</p>

                <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="item_type">Item Type <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                        <x-text-input name="item_type" value="{{ old('item_type', $itemType->item_type) }}" required />
                        </div>
                    </div>

                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="status">Status <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="1" {{ old('status', $itemType->status) === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $itemType->status) === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('item-types.index') }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 ml-2">Update</button>
            </div>
        </div>
    </form>
</x-app-layout>
