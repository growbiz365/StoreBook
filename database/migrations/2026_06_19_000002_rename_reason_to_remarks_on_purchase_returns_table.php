<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('shipping_charges');
        });

        DB::table('purchase_returns')
            ->whereNotNull('reason')
            ->update(['remarks' => DB::raw('reason')]);

        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('status');
        });

        DB::table('purchase_returns')
            ->whereNotNull('remarks')
            ->update(['reason' => DB::raw('remarks')]);

        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
