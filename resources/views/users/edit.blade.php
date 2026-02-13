<x-app-layout>
    @section('title', 'Edit User - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '/users', 'label' => 'Users'],
        ['url' => '#', 'label' => $title],
    ]" />

    <x-dynamic-heading title="{{ $title }}" />

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <div class="">
        <div class="">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Form Start -->
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Name Field -->
                        <div class="mb-6 sm:col-span-1">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input value="{{ old('name', $user->name) }}" id="name" name="name" type="text" placeholder="Enter Name"
                                class="mt-1 block w-full sm:w-2/3 md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('name')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Username Field -->
                        <div class="mb-6 sm:col-span-1">
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input value="{{ old('username', $user->username) }}" id="username" name="username" type="text"
                                placeholder="Enter Username"
                                class="mt-1 block w-full sm:w-2/3 md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('username')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Email Field -->
                        <div class="mb-6 sm:col-span-1">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input value="{{ old('email', $user->email) }}" id="email" name="email" type="email" placeholder="Enter Email"
                                class="mt-1 block w-full sm:w-2/3 md:w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('email')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Roles Field -->
                        <div class="mb-6 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Roles</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                @foreach ($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="role[]" value="{{ $role->id }}"
                                            id="role-{{ $role->id }}" {{ $hasRoles->contains($role->id) ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="role-{{ $role->id }}"
                                            class="text-sm text-gray-700 ml-2">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('role')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                      
                <!-- businesses Field -->
                <div class="mb-6 sm:col-span-1">
                    <x-input-label for="businesses">Assign Businesses <span class="text-red-500">*</span></x-input-label>
                    <div class="mt-2">
                        <select id="businesses" name="businesses[]" multiple
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white text-gray-700 text-sm">
                            @foreach ($businesses as $business)
                                <option value="{{ $business->id }}" {{ $user->businesses->contains($business->id) ? 'selected' : '' }}>
                                    {{ $business->business_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="super-admin-notice" class="mt-2 text-sm text-blue-600 hidden">
                        <i class="fas fa-info-circle"></i> Super Admin role automatically grants access to all businesses.
                    </div>

                    @error('businesses')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="mt-8">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-indigo-700 transition ease-in-out duration-300 w-full">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>

<script>
$(document).ready(function() {
    $('#businesses').select2({
        placeholder: "Search businesses...",
        allowClear: true,
        width: '100%',
        dropdownParent: $(document.body)
    });

    // Store original selected businesses and Super Admin state when page loads
    var originalSelectedBusinesses = $('#businesses').val() ? $('#businesses').val().slice() : [];
    var wasSuperAdminInitially = false;
        var superAdminRoleId = null;
        
        // Find Super Admin role ID
        $('input[name="role[]"]').each(function() {
            if ($(this).next('label').text().trim() === 'Super Admin') {
                superAdminRoleId = $(this).val();
            wasSuperAdminInitially = $(this).is(':checked');
            }
        });

    // Handle Super Admin role selection
    $('input[name="role[]"]').change(function() {
        var allBusinessIds = [];
        
        // Collect all business IDs
        $('#businesses option').each(function() {
            allBusinessIds.push($(this).val());
        });
        
        // Check if Super Admin is selected
        var isSuperAdminSelected = $('input[name="role[]"][value="' + superAdminRoleId + '"]').is(':checked');
        
        if (isSuperAdminSelected) {
            // Select all businesses and show notice
            $('#businesses').val(allBusinessIds).trigger('change');
            $('#super-admin-notice').removeClass('hidden');
            $('#businesses').prop('disabled', true);
        } else {
            // If user was originally Super Admin, clear businesses
            // Otherwise, restore original selected businesses
            if (wasSuperAdminInitially) {
                $('#businesses').val(null).trigger('change');
            } else {
                // Restore original selected businesses
                if (originalSelectedBusinesses && originalSelectedBusinesses.length > 0) {
                    $('#businesses').val(originalSelectedBusinesses).trigger('change');
                } else {
                    $('#businesses').val(null).trigger('change');
                }
            }
            $('#super-admin-notice').addClass('hidden');
            $('#businesses').prop('disabled', false);
        }
    });

    // Check initial state on page load
    $('input[name="role[]"]').trigger('change');
});
</script>
