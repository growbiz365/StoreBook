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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('party_id')->constrained('parties')->onDelete('restrict');
            $table->date('approval_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'party_id']);
            $table->index(['business_id', 'approval_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
