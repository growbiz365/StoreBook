<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-business sale numbers. Existing rows keep legacy labels (sale_number = id).
     */
    public function up(): void
    {
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->unsignedInteger('sale_number')->nullable()->after('business_id');
            $table->unique(['business_id', 'sale_number'], 'sale_invoices_business_sale_number_unique');
        });

        DB::statement('UPDATE sale_invoices SET sale_number = id');

        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->unsignedInteger('sale_number')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropUnique('sale_invoices_business_sale_number_unique');
            $table->dropColumn('sale_number');
        });
    }
};
