<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Preserve historical purchase labels: align purchase_number with legacy id
     * so existing purchases keep familiar numbers (e.g. #140 stays #140).
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropUnique('purchases_business_purchase_number_unique');
        });

        DB::statement('UPDATE purchases SET purchase_number = id');

        Schema::table('purchases', function (Blueprint $table) {
            $table->unique(['business_id', 'purchase_number'], 'purchases_business_purchase_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropUnique('purchases_business_purchase_number_unique');
        });

        $businessIds = DB::table('purchases')
            ->distinct()
            ->orderBy('business_id')
            ->pluck('business_id');

        foreach ($businessIds as $businessId) {
            $purchases = DB::table('purchases')
                ->where('business_id', $businessId)
                ->orderBy('id')
                ->pluck('id');

            $sequence = 1;
            foreach ($purchases as $purchaseId) {
                DB::table('purchases')
                    ->where('id', $purchaseId)
                    ->update(['purchase_number' => $sequence++]);
            }
        }

        Schema::table('purchases', function (Blueprint $table) {
            $table->unique(['business_id', 'purchase_number'], 'purchases_business_purchase_number_unique');
        });
    }
};
