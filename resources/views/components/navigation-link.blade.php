<li class="{{ $topliclass }}">
    <a
        href="{{ $href }}"
        class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold 
        {{ $active ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}"
    >
        <svg
            class="size-6 shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-700' }}"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            aria-hidden="true"
            data-slot="icon"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="{{ $icon }}"
            />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="{{ $icon2 }}"
            />
        </svg>
        {{ $slot }}
    </a>
</li>
