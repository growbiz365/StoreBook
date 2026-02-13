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
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropUnique('businesses_email_unique');
            $table->dropForeign(['country_id']);
            $table->dropForeign(['timezone_id']);
            $table->dropForeign(['currency_id']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->string('cnic')->nullable()->change();
            $table->string('contact_no')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->unsignedBigInteger('country_id')->nullable()->change();
            $table->unsignedBigInteger('timezone_id')->nullable()->change();
            $table->unsignedBigInteger('currency_id')->nullable()->change();
            $table->string('date_format')->nullable()->comment('Allowed: Y-m-d, d/m/Y, m/d/Y')->change();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->unique('email', 'businesses_email_unique');
            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
            $table->foreign('timezone_id')->references('id')->on('timezones')->nullOnDelete();
            $table->foreign('currency_id')->references('id')->on('currencies')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropUnique('businesses_email_unique');
            $table->dropForeign(['country_id']);
            $table->dropForeign(['timezone_id']);
            $table->dropForeign(['currency_id']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->string('cnic')->nullable(false)->change();
            $table->string('contact_no')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->unsignedBigInteger('country_id')->nullable(false)->change();
            $table->unsignedBigInteger('timezone_id')->nullable(false)->change();
            $table->unsignedBigInteger('currency_id')->nullable(false)->change();
            $table->string('date_format')->nullable(false)->comment('Allowed: Y-m-d, d/m/Y, m/d/Y')->change();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->unique('email', 'businesses_email_unique');
            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
            $table->foreign('timezone_id')->references('id')->on('timezones')->cascadeOnDelete();
            $table->foreign('currency_id')->references('id')->on('currencies')->cascadeOnDelete();
        });
    }
};

