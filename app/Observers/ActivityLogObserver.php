<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogObserver
{
    /**
     * Handle the model "created" event.
     */
    public function created(Model $model): void
    {
        $description = $this->buildDescription($model, 'created');
        $this->writeLog($model, 'created', $description);
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        $ignoredAttributes = array_merge(
            ['updated_at', 'created_at', 'deleted_at'],
            $this->getIgnoredAttributesForModel($model)
        );

        $oldValues = [];
        $newValues = [];
        $meaningfulChanges = [];

        foreach ($changes as $key => $value) {
            if (in_array($key, $ignoredAttributes, true)) {
                continue;
            }
            
            $oldValue = $model->getOriginal($key);
            
            // Only track if value actually changed
            if ($oldValue != $value) {
                $oldValues[$key] = $oldValue;
                $newValues[$key] = $value;
                $meaningfulChanges[$key] = [
                    'old' => $this->formatValue($oldValue),
                    'new' => $this->formatValue($value),
                ];
            }
        }

        // Skip logging if there are no meaningful changes
        if (empty($meaningfulChanges)) {
            return;
        }

        // Build enhanced description for status changes
        $description = $this->buildDescription($model, 'updated', $meaningfulChanges);
        
        $this->writeLog($model, 'updated', $description, [
            'old' => $oldValues,
            'new' => $newValues,
            'changes' => $meaningfulChanges,
        ]);
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $description = $this->buildDescription($model, 'deleted');
        $this->writeLog($model, 'deleted', $description);
    }

    /**
     * Build a user-friendly description for the activity.
     */
    private function buildDescription(Model $model, string $event, array $changes = []): string
    {
        $modelName = class_basename($model);
        $modelId = $model->getKey();
        
        // Get display name for the model
        $displayName = $this->getModelDisplayName($modelName);
        
        // Get identifier (invoice_number, name, etc.)
        $identifier = $this->getModelIdentifier($model);
        
        // Build base description
        if ($identifier) {
            $baseDescription = "{$displayName} {$identifier}";
        } else {
            $baseDescription = "{$displayName} #{$modelId}";
        }
        
        // Enhance description based on event and changes
        if ($event === 'updated' && !empty($changes)) {
            // Check for status changes (most important)
            if (isset($changes['status'])) {
                $oldStatus = $changes['status']['old'];
                $newStatus = $changes['status']['new'];
                return "{$baseDescription} status changed from {$oldStatus} to {$newStatus}";
            }
            
            // Check for other important fields
            $importantFields = ['invoice_number', 'total_amount', 'party_id', 'payment_type'];
            foreach ($importantFields as $field) {
                if (isset($changes[$field])) {
                    $fieldName = ucwords(str_replace('_', ' ', $field));
                    return "{$baseDescription} {$fieldName} updated";
                }
            }
            
            // Generic update
            $changeCount = count($changes);
            return "{$baseDescription} updated ({$changeCount} field" . ($changeCount > 1 ? 's' : '') . " changed)";
        }
        
        return "{$baseDescription} was " . ($event === 'created' ? 'created' : ($event === 'deleted' ? 'deleted' : 'updated'));
    }

    /**
     * Get display name for model.
     */
    private function getModelDisplayName(string $modelName): string
    {
        $displayNames = [
            // System & User Management
            'User' => 'User',
            'Business' => 'Business',
            'Role' => 'Role',
            'Permission' => 'Permission',
            
            // Configuration & Master Data
            'ArmsType' => 'Arm Type',
            'ArmsMake' => 'Arm Make',
            'ArmsCategory' => 'Arm Category',
            'ArmsCaliber' => 'Arm Caliber',
            'ArmsCondition' => 'Arm Condition',
            'ItemType' => 'Item Type',
            
            // Sales Module
            'SaleInvoice' => 'Invoice',
            'SaleReturn' => 'Sale Return',
            'Purchase' => 'Purchase Order',
            'PurchaseReturn' => 'Purchase Return',
            
            // Inventory Module
            'GeneralItem' => 'Item',
            'Arm' => 'Arm',
            'GeneralBatch' => 'Batch',
            'StockAdjustment' => 'Stock Adjustment',
            
            // Party Module
            'Party' => 'Party',
            'PartyLedger' => 'Party Ledger',
            'PartyTransfer' => 'Party Transfer',
            
            // Financial Module
            'Bank' => 'Bank',
            'BankTransfer' => 'Bank Transfer',
            'BankLedger' => 'Bank Ledger',
            'Expense' => 'Expense',
            'OtherIncome' => 'Other Income',
            'JournalEntry' => 'Journal Entry',
            'GeneralVoucher' => 'General Voucher',
            
            // Accounting Module
            'ChartOfAccount' => 'Account',
            
            // Approval Module
            'Approval' => 'Approval',
            
            // Quotation Module
            'Quotation' => 'Quotation',
        ];
        
        return $displayNames[$modelName] ?? $modelName;
    }

    /**
     * Get identifier for the model (invoice_number, name, etc.).
     */
    private function getModelIdentifier(Model $model): ?string
    {
        $modelName = class_basename($model);
        
        // Model-specific identifier fields
        $modelIdentifiers = [
            'User' => ['username', 'name', 'email'],
            'Business' => ['business_name'],
            'Role' => ['name'],
            'Permission' => ['name'],
            
            // Configuration & Master Data
            'ArmsType' => ['arm_type'],
            'ArmsMake' => ['arm_make'],
            'ArmsCategory' => ['arm_category'],
            'ArmsCaliber' => ['arm_caliber'],
            'ArmsCondition' => ['arm_condition'],
            'ItemType' => ['item_type'],
        ];
        
        // Get identifier fields for this model
        if (isset($modelIdentifiers[$modelName])) {
            foreach ($modelIdentifiers[$modelName] as $field) {
                if ($model->offsetExists($field) && $model->getAttribute($field)) {
                    return $model->getAttribute($field);
                }
            }
        }
        
        // Try common identifier fields for other models
        $identifierFields = ['invoice_number', 'name', 'account_name', 'title', 'voucher_number'];
        
        foreach ($identifierFields as $field) {
            if ($model->offsetExists($field) && $model->getAttribute($field)) {
                return "#{$model->getAttribute($field)}";
            }
        }
        
        return null;
    }

    /**
     * Format value for display.
     */
    private function formatValue($value)
    {
        if (is_null($value)) {
            return 'Empty';
        }
        
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        
        if (is_array($value)) {
            return json_encode($value);
        }
        
        return $value;
    }

    /**
     * Write the activity log.
     */
    private function writeLog(Model $model, string $event, string $description, array $properties = null): void
    {
        try {
            $businessId = session('active_business');
            $userId = auth()->id();

            // Only log when business context is available
            if (!$businessId || !$userId) {
                return;
            }

            // Skip logging ActivityLog itself to prevent infinite loops
            if ($model instanceof ActivityLog) {
                return;
            }

            ActivityLog::create([
                'business_id' => $businessId,
                'user_id' => $userId,
                'log_name' => strtolower(class_basename($model)),
                'description' => $description,
                'subject_type' => get_class($model),
                'subject_id' => $model->getKey(),
                'event' => $event,
                'properties' => $properties,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Silently ignore logging errors to not break main flow
            // Optionally log to Laravel log for debugging
            // \Log::error('Activity log error: ' . $e->getMessage());
        }
    }

    /**
     * Get ignored attributes for specific models.
     */
    private function getIgnoredAttributesForModel(Model $model): array
    {
        $map = [
            // User model - ignore sensitive fields
            \App\Models\User::class => [
                'password',
                'remember_token',
                'email_verified_at',
            ],
            
            // Business model - ignore sensitive fields
            \App\Models\Business::class => [
                // Add any sensitive business fields if needed
            ],
            
            // Role and Permission - no sensitive fields to ignore
            \Spatie\Permission\Models\Role::class => [],
            \Spatie\Permission\Models\Permission::class => [],
            
            // Other models
            \App\Models\Arm::class => ['arm_title'],
            \App\Models\GeneralItem::class => [],
            \App\Models\SaleInvoice::class => [],
            \App\Models\Purchase::class => [],
            // Add more model-specific ignored attributes as needed
        ];

        foreach ($map as $class => $attributes) {
            if ($model instanceof $class) {
                return $attributes;
            }
        }

        return [];
    }
}


