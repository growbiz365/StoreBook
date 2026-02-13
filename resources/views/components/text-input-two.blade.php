    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $id }}" 
        value="{{ old($name, $value) }}" 
        placeholder="{{ $placeholder }}" 
        autocomplete="{{ $autocomplete }}" 
        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 {{ $errors->has($name) ? 'outline-red-600 focus:outline-red-600' : '' }}"
    />
    @error($name)
        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
    @enderror
