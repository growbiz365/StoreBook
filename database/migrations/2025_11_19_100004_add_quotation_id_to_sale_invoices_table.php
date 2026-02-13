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
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('quotation_id')->nullable()->after('approval_id');
            
            // Foreign key constraint
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('set null');
            
            // Index
            $table->index(['quotation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
            $table->dropIndex(['quotation_id']);
            $table->dropColumn('quotation_id');
        });
    }
};

