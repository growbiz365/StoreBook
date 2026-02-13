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
        // Drop the global unique constraint on serial_no
        Schema::table('arms', function (Blueprint $table) {
            $table->dropUnique(['serial_no']);
        });
        
        // Add a composite unique constraint for serial_no + business_id
        Schema::table('arms', function (Blueprint $table) {
            $table->unique(['serial_no', 'business_id'], 'arms_serial_business_unique');
        });
        
        // Also fix the purchase_arm_serials table if it exists
        if (Schema::hasTable('purchase_arm_serials')) {
            Schema::table('purchase_arm_serials', function (Blueprint $table) {
                // Drop the global unique constraint if it exists
                if (Schema::hasIndex('purchase_arm_serials', 'uq_global_arm_serial')) {
                    $table->dropUnique('uq_global_arm_serial');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the composite unique constraint
        Schema::table('arms', function (Blueprint $table) {
            $table->dropUnique('arms_serial_business_unique');
        });
        
        // Restore the global unique constraint
        Schema::table('arms', function (Blueprint $table) {
            $table->unique('serial_no');
        });
    }
};