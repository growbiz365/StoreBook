<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OwnerDrawing extends Model
{
    public const VOUCHER_TYPE = 'Owner Drawing';

    protected $fillable = [
        'business_id',
        'drawing_via',
        'bank_id',
        'from_account_id',
        'to_account_id',
        'amount',
        'drawing_date',
        'paid_via',
        'reference_number',
        'description',
        'created_by',
    ];

    protected $casts = [
        'drawing_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'to_account_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(OwnerDrawingAttachment::class);
    }

    public function scopeForBusiness($query, ?int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $businessId = session('active_business');
        if (! $businessId) {
            abort(403);
        }

        return $this->where($field ?: $this->getRouteKeyName(), $value)
            ->where('business_id', $businessId)
            ->firstOrFail();
    }
}
