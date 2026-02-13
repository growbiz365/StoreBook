<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'business_id',
        'parent_id',
        'code',
        'name',
        'type',
        'description',
        'is_default',
        'is_active',
        'bank_name',
        'account_number',
        'branch_code',
        'iban',
        'swift_code',
        'bank_address'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id')->orderBy('code');
    }

    // Recursive relationship for all descendants
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    // Get all parent accounts (no parent_id)
    public function scopeParentAccounts($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'account_head');
    }

    public function expenseHead(): HasMany
    {
        return $this->hasMany(ExpenseHead::class, 'chart_of_account_id');
    }

    public function incomeHead(): HasMany
    {
        return $this->hasMany(IncomeHead::class, 'chart_of_account_id');
    }

    public static function getCashOrBankAccountId($paymentMode)
    {
        $account = match ($paymentMode) {
            'cash' => self::where('name', 'Cash in Hand')
                ->where('business_id', session('active_business'))
                ->first(),
            'bank_transfer', 'cheque', 'online' => self::where('name', 'Bank Accounts')
                ->where('business_id', session('active_business'))
                ->first(),
            default => null
        };

        if (!$account) {
            throw new \Exception('Required account not found: ' .
                ($paymentMode === 'cash' ? 'Cash in Hand' : 'Bank Accounts'));
        }

        return $account->id;
    }

    public static function getFeeIncomeAccountId()
    {
        $account = self::where('name', 'Fee Income')
            ->where('business_id', session('active_business'))
            ->first();

        if (!$account) {
            throw new \Exception('Required account not found: Fee Income');
        }

        return $account->id;
    }

    public static function getFeeDiscountAccountId()
    {
        $account = self::where('name', 'Fee Discount Allowed')
            ->where('business_id', session('active_business'))
            ->first();

        if (!$account) {
            throw new \Exception('Required account not found: Fee Discount');
        }

        return $account->id;
    }

    public static function getInventoryAssetAccountId(): int
    {
        $businessId = session('active_business');
        
        // First try to find existing Inventory account (1250)
        $account = self::where('code', '1250')
            ->where('business_id', $businessId)
            ->first();

        if ($account) {
            return $account->id;
        }

        // Fallback: try to find by name
        $account = self::where('name', 'Inventory')
            ->where('business_id', $businessId)
            ->first();

        if ($account) {
            return $account->id;
        }

        // If not found, throw an exception
        throw new \Exception('Inventory account (1250) not found. Please ensure the chart of accounts is properly set up.');
    }

    public static function getOpeningStockEquityAccountId(): int
    {
        $businessId = session('active_business');
        $account = self::where('name', 'Opening Stock Equity')
            ->where('business_id', $businessId)
            ->first();

        if ($account) {
            return $account->id;
        }

        $equity = self::where('name', 'Equity')->where('business_id', $businessId)->first();
        if (!$equity) {
            throw new \Exception('Equity account not found for business. Please seed chart of accounts.');
        }

        $created = self::create([
            'code' => '5110',
            'name' => 'Opening Stock Equity',
            'type' => 'equity',
            'parent_id' => $equity->id,
            'business_id' => $businessId,
            'is_default' => true,
        ]);

        return $created->id;
    }

    /**
     * Generate a unique account code for party accounts
     * Party accounts are created as root accounts in the liability range (2000-2999)
     * Starts from 2110 and finds next available code sequentially
     * 
     * @param int $businessId
     * @return string
     */
    public static function generatePartyAccountCode(int $businessId): string
    {
        // Start from 2110 (after Accounts Payable 2102)
        $startCode = 2110;
        $endCode = 2999; // Full liability range
        
        // Get all existing codes in the liability range
        $existingCodes = self::where('business_id', $businessId)
            ->where('code', '>=', $startCode)
            ->where('code', '<=', $endCode)
            ->pluck('code')
            ->map(fn($code) => (int) $code)
            ->sort()
            ->values()
            ->toArray();
        
        // Find the first available code starting from 2110
        $nextCode = $startCode;
        
        // If there are existing codes, find the next available one
        if (!empty($existingCodes)) {
            // Find gaps in the sequence
            foreach ($existingCodes as $existingCode) {
                if ($existingCode >= $nextCode) {
                    if ($existingCode == $nextCode) {
                        // This code is taken, try next
                        $nextCode++;
                    } else {
                        // Found a gap, use it
                        break;
                    }
                }
            }
        }
        
        // Ensure we don't exceed the liability range
        if ($nextCode > $endCode) {
            throw new \Exception('Maximum number of party accounts reached in liability range (2000-2999). Cannot create more party accounts.');
        }
        
        return str_pad($nextCode, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create chart of account for a party
     * Creates party accounts as root accounts (no parent) in the liability range (2000-2999)
     * 
     * @param string $partyName
     * @param int $businessId
     * @return self
     */
    public static function createPartyAccount(string $partyName, int $businessId): self
    {
        // Generate unique code in liability range (starts from 2110)
        $code = self::generatePartyAccountCode($businessId);

        // Check if account with this name already exists (any liability account)
        $existingAccount = self::where('name', $partyName)
            ->where('business_id', $businessId)
            ->where('type', 'liability')
            ->where('code', '>=', '2110') // Party accounts start from 2110
            ->first();

        if ($existingAccount) {
            return $existingAccount;
        }

        // Create the account as a root account (no parent_id)
        return self::create([
            'code' => $code,
            'name' => $partyName, // Just the party name, no "Party:" prefix
            'type' => 'liability',
            'parent_id' => null, // Root account, not a child of Accounts Payable
            'business_id' => $businessId,
            'is_default' => false,
            'is_active' => true,
        ]);
    }
}
