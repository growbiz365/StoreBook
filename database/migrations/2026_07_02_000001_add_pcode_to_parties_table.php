<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->string('pcode', 50)->nullable()->after('name');
            $table->unique(['business_id', 'pcode'], 'parties_business_pcode_unique');
        });
    }

    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->dropUnique('parties_business_pcode_unique');
            $table->dropColumn('pcode');
        });
    }
};

