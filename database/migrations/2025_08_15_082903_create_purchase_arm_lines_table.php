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
        Schema::create('purchase_arm_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->integer('line_no');
            $table->text('description')->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 18, 4); // default per-arm price for the line
            $table->decimal('sale_price', 18, 4)->default(0);
            $table->unsignedBigInteger('arm_type_id')->nullable();
            $table->unsignedBigInteger('arm_make_id')->nullable();
            $table->unsignedBigInteger('arm_caliber_id')->nullable();
            $table->unsignedBigInteger('arm_category_id')->nullable();
            $table->unsignedBigInteger('arm_condition_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('arm_type_id')->references('id')->on('arms_types')->onDelete('set null');
            $table->foreign('arm_make_id')->references('id')->on('arms_makes')->onDelete('set null');
            $table->foreign('arm_caliber_id')->references('id')->on('arms_calibers')->onDelete('set null');
            $table->foreign('arm_category_id')->references('id')->on('arms_categories')->onDelete('set null');
            $table->foreign('arm_condition_id')->references('id')->on('arms_conditions')->onDelete('set null');

            // Indexes
            $table->index(['purchase_id', 'line_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_arm_lines');
    }
};
