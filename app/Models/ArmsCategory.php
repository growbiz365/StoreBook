<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArmsCategory extends Model
{
    protected $fillable = [
        'arm_category',
        'business_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function setArmCategoryAttribute($value): void
    {
        $this->attributes['arm_category'] = $value !== null ? Str::upper($value) : $value;
    }

    public function getArmCategoryAttribute($value)
    {
        return $value !== null ? Str::upper($value) : $value;
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}

