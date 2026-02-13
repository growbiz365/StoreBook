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
        Schema::create('arms_makes', function (Blueprint $table) {
            $table->id();
            $table->string('arm_make');
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->boolean('status')->default(1);
            $table->timestamps();
            
            // Ensure unique arm_make per business
            $table->unique(['arm_make', 'business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arms_makes');
    }
};
