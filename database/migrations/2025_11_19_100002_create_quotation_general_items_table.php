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
        Schema::create('quotation_general_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('general_item_id');
            $table->decimal('quantity', 14, 4);
            $table->decimal('sale_price', 18, 4); // quoted sale price per unit
            $table->decimal('line_total', 18, 4);
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
            $table->foreign('general_item_id')->references('id')->on('general_items')->onDelete('restrict');

            // Indexes
            $table->index(['quotation_id']);
            $table->index(['general_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_general_items');
    }
};

