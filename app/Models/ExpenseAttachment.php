<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseAttachment extends Model
{
    protected $fillable = [
        'expense_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'user_id',
    ];

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
