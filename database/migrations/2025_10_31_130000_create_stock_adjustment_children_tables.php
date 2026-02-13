<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->onDelete('cascade');
            $table->foreignId('general_item_id')->constrained('general_items')->onDelete('cascade');
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();

            $table->index(['stock_adjustment_id']);
        });

        Schema::create('stock_adjustment_arms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->onDelete('cascade');
            $table->foreignId('arm_id')->constrained('arms')->onDelete('cascade');
            $table->enum('reason', ['damage', 'theft']);
            $table->decimal('price', 15, 2)->nullable();
            $table->timestamps();

            $table->index(['stock_adjustment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_arms');
        Schema::dropIfExists('stock_adjustment_items');
    }
};


