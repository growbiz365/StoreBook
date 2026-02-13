@php
    // Helper function to get module name from permission
    function getModuleName($permission) {
        $name = $permission->name;
        // Remove CRUD prefixes
        $withoutCrud = preg_replace('/^(view|create|edit|delete)\s+/', '', $name);
        return $withoutCrud; // Return full name without truncating
    }

    // Helper function to check if permission is CRUD type
    function isCrudPermission($permission) {
        return str_starts_with($permission->name, 'view ') ||
               str_starts_with($permission->name, 'create ') ||
               str_starts_with($permission->name, 'edit ') ||
               str_starts_with($permission->name, 'delete ');
    }

    // Get all possible CRUD actions
    $crudActions = ['view', 'create', 'edit', 'delete'];

    // Separate CRUD and non-CRUD permissions
    $crudPermissions = $permissions->filter(function($permission) {
        return isCrudPermission($permission);
    });

    // Group CRUD permissions by module
    $modulePermissions = $crudPermissions->groupBy(function($permission) {
        return getModuleName($permission);
    })->sortKeys();

    // Filter modules to only include those with at least one CRUD action
    $modulePermissions = $modulePermissions->filter(function($perms) use ($crudActions) {
        return collect($crudActions)->some(function($action) use ($perms) {
            return $perms->contains(function($p) use ($action) {
                return str_starts_with($p->name, $action);
            });
        });
    });

    // Get permissions that don't follow CRUD pattern
    $otherPermissions = $permissions->filter(function($permission) {
        return !isCrudPermission($permission);
    });
@endphp

<x-app-layout>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Dashboard'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '#', 'label' => 'Roles'],
        ['url' => '#', 'label' => 'Create Role'],
    ]" />
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800 leading-tight">
                Create Role
            </h2>
            <!-- Back Button -->
            <a href="{{ route('roles.index') }}"
                class="inline-block px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
            <!-- Form Start -->
            <form action="{{ route('roles.store') }}" method="POST" x-data="permissionsForm()">
                @csrf
                <!-- Name Field -->
                <div class="mb-6">
                    <label for="name" class="block text-lg font-medium text-gray-700">Role Name</label>
                    <div class="mt-2">
                        <input value="{{ old('name') }}" id="name" name="name" placeholder="Enter Role Name"
                            type="text"
                            class="border border-gray-300 focus:ring-indigo-500 focus:ring-1 rounded-md shadow-sm w-full px-4 py-2 text-sm">
                        @error('name')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Permissions Field -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-lg font-medium text-gray-700">Permissions</label>
                        <div class="flex gap-2">
                        <button type="button" @click="checkAll"
                                class="text-sm bg-green-50 text-green-600 px-4 py-2 rounded-md hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Check All
                            </button>
                        <button type="button" @click="uncheckAll"
                                class="text-sm bg-red-50 text-red-600 px-4 py-2 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Uncheck All
                            </button>
                        </div>
                    </div>

                    <!-- Module Based Permissions -->
                    <div class="relative overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-2 font-semibold">Module</th>
                                    @foreach($crudActions as $action)
                                        <th scope="col" class="px-4 py-2 font-semibold capitalize w-32 text-center">{{ $action }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modulePermissions as $module => $modulePerms)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium">{{ $module }}</td>
                                        @foreach($crudActions as $action)
                                            <td class="px-4 py-2 text-center">
                                                @php
                                                    $permission = $modulePerms->first(function($p) use ($action, $module) {
                                                        return str_starts_with($p->name, $action . ' ' . $module);
                                                    });
                                                @endphp
                                                @if($permission)
                                                    <input type="checkbox"
                                                        name="permission[]"
                                                        value="{{ $permission->id }}"
                                                        id="permission_{{ $action }}_{{ Str::slug($module) }}"
                                                        x-model="selectedPermissions"
                                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Other Permissions Section -->
                    @if($otherPermissions->isNotEmpty())
                        <div class="mt-8">
                            <h3 class="text-base font-semibold text-gray-700 mb-4">Other Permissions</h3>
                            <div class="grid grid-cols-3 gap-4 bg-white p-4 border border-gray-200 rounded-lg">
                                @foreach($otherPermissions->sortBy('name') as $permission)
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox"
                                            name="permission[]"
                                            value="{{ $permission->id }}"
                                            id="permission_other_{{ $permission->id }}"
                                            x-model="selectedPermissions"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <label for="permission_other_{{ $permission->id }}" class="text-sm text-gray-700">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @error('permissions')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="mt-8">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-indigo-700 transition ease-in-out duration-300 w-full">
                        Create Role
                    </button>
                </div>
            </form>
            <!-- Form End -->
        </div>
    </div>

    <script>
        function permissionsForm() {
            return {
                selectedPermissions: [],
                uncheckAll() {
                    this.selectedPermissions = [];
                },
                checkAll() {
                    // Get all permission checkboxes
                    const checkboxes = document.querySelectorAll('input[name="permission[]"]');
                    // Map their values to an array
                    this.selectedPermissions = Array.from(checkboxes).map(cb => cb.value);
                }
            }
        }
    </script>
</x-app-layout>