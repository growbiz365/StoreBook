<x-app-layout>
    @section('title', 'Edit Party - Party Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/party-management', 'label' => 'Party Management'],['url' => '/parties', 'label' => 'Parties'], ['url' => '#', 'label' => 'Edit Party']]" />

    <x-dynamic-heading title="Edit Party" />

    <form action="{{ route('parties.update', $party) }}" method="POST" id="editPartyForm">
        @csrf
        @method('PUT')

        {{-- Error Display --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Whoops! Something went wrong.</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-6">
            <h2 class="text-md font-semibold text-gray-900 mb-4">Party Information</h2>
            <p class="text-sm text-gray-600 mb-6">Update the party details below.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-4">
                <!-- Name -->
                <div>
                    <x-input-label for="name">Name <span class="text-red-500">*</span></x-input-label>
                    <input type="text" id="name" name="name" value="{{ old('name', $party->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                </div>

                <!-- Phone -->
                <div>
                    <x-input-label for="phone_no">Phone Number</x-input-label>
                    <input type="text" id="phone_no" name="phone_no" value="{{ old('phone_no', $party->phone_no) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        data-mask="0000-0000000" placeholder="03XX-XXXXXXX" />
                </div>

                <!-- WhatsApp -->
                <div>
                    <x-input-label for="whatsapp_no">WhatsApp Number</x-input-label>
                    <input type="text" id="whatsapp_no" name="whatsapp_no" value="{{ old('whatsapp_no', $party->whatsapp_no) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        data-mask="0000-0000000" placeholder="03XX-XXXXXXX" />
                </div>

                <!-- CNIC -->
                <div>
                    <x-input-label for="cnic">CNIC</x-input-label>
                    <input type="text" id="cnic" name="cnic" value="{{ old('cnic', $party->cnic) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        data-mask="00000-0000000-0" placeholder="XXXXX-XXXXXXX-X" />
                </div>

                <!-- NTN -->
                <div>
                    <x-input-label for="ntn">NTN</x-input-label>
                    <input type="text" id="ntn" name="ntn" value="{{ old('ntn', $party->ntn) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                </div>

                <!-- Address -->
                <div>
                    <x-input-label for="address">Address</x-input-label>
                    <input type="text" id="address" name="address" value="{{ old('address', $party->address) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                </div>

                <!-- Opening Balance -->
                <div>
                    <x-input-label for="opening_balance">Opening Balance</x-input-label>
                    <input type="number" id="opening_balance" name="opening_balance" step="1" value="{{ old('opening_balance', round($party->opening_balance)) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0" />
                </div>

                <!-- Opening Type -->
                <div>
                    <x-input-label for="opening_type">Opening Type</x-input-label>
                    <select id="opening_type" name="opening_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Opening Type</option>
                        <option value="credit" {{ old('opening_type', $party->opening_type) === 'credit' ? 'selected' : '' }}>Credit (Jama)</option>
                        <option value="debit" {{ old('opening_type', $party->opening_type) === 'debit' ? 'selected' : '' }}>Debit (Banam)</option>
                    </select>
                </div>

                <!-- Opening Date -->
                <div>
                    <x-input-label for="opening_date">Opening Date</x-input-label>
                    <input type="date" id="opening_date" name="opening_date" value="{{ old('opening_date', $party->opening_date ? $party->opening_date->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                </div>

                <!-- Status -->
                <div>
                    <x-input-label for="status">Status <span class="text-red-500">*</span></x-input-label>
                    <select id="status" name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="1" {{ old('status', $party->status) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $party->status) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4">
                <a href="{{ route('parties.index') }}" class="rounded-md bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-500">Update</button>
            </div>
        </div>
    </form>
</x-app-layout>

{{-- Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(function() {
    $('[name="phone_no"]').mask('0000-0000000');
    $('[name="whatsapp_no"]').mask('0000-0000000');
    $('[name="cnic"]').mask('00000-0000000-0');
});
</script>
