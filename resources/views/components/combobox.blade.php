<div x-data="dynamicCombobox('{{ route('states.search') }}')" class="relative w-64">
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-900">{{ $label }}</label>
    <div class="relative mt-2">
        <input 
            id="{{ $id }}" 
            type="text" 
            x-model="search" 
            x-on:input.debounce.300ms="fetchItems" 
            x-on:focus="open = true" 
            x-on:keydown.arrow-down.prevent="highlightNext()" 
            x-on:keydown.arrow-up.prevent="highlightPrevious()" 
            x-on:keydown.enter.prevent="selectHighlighted()"
            class="block w-full rounded-md bg-white py-1.5 pl-3 pr-12 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm" 
            role="combobox" 
            aria-controls="{{ $id }}-options" 
            :aria-expanded="open.toString()" 
            placeholder="{{ $placeholder }}"
        >
        <input type="hidden" x-model="selectedId" name="{{ $id }}_value">
        <button 
            type="button" 
            x-on:click="toggleDropdown()" 
            class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
            <svg class="size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10.53 3.47a.75.75 0 0 0-1.06 0L6.22 6.72a.75.75 0 0 0 1.06 1.06L10 5.06l2.72 2.72a.75.75 0 1 0 1.06-1.06l-3.25-3.25Zm-4.31 9.81 3.25 3.25a.75.75 0 0 0 1.06 0l3.25-3.25a.75.75 0 1 0-1.06-1.06L10 14.94l-2.72-2.72a.75.75 0 0 0-1.06 1.06Z" clip-rule="evenodd" />
            </svg>
        </button>
        <ul 
            x-show="open" 
            x-transition 
            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm" 
            id="{{ $id }}-options" 
            role="listbox">
            <template x-for="(item, index) in items" :key="item.id">
                <li 
                    :id="'{{ $id }}-option-' + item.id" 
                    role="option" 
                    :class="{
                        'relative cursor-default select-none py-2 pl-3 pr-9 text-gray-900': true,
                        'bg-indigo-600 text-white': index === highlightedIndex,
                        'font-semibold': selectedId === item.id
                    }"
                    tabindex="-1" 
                    x-on:click="selectItem(item)" 
                    x-on:mouseenter="highlightedIndex = index"
                >
                    <span class="block truncate" x-text="item.name"></span>
                </li>
            </template>
            <li x-show="items.length === 0" class="relative cursor-default select-none py-2 pl-3 pr-9 text-gray-500">
                No results found
            </li>
        </ul>
    </div>
</div>

<script>
    function dynamicCombobox(fetchUrl) {
        return {
            open: false,
            search: '',
            items: [],
            highlightedIndex: 0,
            selectedId: null,
            async fetchItems() {
                if (this.search.length === 0) {
                    this.items = [];
                    return;
                }
                const response = await fetch(`${fetchUrl}?query=${this.search}`);
                if (response.ok) {
                    this.items = await response.json();
                }
            },
            highlightNext() {
                if (this.highlightedIndex < this.items.length - 1) {
                    this.highlightedIndex++;
                }
            },
            highlightPrevious() {
                if (this.highlightedIndex > 0) {
                    this.highlightedIndex--;
                }
            },
            selectHighlighted() {
                if (this.items[this.highlightedIndex]) {
                    this.selectItem(this.items[this.highlightedIndex]);
                }
            },
            selectItem(item) {
                this.search = item.name;
                this.selectedId = item.id;
                this.open = false;
            },
        };
    }
</script>
