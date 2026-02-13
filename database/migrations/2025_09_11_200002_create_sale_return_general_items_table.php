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
        Schema::create('sale_return_general_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->unsignedBigInteger('general_item_id');
            $table->unsignedBigInteger('batch_id')->nullable(); // for FIFO return
            $table->decimal('quantity', 14, 4);
            $table->decimal('return_price', 18, 4); // return price per unit
            $table->decimal('line_total', 18, 4);
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('sale_return_id')->references('id')->on('sale_returns')->onDelete('cascade');
            $table->foreign('general_item_id')->references('id')->on('general_items')->onDelete('restrict');
            $table->foreign('batch_id')->references('id')->on('general_batches')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index(['sale_return_id']);
            $table->index(['general_item_id']);
            $table->index(['batch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_general_items');
    }
};

