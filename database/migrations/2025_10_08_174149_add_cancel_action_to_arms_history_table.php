<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'cancel' to the existing enum (including 'return' which was added previously)
        DB::statement("ALTER TABLE arms_history MODIFY COLUMN action ENUM('opening', 'purchase', 'sale', 'transfer', 'repair', 'decommission', 'price_adjustment', 'edit', 'cancel', 'delete', 'return') DEFAULT 'opening'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'cancel' from the enum (keeping 'return' which was added previously)
        DB::statement("ALTER TABLE arms_history MODIFY COLUMN action ENUM('opening', 'purchase', 'sale', 'transfer', 'repair', 'decommission', 'price_adjustment', 'edit', 'delete', 'return') DEFAULT 'opening'");
    }
};