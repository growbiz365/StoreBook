<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\MigrationHelper;

return new class extends Migration
{
    use MigrationHelper;

    public function up()
    {
        $this->safeAddColumn('chart_of_accounts', 'is_active', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_default');
        });
    }

    public function down()
    {
        $this->safeDropColumn('chart_of_accounts', 'is_active');
    }
};
