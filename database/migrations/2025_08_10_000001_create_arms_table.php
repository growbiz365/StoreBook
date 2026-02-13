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
        Schema::create('arms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('arm_type_id')->constrained('arms_types')->onDelete('cascade');
            $table->foreignId('arm_category_id')->constrained('arms_categories')->onDelete('cascade');
            $table->string('make');
            $table->foreignId('arm_caliber_id')->constrained('arms_calibers')->onDelete('cascade');
            $table->foreignId('arm_condition_id')->constrained('arms_conditions')->onDelete('cascade');
            $table->string('serial_no')->unique();
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('sale_price', 15, 2);
            $table->date('purchase_date');
            $table->enum('status', ['available', 'sold', 'under_repair', 'decommissioned'])->default('available');
            $table->text('notes')->nullable();
            $table->string('arm_title');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'arm_type_id']);
            $table->index(['business_id', 'arm_category_id']);
            $table->index('serial_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arms');
    }
};
