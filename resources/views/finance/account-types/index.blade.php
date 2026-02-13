<x-app-layout>
    <x-breadcrumb :breadcrumbs="[
        ['url' => '/', 'label' => 'Home'],
        ['url' => '/finance', 'label' => 'Finance'],
        ['url' => '#', 'label' => 'Account Types'],
    ]" />

    <div class="flex justify-between items-center mb-6">
        <x-dynamic-heading title="Chart of Accounts - Account Types" />
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="mb-6">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Main Account Types</h2>
                        <p class="mt-1 text-sm text-gray-600">The foundation of double-entry bookkeeping system. These account types follow the accounting equation: Assets = Liabilities + Equity</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <div class="text-indigo-600 font-semibold mb-2">Balance Sheet Accounts</div>
                    <p class="text-sm text-gray-600">Shows financial position (Assets, Liabilities, Equity)</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-green-600 font-semibold mb-2">Income Statement Accounts</div>
                    <p class="text-sm text-gray-600">Shows performance (Income, Expenses)</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="text-red-600 font-semibold mb-2">Accounting Period</div>
                    <p class="text-sm text-gray-600">Follows fiscal year cycle for reporting</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Common Examples</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Normal Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-indigo-600">Assets</div>
                                <div class="text-xs text-gray-500">Balance Sheet</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">What the business owns</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Cash, Bank Accounts, Accounts Receivable, Inventory, Fixed Assets</td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600">Debit</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-indigo-600">Liabilities</div>
                                <div class="text-xs text-gray-500">Balance Sheet</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">What the business owes</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Accounts Payable, Bank Loans, Accrued Expenses</td>
                            <td class="px-6 py-4 text-sm font-medium text-red-600">Credit</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-green-600">Income</div>
                                <div class="text-xs text-gray-500">Income Statement</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">Revenue generated</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Sales, Service Revenue, Commission Income</td>
                            <td class="px-6 py-4 text-sm font-medium text-red-600">Credit</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-red-600">Expenses</div>
                                <div class="text-xs text-gray-500">Income Statement</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">Costs incurred</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Salaries, Rent, Utilities, Office Supplies</td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600">Debit</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-purple-600">Equity</div>
                                <div class="text-xs text-gray-500">Balance Sheet</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">Owner's capital</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Opening Balance, Retained Earnings</td>
                            <td class="px-6 py-4 text-sm font-medium text-red-600">Credit</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 space-y-4">
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-yellow-800 mb-2">Key Accounting Principles</h3>
                    <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                        <li>Debit accounts: Assets and Expenses increase with debits</li>
                        <li>Credit accounts: Liabilities, Equity, and Income increase with credits</li>
                        <li>Every transaction affects at least two accounts (double-entry)</li>
                        <li>Total debits must equal total credits</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>