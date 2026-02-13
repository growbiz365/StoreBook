<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\MigrationHelper;
return new class extends Migration {
    use MigrationHelper;
    public function up(): void
    {
        $this->safeCreateTable('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->string('code', 20);
            $table->string('name', 100);
            $table->enum('type', ['asset', 'liability', 'income', 'expense', 'equity']);
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            // Bank account details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            $table->text('bank_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->safeDropTable('chart_of_accounts');
    }
};
