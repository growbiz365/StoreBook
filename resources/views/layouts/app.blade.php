<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'StoreBook'))</title>

    
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        
    </head>
    <body class="h-full bg-slate-50">

      @include('layouts.sidebar')

    @include('layouts.header')
    
    <main class="py-2 ">
         <div class="px-4 sm:px-6 lg:px-8">
            {{$slot}}
         </div>
      </main>
   </div>
</div>
         
        
    </body>
</html>
