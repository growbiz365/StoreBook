<x-app-layout>
  @section('title', 'Settings - StoreBook')
  <x-breadcrumb :breadcrumbs="[
    ['url' => '/', 'label' => 'Home'],
    ['url' => '#', 'label' => 'Settings'],
]" />

  <x-dynamic-heading title="Settings" />  

  <div class="mb-10 p-10 mt-5 bg-white py-5">
        <h3 class="text-base py-5 font-semibold text-gray-900">Master Files</h3>
        <div>
            <ul role="list" class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-4">
                @can('view countries')
                    <x-setting-link initials="MC" url="{{ url('countries') }}" title="Manage" subtitle="Countries"
                        bgColor="bg-green-500" />
                @endcan

                @can('view cities')
                    <x-setting-link initials="MC" url="{{ url('cities') }}" title="Manage" subtitle="Cities"
                        bgColor="bg-yellow-500" />
                @endcan

                @can('view timezones')
                    <x-setting-link initials="MT" url="{{ url('timezones') }}" title="Manage" subtitle="Timezones"
                        bgColor="bg-purple-600" />
                @endcan

                @can('view currencies')
                    <x-setting-link initials="MC" url="{{ url('currencies') }}" title="Manage" subtitle="Currencies"
                        bgColor="bg-yellow-500" />
                @endcan

                @can('view packages')
                    <x-setting-link initials="MC" url="{{ url('packages') }}" title="Manage" subtitle="Packages"
                        bgColor="bg-yellow-800" />
                @endcan

                @can('module','view businesses')
                    <x-setting-link initials="MC" url="{{ url('businesses') }}" title="Manage" subtitle="Businesses"
                        bgColor="bg-yellow-800" />
                        @endcan

                        @can('view subusers')
                        <x-setting-link initials="SB" url="{{ url('subusers') }}" title="Manage" subtitle="Sub Users"
                        bgColor="bg-green-500" />
                        @endcan
                        
                        <x-setting-link initials="AL" url="{{ url('activity-logs') }}" title="Activity" subtitle="Logs"
                        bgColor="bg-green-900" />
                        

               

                  @can('view modules')
                  <x-setting-link initials="MM" url="{{ url('modules') }}" title="Manage" subtitle="Modules"
                  bgColor="bg-purple-600" />
                   @endcan


                   <!-- <x-setting-link initials="MB" url="{{ url('banks') }}" title="Manage" subtitle="Banks"
                  bgColor="bg-purple-800" /> -->
               



                

            </ul>
        </div>
    </div>



    <!-- <div class="mb-10 p-10 mt-5 bg-white py-5">
        <h3 class="text-base py-5 font-semibold text-gray-900">Arms Management</h3>
        <div>
            <ul role="list" class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-4">
                  @can('view arm_types')
                    <x-setting-link initials="AM" url="{{ url('arms-types') }}" title="Arm" subtitle="Types"
                        bgColor="bg-green-800" /> 
                        @endcan

                        @can('view arm_categories')
                        <x-setting-link initials="AC" url="{{ url('arms-categories') }}" title="Arm" subtitle="Categories"
                        bgColor="bg-green-600" /> 
                        @endcan

                        @can('view arm_makes')
                        <x-setting-link initials="AM" url="{{ url('arms-makes') }}" title="Arm" subtitle="Makes"
                        bgColor="bg-green-500" /> 
                        @endcan

                        @can('view arm_calibers')
                        <x-setting-link initials="AC" url="{{ url('arms-calibers') }}" title="Arm" subtitle="Calibers"
                        bgColor="bg-green-900" /> 
                        @endcan

                        @can('view arm_conditions')
                        <x-setting-link initials="AC" url="{{ url('arms-conditions') }}" title="Arm" subtitle="Conditions"
                        bgColor="bg-green-700" /> 
                        @endcan

            </ul>
        </div>
    </div> -->




  @can('view user management')
  <div class="mb-10 p-10 mt-5 bg-white py-5">
    <h3 class="text-base py-5 font-semibold text-gray-900">User Management</h3>
    <div>
      <ul role="list" class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-4">
        @can('view users')
        <x-setting-link
          initials="MU"
          url="{{ url('users')}}"
          title="Manage"
          subtitle="Users"
          bgColor="bg-purple-600"
        />
    @endcan
    
        @can('view roles')
        <x-setting-link
          initials="MR"
          url="{{ url('roles')}}"
          title="Manage"
          subtitle="Roles"
          bgColor="bg-yellow-500"
        />
        @endcan

        @can('view permissions')
        <x-setting-link
          initials="MP"
          url="{{ url('permissions')}}"
          title="Manage"
          subtitle="Permission"
          bgColor="bg-green-500"
        />
        @endcan

      </ul>
    </div>
  </div>
  @endcan
</x-app-layout>
