<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Observers\ActivityLogObserver;
use App\Observers\BusinessObserver;
use App\Http\View\Composers\BusinessComposer;
use App\Models\Business;
use App\Models\SaleInvoice;
use App\Models\Purchase;
use App\Models\Arm;
use App\Models\GeneralItem;
use App\Models\Party;
use App\Models\BankTransfer;
use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\ChartOfAccount;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // $this->app->singleton(Gate::class, function ($app) {
        //     return $app->make(Gate::class);
        // });

        // Register date helper functions
        require_once app_path('Helpers/DateHelper.php');

        // Register custom Blade directives for business date formatting
        Blade::directive('businessDate', function ($expression) {
            return "<?php echo formatBusinessDate($expression); ?>";
        });

        Blade::directive('businessDateTime', function ($expression) {
            return "<?php echo formatBusinessDateTime($expression); ?>";
        });

        // Register custom Blade directives for business currency formatting
        Blade::directive('currency', function ($expression) {
            return "<?php echo formatBusinessCurrency($expression); ?>";
        });

        Blade::directive('amount', function ($expression) {
            return "<?php echo formatBusinessAmount($expression); ?>";
        });

        // Share business settings with all views
        View::composer('*', BusinessComposer::class);

        // Register BusinessObserver to auto-create chart of accounts
        Business::observe(BusinessObserver::class);

        // Define morph map for polymorphic relationships
        Relation::morphMap([
            'Expense' => \App\Models\Expense::class,
            'BankTransfer' => \App\Models\BankTransfer::class,
            'General Voucher' => \App\Models\GeneralVoucher::class,
            'PartyTransfer' => \App\Models\PartyTransfer::class,
            'Party OB' => \App\Models\Party::class,
            'Bank OB' => \App\Models\Bank::class,
            'Bank Transfer' => \App\Models\BankTransfer::class,
            'General Item' => \App\Models\GeneralItem::class,
        ]);

        // Register activity logging observers for all business-critical models
        // Skip in console to speed up migrations and seeders
        if (!app()->runningInConsole()) {
            // Core Business Models - High Priority
            $coreModels = [
                // System & User Management
                \App\Models\User::class,
                \App\Models\Business::class,
                \Spatie\Permission\Models\Role::class,
                \Spatie\Permission\Models\Permission::class,
                
                // Configuration & Master Data
                \App\Models\ArmsType::class,
                \App\Models\ArmsMake::class,
                \App\Models\ArmsCategory::class,
                \App\Models\ArmsCaliber::class,
                \App\Models\ArmsCondition::class,
                \App\Models\ItemType::class,
                
                // Sales Module
                \App\Models\SaleInvoice::class,
                \App\Models\SaleReturn::class,
                
                // Purchase Module
                \App\Models\Purchase::class,
                \App\Models\PurchaseReturn::class,
                
                // Inventory Module
                \App\Models\GeneralItem::class,
                \App\Models\Arm::class,
                \App\Models\GeneralBatch::class,
                \App\Models\StockAdjustment::class,
                
                // Party Module
                \App\Models\Party::class,
                \App\Models\PartyLedger::class,
                \App\Models\PartyTransfer::class,
                
                // Financial Module
                \App\Models\Bank::class,
                \App\Models\BankTransfer::class,
                \App\Models\BankLedger::class,
                \App\Models\Expense::class,
                \App\Models\OtherIncome::class,
                // JournalEntry excluded from activity logging
                \App\Models\GeneralVoucher::class,
                
                // Accounting Module
                \App\Models\ChartOfAccount::class,
                
                // Approval Module
                \App\Models\Approval::class,
                
                // Quotation Module
                \App\Models\Quotation::class,
            ];

            // Register observers for all models
            foreach ($coreModels as $modelClass) {
                if (class_exists($modelClass)) {
                    try {
                        $modelClass::observe(ActivityLogObserver::class);
                    } catch (\Exception $e) {
                        // Silently skip if model doesn't exist or has issues
                        continue;
                    }
                }
            }
        }
    }
}
