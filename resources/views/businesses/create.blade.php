<x-app-layout>
    @section('title', 'Create Business - Settings - StoreBook')
    <x-breadcrumb :breadcrumbs="[['url' => '/', 'label' => 'Home'], ['url' => '/settings', 'label' => 'Settings'], ['url' => route('businesses.index'), 'label' => 'Businesses'], ['url' => '#', 'label' => 'Add Business']]" />
    <x-dynamic-heading title="Add Business" />
    <form action="{{ route('businesses.store') }}" method="POST">
        @csrf
        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <strong class="font-bold">Whoops! Something went wrong.</strong>
          <ul class="mt-2">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
        <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 p-8">
            <div class="pb-10 mb-10 border-b border-gray-150 my-8">
                <h2 class="text-lg font-semibold text-gray-900">Business Information</h2>
                <p class="mt-1 text-sm text-gray-600">Please provide details of the business.</p>
                <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="business_name">Business Name <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                            <x-text-input name="business_name" value="{{ old('business_name') }}" required />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="owner_name">Owner Name <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                            <x-text-input name="owner_name" value="{{ old('owner_name') }}" required />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="cnic">CNIC</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="cnic" value="{{ old('cnic') }}" data-mask="00000-0000000-0" placeholder="12345-1234567-1" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="contact_no">Contact No</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="contact_no" value="{{ old('contact_no') }}" data-mask="0000-0000000" placeholder="03XX-XXXXXXX" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="email">Email</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="email" type="email" value="{{ old('email') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="address">Address</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="address" value="{{ old('address') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="country_id">Country</x-input-label>
                        <select name="country_id" id="country_id" class="block w-full border-gray-300 rounded-md shadow-sm chosen-select @error('country_id') border-red-500 @enderror">
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" @selected(old('country_id')==$country->id)>{{ $country->country_name }}</option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container">
                            @error('country_id')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="timezone_id">Timezone</x-input-label>
                        <select name="timezone_id" id="timezone_id" class="block w-full border-gray-300 rounded-md shadow-sm chosen-select @error('timezone_id') border-red-500 @enderror">
                            <option value="">Select Timezone</option>
                            @foreach($timezones as $tz)
                                <option value="{{ $tz->id }}" @selected(old('timezone_id')==$tz->id)>{{ $tz->timezone_name }}</option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container">
                            @error('timezone_id')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="currency_id">Currency</x-input-label>
                        <select name="currency_id" id="currency_id" class="block w-full border-gray-300 rounded-md shadow-sm chosen-select @error('currency_id') border-red-500 @enderror">
                            <option value="">Select Currency</option>
                            @foreach($currencies as $cur)
                                <option value="{{ $cur->id }}" @selected(old('currency_id')==$cur->id)>{{ $cur->currency_name }}</option>
                            @endforeach
                        </select>
                        <div class="chosen-error-container">
                            @error('currency_id')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="package_id">Package <span class="text-red-500">*</span></x-input-label>
                        <div class="mt-2">
                            <select name="package_id" id="package_id" class="block w-full border-gray-300 rounded-md shadow-sm @error('package_id') border-red-500 @enderror">
                                <option value="">Select Package</option>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}" @selected(old('package_id')==$pkg->id)>{{ $pkg->package_name }}</option>
                                @endforeach
                            </select>
                            @error('package_id')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="date_format">Date Format</x-input-label>
                        <div class="mt-2">
                            <select name="date_format" id="date_format" class="block w-full border-gray-300 rounded-md shadow-sm @error('date_format') border-red-500 @enderror">
                                <option value="">Select Date Format</option>
                                <option value="Y-m-d" @selected(old('date_format')=='Y-m-d')>YYYY-MM-DD (2024-07-13)</option>
                                <option value="d/m/Y" @selected(old('date_format')=='d/m/Y')>DD/MM/YYYY (13/07/2024)</option>
                                <option value="m/d/Y" @selected(old('date_format')=='m/d/Y')>MM/DD/YYYY (07/13/2024)</option>
                                <option value="d-m-Y" @selected(old('date_format')=='d-m-Y')>DD-MM-YYYY (13-07-2024)</option>
                            </select>
                            @error('date_format')
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-10 mb-10 border-b border-gray-150 my-8">
                <h2 class="text-lg font-semibold text-gray-900">Business Legal Information</h2>
                <p class="mt-1 text-sm text-gray-600">Please provide legal details of the business (optional).</p>
                <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="store_name">Business Legal Name</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="store_name" value="{{ old('store_name') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="store_license_number">Business License Number</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="store_license_number" value="{{ old('store_license_number') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="license_expiry_date">License Expiry Date</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="license_expiry_date" type="date" value="{{ old('license_expiry_date') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="issuing_authority">Issuing Authority</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="issuing_authority" value="{{ old('issuing_authority') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="store_type">Business Type</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="store_type" value="{{ old('store_type') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="ntn">NTN</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="ntn" value="{{ old('ntn') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="strn">STRN</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="strn" value="{{ old('strn') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="store_phone">Business Phone</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="store_phone" value="{{ old('store_phone') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="store_email">Business Email</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="store_email" type="email" value="{{ old('store_email') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="store_address">Business Address</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="store_address" value="{{ old('store_address') }}" />
                        </div>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="store_city_id">Business City</x-input-label>
                        <select name="store_city_id" id="store_city_id" class="block w-full border-gray-300 rounded-md shadow-sm chosen-select">
                            <option value="">Select City</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" @selected(old('store_city_id')==$city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-1 mb-4 ml-4">
                        <x-input-label for="store_country_id">Business Country</x-input-label>
                        <select name="store_country_id" id="store_country_id" class="block w-full border-gray-300 rounded-md shadow-sm chosen-select">
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" @selected(old('store_country_id')==$country->id)>{{ $country->country_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-1 mb-4">
                        <x-input-label for="store_postal_code">Postal Code</x-input-label>
                        <div class="mt-2">
                            <x-text-input name="store_postal_code" value="{{ old('store_postal_code') }}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('businesses.index') }}" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 ml-2">Save</button>
            </div>
        </div>
    </form>
</x-app-layout> 

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<style>
/* Make Chosen match Tailwind input styles */
.chosen-container { width: 100% !important; }
.chosen-container-single .chosen-single {
    height: 42px; /* similar to form inputs */
    line-height: 40px;
    border: 1px solid #d1d5db; /* border-gray-300 */
    border-radius: 0.375rem; /* rounded-md */
    padding: 0 2.25rem 0 0.75rem; /* right space for arrow */
    background: #fff;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); /* shadow-sm */
    font-size: 0.875rem; /* text-sm */
    color: #111827; /* text-gray-900 */
}
.chosen-container-single .chosen-single span { margin-right: 0.5rem; }
.chosen-container-single .chosen-single div { right: 0.5rem; }
.chosen-container-active .chosen-single,
.chosen-container .chosen-single:focus {
    border-color: #6366f1; /* indigo-500 */
    box-shadow: 0 0 0 1px #6366f1 inset, 0 0 0 1px rgba(99,102,241,0.2);
}
/* Error state for Chosen dropdowns */
.chosen-container .chosen-single.border-red-500 {
    border-color: #ef4444 !important; /* red-500 */
    box-shadow: 0 0 0 1px #ef4444 inset, 0 0 0 1px rgba(239,68,68,0.2);
}
.chosen-container .chosen-search input {
    height: 38px;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 0 0.75rem;
}
</style>
<script>
$(function() {
    $('[name="cnic"]').mask('00000-0000000-0');
    $('[name="contact_no"]').mask('0000-0000000');
    $('[name="store_phone"]').mask('0000-0000000');
    
    // Initialize Chosen dropdowns
    $('.chosen-select').chosen({
        width: '100%',
        search_contains: true,
        allow_single_deselect: true,
        placeholder_text_single: 'Select an option'
    });
    
    // Handle error display for Chosen dropdowns
    $('.chosen-select').each(function() {
        var $select = $(this);
        var $errorContainer = $select.next('.chosen-error-container');
        
        // If there's a validation error, style the Chosen dropdown
        if ($errorContainer.find('span.text-red-600').length > 0) {
            var $chosenContainer = $select.next('.chosen-container');
            $chosenContainer.find('.chosen-single').addClass('border-red-500');
        }
    });
});
</script>
