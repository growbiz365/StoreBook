<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Migration to create countries table
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id(); // Default primary key
            $table->string('country_name');
            $table->char('country_code', 2);  // ISO 3166-1 alpha-2 country code
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('countries');
    }
};
