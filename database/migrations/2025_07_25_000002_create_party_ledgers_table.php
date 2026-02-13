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
        $this->safeCreateTable('party_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('parties')->cascadeOnDelete();
            $table->unsignedBigInteger('voucher_id');
            $table->string('voucher_type');
            $table->date('date_added');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('debit_amount', 10, 2)->default(0);
            $table->decimal('credit_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->safeDropTable('party_ledgers');
    }
}; 