@props([
    'name' => 'party_id',
    'id' => null,
    'inputId' => null,
    'value' => null,
    'display' => '',
    'required' => false,
    'placeholder' => 'Search by name or pcode...',
    'inputClass' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm',
    'excludePartyId' => null,
])

@php
    $hiddenId = $id ?: $name;
    $visibleInputId = $inputId ?: ($hiddenId . '_search');
    $selectedValue = old($name, $value);
    $selectedDisplay = $display;
@endphp

@include('partials.party-searchable-dropdown-script')
@include('partials.ajax-party-select-styles')

<div
    class="ajax-party-select searchable-select-container relative"
    data-ajax-party-select
    @if($excludePartyId) data-exclude-party-id="{{ $excludePartyId }}" @endif
>
    <input
        type="text"
        id="{{ $visibleInputId }}"
        class="searchable-input bg-white {{ $inputClass }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        value="{{ $selectedDisplay }}"
    >
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $hiddenId }}"
        class="selected-item-id"
        value="{{ $selectedValue }}"
        @if($required) required @endif
    >
    <div class="searchable-dropdown hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-52 overflow-hidden">
        <div class="search-results-container max-h-44 overflow-y-auto"></div>
        <div class="pagination-container hidden border-t border-gray-100 p-2 bg-gray-50">
            <div class="flex justify-between items-center text-xs">
                <button type="button" class="prev-page text-indigo-600 hover:text-indigo-800 disabled:opacity-40 px-2 py-1 rounded">Previous</button>
                <span class="page-info text-gray-500 text-xs"></span>
                <button type="button" class="next-page text-indigo-600 hover:text-indigo-800 disabled:opacity-40 px-2 py-1 rounded">Next</button>
            </div>
        </div>
    </div>
    <div class="loading-indicator hidden absolute right-2 top-1/2 -translate-y-1/2">
        <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>
