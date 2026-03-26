<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'inline-flex w-full sm:w-auto items-center justify-center rounded-md bg-indigo-600 px-4 py-2 font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}
>
    {{ $slot }}
</a>
