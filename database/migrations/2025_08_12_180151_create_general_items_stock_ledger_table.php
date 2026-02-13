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
        Schema::create('general_items_stock_ledger', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('general_item_id')->constrained('general_items')->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('general_batches')->onDelete('set null');
            
            // Transaction Details
            $table->enum('transaction_type', [
                'opening',
                'purchase', 
                'issue',
                'sale',
                'adjustment',
                'reversal',
                'edit',
                'stock_adjustment',
                'return'

            ])->default('opening');
            $table->timestamp('transaction_date');
            
            // Quantity Fields (Enhanced)
            $table->decimal('quantity', 14, 4)->comment('Positive for IN, negative for OUT');
            $table->decimal('quantity_in', 14, 4)->default(0)->comment('Calculated from quantity');
            $table->decimal('quantity_out', 14, 4)->default(0)->comment('Calculated from quantity');
            $table->decimal('balance_quantity', 14, 4)->comment('Running balance after transaction');
            
            // Cost Fields (Enhanced precision)
            $table->decimal('unit_cost', 18, 4)->nullable()->comment('Purchase rate per unit');
            $table->decimal('total_cost', 18, 4)->nullable()->comment('qty * unit cost');
            
            // Reference Fields
            $table->string('reference_no')->nullable()->comment('Legacy reference field');
            $table->string('reference_id')->nullable()->comment('Invoice no / import_id / reference');
            
            // Additional Fields
            $table->text('remarks')->nullable();
            $table->unsignedInteger('created_by')->comment('User who created this entry');
            $table->timestamps();
            
            // Enhanced Indexes
            $table->index(['business_id', 'general_item_id', 'transaction_date'], 'gen_item_stock_ledger_business_item_date_idx');
            $table->index(['general_item_id', 'transaction_date'], 'gen_item_stock_ledger_item_date_idx');
            $table->index('batch_id', 'gen_item_stock_ledger_batch_idx');
            $table->index('transaction_type', 'gen_item_stock_ledger_type_idx');
            $table->index('reference_id', 'gen_item_stock_ledger_ref_id_idx');
            $table->index('created_by', 'gen_item_stock_ledger_user_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_items_stock_ledger');
    }
};
