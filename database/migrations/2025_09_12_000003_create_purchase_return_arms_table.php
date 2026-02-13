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
        Schema::create('purchase_return_arms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained()->onDelete('cascade');
            $table->foreignId('arm_id')->constrained()->onDelete('cascade');
            $table->decimal('return_price', 15, 2);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['purchase_return_id', 'arm_id'], 'pra_pr_arm_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_arms');
    }
};
