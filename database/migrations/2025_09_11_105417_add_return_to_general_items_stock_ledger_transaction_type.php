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
        // Add 'return' and 'stock_adjustment' to the transaction_type enum
        DB::statement("ALTER TABLE general_items_stock_ledger MODIFY COLUMN transaction_type ENUM('opening', 'purchase', 'issue', 'sale', 'adjustment', 'reversal', 'edit', 'return', 'stock_adjustment') DEFAULT 'opening'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'return' and 'stock_adjustment' from the transaction_type enum
        DB::statement("ALTER TABLE general_items_stock_ledger MODIFY COLUMN transaction_type ENUM('opening', 'purchase', 'issue', 'sale', 'adjustment', 'reversal', 'edit') DEFAULT 'opening'");
    }
};