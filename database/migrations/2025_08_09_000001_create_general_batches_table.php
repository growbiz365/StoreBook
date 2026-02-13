<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('general_items')->cascadeOnDelete();
            $table->integer('qty_received');
            $table->integer('qty_remaining');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->date('received_date');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_batches');
    }
};


