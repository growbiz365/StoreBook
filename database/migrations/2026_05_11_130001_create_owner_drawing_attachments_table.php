<?php

use App\Traits\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use MigrationHelper;

    public function up(): void
    {
        $this->safeCreateTable('owner_drawing_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_drawing_id')->constrained('owner_drawings')->cascadeOnDelete();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_extension', 20)->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->safeDropTable('owner_drawing_attachments');
    }
};
