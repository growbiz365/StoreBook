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
    public function up(): void
    {
        $this->safeCreateTable('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Activity details
            $table->string('log_name')->nullable(); // Module name (e.g., 'purchases', 'sales', 'arms')
            $table->string('description'); // What happened
            $table->string('subject_type')->nullable(); // Model class name
            $table->unsignedBigInteger('subject_id')->nullable(); // Model ID
            $table->string('event')->nullable(); // created, updated, deleted, etc.
            
            // Properties
            $table->json('properties')->nullable(); // Additional data, old/new values
            
            // User tracking
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('log_name');
            $table->index('event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->safeDropTable('activity_logs');
    }
};
