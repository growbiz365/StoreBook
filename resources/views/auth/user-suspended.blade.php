<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Main Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <!-- Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                    </svg>
                </div>

                <!-- Title -->
                <h1 class="text-xl font-bold text-gray-900 mb-2">Account Suspended</h1>
                <p class="text-sm text-gray-600 mb-6">Your account has been temporarily suspended</p>

                <!-- Suspension Details -->
                @if(session('user_suspended'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-left">
                        <h3 class="text-sm font-semibold text-red-800 mb-2">Suspension Details</h3>
                        <div class="text-sm text-red-700 space-y-1">
                            @if(session('user_suspended_at'))
                                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse(session('user_suspended_at'))->format('M d, Y g:i A') }}</p>
                            @endif
                            @if(session('user_suspended_by'))
                                <p><strong>Suspended by:</strong> {{ session('user_suspended_by') }}</p>
                            @endif
                            @if(session('user_suspension_reason'))
                                <p><strong>Reason:</strong> {{ session('user_suspension_reason') }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Contact Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Need Help?</h3>
                    <div class="text-sm text-blue-800">
                        <p class="mb-1">📧 support@armportal.com</p>
                        <p>📞 +1 (555) 123-4567</p>
                    </div>
                </div>

                <!-- Footer -->
                <p class="text-xs text-gray-500">
                    Contact support if you believe this is an error
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
