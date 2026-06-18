<div class="grid grid-cols-2 gap-2">
    <div>
        <x-input-label for="attachment_titles[]" class="!text-xs">Title</x-input-label>
        <x-text-input type="text" name="attachment_titles[]" class="mt-1 block w-full text-sm" />
    </div>
    <div>
        <x-input-label for="attachment_files[]" class="!text-xs">File</x-input-label>
        <input type="file" name="attachment_files[]"
            class="mt-1 block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
    </div>
</div>
