<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralVoucherAttachment extends Model
{
    protected $fillable = [
        'general_voucher_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'user_id',
    ];

    public function generalVoucher(): BelongsTo
    {
        return $this->belongsTo(GeneralVoucher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}