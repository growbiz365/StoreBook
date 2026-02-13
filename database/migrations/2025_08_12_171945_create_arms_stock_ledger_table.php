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
        Schema::create('arms_stock_ledger', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('arm_id')->constrained('arms')->onDelete('cascade');
            
            // Transaction Details
            $table->date('transaction_date');
            $table->enum('transaction_type', [
                'opening_stock',
                'purchase',
                'sale',
                'adjustment',
                'transfer',
                'return',
                'damage',
                'theft',
                'reversal',
                'other'
            ])->default('opening_stock');
            
            // Quantity Fields
            $table->integer('quantity_in')->nullable()->default(0);
            $table->integer('quantity_out')->nullable()->default(0);
            $table->integer('balance')->comment('Running total after transaction');
            
            // Reference and Remarks
            $table->string('reference_id')->nullable()->comment('Points to related purchase/sale/etc.');
            $table->text('remarks')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'arm_id']);
            $table->index(['business_id', 'transaction_date']);
            $table->index(['arm_id', 'transaction_date']);
            $table->index('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arms_stock_ledger');
    }
};
