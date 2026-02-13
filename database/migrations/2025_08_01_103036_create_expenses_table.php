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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_head_id');
            $table->unsignedBigInteger('bank_id');
            $table->decimal('amount', 15, 2);
            $table->date('date_added');
            $table->text('details')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('expense_head_id')->references('id')->on('expense_heads')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
