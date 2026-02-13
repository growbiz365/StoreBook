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
        Schema::create('sale_invoice_arms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_invoice_id');
            $table->unsignedBigInteger('arm_id'); // direct link to arms inventory
            $table->decimal('sale_price', 18, 4); // sale price per arm
            $table->decimal('line_total', 18, 4);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('sale_invoice_id')->references('id')->on('sale_invoices')->onDelete('cascade');
            $table->foreign('arm_id')->references('id')->on('arms')->onDelete('restrict');

            // Indexes
            $table->index(['sale_invoice_id']);
            $table->index(['arm_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoice_arms');
    }
};
