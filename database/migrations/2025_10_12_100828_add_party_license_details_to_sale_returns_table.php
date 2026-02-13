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
        Schema::table('sale_returns', function (Blueprint $table) {
            // Party License Details - only add if they don't exist
            if (!Schema::hasColumn('sale_returns', 'party_license_no')) {
                $table->string('party_license_no')->nullable();
            }
            if (!Schema::hasColumn('sale_returns', 'party_license_issue_date')) {
                $table->date('party_license_issue_date')->nullable();
            }
            if (!Schema::hasColumn('sale_returns', 'party_license_valid_upto')) {
                $table->date('party_license_valid_upto')->nullable();
            }
            if (!Schema::hasColumn('sale_returns', 'party_license_issued_by')) {
                $table->string('party_license_issued_by')->nullable();
            }
            if (!Schema::hasColumn('sale_returns', 'party_re_reg_no')) {
                $table->string('party_re_reg_no')->nullable();
            }
            if (!Schema::hasColumn('sale_returns', 'party_dc')) {
                $table->string('party_dc')->nullable();
            }
            if (!Schema::hasColumn('sale_returns', 'party_dc_date')) {
                $table->date('party_dc_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropColumn([
                'party_license_no',
                'party_license_issue_date',
                'party_license_valid_upto',
                'party_license_issued_by',
                'party_re_reg_no',
                'party_dc',
                'party_dc_date'
            ]);
        });
    }
};
