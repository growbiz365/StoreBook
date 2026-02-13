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
        $this->safeCreateTable('parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('whatsapp_no')->nullable();
            $table->string('cnic')->nullable();
            $table->string('ntn')->nullable();
            $table->decimal('opening_balance', 10, 2)->nullable();
            $table->date('opening_date')->nullable();
            $table->enum('opening_type', ['credit', 'debit'])->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('status')->nullable()->default(1)->comment('1 = Active, 0 = Inactive');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->safeDropTable('parties');
    }
}; 