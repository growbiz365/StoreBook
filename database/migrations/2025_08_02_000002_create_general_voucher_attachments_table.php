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
        $this->safeCreateTable('general_voucher_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_voucher_id')->constrained('general_vouchers')->cascadeOnDelete();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->safeDropTable('general_voucher_attachments');
    }
};