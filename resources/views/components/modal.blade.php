@props([
    'name',
    'show' => false,
    'maxWidth' => 'md',
    'message' => '',
    'confirmRoute' => null
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        confirmAction() {
            if ('{{ $confirmRoute }}') {
                document.getElementById('confirmPaymentForm').submit();
            }
        }
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6"
    x-show="show"
    style="display: none;"
>
    <!-- Dark Background -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" x-on:click="show = false"></div>

    <!-- Modal Content -->
    <div class="relative bg-white rounded-lg shadow-lg {{ $maxWidth }} w-full p-6 transform transition-all sm:w-full"
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:leave="ease-in duration-200"
    >
        <!-- Icon & Title -->
        <div class="flex items-center space-x-4">
            <!-- Alert Icon -->
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 text-red-600 rounded-full">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">Confirm Action</h2>
        </div>

        <!-- Message -->
        <p class="text-sm text-gray-600 mt-3">{{ $message }}</p>

        <!-- Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <button x-on:click="show = false" class="px-4 py-2 bg-gray-300 text-gray-900 rounded-md hover:bg-gray-400">
                Cancel
            </button>

            <form id="confirmPaymentForm" action="{{ $confirmRoute }}" method="POST">
                @csrf
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500">
                    Confirm
                </button>
            </form>
        </div>
    </div>
</div>
