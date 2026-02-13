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
        Schema::create('general_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->unsignedBigInteger('item_type_id');
            $table->string('item_code')->unique();
            $table->integer('min_stock_limit')->nullable();
            $table->string('carton_or_pack_size')->nullable();
            $table->decimal('cost_price', 10, 2);
            $table->integer('opening_stock')->default(0);
            $table->decimal('opening_total', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2);
            $table->unsignedBigInteger('business_id');
            $table->timestamps();

            $table->foreign('item_type_id')->references('id')->on('item_types')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->unique(['item_code', 'business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_items');
    }
};
