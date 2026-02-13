<x-app-layout>
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/finance', 'label' => 'Finance'],
        ['url' => '#', 'label' => 'Chart of Accounts'],
    ]" />

    <x-dynamic-heading title="Chart of Accounts" />

    <div class="">
        <div class="mx-auto">
            @if (Session::has('success'))
                <x-success-alert message="{{ Session::get('success') }}" />
            @endif

            @if (Session::has('error'))
                <x-error-alert message="{{ Session::get('error') }}" />
            @endif

            <!-- Card Container -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="space-y-4 pb-8">
                    <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                        <!-- Search Form -->
                        <form action="{{ route('chart-of-accounts.index') }}" method="GET" class="flex w-full max-w-md">
                            <div class="relative flex-grow">
                                <span class="relative isolate block">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="relative block w-full appearance-none rounded-l-lg pl-10 px-[calc(theme(spacing[3.5])-1px)] py-[calc(theme(spacing[2.5])-1px)] text-base/6 text-zinc-950 placeholder:text-zinc-500 border border-zinc-950/10 bg-transparent dark:bg-white/5 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        placeholder="Search by Code or Name...">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        aria-hidden="true"
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-zinc-500 dark:text-zinc-400 pointer-events-none">
                                        <path fill-rule="evenodd"
                                            d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </div>
                            <button type="submit"
                                class="flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
                                </svg>
                            </button>
                        </form>

                        <!-- Add Account Button -->
                        <div class="ml-0 sm:ml-4 mt-4 sm:mt-0 w-full sm:w-auto">
                            @can('module', 'create chart of accounts')
                                <a href="{{ route('chart-of-accounts.create') }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-700 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:from-indigo-500 hover:to-indigo-600 hover:shadow-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add New Account
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Code for Delete -->
                <div x-data="{ showModal: false, deleteId: null }">
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">
                                        Code
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">
                                        Name
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">
                                        Type
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">
                                        Parent Account
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">
                                        Status
                                    </th>
                                    <th
                                        class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-gray-700">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ... previous code remains the same ... -->

                                @forelse($accounts as $account)
                                    @php
                                        $renderAccount = function ($account, $level = 0) use (&$renderAccount) {
                                            $output = '';
                                            $isLocked = $account->is_default;

                                            // Current row with enhanced hover effect
                                            $output .= '<tr class="hover:bg-indigo-50/30 transition-colors duration-150">';

                                            // Code column with indentation
                                            $output .= '<td class="border border-gray-200 px-4 py-3 text-sm">';
                                            $output .= '<div class="flex items-center">';
                                            if ($level > 0) {
                                                $output .= '<span class="text-gray-400">' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '</span>';
                                                $output .= '<span class="text-gray-400 mr-1">└─</span>';
                                            }
                                            $output .= '<span class="font-mono text-gray-700 font-medium">' . e($account->code) . '</span>';
                                            $output .= '</div></td>';

                                            // Name column with indentation
                                            $output .= '<td class="border border-gray-200 px-4 py-3 text-sm">';
                                            $output .= '<div class="flex items-center gap-2">';
                                            if ($level > 0) {
                                                $output .= '<span class="text-gray-400">' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '</span>';
                                            }
                                            $output .= '<span class="text-gray-900 font-medium">' . e($account->name) . '</span>';
                                            if ($account->is_default) {
                                                $output .= '<span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 border border-amber-200">';
                                                $output .= '<svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">';
                                                $output .= '<path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />';
                                                $output .= '</svg>Default';
                                                $output .= '</span>';
                                            }
                                            $output .= '</div></td>';

                                            // Type column with enhanced badge
                                            $output .= '<td class="border border-gray-200 px-4 py-3 text-sm">';
                                            $typeClasses = [
                                                'asset' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'liability' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                'income' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'expense' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'equity' => 'bg-purple-100 text-purple-800 border-purple-200',
                                            ];
                                            $output .= '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border ' .
                                                ($typeClasses[$account->type] ?? 'bg-gray-100 text-gray-800 border-gray-200') . '">';
                                            $output .= ucfirst($account->type);
                                            $output .= '</span>';
                                            $output .= '</td>';

                                            // Parent account column
                                            $output .= '<td class="border border-gray-200 px-4 py-3 text-sm text-gray-700">';
                                            if ($account->parent) {
                                                $output .= '<span class="text-gray-900">' . e($account->parent->name) . '</span>';
                                            } else {
                                                $output .= '<span class="text-gray-400 italic">Root Account</span>';
                                            }
                                            $output .= '</td>';

                                            // Status column with enhanced badge
                                            $output .= '<td class="border border-gray-200 px-4 py-3 text-sm">';
                                            $output .= '<span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border ' .
                                                ($account->is_active ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200') . '">';
                                            $output .= '<svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 8 8">';
                                            $output .= '<circle cx="4" cy="4" r="3" />';
                                            $output .= '</svg>';
                                            $output .= ($account->is_active ? 'Active' : 'Inactive');
                                            $output .= '</span>';
                                            $output .= '</td>';

                                            // Actions column with dropdown menu
                                            $output .= '<td class="border border-gray-200 px-4 py-3 text-sm">';
                                            $output .= '<div class="relative" x-data="{ open: false }">';

                                            if ($account->is_default) {
                                                // Show disabled state for default accounts
                                                $output .= '<span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-400 cursor-not-allowed" title="Default accounts cannot be modified">';
                                                $output .= '<svg class="mr-1 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                                                $output .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />';
                                                $output .= '</svg>';
                                                $output .= 'Locked';
                                                $output .= '</span>';
                                            } else {
                                                // Dropdown button
                                                $output .= '<button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">';
                                                $output .= '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                                                $output .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />';
                                                $output .= '</svg>';
                                                $output .= '</button>';

                                                // Dropdown menu
                                                $output .= '<div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">';
                                                $output .= '<div class="py-1">';

                                                // Mark as Active
                                                $csrfToken = csrf_token();
                                                $output .= '<form action="' . route('chart-of-accounts.mark-active', $account) . '" method="POST" class="inline w-full">';
                                                $output .= '<input type="hidden" name="_token" value="' . $csrfToken . '">';
                                                $output .= '<input type="hidden" name="_method" value="PATCH">';
                                                $output .= '<button type="submit" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-green-50 hover:text-green-700 flex items-center">';
                                                $output .= '<svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                                                $output .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                                                $output .= '</svg>';
                                                $output .= 'Mark as Active';
                                                $output .= '</button>';
                                                $output .= '</form>';

                                                // Mark as Inactive
                                                $output .= '<form action="' . route('chart-of-accounts.mark-inactive', $account) . '" method="POST" class="inline w-full">';
                                                $output .= '<input type="hidden" name="_token" value="' . $csrfToken . '">';
                                                $output .= '<input type="hidden" name="_method" value="PATCH">';
                                                $output .= '<button type="submit" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 flex items-center">';
                                                $output .= '<svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                                                $output .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                                                $output .= '</svg>';
                                                $output .= 'Mark as Inactive';
                                                $output .= '</button>';
                                                $output .= '</form>';

                                                // Delete
                                                $output .= '<form action="' . route('chart-of-accounts.destroy', $account) . '" method="POST" class="inline w-full" onsubmit="return confirm(\'Are you sure you want to delete this account? This action cannot be undone.\');">';
                                                $output .= '<input type="hidden" name="_token" value="' . $csrfToken . '">';
                                                $output .= '<input type="hidden" name="_method" value="DELETE">';
                                                $output .= '<button type="submit" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center">';
                                                $output .= '<svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                                                $output .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />';
                                                $output .= '</svg>';
                                                $output .= 'Delete';
                                                $output .= '</button>';
                                                $output .= '</form>';

                                                $output .= '</div>';
                                                $output .= '</div>';
                                            }

                                            $output .= '</div></td></tr>';

                                            // Render children if any exist
                                            foreach ($account->children as $child) {
                                                $output .= $renderAccount($child, $level + 1);
                                            }

                                            return $output;
                                        };
                                    @endphp

                                    {!! $renderAccount($account) !!}
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-sm font-medium text-gray-900 mb-1">No accounts found</p>
                                                <p class="text-xs text-gray-500">Get started by creating your first account</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse

                                <!-- ... rest of the code remains the same ... -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
