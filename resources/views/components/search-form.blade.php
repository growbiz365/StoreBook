<form action="{{ $action }}" method="GET" class="flex w-full max-w-md">
    <div class="relative flex-grow">
        <span class="relative isolate block">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                class="relative block w-full appearance-none rounded-l-lg pl-10 px-[calc(theme(spacing[3.5])-1px)] py-[calc(theme(spacing[2.5])-1px)] text-base/6 text-zinc-950 placeholder:text-zinc-500 border border-zinc-950/10 bg-transparent dark:bg-white/5 focus:outline-none focus:ring-1 focus:ring-blue-500" 
                placeholder="{{ $placeholder }}"
            >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 16 16"
                fill="currentColor"
                aria-hidden="true"
                class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-zinc-500 dark:text-zinc-400 pointer-events-none"
            >
                <path
                    fill-rule="evenodd"
                    d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                    clip-rule="evenodd"
                ></path>
            </svg>
        </span>
    </div>
    <button
        type="submit"
        class="flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:outline-none"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            class="w-5 h-5"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"
            />
        </svg>
    </button>
</form>
