<div x-data="{ show: true }" x-show="show" 
    class="rounded-md mt-3 mb-3 bg-red-50 border border-red-400 p-4 w-full text-red-800"
    x-init="setTimeout(() => show = false, 5000)">
    
    <div class="flex">
        <!-- Error Icon -->
        <div class="shrink-0">
             <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </div>

        <!-- Error Message -->
        <div class="ml-3">
            <p class="text-sm font-medium">{{ $message }}</p>
        </div>

        <!-- Dismiss Button -->
        <div class="ml-auto pl-3">
            <button @click="show = false" type="button" 
                class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50">
                <span class="sr-only">Dismiss</span>
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
            </button>
        </div>
    </div>
</div>
