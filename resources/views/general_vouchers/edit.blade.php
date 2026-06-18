<x-app-layout>
    @section('title', 'Edit General Voucher - Finance Management - StoreBook')
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/finance', 'label' => 'Finance'],
        ['url' => '/general-vouchers', 'label' => 'General Vouchers'],
        ['url' => '#', 'label' => 'Edit']
    ]" />

    <x-dynamic-heading title="Edit General Voucher" />

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

        <form method="POST" action="{{ route('general-vouchers.update', $generalVoucher) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('general_vouchers._form_fields', ['voucher' => $generalVoucher])

            <!-- Form Actions -->
            <div class="flex items-center justify-start gap-2 mt-2 border-t pt-2">
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-wider hover:bg-indigo-700">
                    Update Voucher
                </button>
                <a href="{{ route('general-vouchers.index') }}"
                    class="inline-flex items-center px-3 py-1.5 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-wider hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    @include('general_vouchers._chosen_assets')
    @include('general_vouchers._form_scripts')
    <script>
        function deleteAttachment(id) {
            if (confirm('Are you sure you want to delete this attachment?')) {
                fetch(`/general-vouchers/attachments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error deleting attachment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting attachment');
                });
            }
        }
    </script>
</x-app-layout>