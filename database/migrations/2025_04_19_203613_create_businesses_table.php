<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
        
            // Business profile
            $table->string('business_name');
            $table->string('owner_name');
            $table->string('cnic');
            $table->string('contact_no');
            $table->string('email')->unique();
            $table->text('address');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->foreignId('timezone_id')->constrained()->onDelete('cascade');
            $table->foreignId('currency_id')->constrained()->onDelete('cascade');
            $table->string('date_format')->comment('Allowed: Y-m-d, d/m/Y, m/d/Y');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
        
            // Store Info (nullable)
            $table->string('store_name')->nullable();
            $table->string('store_license_number')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->string('store_type')->nullable();
            $table->string('ntn')->nullable();
            $table->string('strn')->nullable();
            $table->string('store_phone')->nullable();
            $table->string('store_email')->nullable();
            $table->text('store_address')->nullable();
            $table->foreignId('store_city_id')->nullable() ->constrained('cities')->nullOnDelete();      // references id on cities
            $table->foreignId('store_country_id')->nullable()->constrained('countries')->nullOnDelete();   // references id on countries
      
            $table->string('store_postal_code')->nullable();
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
