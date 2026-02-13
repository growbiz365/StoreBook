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
        Schema::create('income_heads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('chart_of_account_id')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_heads');
    }
};
