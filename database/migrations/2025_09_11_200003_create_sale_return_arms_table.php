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
        Schema::create('sale_return_arms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->unsignedBigInteger('arm_id');
            $table->decimal('return_price', 18, 4); // return price per arm
            $table->decimal('line_total', 18, 4);
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('sale_return_id')->references('id')->on('sale_returns')->onDelete('cascade');
            $table->foreign('arm_id')->references('id')->on('arms')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index(['sale_return_id']);
            $table->index(['arm_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_arms');
    }
};

