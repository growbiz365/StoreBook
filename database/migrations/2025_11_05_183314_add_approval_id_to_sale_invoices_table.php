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
            if (!Schema::hasColumn('sale_invoices', 'approval_id')) {
                $table->foreignId('approval_id')->nullable()->after('party_id')->constrained('approvals')->onDelete('set null');
                $table->index('approval_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropForeign(['approval_id']);
            $table->dropIndex(['approval_id']);
            $table->dropColumn('approval_id');
        });
    }
};
