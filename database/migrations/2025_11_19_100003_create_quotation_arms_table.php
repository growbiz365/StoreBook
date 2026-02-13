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
        Schema::create('quotation_arms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('arm_id'); // direct link to arms inventory
            $table->decimal('sale_price', 18, 4); // quoted sale price per arm
            $table->decimal('line_total', 18, 4);
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
            $table->foreign('arm_id')->references('id')->on('arms')->onDelete('restrict');

            // Indexes
            $table->index(['quotation_id']);
            $table->index(['arm_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_arms');
    }
};

