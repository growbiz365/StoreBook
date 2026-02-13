<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arms_history', function (Blueprint $table) {
            $table->id();

            // Context
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('arm_id')->constrained('arms')->onDelete('cascade');

            // Action Type
            $table->enum('action', [
                'opening', 
                'purchase', 
                'sale', 
                'transfer', 
                'repair', 
                'decommission', 
                'price_adjustment', 
                'edit', 
                'delete'
            ])->default('opening');

            // Before & After values (for audit)
            $table->json('old_values')->nullable(); // store fields before change
            $table->json('new_values')->nullable(); // store fields after change

            // Transaction details
            $table->date('transaction_date');
            $table->decimal('price', 15, 2)->nullable();
            $table->text('remarks')->nullable();

            // Who did it
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // IP & device info for forensic audit
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['business_id', 'arm_id']);
            $table->index(['business_id', 'action']);
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arms_history');
    }
};
