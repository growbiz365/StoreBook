<div x-data="{ sidebarOpen: false, profileMenuOpen: false }">
            <!-- Off-canvas menu for mobile, show/hide based on off-canvas menu state -->
        <div
        class="relative z-50 lg:hidden"
        role="dialog"
        aria-modal="true"
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" >
      

      <div class="fixed inset-0 bg-gray-900/80" aria-hidden="true" @click="sidebarOpen = false"></div>
      <div
         class="fixed inset-0 flex"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         >
         <div class="relative mr-16 flex w-full max-w-xs flex-1">
            <!-- Close button -->
            <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
               <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                  <span class="sr-only">Close sidebar</span>
                  <svg
                     class="h-6 w-6 text-white"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor"
                     aria-hidden="true"
                  >
                     <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
               </button>
            </div>
            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div
               class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4"
               >
               <div class="flex h-16 shrink-0 items-center">
                  <a href="{{url('/')}}">
                  <img
                     class="h-14 w-auto"
                     src="{{ asset('images/form-logo.png') }}"
                     alt="Your Company"
                     />
                     </a>
               </div>
               <nav class="flex flex-1 flex-col">
                  <ul role="list" class="flex flex-1 flex-col gap-y-7">
                     <li>
                        <ul role="list" class="-mx-2 space-y-1">
                          
                        
                  <x-navigation-link 
                  href="{{ route('dashboard') }}" 
                  topliclass=""
                  icon="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
                  icon2=""
                  >
                  Dashboard
               </x-navigation-link>

                                       {{-- Arms menu hidden - StoreBook is items-only --}}
                                       {{-- @can('module', 'view arms')
                                     <x-navigation-link href="{{ url('arms-dashboard') }}" topliclass="mt-auto"
                                         icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                         icon2="">
                                             Arms
                                         </x-navigation-link>
                                         @endcan --}}

                                         @can('module', 'view items')
                                         <x-navigation-link href="{{ route('general-items.dashboard') }}" topliclass="mt-auto"
                                         icon="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                         icon2="">
                                             General Items
                                         </x-navigation-link>
                                         @endcan


                                         @can('module', 'view purchases')
                                         <x-navigation-link href="{{ route('purchases.dashboard') }}" topliclass="mt-auto"
                                         icon="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                         icon2="">
                                             Purchases
                                         </x-navigation-link>
                                         @endcan

                                         
                                          @can('module', 'view sales')
                                         <x-navigation-link href="{{ route('sales.dashboard') }}" topliclass="mt-auto"
                                         icon="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"
                                         icon2="">
                                             Sales
                                         </x-navigation-link>
                                         @endcan

                                         @can('module', 'view sales')
                                         <x-navigation-link href="{{ route('approvals.index') }}" topliclass="mt-auto"
                                         icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                         icon2="">
                                             Approvals
                                         </x-navigation-link>
                                         @endcan

                                         

                                    

                                     

                                     



                                    

             
                                     @can('module', 'view parties')
                                     <x-navigation-link href="{{ route('party-management.dashboard') }}" topliclass="mt-auto"
                                     topliclass=""
                  icon="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"
                  icon2="">
                                             Parties
                                         </x-navigation-link>
                                          @endcan
                                          @can('module', 'view banks')
                                         <x-navigation-link href="{{ route('bank-management') }}" topliclass="mt-auto"
    icon="M3 10h18M5 10v10m14-10v10M12 2L2 7h20L12 2zm0 0v6" icon2="">
    Banks
</x-navigation-link>
@endcan
                                          @can('module', 'view expenses')
                                          <x-navigation-link href="{{ route('expenses.dashboard') }}" topliclass="mt-auto"
    icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" icon2="">
    Expenses
</x-navigation-link>
@endcan

                                          
                                          @can('module', 'view other incomes')
                                          <x-navigation-link href="{{ route('other-incomes.index') }}" topliclass="mt-auto"
    icon="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" icon2="">
    Other Incomes
