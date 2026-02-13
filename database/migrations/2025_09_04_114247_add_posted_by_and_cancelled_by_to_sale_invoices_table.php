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
            $table->unsignedBigInteger('posted_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('posted_by');
            
            // Foreign key constraints
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropForeign(['posted_by']);
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['posted_by', 'cancelled_by']);
        });
    }
};
