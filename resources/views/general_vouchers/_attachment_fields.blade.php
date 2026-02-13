<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="attachment_titles[]">Title</x-input-label>
        <x-text-input type="text" name="attachment_titles[]" class="mt-1 block w-full" />
    </div>
    <div>
        <x-input-label for="attachment_files[]">File</x-input-label>
        <input type="file" name="attachment_files[]"
            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
    </div>
</div>