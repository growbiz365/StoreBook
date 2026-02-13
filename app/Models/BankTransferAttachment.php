<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankTransferAttachment extends Model
{
    protected $fillable = [
        'bank_transfer_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'user_id',
    ];

    public function bankTransfer(): BelongsTo
    {
        return $this->belongsTo(BankTransfer::class);
    }

    
}