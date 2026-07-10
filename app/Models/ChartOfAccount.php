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

    public static function partyAccountStartCode(): int
    {
        return 2110;
    }

    /**
     * Root liability COA rows used by the standard chart (not individual parties).
     */
    public static function reservedRootLiabilityCodes(): array
    {
        return [2000];
    }

    public function isPartyLedgerAccount(): bool
    {
        $code = (int) $this->code;

        return $this->type === 'liability'
            && $this->parent_id === null
            && $code >= self::partyAccountStartCode()
            && ! in_array($code, self::reservedRootLiabilityCodes(), true);
    }

    public static function partyAccountCodeQuery($query, int $businessId)
    {
        return $query
            ->where('business_id', $businessId)
            ->where('type', 'liability')
            ->whereNull('parent_id')
            ->whereRaw('CAST(code AS UNSIGNED) >= ?', [self::partyAccountStartCode()]);
    }

    /**
     * Generate a unique account code for party accounts.
     * Party accounts are root liability rows starting at 2110.
     * Codes continue beyond 2999 when needed (bulk imports may already use higher codes).
     */
    public static function generatePartyAccountCode(int $businessId): string
    {
        $startCode = self::partyAccountStartCode();
        $reservedCodes = self::reservedRootLiabilityCodes();

        $existingCodes = self::partyAccountCodeQuery(self::query(), $businessId)
            ->pluck('code')
            ->map(fn ($code) => (int) $code)
            ->filter(fn ($code) => ! in_array($code, $reservedCodes, true))
            ->sort()
            ->values()
            ->toArray();

        $nextCode = $startCode;

        foreach ($existingCodes as $existingCode) {
            if ($existingCode >= $nextCode) {
                if ($existingCode === $nextCode) {
                    $nextCode++;
                } else {
                    break;
                }
            }
        }

        if ($nextCode > 999999) {
            throw new \Exception('Maximum number of party accounts reached. Cannot create more party accounts.');
        }

        return (string) $nextCode;
    }

    /**
     * Create chart of account for a party.
     * Creates party accounts as root liability accounts (no parent), starting at 2110.
     */
    public static function createPartyAccount(string $partyName, int $businessId): self
    {
        $code = self::generatePartyAccountCode($businessId);

        $existingAccount = self::partyAccountCodeQuery(self::query(), $businessId)
            ->where('name', $partyName)
            ->first();

        if ($existingAccount) {
            return $existingAccount;
        }

        return self::create([
            'code' => $code,
            'name' => $partyName,
            'type' => 'liability',
            'parent_id' => null,
            'business_id' => $businessId,
            'is_default' => false,
            'is_active' => true,
        ]);
    }
}
