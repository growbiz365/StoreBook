<li class="col-span-1 flex rounded-md shadow-sm">
    <div class="flex w-16 shrink-0 items-center justify-center rounded-l-md text-sm font-medium text-white {{ $bgColor }}">
        {{ $initials }}
    </div>
    <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white">
        <div class="flex-1 truncate px-4 py-2 text-sm">
            <a href="{{ $url }}" class="font-medium text-gray-900 hover:text-gray-600">{{ $title }}</a>
            <p class="text-gray-500">{{ $subtitle }}</p>
        </div>
    </div>
</li>
