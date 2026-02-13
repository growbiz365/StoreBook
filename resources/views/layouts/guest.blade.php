<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'StoreBook') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            /* Responsive adjustments for login page */
            @media (max-width: 640px) {
                /* Ensure form fits on small screens */
                body {
                    overflow-y: auto;
                }
                
                /* Compact spacing on mobile */
                .space-y-3 > * + * {
                    margin-top: 0.75rem;
                }
            }
            
            /* Prevent zoom on iOS for inputs */
            @media (max-width: 768px) {
                input[type="text"],
                input[type="password"],
                input[type="email"],
                select,
                textarea {
                    font-size: 16px !important;
                }
            }
            
            /* Smooth scrolling */
            html {
                scroll-behavior: smooth;
            }
            
            /* Remove white background from logo */
            .logo-img {
                background: transparent;
                mix-blend-mode: multiply;
                filter: contrast(1.05) brightness(0.98);
            }
            
            /* Ensure logo blends well on light background */
            @media (prefers-color-scheme: light) {
                .logo-img {
                    mix-blend-mode: multiply;
                }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased overflow-hidden">
        <div class="min-h-screen flex flex-col lg:flex-row bg-gray-50">
            <!-- Left Column - Login Form -->
            <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 overflow-y-auto">
                <div class="w-full max-w-md py-4 sm:py-6 lg:py-8">
                    {{ $slot }}
                </div>
            </div>

            <!-- Right Column - Branding -->
            <div class="hidden lg:flex lg:flex-1 bg-gradient-to-br from-purple-700 via-purple-800 to-teal-700 relative overflow-hidden min-h-screen">
                <!-- Decorative Background Elements -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-10 right-20 w-64 h-64 bg-purple-400 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-10 left-20 w-80 h-80 bg-teal-500 rounded-full blur-3xl"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-56 h-56">
                        <svg viewBox="0 0 100 100" class="w-full h-full opacity-20">
                            <circle cx="50" cy="50" r="40" fill="none" stroke="white" stroke-width="0.5"/>
                            <circle cx="50" cy="50" r="30" fill="none" stroke="white" stroke-width="0.5"/>
                            <circle cx="50" cy="50" r="20" fill="none" stroke="white" stroke-width="0.5"/>
                            <circle cx="50" cy="50" r="10" fill="none" stroke="white" stroke-width="0.5"/>
                        </svg>
                    </div>
                </div>

                <!-- Content -->
                <div class="relative z-10 flex flex-col justify-center items-start p-6 xl:p-8 2xl:p-12 text-white overflow-y-auto">
                    <div class="mb-6 xl:mb-8">
                        <div class="inline-flex items-center justify-center w-12 h-12 xl:w-14 xl:h-14 bg-white/20 backdrop-blur-sm rounded-xl mb-3 xl:mb-4 shadow-lg">
                            <svg class="w-6 h-6 xl:w-8 xl:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl xl:text-3xl 2xl:text-4xl font-bold mb-2 xl:mb-3 leading-tight">
                            Store<span class="text-teal-300">Book</span>
                        </h1>
                        <p class="text-base xl:text-lg 2xl:text-xl text-purple-100 mb-1 xl:mb-1.5">
                            Inventory Management System
                        </p>
                        <p class="text-xs xl:text-sm 2xl:text-base text-purple-200 opacity-90 leading-relaxed">
                            Your comprehensive platform for inventory and business management
                        </p>
                    </div>

                    <div class="space-y-4 xl:space-y-5 w-full max-w-md">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 xl:w-9 xl:h-9 bg-white/20 backdrop-blur-sm rounded-lg">
                                    <svg class="w-4 h-4 xl:w-5 xl:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm xl:text-base font-semibold mb-0.5">Secure & Reliable</h3>
                                <p class="text-xs xl:text-sm text-purple-200 leading-relaxed">
                                    Enterprise-grade security with advanced encryption and compliance standards
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 xl:w-9 xl:h-9 bg-white/20 backdrop-blur-sm rounded-lg">
                                    <svg class="w-4 h-4 xl:w-5 xl:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm xl:text-base font-semibold mb-0.5">Easy Management</h3>
                                <p class="text-xs xl:text-sm text-purple-200 leading-relaxed">
                                    Streamlined workflows and intuitive interface for efficient operations
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 xl:w-9 xl:h-9 bg-white/20 backdrop-blur-sm rounded-lg">
                                    <svg class="w-4 h-4 xl:w-5 xl:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm xl:text-base font-semibold mb-0.5">Professional Dashboard</h3>
                                <p class="text-xs xl:text-sm text-purple-200 leading-relaxed">
                                    Real-time insights and comprehensive reporting for informed decisions
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 xl:mt-8 pt-4 xl:pt-6 border-t border-white/20">
                        <p class="text-xs text-purple-300">
                            &copy; {{ date('Y') }} StoreBook. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>