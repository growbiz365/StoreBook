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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('party_id'); // customer
            $table->date('quotation_date');
            $table->date('valid_until'); // expiry date
            $table->enum('payment_type', ['cash', 'credit'])->default('credit');
            $table->unsignedBigInteger('bank_id')->nullable(); // for cash quotations
            $table->decimal('subtotal', 18, 4)->default(0);
            $table->decimal('shipping_charges', 18, 4)->default(0);
            $table->decimal('total_amount', 18, 4)->default(0);
            $table->enum('status', ['sent', 'expired', 'rejected', 'converted'])->default('sent');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('converted_to_sale_id')->nullable(); // link to sale invoice
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('party_id')->references('id')->on('parties')->onDelete('restrict');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('converted_to_sale_id')->references('id')->on('sale_invoices')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'party_id']);
            $table->index(['business_id', 'quotation_date']);
            $table->index(['converted_to_sale_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};

