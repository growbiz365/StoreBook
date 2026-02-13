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
        $this->safeCreateTable('other_income_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('other_income_id')->constrained('other_incomes')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->safeDropTable('other_income_attachments');
    }
};