<x-app-layout>
    @section('title', 'Add Party - Party Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/party-management', 'label' => 'Party Management'],['url' => '/parties', 'label' => 'Parties'], ['url' => '#', 'label' => 'Add Party']]" />

    <x-dynamic-heading title="Add Party" />

    <form action="{{ route('parties.store') }}" method="POST" id="partyForm">
        @csrf

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

        @if (Session::has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Error!</strong>
                <p>{{ Session::get('error') }}</p>
            </div>
        @endif

        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-6">
            <h2 class="text-md font-semibold text-gray-900 mb-4">Party Information</h2>
            <p class="text-sm text-gray-600 mb-6">Please provide details of the party.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-4">
                <!-- Name -->
                <div>
                    <x-input-label for="name">Name <span class="text-red-500">*</span></x-input-label>
                    <x-text-input id="name" name="name" value="{{ old('name') }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Phone -->
                <div>
                    <x-input-label for="phone_no">Phone Number</x-input-label>
                    <x-text-input id="phone_no" name="phone_no" value="{{ old('phone_no') }}" data-mask="0000-0000000" placeholder="03XX-XXXXXXX" />
                    @error('phone_no') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- WhatsApp -->
                <div>
                    <x-input-label for="whatsapp_no">WhatsApp Number</x-input-label>
                    <x-text-input id="whatsapp_no" name="whatsapp_no" value="{{ old('whatsapp_no') }}" data-mask="0000-0000000" placeholder="03XX-XXXXXXX" />
                    @error('whatsapp_no') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- CNIC -->
                <div>
                    <x-input-label for="cnic">CNIC</x-input-label>
                    <x-text-input id="cnic" name="cnic" value="{{ old('cnic') }}" data-mask="00000-0000000-0" placeholder="XXXXX-XXXXXXX-X" />
                    @error('cnic') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- NTN -->
                <div>
                    <x-input-label for="ntn">NTN</x-input-label>
                    <x-text-input id="ntn" name="ntn" value="{{ old('ntn') }}" />
                    @error('ntn') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Address -->
                <div>
                    <x-input-label for="address">Address</x-input-label>
                    <x-text-input id="address" name="address" value="{{ old('address') }}" />
                    @error('address') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Opening Balance -->
                <div>
                    <x-input-label for="opening_balance">Opening Balance</x-input-label>
                    <x-text-input id="opening_balance" type="number" step="1" name="opening_balance" value="{{ old('opening_balance') }}" placeholder="0" />
                    @error('opening_balance') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Opening Type -->
                <div>
                    <x-input-label for="opening_type">Opening Type</x-input-label>
                    <select id="opening_type" name="opening_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Opening Type</option>
                        <option value="credit" {{ old('opening_type') === 'credit' ? 'selected' : '' }}>Credit (Jama)</option>
                        <option value="debit" {{ old('opening_type') === 'debit' ? 'selected' : '' }}>Debit (Banam)</option>
                    </select> 
                    <x-input-error :messages="$errors->get('opening_type')" class="mt-2" />
                </div>

                <!-- Opening Date -->
                <div>
                    <x-input-label for="opening_date">Opening Date</x-input-label>
                    <x-text-input id="opening_date" type="date" name="opening_date" value="{{ old('opening_date', date('Y-m-d')) }}" />
                    @error('opening_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Status -->
                <div>
                    <x-input-label for="status">Status <span class="text-red-500">*</span></x-input-label>
                    <select id="status" name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex items-center justify-end gap-x-4">
                <a href="{{ route('parties.index') }}" class="rounded-md bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-500">Save</button>
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
