<x-app-layout>
    @section('title', 'General Items List - General Items Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'],['url' => '/general-items-dashboard', 'label' => 'General Items Dashboard'], ['url' => '#', 'label' => 'General Items']]" />

    <x-dynamic-heading title="General Items">
        <x-slot name="actions">
            <a href="{{ route('general-items.export', request()->query()) }}"
                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-center text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 whitespace-nowrap">
                Export CSV
            </a>
        </x-slot>
    </x-dynamic-heading>

    <div class="space-y-4 pb-8">
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-white">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <form method="GET" action="{{ route('general-items.index') }}" class="w-full lg:flex-1">
                        @foreach(request()->except(['item_name', 'item_type_id', 'item_code', 'page', 'status']) as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                            <div class="w-full">
                                <input
                                    type="text"
                                    name="item_name"
                                    value="{{ request('item_name') }}"
                                    placeholder="Filter by item name..."
                                    class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                />
                            </div>

                            <div class="w-full">
                                <select name="status" class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="" @selected(request('status') === null || request('status') === '')>All statuses</option>
                                    <option value="active" @selected(request('status') === 'active')>Active only</option>
                                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive only</option>
                                </select>
                            </div>

                            <div class="w-full">
                                <select name="item_type_id" class="chosen-select w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">All Item Types</option>
                                    @foreach($itemTypes ?? [] as $type)
                                        <option value="{{ $type->id }}" @selected((string)request('item_type_id') === (string)$type->id)>
                                            {{ $type->item_type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-full">
                                <input
                                    type="text"
                                    name="item_code"
                                    value="{{ request('item_code') }}"
                                    placeholder="Filter by item code..."
                                    class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                />
                            </div>

                            <div class="flex gap-2 justify-start lg:justify-start">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Apply
                                </button>

                                <a href="{{ route('general-items.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:bg-gray-50 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    @can('create items')
                        <div class="w-full lg:w-auto lg:ml-2 lg:shrink-0">
                            <x-button
                                href="{{ route('general-items.create') }}"
                                class="w-full justify-center sm:w-auto lg:whitespace-nowrap bg-emerald-600 hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus-visible:outline-emerald-600 focus:ring-emerald-500"
                            >
                                Add General Item
                            </x-button>
                        </div>
                    @endcan
                </div>
            </div>

    @if (Session::has('success'))
        <x-success-alert message="{{ Session::get('success') }}" />
    @endif

    @if (Session::has('error'))
        <x-error-alert message="{{ Session::get('error') }}" />
    @endif

    @if ($errors->has('delete_error'))
        <x-error-alert message="{{ $errors->first('delete_error') }}" />
    @endif

            <form id="bulk-barcode-print-form" method="GET" action="{{ route('general-items.barcode-labels') }}" target="_blank">
                <input type="hidden" name="auto_print" value="1">
                <div id="bulk-label-toolbar"
                    class="hidden px-4 py-2 border-b border-emerald-100 bg-emerald-50/60 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
                    <span id="bulk-selection-count" class="font-medium text-emerald-800 tabular-nums">0 selected</span>
                    <div class="flex flex-wrap items-center gap-2 ml-auto">
                        <label class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                            <span>Layout</span>
                            <select name="layout" class="rounded-md border-gray-300 py-1 pl-2 pr-7 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="thermal">Thermal</option>
                                <option value="a4">A4 sheet</option>
                            </select>
                        </label>
                        <label class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                            <span>Copies</span>
                            <input type="number" name="copies" value="1" min="1" max="50"
                                class="w-14 rounded-md border-gray-300 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                        </label>
                        <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-600 text-xs font-semibold text-white hover:bg-emerald-700">
                            Print labels
                        </button>
                        <button type="button" id="bulk-selection-clear"
                            class="text-xs text-gray-500 hover:text-gray-800 underline underline-offset-2">
                            Clear
                        </button>
                    </div>
                </div>
            </form>

            <div class="overflow-hidden">
            <table class="w-full table-fixed divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-3 w-9">
                            <input type="checkbox" id="select_all_items" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                title="Select all goods on this page">
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">
                            <a href="{{ route('general-items.index', array_merge(request()->query(), ['sort_by' => 'item_code', 'sort_order' => request('sort_by') == 'item_code' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Item Code</span>
                                @if(request('sort_by') == 'item_code')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        @if(request('sort_order') == 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('general-items.index', array_merge(request()->query(), ['sort_by' => 'item_name', 'sort_order' => request('sort_by') == 'item_name' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Item Name</span>
                                @if(request('sort_by') == 'item_name')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        @if(request('sort_order') == 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">Kind</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Item Type</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Available stock</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">
                            <a href="{{ route('general-items.index', array_merge(request()->query(), ['sort_by' => 'cost_price', 'sort_order' => request('sort_by') == 'cost_price' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Cost Price</span>
                                @if(request('sort_by') == 'cost_price')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        @if(request('sort_order') == 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">
                            <a href="{{ route('general-items.index', array_merge(request()->query(), ['sort_by' => 'sale_price', 'sort_order' => request('sort_by') == 'sale_price' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Sale Price</span>
                                @if(request('sort_by') == 'sale_price')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        @if(request('sort_order') == 'asc')
                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($generalItems as $item)
                        <tr class="hover:bg-gray-50 cursor-pointer {{ $item->is_active ? '' : 'bg-gray-50/80' }}" onclick="window.location.href='{{ route('general-items.show', $item->id) }}'">
                            <td class="px-2 py-3 align-middle" onclick="event.stopPropagation()">
                                @if($item->isGoods())
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}" form="bulk-barcode-print-form"
                                        class="item-print-checkbox rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                @endif
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->item_code }}</div>
                            </td>
                            <td class="px-3 py-3">
                                <div class="text-sm font-medium text-gray-900 break-words">{{ $item->item_name }}</div>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                @if($item->isService())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Service</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Goods</span>
                                @endif
                                @if($item->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-1">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-700 ml-1">Inactive</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="text-sm text-gray-900 break-words">{{ $item->itemType->item_type ?? '—' }}</div>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-right">
                                @if($item->isService())
                                    <div class="text-sm text-gray-400">—</div>
                                @else
                                    @php $availableStock = $item->getAvailableStockQuantity(); @endphp
                                    <div class="text-sm text-gray-900">{{ \App\Support\StockQuantity::format($availableStock) }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                @if($item->isService())
                                    <div class="text-sm text-gray-400">—</div>
                                @else
                                    <div class="text-sm text-gray-900">{{ formatBusinessCurrency($item->cost_price, true, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ formatBusinessCurrency($item->sale_price, true, 2) }}</div>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3" onclick="event.stopPropagation()">
                                    @can('edit items')
                                    <a href="{{ route('general-items.edit', $item) }}"
                                       class="text-indigo-600 hover:text-indigo-900"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @if($item->isGoods())
                                    <a href="{{ route('general-items.edit-opening-stock', $item->id) }}"
                                       class="text-amber-600 hover:text-amber-900"
                                       title="Edit opening stock">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                    </a>
                                    @endif
                                    <form action="{{ route('general-items.update-status', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $item->is_active ? '0' : '1' }}">
                                        <button type="submit"
                                                class="{{ $item->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}"
                                                title="{{ $item->is_active ? 'Deactivate item' : 'Activate item' }}">
                                            @if($item->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $generalItems->links() }}
    </div>
</x-app-layout>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<style>
    /* Make Chosen match Tailwind input styles used in filters */
    .chosen-container { width: 100% !important; }
    .chosen-container-single .chosen-single {
        height: 40px;
        line-height: 38px;
        border: 1px solid #d1d5db; /* border-gray-300 */
        border-radius: 0.375rem; /* rounded-md */
        padding: 0 2.25rem 0 0.75rem;
        background: #fff;
        font-size: 0.875rem; /* text-sm */
        color: #111827; /* text-gray-900 */
        box-shadow: none;
    }
    .chosen-container-single .chosen-single div { right: 0.5rem; }
    .chosen-container-active .chosen-single {
        border-color: #6366f1; /* indigo-500 */
        box-shadow: 0 0 0 1px rgba(99,102,241,0.2);
    }
    .chosen-container .chosen-drop {
        border-color: #e5e7eb; /* gray-200 */
        border-radius: 0.375rem;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }
    .chosen-container .chosen-search input {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0 0.75rem;
        box-shadow: none;
    }
</style>
<script>
    $(function () {
        $('.chosen-select').chosen({
            width: '100%',
            search_contains: true,
            allow_single_deselect: true,
            placeholder_text_single: 'All Item Types'
        });

        const toolbar = document.getElementById('bulk-label-toolbar');
        const countEl = document.getElementById('bulk-selection-count');
        const selectAll = document.getElementById('select_all_items');
        const clearBtn = document.getElementById('bulk-selection-clear');
        const printForm = document.getElementById('bulk-barcode-print-form');
        const itemChecks = () => Array.from(document.querySelectorAll('.item-print-checkbox'));

        function updateBulkToolbar() {
            const checked = itemChecks().filter(cb => cb.checked);
            const count = checked.length;
            if (countEl) {
                countEl.textContent = count === 1 ? '1 item selected' : `${count} items selected`;
            }
            if (toolbar) {
                toolbar.classList.toggle('hidden', count === 0);
            }
            if (selectAll) {
                const goods = itemChecks();
                selectAll.indeterminate = count > 0 && count < goods.length;
                selectAll.checked = goods.length > 0 && count === goods.length;
            }
        }

        itemChecks().forEach(cb => cb.addEventListener('change', updateBulkToolbar));
        selectAll?.addEventListener('change', function () {
            itemChecks().forEach(cb => { cb.checked = selectAll.checked; });
            updateBulkToolbar();
        });
        clearBtn?.addEventListener('click', function () {
            itemChecks().forEach(cb => { cb.checked = false; });
            if (selectAll) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
            updateBulkToolbar();
        });
        printForm?.addEventListener('submit', function (e) {
            if (itemChecks().some(cb => cb.checked)) {
                return;
            }
            e.preventDefault();
            alert('Select at least one goods item to print labels.');
        });
    });
</script>
