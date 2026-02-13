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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('party_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('return_type', ['cash', 'credit'])->default('credit');
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('original_purchase_id')->nullable()->constrained('purchases')->onDelete('set null');
            $table->date('return_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('shipping_charges', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('posted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'return_date']);
            $table->index(['business_id', 'party_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
