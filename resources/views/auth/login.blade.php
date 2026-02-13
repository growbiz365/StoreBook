<x-guest-layout>
    <!-- Logo -->
    <div class="text-center mb-4 sm:mb-5 lg:mb-6">
        <a href="/" class="inline-block logo-container">
            <img
                class="logo-img h-16 sm:h-14 lg:h-16 w-auto mx-auto"
                src="{{ asset('images/form-logo.png') }}"
                alt="StoreBook365"
            />
        </a>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-2 sm:mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-3 sm:space-y-4 lg:space-y-5">
        @csrf

        <!-- Email or Username -->
        <div>
            <label for="login" class="text-purple-700 font-semibold text-xs sm:text-sm mb-1.5 sm:mb-2 block">{{ __('Email or Username') }}</label>
            <div class="relative">
                <input 
                    id="login" 
                    class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2.5 sm:py-3 pr-10 placeholder-gray-400 shadow-sm focus:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-600/20 text-sm sm:text-base transition-colors text-slate-900 min-h-[44px]" 
                    type="text" 
                    name="login" 
                    value="{{ old('login') }}" 
                    autofocus 
                    autocomplete="username"
                    placeholder="Enter your email or username" 
                />
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            @error('login')
                <p class="mt-1 text-xs text-red-600 flex items-center">
                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="text-purple-700 font-semibold text-xs sm:text-sm mb-1.5 sm:mb-2 block">{{ __('Password') }}</label>
            <div class="relative">
                <input 
                    id="password" 
                    class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2.5 sm:py-3 pr-10 placeholder-gray-400 shadow-sm focus:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-600/20 text-sm sm:text-base transition-colors text-slate-900 min-h-[44px]"
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    placeholder="Enter your password" 
                />
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-red-600 flex items-center">
                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="rounded border-purple-300 text-purple-700 shadow-sm focus:ring-purple-600 focus:ring-offset-0 cursor-pointer h-4 w-4 transition duration-200 flex-shrink-0" 
                    name="remember"
                >
                <span class="ml-2 text-xs sm:text-sm text-purple-600 group-hover:text-purple-800 transition duration-200">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs sm:text-sm text-purple-600 hover:text-purple-800 font-medium transition duration-200 hover:underline whitespace-nowrap" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div class="pt-1 sm:pt-2">
            <button type="submit" class="w-full group relative flex justify-center items-center px-4 py-2.5 sm:py-3 bg-gradient-to-r from-purple-600 via-purple-700 to-teal-600 hover:from-purple-700 hover:via-purple-800 hover:to-teal-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 text-sm sm:text-base min-h-[44px]">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-purple-200 group-hover:text-white transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </span>
                <span>{{ __('Sign in to Dashboard') }}</span>
            </button>
        </div>

        
    </form>
</x-guest-layout>
