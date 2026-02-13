<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    use HasFactory;

    protected $fillable = ['timezone_name', 'utc_offset'];

    // You can add relationships or additional methods here if needed
}
