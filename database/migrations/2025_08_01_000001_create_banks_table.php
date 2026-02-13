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
        $this->safeCreateTable('banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->enum('account_type', ['bank', 'cash']);
            $table->string('account_name');
            $table->string('bank_name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('opening_balance', 10, 2)->nullable();
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->safeDropTable('banks');
    }
}; 