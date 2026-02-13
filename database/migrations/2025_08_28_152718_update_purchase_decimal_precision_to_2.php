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
        // Update purchases table
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('subtotal', 18, 2)->default(0)->change();
            $table->decimal('shipping_charges', 18, 2)->default(0)->change();
            $table->decimal('total_amount', 18, 2)->default(0)->change();
        });

        // Update purchase_general_lines table
        Schema::table('purchase_general_lines', function (Blueprint $table) {
            $table->decimal('qty', 14, 2)->change();
            $table->decimal('unit_price', 18, 2)->change();
            $table->decimal('sale_price', 18, 2)->default(0)->change();
            $table->decimal('line_total', 18, 2)->change();
        });

        // Update purchase_arm_lines table
        Schema::table('purchase_arm_lines', function (Blueprint $table) {
            $table->decimal('unit_price', 18, 2)->change();
            $table->decimal('sale_price', 18, 2)->default(0)->change();
        });

        // Update purchase_arm_serials table
        Schema::table('purchase_arm_serials', function (Blueprint $table) {
            $table->decimal('purchase_price', 18, 2)->nullable()->change();
            $table->decimal('sale_price', 18, 2)->nullable()->change();
        });

        // Update general_items_stock_ledger table
        Schema::table('general_items_stock_ledger', function (Blueprint $table) {
            $table->decimal('quantity', 14, 2)->change();
            $table->decimal('quantity_in', 14, 2)->default(0)->change();
            $table->decimal('quantity_out', 14, 2)->default(0)->change();
            $table->decimal('balance_quantity', 14, 2)->change();
            $table->decimal('unit_cost', 18, 2)->nullable()->change();
            $table->decimal('total_cost', 18, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert purchases table
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('subtotal', 18, 4)->default(0)->change();
            $table->decimal('shipping_charges', 18, 4)->default(0)->change();
            $table->decimal('total_amount', 18, 4)->default(0)->change();
        });

        // Revert purchase_general_lines table
        Schema::table('purchase_general_lines', function (Blueprint $table) {
            $table->decimal('qty', 14, 4)->change();
            $table->decimal('unit_price', 18, 4)->change();
            $table->decimal('sale_price', 18, 4)->default(0)->change();
            $table->decimal('line_total', 18, 4)->change();
        });

        // Revert purchase_arm_lines table
        Schema::table('purchase_arm_lines', function (Blueprint $table) {
            $table->decimal('unit_price', 18, 4)->change();
            $table->decimal('sale_price', 18, 4)->default(0)->change();
        });

        // Revert purchase_arm_serials table
        Schema::table('purchase_arm_serials', function (Blueprint $table) {
            $table->decimal('purchase_price', 18, 4)->nullable()->change();
            $table->decimal('sale_price', 18, 4)->nullable()->change();
        });

        // Revert general_items_stock_ledger table
        Schema::table('general_items_stock_ledger', function (Blueprint $table) {
            $table->decimal('quantity', 14, 4)->change();
            $table->decimal('quantity_in', 14, 4)->default(0)->change();
            $table->decimal('quantity_out', 14, 4)->default(0)->change();
            $table->decimal('balance_quantity', 14, 4)->change();
            $table->decimal('unit_cost', 18, 4)->nullable()->change();
            $table->decimal('total_cost', 18, 4)->nullable()->change();
        });
    }
};
