<x-app-layout>
    @section('title', 'Users List - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Dashboard'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '#', 'label' => 'Users'],
    ]" />
    
    
        <div class="flex justify-between items-center">
            <div>
                
            </div>
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New User
            </a>
        </div>
    

    <div class="py-6">
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
            
@if(Session::has('success')) 
                <div class="mx-6 mt-6">
<x-success-alert message="{{ Session::get('success') }}" />
                </div>
@endif

            <!-- Search Section -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <form action="{{ route('users.index') }}" method="GET" class="flex gap-3">
                    <div class="flex-1">
                        <div class="relative">
                       <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                                class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                placeholder="Search users by name or email..."
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                 </svg>
           </div>
        </div>
     </div>
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors duration-200">
                            Search
                        </button>
                    @if(request('search'))
                        <a href="{{ route('users.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:outline-none transition-colors duration-200">
                            Clear
                        </a>
                    @endif
                    </form>
            </div>

            <!-- Users Table -->
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Roles
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Businesses
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <!-- User Info -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                                                    <span class="text-sm font-semibold text-white">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4 min-w-0 flex-1">
                                                <div class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500 truncate">{{ $user->email }}</div>
                                                @if($user->username)
                                                    <div class="text-xs text-gray-400 truncate">{{ $user->username }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        </td>

                                    <!-- Roles -->
                                    <td class="px-6 py-4">
                                        @if($user->roles->count() > 0)
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($user->roles->take(2) as $role)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                                @if($user->roles->count() > 2)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                        +{{ $user->roles->count() - 2 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 italic">No roles</span>
                                        @endif
                                        </td>

                                    <!-- Businesses -->
                                    <td class="px-6 py-4">
                                            @if($user->businesses->count() > 0)
                                            <div class="flex flex-wrap gap-1.5">
                                                    @foreach($user->businesses->take(2) as $business)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                            {{ $business->business_name }}
                                                    </span>
                                                    @endforeach
                                                    @if($user->businesses->count() > 2)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                        +{{ $user->businesses->count() - 2 }}
                                                    </span>
                                                    @endif
                                                </div>
                                            @else
                                            <span class="text-sm text-gray-400 italic">No businesses</span>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        @if($user->isSuspended())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Suspended
                                            </span>
                                        @elseif($user->isSuperAdmin())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-rule="evenodd"></path>
                                                </svg>
                                                Super Admin
                                            </span>
                                        @elseif($user->parent_id)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                </svg>
                                                Sub User
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Active
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Created Date -->
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="font-medium">{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($user->created_at)->format('g:i A') }}</div>
                                        </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('users.edit', $user->id) }}" 
                                               class="inline-flex items-center p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-200"
                                               title="Edit user">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            
                                            @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
                                                @if($user->isSuspended())
                                                    <button onclick="confirmUnsuspend({{ $user->id }})" 
                                                            class="inline-flex items-center p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                                            title="Unsuspend user">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <button onclick="confirmSuspend({{ $user->id }})" 
                                                            class="inline-flex items-center p-2 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors duration-200"
                                                            title="Suspend user">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                                
                                                <!-- <button onclick="confirmDelete({{ $user->id }})" 
                                                        class="inline-flex items-center p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                                        title="Delete user">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button> -->
                                            @endif
                                        </div>
                                    </td>
                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No users found</h3>
                                            <p class="text-gray-500 mb-6 max-w-sm text-center">
                                                @if(request('search'))
                                                    No users match your search criteria. Try adjusting your search terms.
                                                @else
                                                    Get started by creating your first user account.
                                                @endif
                                            </p>
                                            @if(!request('search'))
                                                <a href="{{ route('users.create') }}" 
                                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors duration-200">
                                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Create First User
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
</div>

                <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-xl rounded-xl bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mt-4">Delete User</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Are you sure you want to delete this user? This action cannot be undone and will permanently remove all user data.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <form id="deleteForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="closeDeleteModal()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 mr-3 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                            Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspend Confirmation Modal -->
    <div id="suspendModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-xl rounded-xl bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mt-4">Suspend User</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 leading-relaxed mb-4">
                        Are you sure you want to suspend this user? They will not be able to log in until manually unsuspended.
                    </p>
                    <div class="text-left">
                        <label for="suspendReason" class="block text-sm font-medium text-gray-700 mb-2">Reason (optional)</label>
                        <textarea id="suspendReason" name="reason" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                  placeholder="Enter reason for suspension..."></textarea>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <form id="suspendForm" method="POST" class="inline">
                        @csrf
                        <button type="button" onclick="closeSuspendModal()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 mr-3 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-colors duration-200">
                            Suspend User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Unsuspend Confirmation Modal -->
    <div id="unsuspendModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-xl rounded-xl bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mt-4">Unsuspend User</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Are you sure you want to unsuspend this user? They will be able to log in again.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <form id="unsuspendForm" method="POST" class="inline">
                        @csrf
                        <button type="button" onclick="closeUnsuspendModal()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 mr-3 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200">
                            Unsuspend User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(userId) {
            document.getElementById('deleteForm').action = `/users/${userId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function confirmSuspend(userId) {
            document.getElementById('suspendForm').action = `/users/${userId}/suspend`;
            document.getElementById('suspendModal').classList.remove('hidden');
        }

        function closeSuspendModal() {
            document.getElementById('suspendModal').classList.add('hidden');
            document.getElementById('suspendReason').value = '';
        }

        function confirmUnsuspend(userId) {
            document.getElementById('unsuspendForm').action = `/users/${userId}/unsuspend`;
            document.getElementById('unsuspendModal').classList.remove('hidden');
        }

        function closeUnsuspendModal() {
            document.getElementById('unsuspendModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        document.getElementById('suspendModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuspendModal();
            }
        });

        document.getElementById('unsuspendModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUnsuspendModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
                closeSuspendModal();
                closeUnsuspendModal();
            }
        });
    </script>
</x-app-layout>