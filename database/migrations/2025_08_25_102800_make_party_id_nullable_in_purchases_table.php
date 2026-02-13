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
        Schema::table('purchases', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['party_id']);
            
            // Make party_id nullable
            $table->unsignedBigInteger('party_id')->nullable()->change();
            
            // Re-add the foreign key constraint with nullable support
            $table->foreign('party_id')->references('id')->on('parties')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['party_id']);
            
            // Make party_id NOT NULL again
            $table->unsignedBigInteger('party_id')->nullable(false)->change();
            
            // Re-add the original foreign key constraint
            $table->foreign('party_id')->references('id')->on('parties')->onDelete('restrict');
        });
    }
};
