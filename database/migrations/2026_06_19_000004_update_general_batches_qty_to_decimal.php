<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Support fractional quantities (e.g. 2.5) in batch stock tracking.
     */
    public function up(): void
    {
        Schema::table('general_batches', function (Blueprint $table) {
            $table->decimal('qty_received', 14, 2)->change();
            $table->decimal('qty_remaining', 14, 2)->change();
        });

        // Align batch remaining qty with stock ledger (source of truth per batch).
        DB::statement('
            UPDATE general_batches gb
            INNER JOIN (
                SELECT batch_id, ROUND(SUM(quantity), 2) AS net_qty
                FROM general_items_stock_ledger
                WHERE batch_id IS NOT NULL
                GROUP BY batch_id
            ) led ON led.batch_id = gb.id
            SET gb.qty_remaining = GREATEST(0, led.net_qty)
        ');
    }

    public function down(): void
    {
        Schema::table('general_batches', function (Blueprint $table) {
            $table->integer('qty_received')->change();
            $table->integer('qty_remaining')->change();
        });
    }
};
