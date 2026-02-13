

<nav class="flex py-2" aria-label="Breadcrumb">
    <ol role="list" class="flex items-center space-x-4">
        @foreach ($breadcrumbs as $breadcrumb)
            <li>
                <div class="flex items-center">
                    @if (!$loop->first)
                        <svg class="size-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    @endif
                    <a href="{{ $breadcrumb['url'] }}" class="ml-4 text-sm font-bold text-gray-500 hover:text-gray-700 {{ $loop->last ? 'aria-current="page"' : '' }}">
                        {{ $breadcrumb['label'] }}
                    </a>
                </div>
            </li>
        @endforeach
    </ol>
</nav>
