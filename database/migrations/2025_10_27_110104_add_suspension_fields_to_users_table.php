<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_suspended')->default(false)->after('email');
            $table->timestamp('suspended_at')->nullable()->after('is_suspended');
            $table->foreignId('suspended_by')->nullable()->constrained('users')->onDelete('set null')->after('suspended_at');
            $table->text('suspension_reason')->nullable()->after('suspended_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['suspended_by']);
            $table->dropColumn(['is_suspended', 'suspended_at', 'suspended_by', 'suspension_reason']);
        });
    }
};