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
    public function up()
    {
        $this->safeCreateTable('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('account_head')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->decimal('debit_amount', 10, 2)->default(0);
            $table->decimal('credit_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('voucher_id');
            $table->string('voucher_type');
            $table->text('comments')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date_added');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->safeDropTable('journal_entries');
    }
};
