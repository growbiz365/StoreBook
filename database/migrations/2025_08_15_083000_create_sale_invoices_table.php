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
        Schema::create('sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('party_id'); // customer
            $table->enum('sale_type', ['cash', 'credit'])->default('credit');
            $table->unsignedBigInteger('bank_id')->nullable(); // for cash sales
            $table->date('invoice_date');
            $table->decimal('subtotal', 18, 4)->default(0);
            $table->decimal('shipping_charges', 18, 4)->default(0);
            $table->decimal('total_amount', 18, 4)->default(0);
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('party_id')->references('id')->on('parties')->onDelete('restrict');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'party_id']);
            $table->index(['business_id', 'invoice_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoices');
    }
};
