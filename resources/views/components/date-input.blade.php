@props(['id', 'name', 'value'])

<input 
    type="date" 
    name="{{ $name }}" 
    id="{{ $id }}" 
    value="{{ old($name, $value) }}" 
    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
/>
