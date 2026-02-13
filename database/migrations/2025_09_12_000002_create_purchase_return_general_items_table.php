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
        Schema::create('purchase_return_general_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained()->onDelete('cascade');
            $table->foreignId('general_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('general_batches')->onDelete('set null');
            $table->decimal('quantity', 10, 2);
            $table->decimal('return_price', 15, 2);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['purchase_return_id', 'general_item_id'], 'prgi_pr_item_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_general_items');
    }
};
