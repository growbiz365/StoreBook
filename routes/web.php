<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\TimezoneController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\SubUserController;
use App\Http\Middleware\CheckModuleAndPermission;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\BalanceSheetController;
use App\Http\Controllers\ProfitLossController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\DetailedGeneralLedgerController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PartyTransferController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\GeneralVoucherController;
use App\Http\Controllers\ExpenseHeadController;
use App\Http\Controllers\IncomeHeadController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\OtherIncomeController;
use App\Http\Controllers\GeneralItemController;
use App\Http\Controllers\ItemTypeController;
use App\Http\Controllers\GeneralBatchController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\ArmController;

use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\GeneralItemStockLedgerController;
use App\Http\Controllers\SaleInvoiceController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\QuotationController;

Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');




Route::get('/settings', function () {
    return view('settings');
})->middleware(['auth', 'verified', 'can:view settings'])->name('settings');

// Arms routes disabled - StoreBook is items-only
// Route::middleware('can:view arms')->group(function () {
// Route::get('/arms-dashboard', [App\Http\Controllers\ArmController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('arms.dashboard');
// });

Route::middleware('can:view items')->group(function () {
Route::get('/general-items-dashboard', [App\Http\Controllers\GeneralItemsDashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('general-items.dashboard');
});


Route::middleware([CheckModuleAndPermission::class . ':view purchases'])->group(function () {
    Route::get('/purchases-dashboard', [App\Http\Controllers\PurchaseDashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('purchases.dashboard');
});



// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// API routes for searchable dropdown - moved inside auth middleware

Route::middleware('auth')->group(function () {
    // API routes for searchable dropdown
    Route::prefix('api')->group(function () {
        Route::get('/general-items/search', [App\Http\Controllers\Api\GeneralItemController::class, 'search']);
        Route::get('/general-items/{id}', [App\Http\Controllers\GeneralItemController::class, 'getItemData'])->name('api.general-items.show');
        Route::get('/general-items/{generalItem}', [App\Http\Controllers\Api\GeneralItemController::class, 'show']);
        Route::get('/general-items', [App\Http\Controllers\Api\GeneralItemController::class, 'index']);
        
        // Arm-related API routes - Disabled: StoreBook is items-only
        // Route::get('/arm-types/search', [App\Http\Controllers\Api\ArmTypeController::class, 'search']);
        // Route::get('/arm-types', [App\Http\Controllers\Api\ArmTypeController::class, 'index']);
        
        // Route::get('/arm-categories/search', [App\Http\Controllers\Api\ArmCategoryController::class, 'search']);
        // Route::get('/arm-categories', [App\Http\Controllers\Api\ArmCategoryController::class, 'index']);
        
        // Route::get('/arm-makes/search', [App\Http\Controllers\Api\ArmMakeController::class, 'search']);
        // Route::get('/arm-makes', [App\Http\Controllers\Api\ArmMakeController::class, 'index']);
        
        // Route::get('/arm-calibers/search', [App\Http\Controllers\Api\ArmCaliberController::class, 'search']);
        // Route::get('/arm-calibers', [App\Http\Controllers\Api\ArmCaliberController::class, 'index']);
        
        // Route::get('/arm-conditions/search', [App\Http\Controllers\Api\ArmConditionController::class, 'search']);
        // Route::get('/arm-conditions', [App\Http\Controllers\Api\ArmConditionController::class, 'index']);
        
        // Arms API routes - Disabled: StoreBook is items-only
        // Route::get('/arms/search', [App\Http\Controllers\Api\ArmController::class, 'search']);
        // Route::get('/arms', [App\Http\Controllers\Api\ArmController::class, 'index']);
        // Route::get('/arms/check-serial', [App\Http\Controllers\Api\ArmController::class, 'checkSerial']);
        
        // Party-related API routes
        Route::get('/parties/search', [App\Http\Controllers\Api\PartyController::class, 'search']);
        Route::get('/parties', [App\Http\Controllers\Api\PartyController::class, 'index']);
        Route::get('/parties/{party}', [App\Http\Controllers\Api\PartyController::class, 'show']);
        
        // // Test route for debugging
        // Route::get('/test', function() {
        //     return response()->json([
        //         'message' => 'API is working',
        //         'business_id' => session('active_business'),
        //         'user' => auth()->user() ? auth()->user()->id : 'not authenticated',
        //         'timestamp' => now()
        //     ]);
        // });
        

    });
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/businesses/activate/{businessId}', [BusinessController::class, 'setActiveBusiness'])->name('businesses.activate');

    Route::middleware('can:view permissions')->group(function () {
        Route::resource('permissions', PermissionController::class)->except('show');
    });

    Route::middleware('can:view users')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::post('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        Route::post('users/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('users.unsuspend');
    });

    Route::middleware([CheckModuleAndPermission::class . ':view businesses'])->group(function () {
        Route::resource('businesses', BusinessController::class);
        Route::post('businesses/{business}/suspend', [BusinessController::class, 'suspend'])->name('businesses.suspend');
        Route::post('businesses/{business}/unsuspend', [BusinessController::class, 'unsuspend'])->name('businesses.unsuspend');
        Route::post('businesses/{business}/clear-all-data', [BusinessController::class, 'clearAllData'])->name('businesses.clear-all-data');
    });


    Route::middleware([CheckModuleAndPermission::class . ':view journal entries'])->group(function () {
        Route::resource('journal-entries', JournalEntryController::class)->only(['index']);
    });


    Route::get('/finance', [FinanceController::class, 'index'])
        ->middleware([CheckModuleAndPermission::class . ':view finance'])
        ->name('finance.index');

    // Balance Sheet Routes
        Route::get('/balance-sheet', [BalanceSheetController::class, 'index'])->name('balance-sheet.index');
        Route::get('/balance-sheet/export-pdf', [BalanceSheetController::class, 'exportPdf'])->name('balance-sheet.export-pdf');

    // Trial Balance Routes
    Route::get('/trial-balance', [TrialBalanceController::class, 'index'])->name('trial-balance.index');
    Route::get('/detailed-general-ledger', [DetailedGeneralLedgerController::class, 'index'])->name('finance.detailed-general-ledger.index');

    // Profit & Loss Routes
        Route::get('/profit-loss', [ProfitLossController::class, 'index'])->name('profit-loss.index');
        Route::get('/profit-loss/export-pdf', [ProfitLossController::class, 'exportPdf'])->name('profit-loss.export-pdf');

    // General Ledger Routes
        Route::get('/general-ledger', [GeneralLedgerController::class, 'index'])->name('general-ledger.index');
        Route::get('/general-ledger/export-pdf', [GeneralLedgerController::class, 'exportPdf'])->name('general-ledger.export-pdf');
    
    // Activity Logs Routes
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');

    Route::get('/account-types', function () {
        return view('finance.account-types.index');
    })->middleware([CheckModuleAndPermission::class . ':view account types'])->name('account-types.index');

    Route::middleware([CheckModuleAndPermission::class . ':view chart of accounts'])->group(function () {
        Route::resource('chart-of-accounts', ChartOfAccountController::class);
        
        // Mark active/inactive routes
        Route::patch('chart-of-accounts/{chartOfAccount}/mark-active', [ChartOfAccountController::class, 'markActive'])
            ->name('chart-of-accounts.mark-active');
        Route::patch('chart-of-accounts/{chartOfAccount}/mark-inactive', [ChartOfAccountController::class, 'markInactive'])
            ->name('chart-of-accounts.mark-inactive');
    });

    Route::middleware('can:view roles')->group(function () {
        Route::resource('roles', RoleController::class)->except('show');
    });

    Route::middleware('can:view countries')->group(function () {
        Route::resource('countries', CountryController::class);
    });


    Route::middleware('can:view timezones')->group(function () {
        Route::resource('timezones', TimezoneController::class);
    });

    Route::middleware('can:view currencies')->group(function () {
        Route::resource('currencies', CurrencyController::class);
    });

    Route::middleware('can:view packages')->group(function () {
        Route::resource('packages', PackageController::class);
    });

    Route::middleware('can:view modules')->group(function () {
        Route::resource('modules', ModuleController::class);
    });

    Route::get('packages/{package}/modules', [PackageController::class, 'modules'])->name('packages.modules');
    Route::post('packages/{package}/modules', [PackageController::class, 'storeModules'])->name('packages.storeModules');


    Route::middleware('can:view cities')->group(function () {
        Route::resource('cities', CityController::class);
    });

    Route::get('businesses/{business}/edit-store-info', [BusinessController::class, 'editStoreInfo'])->name('businesses.editStoreInfo');
    Route::put('businesses/{business}/update-store-info', [BusinessController::class, 'updateStoreInfo'])->name('businesses.updateStoreInfo');




    Route::middleware('can:view subusers')->group(function () {
        Route::resource('subusers', SubUserController::class);
        Route::post('subusers/{subuser}/suspend', [SubUserController::class, 'suspend'])->name('subusers.suspend');
        Route::post('subusers/{subuser}/unsuspend', [SubUserController::class, 'unsuspend'])->name('subusers.unsuspend');
    });

    // Arms routes disabled - StoreBook is items-only
    // Route::middleware('can:view arm_types')->group(function () {
    //     Route::resource('arms-types', App\Http\Controllers\ArmsTypeController::class);
    // });

    // Route::middleware('can:view arm_categories')->group(function () {
    //     Route::resource('arms-categories', App\Http\Controllers\ArmsCategoryController::class);
    // });

    // Route::middleware('can:view arm_makes')->group(function () {
    //     Route::resource('arms-makes', App\Http\Controllers\ArmsMakeController::class);
    // });

    // Route::middleware('can:view arm_calibers')->group(function () {
    //     Route::resource('arms-calibers', App\Http\Controllers\ArmsCaliberController::class);
    // });

    // Route::middleware('can:view arm_conditions')->group(function () {
    //     Route::resource('arms-conditions', App\Http\Controllers\ArmsConditionController::class);
    // });

    // Arms Inventory Management - Disabled: StoreBook is items-only
    // Route::middleware([CheckModuleAndPermission::class . ':view arms'])->group(function () {
    //     Route::resource('arms', ArmController::class);
    // });
    
    // Arms Report Route - Disabled: StoreBook is items-only
    // Route::get('/arms-report', [App\Http\Controllers\ArmController::class, 'report'])->name('arms.report');
    // Route::get('/arms-report/export', [App\Http\Controllers\ArmController::class, 'exportReport'])->name('arms.report.export');
    
    // Opening Stock Arms Route - Disabled: StoreBook is items-only
    // Route::get('/arms-opening-stock', [App\Http\Controllers\ArmController::class, 'openingStock'])->name('arms.opening-stock');

    // Arms Stock Ledger Report Routes - Disabled: StoreBook is items-only
    // Route::get('/arms-stock-ledger', [App\Http\Controllers\ArmsStockLedgerController::class, 'index'])->name('arms-stock-ledger');
    // Route::get('/arms-stock-ledger/export', [App\Http\Controllers\ArmsStockLedgerController::class, 'export'])->name('arms-stock-ledger.export');
    
    // Arms History Report Routes - Disabled: StoreBook is items-only
    // Route::get('/arms-history', [App\Http\Controllers\ArmsHistoryController::class, 'index'])->name('arms-history');
    // Route::get('/arms-history/export', [App\Http\Controllers\ArmsHistoryController::class, 'export'])->name('arms-history.export');
        

    
    

    
        Route::resource('item-types', App\Http\Controllers\ItemTypeController::class);
    

    
        // Inventory Valuation Summary Report Route (must be before resource routes)
        Route::get('/general-items/inventory-valuation-summary', [GeneralItemStockLedgerController::class, 'inventoryValuationSummary'])->name('general-items.inventory-valuation-summary');
        Route::get('/general-items/inventory-valuation-summary/export', [GeneralItemStockLedgerController::class, 'exportInventoryValuationSummary'])->name('general-items.inventory-valuation-summary.export');
        
        // Detailed Inventory Valuation Report Route (must be before resource routes)
        Route::get('/general-items/{item}/detailed-inventory-valuation', [GeneralItemStockLedgerController::class, 'detailedInventoryValuation'])->name('general-items.detailed-inventory-valuation');
        Route::get('/general-items/{item}/detailed-inventory-valuation/export', [GeneralItemStockLedgerController::class, 'exportDetailedInventoryValuation'])->name('general-items.detailed-inventory-valuation.export');
        
       

        Route::middleware([CheckModuleAndPermission::class . ':view items'])->group(function () {
            Route::resource('general-items', App\Http\Controllers\GeneralItemController::class);
        });



        Route::get('/general-items/{generalItem}/edit-opening-stock', [App\Http\Controllers\GeneralItemController::class, 'editOpeningStock'])->name('general-items.edit-opening-stock');
        Route::put('/general-items/{generalItem}/update-opening-stock', [App\Http\Controllers\GeneralItemController::class, 'updateOpeningStock'])->name('general-items.update-opening-stock');
        
        // Stock Ledger Report Routes
        Route::get('/general-items-stock-ledger', [GeneralItemStockLedgerController::class, 'index'])->name('general-items-stock-ledger');
        Route::get('/general-items-stock-ledger/export', [GeneralItemStockLedgerController::class, 'export'])->name('general-items-stock-ledger.export');
        
        // Stock Adjustments Routes
        Route::middleware([CheckModuleAndPermission::class . ':view items'])->group(function () {
            Route::resource('stock-adjustments', StockAdjustmentController::class);
        });
        
       
        Route::resource('general-batches', GeneralBatchController::class)->only(['index','show','edit','update']);
        Route::resource('inventory-transactions', InventoryTransactionController::class)->only(['index','show','edit','update']);
  
    // Party Management Dashboard
    Route::get('/party-management', [PartyController::class, 'dashboard'])->name('party-management.dashboard');

    // Party Ledger Report
    Route::get('/parties/ledger-report', [PartyController::class, 'ledgerReport'])->name('parties.ledger-report');

    // Party Balances Report
    Route::get('/parties/balances-report', [PartyController::class, 'balancesReport'])->name('parties.balances-report');

    // Party Balance API - must come before resource routes
    Route::get('/parties/{party}/balance', [PartyController::class, 'getBalance'])->name('parties.balance');
    
    // Test route for debugging
    Route::get('/test-party-balance/{party}', function($party) {
        $party = \App\Models\Party::find($party);
        if (!$party) {
            return response()->json(['error' => 'Party not found']);
        }
        return response()->json([
            'party_id' => $party->id,
            'party_name' => $party->name,
            'balance' => $party->getBalance(),
            'formatted_balance' => $party->getFormattedBalance(),
            'status' => $party->getBalanceStatus()
        ]);
    });

    // Existing party routes
    Route::middleware([CheckModuleAndPermission::class . ':view parties'])->group(function () {
        Route::resource('parties', PartyController::class);
    });
    

    Route::middleware([CheckModuleAndPermission::class . ':view parties transfers'])->group(function () {
        Route::resource('party-transfers', PartyTransferController::class);
    });
    

    Route::delete('/party-transfers/attachments/{attachment}', [PartyTransferController::class, 'deleteAttachment'])
        ->name('party-transfers.attachments.delete');

    // Bank routes
    Route::middleware([CheckModuleAndPermission::class . ':view banks'])->group(function () {
        Route::resource('banks', BankController::class)->except(['show', 'destroy']);
    });
    

    Route::middleware([CheckModuleAndPermission::class . ':view bank-transfers'])->group(function () {
        Route::resource('bank-transfers', BankTransferController::class);
    });
    

Route::delete('bank-transfers/attachments/{attachment}', [BankTransferController::class, 'deleteAttachment'])->name('bank-transfers.delete-attachment');
Route::get('bank-transfers/{bankTransfer}/banks/{bank}/available-balance', [BankTransferController::class, 'getAvailableBalanceForEdit'])->name('bank-transfers.available-balance');
     
Route::get('/bank-management', [BankController::class, 'dashboard'])->name('bank-management');
Route::get('/banks/ledger-report', [BankController::class, 'ledgerReport'])->name('banks.ledger-report');
Route::get('/banks/balances-report', [BankController::class, 'balancesReport'])->name('banks.balances-report');
Route::get('/banks/{bank}/balance', [BankController::class, 'getBalance'])->name('banks.balance');

 // General Vouchers
 Route::middleware('can:view general vouchers')->group(function () {
 Route::resource('general-vouchers', GeneralVoucherController::class);
 });

 Route::delete('general-vouchers/attachments/{attachment}', [GeneralVoucherController::class, 'deleteAttachment'])->name('general-vouchers.attachments.delete');




 Route::get('/expenses/dashboard', [ExpenseController::class, 'dashboard'])->name('expenses.dashboard');
 Route::get('/expenses/report', [ExpenseController::class, 'report'])->name('expenses.report');
// Expense Heads
Route::middleware([CheckModuleAndPermission::class . ':view expense heads'])->group(function () {
Route::resource('expense-heads', ExpenseHeadController::class);
});

// Income Heads
Route::middleware([CheckModuleAndPermission::class . ':view income heads'])->group(function () {
Route::resource('income-heads', IncomeHeadController::class);
});

 // Expenses

 Route::delete('expenses/attachments/{attachment}', [ExpenseController::class, 'deleteAttachment'])->name('expenses.attachments.delete');
 
 Route::middleware([CheckModuleAndPermission::class . ':view expenses'])->group(function () {
    Route::resource('expenses', ExpenseController::class);
});



 // File download routes
 Route::get('files/party-transfer-attachments/{attachment}', [FileController::class, 'downloadPartyTransferAttachment'])->name('files.party-transfer-attachments.download');
 Route::get('files/bank-transfer-attachments/{attachment}', [FileController::class, 'downloadBankTransferAttachment'])->name('files.bank-transfer-attachments.download');
 Route::get('files/general-voucher-attachments/{attachment}', [FileController::class, 'downloadGeneralVoucherAttachment'])->name('files.general-voucher-attachments.download');
 Route::get('files/expense-attachments/{attachment}', [FileController::class, 'downloadExpenseAttachment'])->name('files.expense-attachments.download');

 // Purchase Management
 
 Route::middleware([CheckModuleAndPermission::class . ':view purchases'])->group(function () {
    Route::resource('purchases', PurchaseController::class);
});




     
Route::post('purchases/{purchase}/post', [PurchaseController::class, 'post'])->name('purchases.post');
Route::post('purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
Route::get('purchases/{purchase}/stock-impacts', [PurchaseController::class, 'stockImpacts'])->name('purchases.stock-impacts');
Route::get('purchases/{purchase}/audit-log', [PurchaseController::class, 'auditLog'])->name('purchases.audit-log');
// Arms route disabled - StoreBook is items-only
// Route::post('/api/check-arm-serials', [PurchaseController::class, 'checkArmSerials'])->name('api.check-arm-serials');
 
 // Sale Invoice Management
 Route::middleware([CheckModuleAndPermission::class . ':view sales'])->group(function () {
    Route::get('sales-dashboard', [SaleInvoiceController::class, 'dashboard'])->name('sales.dashboard');
});


Route::middleware([CheckModuleAndPermission::class . ':view sales'])->group(function () {
    Route::get('sale-invoices/profit-loss-report', [SaleInvoiceController::class, 'profitLossReport'])->name('sale-invoices.profit-loss-report');
    Route::resource('sale-invoices', SaleInvoiceController::class);
});





Route::post('sale-invoices/{saleInvoice}/post', [SaleInvoiceController::class, 'post'])->name('sale-invoices.post');
Route::post('sale-invoices/{saleInvoice}/cancel', [SaleInvoiceController::class, 'cancel'])->name('sale-invoices.cancel');
Route::get('sale-invoices/{saleInvoice}/audit-log', [SaleInvoiceController::class, 'auditLog'])->name('sale-invoices.audit-log');
Route::post('sale-invoices/{id}/restore', [SaleInvoiceController::class, 'restore'])->name('sale-invoices.restore');
Route::delete('sale-invoices/{id}/force-delete', [SaleInvoiceController::class, 'forceDelete'])->name('sale-invoices.force-delete');

// Approvals Routes
Route::middleware([CheckModuleAndPermission::class . ':view sales'])->group(function () {
    Route::get('approvals/report/view', [ApprovalController::class, 'report'])->name('approvals.report');
    Route::resource('approvals', ApprovalController::class);
    Route::get('approvals/{approval}/process', [ApprovalController::class, 'process'])->name('approvals.process');
    Route::post('approvals/{approval}/process-action', [ApprovalController::class, 'processAction'])->name('approvals.process-action');
});



// Sale Return Routes
Route::middleware('can:view sale returns')->group(function () {
Route::resource('sale-returns', SaleReturnController::class);
});

Route::post('sale-returns/{saleReturn}/post', [SaleReturnController::class, 'post'])->name('sale-returns.post');
Route::post('sale-returns/{saleReturn}/cancel', [SaleReturnController::class, 'cancel'])->name('sale-returns.cancel');
Route::get('sale-returns/{saleReturn}/audit-log', [SaleReturnController::class, 'auditLog'])->name('sale-returns.audit-log');
Route::post('sale-returns/{id}/restore', [SaleReturnController::class, 'restore'])->name('sale-returns.restore');
Route::delete('sale-returns/{id}/force-delete', [SaleReturnController::class, 'forceDelete'])->name('sale-returns.force-delete');
// Purchase Return Routes
Route::middleware('can:view purchase returns')->group(function () {
Route::resource('purchase-returns', PurchaseReturnController::class);
});
Route::post('purchase-returns/{purchaseReturn}/post', [PurchaseReturnController::class, 'post'])->name('purchase-returns.post');
Route::post('purchase-returns/{purchaseReturn}/cancel', [PurchaseReturnController::class, 'cancel'])->name('purchase-returns.cancel');
Route::get('purchase-returns/{purchaseReturn}/audit-log', [PurchaseReturnController::class, 'auditLog'])->name('purchase-returns.audit-log');
Route::post('purchase-returns/{id}/restore', [PurchaseReturnController::class, 'restore'])->name('purchase-returns.restore');
Route::delete('purchase-returns/{id}/force-delete', [PurchaseReturnController::class, 'forceDelete'])->name('purchase-returns.force-delete');

// Other Income Routes

Route::middleware([CheckModuleAndPermission::class . ':view other incomes'])->group(function () {
Route::resource('other-incomes', OtherIncomeController::class);
});

// Quotation Routes

    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/convert-to-sale', [QuotationController::class, 'convertToSale'])->name('quotations.convert-to-sale');
    Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    Route::post('quotations/{quotation}/expire', [QuotationController::class, 'expire'])->name('quotations.expire');



    Route::delete('other-incomes/attachments/{attachment}', [OtherIncomeController::class, 'deleteAttachment'])->name('other-incomes.attachments.delete');
    Route::get('other-incomes/attachments/{attachment}/download', [OtherIncomeController::class, 'downloadAttachment'])->name('other-incomes.attachments.download');


//  // Debug route for purchase authorization
//  Route::get('debug/purchase/{purchase}', function($purchase) {
//      $purchase = \App\Models\Purchase::find($purchase);
//      if (!$purchase) {
//          return response()->json(['error' => 'Purchase not found']);
//      }
     
//      $user = auth()->user();
//      $businessId = session('active_business');
     
//      return response()->json([
//          'purchase_id' => $purchase->id,
//          'purchase_business_id' => $purchase->business_id,
//          'user_id' => $user ? $user->id : 'not authenticated',
//          'session_business_id' => $businessId,
//          'user_businesses' => $user ? $user->businesses->pluck('id')->toArray() : [],
//          'user_has_access' => $user ? $user->businesses()->where('business_id', $purchase->business_id)->exists() : false
//      ]);
//  })->name('debug.purchase');


});


Route::get('city/search', [CityController::class, 'search'])->name('cities.search');
Route::get('country/search', [CountryController::class, 'search'])->name('countries.search');
Route::get('timezone/search', [TimezoneController::class, 'search'])->name('timezones.search');
Route::get('currency/search', [CurrencyController::class, 'search'])->name('currencies.search');
Route::get('package/search', [PackageController::class, 'search'])->name('packages.search');
Route::get('users/search', [UserController::class, 'search'])->name('users.search');
Route::get('businesses/search', [BusinessController::class, 'search'])->name('businesses.search');

require __DIR__ . '/auth.php';


