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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->char('currency_code', 3); // e.g., USD, EUR
            $table->string('currency_name'); // e.g., Dollar, Euro
            $table->string('symbol', 10)->nullable(); // e.g., $, €
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('currencies');
    }

};
