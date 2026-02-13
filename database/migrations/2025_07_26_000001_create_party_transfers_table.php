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
        $this->safeCreateTable('party_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('debit_party_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('credit_party_id')->constrained('parties')->cascadeOnDelete();
            $table->decimal('transfer_amount', 10, 2);
            $table->text('details')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->safeDropTable('party_transfers');
    }
}; 