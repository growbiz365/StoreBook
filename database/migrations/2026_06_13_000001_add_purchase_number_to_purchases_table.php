<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedInteger('purchase_number')->nullable()->after('business_id');
            $table->unique(['business_id', 'purchase_number'], 'purchases_business_purchase_number_unique');
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

            foreach ($purchases as $purchaseId) {
                DB::table('purchases')
                    ->where('id', $purchaseId)
                    ->update(['purchase_number' => $purchaseId]);
            }
        }

        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedInteger('purchase_number')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropUnique('purchases_business_purchase_number_unique');
            $table->dropColumn('purchase_number');
        });
    }
};
