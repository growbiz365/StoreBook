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
        Schema::create('sale_return_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->string('action', 50); // created, posted, cancelled, enhanced_edit, soft_deleted, restored
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('sale_return_id')->references('id')->on('sale_returns')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index(['sale_return_id']);
            $table->index(['action']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_audit_logs');
    }
};

