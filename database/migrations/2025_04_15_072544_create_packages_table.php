<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->integer('duration_months');
            $table->timestamps();

            // Foreign key to the currencies table
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('SET NULL');
        });
    }

    public function down()
    {
        Schema::dropIfExists('packages');
    }
};
