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
        $this->safeCreateTable('bank_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('withdrawal_amount', 10, 2)->default(0);
            $table->string('voucher_type');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->text('details')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->safeDropTable('bank_ledger');
    }
}; 