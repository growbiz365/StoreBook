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
        // Add 'pending_approval' to the status enum
        DB::statement("ALTER TABLE arms MODIFY COLUMN status ENUM('available', 'sold', 'under_repair', 'decommissioned', 'pending_approval') DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'pending_approval' from the status enum
        DB::statement("ALTER TABLE arms MODIFY COLUMN status ENUM('available', 'sold', 'under_repair', 'decommissioned') DEFAULT 'available'");
    }
};
