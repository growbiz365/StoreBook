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
        // Add 'approval' to the action enum (including 'cancel' which was added previously)
        DB::statement("ALTER TABLE arms_history MODIFY COLUMN action ENUM('opening', 'purchase', 'sale', 'transfer', 'repair', 'decommission', 'price_adjustment', 'edit', 'cancel', 'delete', 'return', 'approval') DEFAULT 'opening'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'approval' from the action enum (keeping 'cancel' which was added previously)
        DB::statement("ALTER TABLE arms_history MODIFY COLUMN action ENUM('opening', 'purchase', 'sale', 'transfer', 'repair', 'decommission', 'price_adjustment', 'edit', 'cancel', 'delete', 'return') DEFAULT 'opening'");
    }
};
