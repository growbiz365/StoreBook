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
        // Add 'reversal' to the transaction_type enum
        DB::statement("ALTER TABLE arms_stock_ledger MODIFY COLUMN transaction_type ENUM('opening_stock', 'purchase', 'sale', 'adjustment', 'transfer', 'return', 'damage', 'theft', 'reversal', 'other') DEFAULT 'opening_stock'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'reversal' from the transaction_type enum
        DB::statement("ALTER TABLE arms_stock_ledger MODIFY COLUMN transaction_type ENUM('opening_stock', 'purchase', 'sale', 'adjustment', 'transfer', 'return', 'damage', 'theft', 'other') DEFAULT 'opening_stock'");
    }
};
