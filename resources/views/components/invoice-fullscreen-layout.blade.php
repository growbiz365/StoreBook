@props([
    'pageTitle' => 'Sale invoice',
])
<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-hidden bg-slate-100 antialiased">
    <div id="invoice-fullscreen-root" class="h-full w-full overflow-y-auto overflow-x-hidden">
        {{ $slot }}
    </div>
</body>
</html>
