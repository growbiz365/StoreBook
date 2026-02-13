<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Account Suspended</h1>
                <p class="mt-2 text-sm text-gray-600">Your business account has been temporarily suspended</p>
            </div>

            <!-- Suspended Businesses -->
            @if(session('suspended_businesses'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-red-800 mb-2">Suspended Business(es)</h3>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach(session('suspended_businesses') as $businessName)
                            <li class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $businessName }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Message -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Account Access Restricted</h3>
                        <p class="text-sm text-gray-600 mb-3">This may be due to:</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-2"></span>
                                Payment issues
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-2"></span>
                                Policy violations
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-2"></span>
                                Account verification
                            </li>
                            <li class="flex items-center">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-2"></span>
                                Administrative review
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Contact Support</h3>
                        <div class="text-sm text-blue-800 space-y-1">
                            <p class="flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                support@armportal.com
                            </p>
                            <p class="flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                                +1 (555) 123-4567
                            </p>
                        </div>
                        <p class="text-xs text-blue-600 mt-2">Mon-Fri, 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    If you believe this is an error, please contact support immediately.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>