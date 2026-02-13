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
        Schema::create('approval_arms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->constrained('approvals')->onDelete('cascade');
            $table->foreignId('arm_id')->constrained('arms')->onDelete('restrict');
            $table->decimal('sale_price', 18, 4);
            $table->enum('status', ['pending', 'returned', 'sold'])->default('pending');
            $table->date('returned_date')->nullable();
            $table->date('sold_date')->nullable();
            $table->unsignedBigInteger('sale_invoice_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('sale_invoice_id')->references('id')->on('sale_invoices')->onDelete('set null');

            // Indexes
            $table->index(['approval_id']);
            $table->index(['arm_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_arms');
    }
};
