<x-app-layout>
    @section('title', 'Sub Users List - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '#', 'label' => $title],
    ]" />


    <x-dynamic-heading title="{{ $title }}" />



    <div class="">
        <div class=" mx-auto">


            @if (Session::has('success'))
                <x-success-alert message="{{ Session::get('success') }}" />
            @endif


            <!-- Card Container -->
            <div class="bg-white shadow rounded-lg p-6">






                <div class="space-y-4 pb-8">
                    <!-- Header with Stores Heading -->
                    <!-- Search Form and Add Store Button -->
                    <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                        <!-- Search Form -->
                        <form action="{{ route('subusers.index') }}" method="GET" class="flex w-full max-w-md">
                            <div class="relative flex-grow">
                                <span class="relative isolate block">


                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="relative block w-full appearance-none rounded-l-lg pl-10 px-[calc(theme(spacing[3.5])-1px)] py-[calc(theme(spacing[2.5])-1px)] text-base/6 text-zinc-950 placeholder:text-zinc-500 border border-zinc-950/10 bg-transparent dark:bg-white/5 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        placeholder="Search by Name or Email...">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        aria-hidden="true"
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-zinc-500 dark:text-zinc-400 pointer-events-none">
                                        <path fill-rule="evenodd"
                                            d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </div>
                            <button type="submit"
                                class="flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
                                </svg>
                            </button>
                        </form>
                        <!-- Add Store Button -->
                         
                        <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                            
                                <a href="{{ route('subusers.create') }}"
                                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Add New User
                                </a>
                            


                        </div>
                        
                    </div>
                </div>


                <!-- Code for Delete -->
                <div x-data="{ showModal: false, deleteId: null }">
                    <!-- Code for Delete -->







                    <!-- Card Container -->
                    {{-- <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <!-- Search Form -->
                    <form action="{{ route('users.index') }}" method="GET" class="flex space-x-3">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="border-gray-300 focus:ring focus:ring-indigo-100 rounded-lg shadow-sm px-4 py-2 text-sm w-80"
                            placeholder="Search users..."
                        >
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg shadow text-sm">
                            Search
                        </button>
                    </form>
                </div> --}}

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700 w-1/6">
                                        ID
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700 w-1/4">
                                        Name
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700 w-1/4">
                                        Email
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700 w-1/5">
                                        Roles
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700 w-1/6">
                                        Status
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700 w-1/6">
                                        Created At
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700 w-1/5">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($users->isNotEmpty())
                                    @foreach ($users as $user)
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-800 w-1/6">
                                                {{ $user->id }}
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-800 w-1/4">
                                                {{ $user->name }}
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-800 w-1/4">
                                                {{ $user->email }}
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-800 w-1/5">
                                                @foreach ($user->roles as $role)
                                                    <span class="inline-block px-2 py-1 text-xs text-white bg-indigo-600 rounded-full mr-2 mb-2">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-800 w-1/6">
                                                @if($user->isSuspended())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Suspended
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
                                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-800 w-1/6">
                                                {{ \Carbon\Carbon::parse($user->created_at)->format('d M, Y') }}
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-sm text-gray-800 w-1/5">
                                                <div class="flex space-x-2">
                                                    @can('edit subusers')
                                                        <a href="{{ route('subusers.edit', $user->id) }}"
                                                            class="text-blue-600 hover:underline">
                                                            Edit
                                                        </a>
                                                    @endcan

                                                    @if(!$user->isSuperAdmin())
                                                        @if($user->isSuspended())
                                                            @can('edit subusers')
                                                                <button onclick="confirmUnsuspend({{ $user->id }})" 
                                                                        class="text-green-600 hover:underline">
                                                                    Unsuspend
                                                                </button>
                                                            @endcan
                                                        @else
                                                            @can('edit subusers')
                                                                <button onclick="confirmSuspend({{ $user->id }})" 
                                                                        class="text-orange-600 hover:underline">
                                                                    Suspend
                                                                </button>
                                                            @endcan
                                                        @endif
                                                    @endif

                                                    @can('delete subusers')
                                                        <form
                                                            @submit.prevent="showModal = true; deleteId = {{ $user->id }}"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:underline">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="px-4 py-2 text-center text-sm text-gray-500">
                                            No users found.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>


                    <!-- Code for Delete -->
                    <x-delete-modal title="Delete User"
                        message="Are you sure you want to delete this User? This action cannot be undone."
                        :actionUrl="route('subusers.destroy', '_ID_')" />
                </div>
                <!-- Code for Delete -->



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
                <h3 class="text-lg font-semibold text-gray-900 mt-4">Suspend Sub-User</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 leading-relaxed mb-4">
                        Are you sure you want to suspend this sub-user? They will not be able to log in until manually unsuspended.
                    </p>
                    <form id="suspendForm" method="POST" class="inline">
                        @csrf
                        <div class="text-left">
                            <label for="suspendReason" class="block text-sm font-medium text-gray-700 mb-2">Reason (optional)</label>
                            <textarea id="suspendReason" name="reason" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                      placeholder="Enter reason for suspension..."></textarea>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeSuspendModal()" 
                                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-colors duration-200">
                                Suspend Sub-User
                            </button>
                        </div>
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
                <h3 class="text-lg font-semibold text-gray-900 mt-4">Unsuspend Sub-User</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Are you sure you want to unsuspend this sub-user? They will be able to log in again.
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
                            Unsuspend Sub-User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmSuspend(userId) {
            document.getElementById('suspendForm').action = `/subusers/${userId}/suspend`;
            document.getElementById('suspendModal').classList.remove('hidden');
        }

        function closeSuspendModal() {
            document.getElementById('suspendModal').classList.add('hidden');
            document.getElementById('suspendReason').value = '';
        }

        function confirmUnsuspend(userId) {
            document.getElementById('unsuspendForm').action = `/subusers/${userId}/unsuspend`;
            document.getElementById('unsuspendModal').classList.remove('hidden');
        }

        function closeUnsuspendModal() {
            document.getElementById('unsuspendModal').classList.add('hidden');
        }

        // Close modals when clicking outside
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
                closeSuspendModal();
                closeUnsuspendModal();
            }
        });
    </script>
</x-app-layout>
