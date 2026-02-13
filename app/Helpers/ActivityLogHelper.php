<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogHelper
{
    /**
     * Log a custom event (posted, cancelled, approved, etc.)
     */
    public static function logCustomEvent(
        Model $model,
        string $event,
        string $description = null,
        array $properties = []
    ): void {
        try {
            $businessId = session('active_business');
            $userId = auth()->id();

            if (!$businessId || !$userId) {
                return;
            }

            // Build description if not provided
            if (!$description) {
                $modelName = class_basename($model);
                $displayName = self::getModelDisplayName($modelName);
                $identifier = self::getModelIdentifier($model);
                
                if ($identifier) {
                    $description = "{$displayName} {$identifier} was {$event}";
                } else {
                    $description = "{$displayName} #{$model->getKey()} was {$event}";
                }
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
            // Silently ignore logging errors
        }
    }

    /**
     * Log when an invoice/order is posted.
     */
    public static function logPosted(Model $model, array $additionalProperties = []): void
    {
        $modelName = class_basename($model);
        $displayName = self::getModelDisplayName($modelName);
        $identifier = self::getModelIdentifier($model);
        
        $description = $identifier 
            ? "{$displayName} {$identifier} was posted"
            : "{$displayName} #{$model->getKey()} was posted";

        self::logCustomEvent($model, 'posted', $description, $additionalProperties);
    }

    /**
     * Log when an invoice/order is cancelled.
     */
    public static function logCancelled(Model $model, string $reason = null, array $additionalProperties = []): void
    {
        $modelName = class_basename($model);
        $displayName = self::getModelDisplayName($modelName);
        $identifier = self::getModelIdentifier($model);
        
        $description = $identifier 
            ? "{$displayName} {$identifier} was cancelled"
            : "{$displayName} #{$model->getKey()} was cancelled";
        
        if ($reason) {
            $description .= " - {$reason}";
            $additionalProperties['cancellation_reason'] = $reason;
        }

        self::logCustomEvent($model, 'cancelled', $description, $additionalProperties);
    }

    /**
     * Log when an approval is given.
     */
    public static function logApproved(Model $model, array $additionalProperties = []): void
    {
        $modelName = class_basename($model);
        $displayName = self::getModelDisplayName($modelName);
        $identifier = self::getModelIdentifier($model);
        
        $description = $identifier 
            ? "{$displayName} {$identifier} was approved"
            : "{$displayName} #{$model->getKey()} was approved";

        self::logCustomEvent($model, 'approved', $description, $additionalProperties);
    }

    /**
     * Log when a user or business is suspended.
     */
    public static function logSuspended(Model $model, string $reason = null, array $additionalProperties = []): void
    {
        $modelName = class_basename($model);
        $displayName = self::getModelDisplayName($modelName);
        $identifier = self::getModelIdentifier($model);
        
        $description = $identifier 
            ? "{$displayName} {$identifier} was suspended"
            : "{$displayName} #{$model->getKey()} was suspended";
        
        if ($reason) {
            $description .= " - {$reason}";
            $additionalProperties['suspension_reason'] = $reason;
        }

        self::logCustomEvent($model, 'suspended', $description, $additionalProperties);
    }

    /**
     * Log when a user or business is activated/reactivated.
     */
    public static function logActivated(Model $model, array $additionalProperties = []): void
    {
        $modelName = class_basename($model);
        $displayName = self::getModelDisplayName($modelName);
        $identifier = self::getModelIdentifier($model);
        
        $description = $identifier 
            ? "{$displayName} {$identifier} was activated"
            : "{$displayName} #{$model->getKey()} was activated";

        self::logCustomEvent($model, 'activated', $description, $additionalProperties);
    }

    /**
     * Log when a user or business is deactivated.
     */
    public static function logDeactivated(Model $model, array $additionalProperties = []): void
    {
        $modelName = class_basename($model);
        $displayName = self::getModelDisplayName($modelName);
        $identifier = self::getModelIdentifier($model);
        
        $description = $identifier 
            ? "{$displayName} {$identifier} was deactivated"
            : "{$displayName} #{$model->getKey()} was deactivated";

        self::logCustomEvent($model, 'deactivated', $description, $additionalProperties);
    }

    /**
     * Log when a role or permission is assigned.
     */
    public static function logAssigned(Model $model, Model $target, array $additionalProperties = []): void
    {
        $modelName = class_basename($model);
        $targetName = class_basename($target);
        $displayName = self::getModelDisplayName($modelName);
        $targetDisplayName = self::getModelDisplayName($targetName);
        $identifier = self::getModelIdentifier($model);
        $targetIdentifier = self::getModelIdentifier($target);
        
        $description = $identifier && $targetIdentifier
            ? "{$displayName} {$identifier} assigned to {$targetDisplayName} {$targetIdentifier}"
            : "{$displayName} #{$model->getKey()} assigned to {$targetDisplayName} #{$target->getKey()}";

        $additionalProperties['target_type'] = get_class($target);
        $additionalProperties['target_id'] = $target->getKey();

        self::logCustomEvent($model, 'assigned', $description, $additionalProperties);
    }

    /**
     * Log when a role or permission is revoked.
     */
    public static function logRevoked(Model $model, Model $target, array $additionalProperties = []): void
    {
        $modelName = class_basename($model);
        $targetName = class_basename($target);
        $displayName = self::getModelDisplayName($modelName);
        $targetDisplayName = self::getModelDisplayName($targetName);
        $identifier = self::getModelIdentifier($model);
        $targetIdentifier = self::getModelIdentifier($target);
        
        $description = $identifier && $targetIdentifier
            ? "{$displayName} {$identifier} revoked from {$targetDisplayName} {$targetIdentifier}"
            : "{$displayName} #{$model->getKey()} revoked from {$targetDisplayName} #{$target->getKey()}";

        $additionalProperties['target_type'] = get_class($target);
        $additionalProperties['target_id'] = $target->getKey();

        self::logCustomEvent($model, 'revoked', $description, $additionalProperties);
    }

    /**
     * Get display name for model.
     */
    private static function getModelDisplayName(string $modelName): string
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
     * Get identifier for the model.
     */
    private static function getModelIdentifier(Model $model): ?string
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
}

