<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\MigrationHelper;

return new class extends Migration
{
    use MigrationHelper;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->safeAddColumn('parties', 'chart_of_account_id', function (Blueprint $table) {
            $table->foreignId('chart_of_account_id')->nullable()->after('business_id')->constrained('chart_of_accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->safeDropColumn('parties', 'chart_of_account_id');
    }
};
