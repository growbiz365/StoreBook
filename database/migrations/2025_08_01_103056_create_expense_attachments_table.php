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
        Schema::create('expense_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_id');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_attachments');
    }
};
