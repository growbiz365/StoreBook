@props([
    'name' => 'customer',
    'id' => null,
    'selectedParty' => null,
    'inputClass' => 'w-full px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500',
    'placeholder' => 'Search party...',
])

@php
    $fieldId = $id ?: $name;
    $partyDisplay = $selectedParty ? $selectedParty->display_label : '';
@endphp

<x-ajax-party-select
    :name="$name"
    :id="$fieldId"
    :input-id="$fieldId . '_search'"
    :value="request($name)"
    :display="$partyDisplay"
    :required="false"
    :input-class="$inputClass"
    :placeholder="$placeholder"
/>
