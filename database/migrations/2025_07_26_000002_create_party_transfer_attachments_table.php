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
        $this->safeCreateTable('party_transfer_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_transfer_id')->constrained('party_transfers')->cascadeOnDelete();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->safeDropTable('party_transfer_attachments');
    }
}; 