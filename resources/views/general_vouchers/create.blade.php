<x-app-layout>
    @section('title', 'Create General Voucher - Finance Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/finance', 'label' => 'Finance'],
        ['url' => '/general-vouchers', 'label' => 'General Vouchers'],
        ['url' => '#', 'label' => 'Create']
    ]" />

    <x-dynamic-heading title="Create General Voucher" />

    <div class="bg-white border border-gray-200 shadow-lg sm:rounded-xl p-3 max-w-4xl">

        @if ($errors->any())
            <x-error-alert title="Whoops! Something went wrong.">
                <ul class="mt-2 text-sm list-disc list-inside text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-error-alert>
        @endif

        @if (session('error'))
            <x-error-alert message="{{ session('error') }}" />
        @endif

        @if (session('success'))
            <x-success-alert message="{{ session('success') }}" />
        @endif

        <form method="POST" action="{{ route('general-vouchers.store') }}" enctype="multipart/form-data">
            @csrf

            @include('general_vouchers._form_fields')

            <!-- Form Actions -->
            <div class="flex items-center justify-start gap-2 mt-2 border-t pt-2">
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-wider hover:bg-indigo-700">
                    Create Voucher
                </button>
                <a href="{{ route('general-vouchers.index') }}"
                    class="inline-flex items-center px-3 py-1.5 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-wider hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    @include('general_vouchers._form_scripts')
</x-app-layout>