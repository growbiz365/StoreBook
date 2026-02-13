<x-app-layout>
    @section('title', 'Edit Arms Condition - Arms Management')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/arms-dashboard', 'label' => 'Arms Management'], ['url' => '/arms-conditions', 'label' => 'Arms Conditions'], ['url' => '#', 'label' => 'Edit Arms Condition']]" />

    <x-dynamic-heading title="Edit Arms Condition" />

    <form action="{{ route('arms-conditions.update', $armsCondition) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
            <div class="pb-10 mb-10 border-b border-gray-150 my-8">
                <h2 class="text-lg font-semibold text-gray-900">Arms Condition Information</h2>
                <p class="mt-1 text-sm text-gray-600">Please update the details of the arms condition.</p>

                <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="arm_condition">Arms Condition <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                        <x-text-input name="arm_condition" value="{{ old('arm_condition', $armsCondition->arm_condition) }}" placeholder="e.g., New, Used, Excellent, Good" required />
                        </div>
                    </div>

                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="status">Status <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="1" {{ old('status', $armsCondition->status) === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $armsCondition->status) === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('arms-conditions.index') }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 ml-2">Update</button>
            </div>
        </div>
    </form>
</x-app-layout> 