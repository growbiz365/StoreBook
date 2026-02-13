<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArmsCaliber extends Model
{
    protected $fillable = [
        'arm_caliber',
        'business_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function setArmCaliberAttribute($value): void
    {
        $this->attributes['arm_caliber'] = $value !== null ? Str::upper($value) : $value;
    }

    public function getArmCaliberAttribute($value)
    {
        return $value !== null ? Str::upper($value) : $value;
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
