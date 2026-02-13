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
        Schema::create('timezones', function (Blueprint $table) {
            $table->id();
            $table->string('timezone_name');
            $table->decimal('utc_offset', 4, 2); // The offset from UTC, e.g., +3.00
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('timezones');
    }
};