</x-navigation-link>
@endcan




                                         @can('module', 'view finance')
                                         <x-navigation-link href="{{ route('finance.index') }}" topliclass="mt-auto"
                                             icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                             icon2="">
                                             Finance
                                         </x-navigation-link>
                                     @endcan




                                        
             


               

                
                        </ul>
                     </li>

                     
                     
                     @can('view settings')
                     <x-navigation-link 
                        href="{{route('settings')}}"
                        topliclass="mt-auto"
                        icon="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"
                        icon2="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                        >
                        Settings
                     </x-navigation-link>
                     @endcan
                    
                  </ul>
               </nav>
            </div>
         </div>
      </div>
   </div>
   <!-- Static sidebar for desktop -->
   <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
      <!-- Sidebar component, swap this element with another sidebar if you like -->
      <div
         class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4"
         >
          <div class="flex h-16 shrink-0 items-center">
                  <a href="{{route('dashboard')}}">
                  <img
                     class="h-14 w-auto"
                     src="{{ asset('images/form-logo.png') }}"
                     alt="Your Company"
                     />
                     </a>
               </div>
         <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
               
             
               
               
               <li>
                  <ul role="list" class="-mx-2 space-y-1">
                     
                    
                  <x-navigation-link 
                     href="{{ route('dashboard') }}" 
                     topliclass=""
                     icon="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
                     icon2=""
                     >
                     Dashboard
                  </x-navigation-link>


                 

                                         {{-- Arms menu hidden - StoreBook is items-only --}}
                                         {{-- @can('module', 'view arms')
                                         <x-navigation-link href="{{ url('arms-dashboard') }}" topliclass="mt-auto"
                                         icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                         icon2="">
                                             Arms
                                         </x-navigation-link>
                                         @endcan --}}

                                         @can('module', 'view items')
                                         <x-navigation-link href="{{ route('general-items.dashboard') }}" topliclass="mt-auto"
                                         icon="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                         icon2="">
                                             General Items
                                         </x-navigation-link>
                                         @endcan

                                          @can('module', 'view purchases')

                                     <x-navigation-link href="{{ route('purchases.dashboard') }}" topliclass="mt-auto"
                                         icon="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                         icon2="">
                                             Purchases
                                         </x-navigation-link>
                                         @endcan
                                          @can('module', 'view sales')

                                     <x-navigation-link href="{{ route('sales.dashboard') }}" topliclass="mt-auto"
                                         icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                         icon2="">
                                             Sales
                                         </x-navigation-link>
                                         @endcan

                                         @can('module', 'view sales')
                                         <x-navigation-link href="{{ route('approvals.index') }}" topliclass="mt-auto"
                                         icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                         icon2="">
                                             Approvals
                                         </x-navigation-link>
                                         @endcan


                                         

                                         

                                         

                                          @can('module', 'view parties')
                                         <x-navigation-link href="{{ route('party-management.dashboard') }}" topliclass="mt-auto"
                                         topliclass=""
                  icon="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"
                  icon2="">
                                             Parties
                                         </x-navigation-link>
                                          @endcan

                                          @can('module', 'view banks')
                                         <x-navigation-link href="{{ route('bank-management') }}" topliclass="mt-auto"
    icon="M3 10h18M5 10v10m14-10v10M12 2L2 7h20L12 2zm0 0v6" icon2="">
    Banks
</x-navigation-link>
@endcan

                                          @can('module', 'view expenses')
                                          <x-navigation-link href="{{ route('expenses.dashboard') }}" topliclass="mt-auto"
    icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" icon2="">
    Expenses
</x-navigation-link>
@endcan

                                          @can('module', 'view other incomes')
                                          <x-navigation-link href="{{ route('other-incomes.index') }}" topliclass="mt-auto"
    icon="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" icon2="">
    Other Incomes
</x-navigation-link>
@endcan

                                         @can('module', 'view finance')
                                         <x-navigation-link href="{{ route('finance.index') }}" topliclass="mt-auto"
                                             icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                             icon2="">
                                             Finance
                                         </x-navigation-link>
                                         @endcan



                                         

                                         

                                        


                                     

                              
   
                 
   

   
                  
               
                  
               
            
                     
                
                  
                  </ul>
               </li>

            
             @can('view settings')
            <x-navigation-link 
               href="{{route('settings')}}"
               topliclass="mt-auto"
               icon="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"
               icon2="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
               >
               Settings
            </x-navigation-link>
	    @endcan
            
            

            
            </ul>
         </nav>
      </div>
   </div>