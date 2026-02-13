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
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('name_of_customer')->nullable();
            $table->string('father_name')->nullable();
            $table->string('contact')->nullable();
            $table->text('address')->nullable();
            $table->string('cnic', 20)->nullable();
            $table->string('licence_no')->nullable();
            $table->date('licence_issue_date')->nullable();
            $table->date('licence_valid_upto')->nullable();
            $table->string('licence_issued_by')->nullable();
            $table->string('re_reg_no')->nullable();
            $table->string('dc')->nullable();
            $table->date('Date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn([
                'name_of_customer',
                'father_name',
                'contact',
                'address',
                'cnic',
                'licence_no',
                'licence_issue_date',
                'licence_valid_upto',
                'licence_issued_by',
                're_reg_no',
                'dc',
                'Date'
            ]);
        });
    }
};
