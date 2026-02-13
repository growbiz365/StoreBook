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
        Schema::create('purchase_arm_serials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_arm_line_id');
            $table->string('serial_no', 255);
            $table->string('arm_title', 255)->nullable();
            $table->unsignedBigInteger('make_id')->nullable();
            $table->unsignedBigInteger('caliber_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->decimal('purchase_price', 18, 4)->nullable(); // overrides unit_price if provided
            $table->decimal('sale_price', 18, 4)->nullable();
            $table->date('purchase_date')->nullable();
            $table->json('extra')->nullable(); // for additional arm details
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('purchase_arm_line_id')->references('id')->on('purchase_arm_lines')->onDelete('cascade');
            $table->foreign('make_id')->references('id')->on('arms_makes')->onDelete('set null');
            $table->foreign('caliber_id')->references('id')->on('arms_calibers')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('arms_categories')->onDelete('set null');

            // Unique constraint for serial number
            $table->unique('serial_no', 'uq_global_arm_serial');

            // Indexes
            $table->index('purchase_arm_line_id');
            $table->index(['make_id', 'caliber_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_arm_serials');
    }
};
