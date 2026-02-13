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

    // Separate CRUD and non-CRUD permissions from assignable permissions
    $crudPermissions = $assignablePermissions->filter(function($permission) {
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
    $otherPermissions = $assignablePermissions->filter(function($permission) {
        return !isCrudPermission($permission);
    });
@endphp

<x-app-layout>
    @section('title', 'Edit Role - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Dashboard'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '/roles', 'label' => 'Roles'],
        ['url' => '#', 'label' => 'Edit Role'],
    ]" />
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800 leading-tight">
                Edit Role
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
            <form action="{{ route('roles.update', $role->id) }}" method="POST" x-data="permissionsForm()">
                @csrf
                @method('PUT')
                
                <!-- Name Field -->
                <div class="mb-6">
                    <label for="name" class="block text-lg font-medium text-gray-700">Role Name</label>
                    <div class="mt-2">
                        <input value="{{ old('name', $role->name) }}" id="name" name="name" placeholder="Enter Role Name"
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

                    <p class="text-sm text-gray-600 mb-4">You can only assign permissions that you currently have through your assigned roles.</p>

                    <!-- Search Filter -->
                    <div class="mb-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text"
                                   x-model="searchQuery"
                                   placeholder="Search modules..."
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <p class="mt-2 text-sm text-gray-500" x-show="searchQuery">
                            <span x-text="getVisibleModulesCount()"></span> module(s) found
                        </p>
                    </div>

                    <!-- Module Based Permissions -->
                    @if($modulePermissions->isNotEmpty())
                        <div class="relative overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full text-sm text-left text-gray-700">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 font-semibold">
                                            <div class="flex items-center justify-center gap-2">
                                                <input type="checkbox"
                                                       @change="toggleModuleAll($event.target.checked)"
                                                       x-bind:checked="allModulesSelected"
                                                       class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 focus:ring-2">
                                            </div>
                                        </th>
                                        <th scope="col" class="px-4 py-2 font-semibold">Module</th>
                                        @foreach($crudActions as $action)
                                            <th scope="col" class="px-4 py-2 font-semibold capitalize w-32 text-center">{{ $action }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modulePermissions as $module => $modulePerms)
                                        @php
                                            $moduleSlug = Str::slug($module);
                                            $modulePermissionIds = [];
                                            foreach($crudActions as $action) {
                                                $permission = $modulePerms->first(function($p) use ($action, $module) {
                                                    return str_starts_with($p->name, $action . ' ' . $module);
                                                });
                                                if ($permission) {
                                                    $modulePermissionIds[] = $permission->id;
                                                }
                                            }
                                        @endphp
                                        <tr x-show="isModuleVisible('{{ $moduleSlug }}')"
                                            data-module-slug="{{ $moduleSlug }}"
                                            data-module-name="{{ $module }}"
                                            data-module-permissions="{{ implode(',', $modulePermissionIds) }}"
                                            class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="px-4 py-2 text-center">
                                                <input type="checkbox"
                                                       @change="toggleModuleRow('{{ $moduleSlug }}', $event.target.checked)"
                                                       x-bind:checked="isModuleRowSelected('{{ $moduleSlug }}')"
                                                       class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 focus:ring-2">
                                            </td>
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
                                                            id="permission_{{ $action }}_{{ $moduleSlug }}"
                                                            x-model="selectedPermissions"
                                                            @change="updateModuleCheckbox('{{ $moduleSlug }}')"
                                                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 focus:ring-2">
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

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

                    @if($assignablePermissions->isEmpty())
                        <div class="text-yellow-600 text-sm mt-2 bg-yellow-50 p-3 rounded">
                            <strong>No permissions available:</strong> You don't have any permissions that can be assigned to new roles. Contact your administrator to get appropriate permissions.
                        </div>
                    @endif

                    @error('permission')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="mt-8">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-indigo-700 transition ease-in-out duration-300 w-full">
                        Update Role
                    </button>
                </div>
            </form>
            <!-- Form End -->
        </div>
    </div>

    <script>
        function permissionsForm() {
            return {
                selectedPermissions: @json($selectedPermissionIds),
                searchQuery: '',

                isModuleVisible(moduleSlug) {
                    if (!this.searchQuery) return true;
                    const moduleRow = document.querySelector(`tr[data-module-slug="${moduleSlug}"]`);
                    if (!moduleRow) return false;
                    const moduleName = moduleRow.getAttribute('data-module-name')?.toLowerCase() || '';
                    return moduleName.includes(this.searchQuery.toLowerCase());
                },

                getVisibleModulesCount() {
                    const allRows = document.querySelectorAll('tbody tr[data-module-slug]');
                    return Array.from(allRows).filter(row => {
                        const style = window.getComputedStyle(row);
                        return style.display !== 'none';
                    }).length;
                },

                updateModuleCheckbox(moduleSlug) {
                    const row = document.querySelector(`tr[data-module-slug="${moduleSlug}"]`);
                    if (!row) return;
                    const moduleCheckbox = row.querySelector('td:first-child input[type="checkbox"]');
                    if (moduleCheckbox) {
                        moduleCheckbox.checked = this.isModuleRowSelected(moduleSlug);
                    }
                },

                toggleModuleRow(moduleSlug, checked) {
                    const row = document.querySelector(`tr[data-module-slug="${moduleSlug}"]`);
                    if (!row) return;

                    const permissionIds = row.getAttribute('data-module-permissions')?.split(',').filter(id => id) || [];

                    if (checked) {
                        // Add all permissions for this module
                        permissionIds.forEach(id => {
                            const idStr = String(id);
                            if (!this.selectedPermissions.includes(idStr)) {
                                this.selectedPermissions.push(idStr);
                            }
                            // Also check the actual checkbox
                            const checkbox = document.querySelector(`input[name="permission[]"][value="${id}"]`);
                            if (checkbox && !checkbox.checked) {
                                checkbox.checked = true;
                            }
                        });
                    } else {
                        // Remove all permissions for this module
                        permissionIds.forEach(id => {
                            const idStr = String(id);
                            const index = this.selectedPermissions.indexOf(idStr);
                            if (index > -1) {
                                this.selectedPermissions.splice(index, 1);
                            }
                            // Also uncheck the actual checkbox
                            const checkbox = document.querySelector(`input[name="permission[]"][value="${id}"]`);
                            if (checkbox && checkbox.checked) {
                                checkbox.checked = false;
                            }
                        });
                    }
                },

                isModuleRowSelected(moduleSlug) {
                    const row = document.querySelector(`tr[data-module-slug="${moduleSlug}"]`);
                    if (!row) return false;

                    const permissionIds = row.getAttribute('data-module-permissions')?.split(',').filter(id => id) || [];
                    if (permissionIds.length === 0) return false;
                    return permissionIds.every(id => this.selectedPermissions.includes(String(id)));
                },

                toggleModuleAll(checked) {
                    // Get all visible rows
                    const allRows = document.querySelectorAll('tbody tr[data-module-slug]');
                    const visibleRows = Array.from(allRows).filter(row => {
                        const style = window.getComputedStyle(row);
                        return style.display !== 'none';
                    });

                    visibleRows.forEach(row => {
                        const moduleSlug = row.getAttribute('data-module-slug');
                        if (moduleSlug) {
                            this.toggleModuleRow(moduleSlug, checked);
                            const moduleCheckbox = row.querySelector('td:first-child input[type="checkbox"]');
                            if (moduleCheckbox) {
                                moduleCheckbox.checked = checked;
                            }
                        }
                    });
                },

                get allModulesSelected() {
                    const allRows = document.querySelectorAll('tbody tr[data-module-slug]');
                    const visibleRows = Array.from(allRows).filter(row => {
                        const style = window.getComputedStyle(row);
                        return style.display !== 'none';
                    });

                    if (visibleRows.length === 0) return false;

                    return visibleRows.every(row => {
                        const moduleSlug = row.getAttribute('data-module-slug');
                        return moduleSlug && this.isModuleRowSelected(moduleSlug);
                    });
                },

                uncheckAll() {
                    this.selectedPermissions = [];
                    // Uncheck all checkboxes
                    document.querySelectorAll('input[name="permission[]"]').forEach(cb => cb.checked = false);
                    document.querySelectorAll('td:first-child input[type="checkbox"]').forEach(cb => cb.checked = false);
                },

                checkAll() {
                    // Get all permission checkboxes
                    const checkboxes = document.querySelectorAll('input[name="permission[]"]');
                    // Map their values to an array
                    this.selectedPermissions = Array.from(checkboxes).map(cb => {
                        cb.checked = true;
                        return cb.value;
                    });

                    // Also check all module checkboxes that have all permissions selected
                    document.querySelectorAll('tbody tr[data-module-slug]').forEach(row => {
                        const moduleSlug = row.getAttribute('data-module-slug');
                        if (moduleSlug && this.isModuleRowSelected(moduleSlug)) {
                            const moduleCheckbox = row.querySelector('td:first-child input[type="checkbox"]');
                            if (moduleCheckbox) {
                                moduleCheckbox.checked = true;
                            }
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>