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
        $this->safeCreateTable('general_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('parties')->cascadeOnDelete();
            $table->enum('entry_type', ['debit', 'credit']);
            $table->decimal('amount', 10, 2);
            
            $table->text('details')->nullable();
            $table->date('entry_date');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->safeDropTable('general_vouchers');
    }
};