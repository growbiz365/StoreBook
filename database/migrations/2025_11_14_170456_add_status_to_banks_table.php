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
        $this->safeAddColumn('banks', 'status', function (Blueprint $table) {
            $table->boolean('status')->nullable()->default(1)->comment('1 = Active, 0 = Inactive')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->safeDropColumn('banks', 'status');
    }
};
