@php
    $selected = $selected ?? old('entry_type', 'credit');
@endphp

<div class="mt-0.5 grid grid-cols-2 gap-1.5">
    <label class="relative cursor-pointer" title="In · Party → Bank">
        <input type="radio" name="entry_type" value="credit" class="peer sr-only"
            {{ $selected === 'credit' ? 'checked' : '' }} required>
        <div class="flex h-9 items-center justify-center gap-1 rounded border border-gray-300 px-1 text-[11px] font-medium text-gray-700 transition-colors hover:bg-gray-50 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700">
            <svg class="h-3 w-3 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
            <span class="truncate">Credit (جمـــع)</span>
        </div>
    </label>
    <label class="relative cursor-pointer" title="Out · Bank → Party">
        <input type="radio" name="entry_type" value="debit" class="peer sr-only"
            {{ $selected === 'debit' ? 'checked' : '' }}>
        <div class="flex h-9 items-center justify-center gap-1 rounded border border-gray-300 px-1 text-[11px] font-medium text-gray-700 transition-colors hover:bg-gray-50 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700">
            <svg class="h-3 w-3 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
            <span class="truncate">Debit (بنـــام)</span>
        </div>
    </label>
</div>
