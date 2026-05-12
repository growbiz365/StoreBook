@props(['title'])

<div class="px-4 py-5">
    <div class="md:flex md:items-center md:justify-between md:gap-4">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                {{ $title }}
            </h2>
        </div>
        @isset($actions)
            <div class="mt-4 flex shrink-0 items-center justify-end md:mt-0 md:ml-4">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
