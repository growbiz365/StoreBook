<x-app-layout>
    @section('title', 'Edit Sub User - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '/subusers', 'label' => 'Sub Users'],
        ['url' => '#', 'label' => 'Edit Sub User'],
    ]" />

    <x-dynamic-heading title="Edit Sub User" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <div>
        <div>
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Form Start -->
                    <form action="{{ route('subusers.update', $subUser->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name Field -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <div class="mt-2">
                                <input value="{{ old('name', $subUser->name) }}" id="name" name="name"
                                    placeholder="Enter Name" type="text"
                                    class="border border-gray-300 focus:ring-indigo-500 focus:ring-1 rounded-md shadow-sm w-full px-4 py-2 text-sm">
                                @error('name')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Username Field -->
                        <div class="mb-6">
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <div class="mt-2">
                                <input value="{{ old('username', $subUser->username) }}" id="username" name="username"
                                    placeholder="Enter Username" type="text"
                                    class="border border-gray-300 focus:ring-indigo-500 focus:ring-1 rounded-md shadow-sm w-full px-4 py-2 text-sm">
                                @error('username')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-2">
                                <input value="{{ old('email', $subUser->email) }}" id="email" name="email"
                                    placeholder="Enter Email" type="email"
                                    class="border border-gray-300 focus:ring-indigo-500 focus:ring-1 rounded-md shadow-sm w-full px-4 py-2 text-sm">
                                @error('email')
                                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Assign Businesses Field -->
                        <div class="mb-6">
                            <label for="businesses" class="block text-sm font-medium text-gray-700 mb-2">
                                Assign Businesses <span class="text-red-500">*</span>
                            </label>
                            <select id="businesses" name="businesses[]" multiple
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white text-gray-700 text-sm">
                                @if($businesses && $businesses->count() > 0)
                                    @foreach ($businesses as $business)
                                        @if($business && $business->business_name)
                                            <option value="{{ $business->id }}" {{ in_array($business->id, old('businesses', $assignedBusinesses ?? [])) ? 'selected' : '' }}>
                                                {{ $business->business_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                @else
                                    <option disabled>No businesses available</option>
                                @endif
                            </select>
                            @error('businesses')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Roles Field -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">Roles</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                @foreach ($assignableRoles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                            id="role-{{ $role->id }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            {{ in_array($role->id, $assignedRoles) ? 'checked' : '' }}>
                                        <label for="role-{{ $role->id }}"
                                            class="text-sm text-gray-700 ml-2">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <x-primary-button>{{ __('Update Sub User') }}</x-primary-button>
                        </div>
                    </form>
                    <!-- Form End -->
                </div>
            </div>
        </div>
    </div>

</x-app-layout>


<script>
    $(document).ready(function() {
        console.log('Initializing Select2 for businesses dropdown');
        
        // Check if Select2 is available
        if (typeof $.fn.select2 === 'undefined') {
            console.error('Select2 is not loaded');
            return;
        }
        
        // Initialize Select2 for the businesses select field
        $('#businesses').select2({
            placeholder: "Search businesses...",
            allowClear: true,
            width: '100%',
            dropdownParent: $('body') // Use body as dropdown parent to avoid z-index issues
        });
        
        console.log('Select2 initialized successfully');
    });
</script>
