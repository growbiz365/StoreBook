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
        Schema::create('purchase_general_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->integer('line_no');
            $table->unsignedBigInteger('general_item_id');
            $table->text('description')->nullable();
            $table->decimal('qty', 14, 4);
            $table->decimal('unit_price', 18, 4);
            $table->decimal('sale_price', 18, 4)->default(0);
            $table->decimal('line_total', 18, 4); // qty * unit_price
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('general_item_id')->references('id')->on('general_items')->onDelete('restrict');

            // Indexes
            $table->index(['purchase_id', 'line_no']);
            $table->index('general_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_general_lines');
    }
};
