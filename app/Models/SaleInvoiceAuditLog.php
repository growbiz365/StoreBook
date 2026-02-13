<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleInvoiceAuditLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sale_invoice_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
        'deleted_by',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relationships
    public function saleInvoice(): BelongsTo
    {
        return $this->belongsTo(SaleInvoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
