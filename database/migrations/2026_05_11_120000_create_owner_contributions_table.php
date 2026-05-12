<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\MigrationHelper;

return new class extends Migration
{
    use MigrationHelper;

    public function up(): void
    {
        $this->safeCreateTable('owner_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->enum('contribution_via', ['cash', 'bank'])->default('cash');
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->foreignId('deposit_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->foreignId('from_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('contribution_date');
            $table->enum('received_via', ['cash', 'bank_transfer', 'cheque', 'online'])->default('cash');
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->safeDropTable('owner_contributions');
    }
};
