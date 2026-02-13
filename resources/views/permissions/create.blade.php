<x-app-layout>
    @section('title', 'Create Permission - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/settings', 'label' => 'Settings'],
        ['url' => '/permissions', 'label' => 'Permissions'],
        ['url' => '#', 'label' => $title]
    ]" />
    
        
        <x-dynamic-heading title="{{$title}}" />
    
    
    
    
    
        <div class="">
            <div class="">   
                <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
                
                <!-- Form Start -->
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <!-- Name Field -->
                    <div class="mb-6">
                        <label for="name" class="block text-lg font-medium text-gray-700">Permission Name</label>
                        <div class="mt-2">
                            <input 
                                value="{{ old('name') }}" 
                                id="name" 
                                name="name" 
                                placeholder="Enter Permission Name" 
                                type="text" 
                                class="border border-gray-300 focus:ring-indigo-500 focus:ring-1 rounded-md shadow-sm w-full px-4 py-2 text-sm"
                            >
                            @error('name')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8">
                        <button 
                            type="submit" 
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-indigo-700 transition ease-in-out duration-300 w-full"
                        >
                            Create Permission
                        </button>
                    </div>
                </form>
                <!-- Form End -->
            </div>
        </div>
    </div>
</x-app-layout>