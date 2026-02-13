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
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('party_id')->nullable(); // customer (nullable for cash returns)
            $table->enum('return_type', ['cash', 'credit'])->default('credit');
            $table->unsignedBigInteger('bank_id')->nullable(); // for cash returns
            $table->unsignedBigInteger('original_sale_invoice_id')->nullable(); // reference to original sale
            $table->date('return_date');
            $table->decimal('subtotal', 18, 4)->default(0);
            $table->decimal('shipping_charges', 18, 4)->default(0);
            $table->decimal('total_amount', 18, 4)->default(0);
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->text('reason')->nullable(); // reason for return
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('party_id')->references('id')->on('parties')->onDelete('restrict');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
            $table->foreign('original_sale_invoice_id')->references('id')->on('sale_invoices')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'party_id']);
            $table->index(['business_id', 'return_date']);
            $table->index(['original_sale_invoice_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_returns');
    }
};

