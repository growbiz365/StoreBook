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
        Schema::create('approval_general_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->constrained('approvals')->onDelete('cascade');
            $table->foreignId('general_item_id')->constrained('general_items')->onDelete('restrict');
            $table->foreignId('batch_id')->nullable(); // for tracking specific batch
            $table->decimal('quantity', 14, 4);
            $table->decimal('sale_price', 18, 4);
            $table->decimal('line_total', 18, 4);
            $table->decimal('returned_quantity', 14, 4)->default(0);
            $table->decimal('sold_quantity', 14, 4)->default(0);
            $table->decimal('remaining_quantity', 14, 4); // quantity - returned - sold
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('batch_id')->references('id')->on('general_batches')->onDelete('set null');

            // Indexes
            $table->index(['approval_id']);
            $table->index(['general_item_id']);
            $table->index(['batch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_general_items');
    }
};
