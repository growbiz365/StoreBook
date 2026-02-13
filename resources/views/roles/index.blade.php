
<x-app-layout>
    @section('title', 'Roles List - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '#', 'label' => $title],
    ]" />
    

    <x-dynamic-heading title="{{$title}}" />


    
    <div class="">
        <div class=" mx-auto">

            
@if(Session::has('success')) 
<x-success-alert message="{{ Session::get('success') }}" />
@endif



           <!-- Card Container -->
           <div class="bg-white shadow rounded-lg p-6">
               





            <div class="space-y-4 pb-8">
                <!-- Header with Stores Heading -->
                <!-- Search Form and Add Store Button -->
                <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                  <!-- Search Form -->
                   <form action="{{ route('roles.index') }}" method="GET" class="flex w-full max-w-md">
                      <div class="relative flex-grow">
                         <span class="relative isolate block">
                         

                               <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        class="relative block w-full appearance-none rounded-l-lg pl-10 px-[calc(theme(spacing[3.5])-1px)] py-[calc(theme(spacing[2.5])-1px)] text-base/6 text-zinc-950 placeholder:text-zinc-500 border border-zinc-950/10 bg-transparent dark:bg-white/5 focus:outline-none focus:ring-1 focus:ring-blue-500" 
                        placeholder="Search Role..."
                    >
                            <svg
                               xmlns="http://www.w3.org/2000/svg"
                               viewBox="0 0 16 16"
                               fill="currentColor"
                               aria-hidden="true"
                               class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-zinc-500 dark:text-zinc-400 pointer-events-none"
                               >
                               <path
                                  fill-rule="evenodd"
                                  d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                                  clip-rule="evenodd"
                                  ></path>
                            </svg>
                         </span>
                      </div>
                      <button
                         type="submit"
                         class="flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                         >
                         <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="w-5 h-5"
                            >
                            <path
                               stroke-linecap="round"
                               stroke-linejoin="round"
                               d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"
                               />
                         </svg>
                      </button>
                   </form>
                   <!-- Add Store Button -->
                   <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                    @can('create roles')
                      <a href="{{ route('roles.create') }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                          Add New Role
                      </a>
                      @endcan


                   </div>
                </div>
             </div>


             <!-- Code for Delete -->
             <div x-data="{ showModal: false, deleteId: null }">
             <!-- Code for Delete -->



                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border border-gray-200 px-4 py-2 text-left text-xs font-semibold text-gray-700 w-16">
                                    ID
                                </th>
                                <th class="border border-gray-200 px-4 py-2 text-left text-xs font-semibold text-gray-700">
                                    Role Name
                                </th>
                                <th class="border border-gray-200 px-4 py-2 text-left text-xs font-semibold text-gray-700">
                                    Business
                                </th>
                                <th class="border border-gray-200 px-4 py-2 text-left text-xs font-semibold text-gray-700">
                                    Created By
                                </th>
                                <th class="border border-gray-200 px-4 py-2 text-left text-xs font-semibold text-gray-700">
                                    Created At
                                </th>
                                <th class="border border-gray-200 px-4 py-2 text-center text-xs font-semibold text-gray-700 w-32">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($roles->isNotEmpty())
                                @foreach($roles as $role)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-200 px-4 py-2 text-sm text-gray-800">
                                            {{ $role->id }}
                                        </td>
                                        <td class="border border-gray-200 px-4 py-2 text-sm text-gray-800">
                                            {{ $role->name }}
                                        </td>
                                        <td class="border border-gray-200 px-4 py-2 text-sm text-gray-800">
                                            @if(!$role->creator)
                                                <span class="text-xs text-emerald-700 font-semibold">System / All Businesses</span>
                                            @elseif($role->creator->isSuperAdmin())
                                                <span class="text-xs text-emerald-700 font-semibold">All Businesses</span>
                                            @elseif($role->creator->businesses->isNotEmpty())
                                                @php
                                                    $businessNames = $role->creator->businesses->pluck('business_name')->unique()->values();
                                                @endphp
                                                <span class="text-gray-800 text-xs">
                                                    {{ $businessNames->take(3)->join(', ') }}
                                                    @if($businessNames->count() > 3)
                                                        <span class="text-gray-400">+{{ $businessNames->count() - 3 }} more</span>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-gray-500 italic text-xs">No business linked</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-200 px-4 py-2 text-sm text-gray-800">
                                            @if($role->creator)
                                                {{ $role->creator->name }}
                                            @else
                                                <span class="text-gray-500 italic">System</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-200 px-4 py-2 text-sm text-gray-800">
                                            {{ \Carbon\Carbon::parse($role->created_at)->format('d M, Y') }}
                                        </td>
                                        <td class="border border-gray-200 px-4 py-2 text-sm text-gray-800 space-x-2 text-center">
                                            @if(auth()->user()->canEditRole($role))
                                            <a href="{{ route('roles.edit', $role->id) }}" class="text-blue-600 hover:underline">
                                                Edit
                                            </a>
                                            @endif
                                            @if(auth()->user()->isSuperAdmin() || $role->created_by == auth()->id())
                                               <!-- Code for Delete -->
                                               <form @submit.prevent="showModal = true; deleteId = {{ $role->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">
                                                  Delete
                                                </button>
                                              </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="px-4 py-2 text-center text-sm text-gray-500">
                                        No roles found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                
<!-- Code for Delete -->
<x-delete-modal 
title="Delete Role" 
message="Are you sure you want to delete this Role? This action cannot be undone." 
:actionUrl="route('roles.destroy', '_ID_')"
/>
</div>
<!-- Code for Delete -->

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
