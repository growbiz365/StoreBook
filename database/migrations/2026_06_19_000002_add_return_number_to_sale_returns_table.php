<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-business return numbers aligned with legacy ids for existing rows.
     */
    public function up(): void
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->unsignedInteger('return_number')->nullable()->after('business_id');
        });

        DB::statement('UPDATE sale_returns SET return_number = id');

        Schema::table('sale_returns', function (Blueprint $table) {
            $table->unsignedInteger('return_number')->nullable(false)->change();
            $table->unique(['business_id', 'return_number'], 'sale_returns_business_return_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropUnique('sale_returns_business_return_number_unique');
            $table->dropColumn('return_number');
        });
    }
};
