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
        // Add sale_price to arms table if it doesn't exist
        if (Schema::hasTable('arms') && !Schema::hasColumn('arms', 'sale_price')) {
            Schema::table('arms', function (Blueprint $table) {
                $table->decimal('sale_price', 15, 2)->after('purchase_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove sale_price from arms table
        if (Schema::hasTable('arms') && Schema::hasColumn('arms', 'sale_price')) {
            Schema::table('arms', function (Blueprint $table) {
                $table->dropColumn('sale_price');
            });
        }
    }
};
