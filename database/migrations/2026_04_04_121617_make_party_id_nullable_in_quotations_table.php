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
        Schema::table('quotations', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['party_id']);

            // Make party_id nullable (cash quotations don't require a party)
            $table->unsignedBigInteger('party_id')->nullable()->change();

            // Re-add the foreign key allowing null
            $table->foreign('party_id')->references('id')->on('parties')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['party_id']);
            $table->unsignedBigInteger('party_id')->nullable(false)->change();
            $table->foreign('party_id')->references('id')->on('parties')->onDelete('restrict');
        });
    }
};
