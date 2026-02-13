<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        // Business info
        'business_name',
        'owner_name',
        'cnic',
        'contact_no',
        'email',
        'address',
        'country_id',
        'timezone_id',
        'currency_id',
        'date_format',
        'package_id',

        // Store info
        'store_name',
        'store_license_number',
        'license_expiry_date',
        'issuing_authority',
        'store_type',
        'ntn',
        'strn',
        'store_phone',
        'store_email',
        'store_address',
        'store_city_id',
        'store_country_id',
        'store_postal_code',

        // Suspension fields
        'is_suspended',
        'suspended_at',
        'suspension_reason',
    ];

    /**
     * Get the country that owns the business.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the timezone that owns the business.
     */
    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    /**
     * Get the currency that owns the business.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the users that belong to the business.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'business_user', 'business_id', 'user_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'store_city_id');
    }

    public function storeCity()
    {
        return $this->belongsTo(City::class, 'store_city_id');
    }

    public function storeCountry()
    {
        return $this->belongsTo(Country::class, 'store_country_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Check if the business is suspended
     */
    public function isSuspended(): bool
    {
        return $this->is_suspended;
    }

    /**
     * Suspend the business
     */
    public function suspend(string $reason = null): void
    {
        $this->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => $reason,
        ]);
    }

    /**
     * Unsuspend the business
     */
    public function unsuspend(): void
    {
        $this->update([
            'is_suspended' => false,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);
    }
}