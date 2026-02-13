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
        $this->safeCreateTable('bank_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('from_account_id')->constrained('banks')->cascadeOnDelete();
            $table->foreignId('to_account_id')->constrained('banks')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->text('details')->nullable();
            $table->date('transfer_date');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->safeDropTable('bank_transfers');
    }
};