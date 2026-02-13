<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ItemType extends Model
{
    protected $fillable = [
        'item_type',
        'business_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function setItemTypeAttribute($value): void
    {
        $this->attributes['item_type'] = $value !== null ? Str::upper($value) : $value;
    }

    public function getItemTypeAttribute($value)
    {
        return $value !== null ? Str::upper($value) : $value;
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}

