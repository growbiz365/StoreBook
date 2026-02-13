<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add purchase references to general_batches table
        Schema::table('general_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('purchase_line_id')->nullable()->after('purchase_id');
            $table->string('batch_code')->nullable()->after('purchase_line_id'); // PUR-{purchase_id}-{line_no}
            $table->enum('status', ['active', 'reversed', 'deleted'])->default('active')->after('batch_code');
            
            // Foreign key constraints for purchase references
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('set null');
            $table->foreign('purchase_line_id')->references('id')->on('purchase_general_lines')->onDelete('set null');
            
            // Indexes
            $table->index(['purchase_id', 'purchase_line_id']);
            $table->index('batch_code');
        });

        // Add purchase references to arms table
        Schema::table('arms', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_id')->nullable()->after('arm_title');
            $table->unsignedBigInteger('purchase_arm_serial_id')->nullable()->after('purchase_id');
            
            // Foreign key constraints for purchase references
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('set null');
            $table->foreign('purchase_arm_serial_id')->references('id')->on('purchase_arm_serials')->onDelete('set null');
            
            // Indexes
            $table->index(['purchase_id', 'purchase_arm_serial_id']);
        });

        // Add purchase references to general_items_stock_ledger table
        Schema::table('general_items_stock_ledger', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_id')->nullable()->after('reference_id');
            $table->unsignedBigInteger('purchase_line_id')->nullable()->after('purchase_id');
            
            // Foreign key constraints for purchase references
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('set null');
            $table->foreign('purchase_line_id')->references('id')->on('purchase_general_lines')->onDelete('set null');
        });

        // Add purchase references to arms_stock_ledger table
        Schema::table('arms_stock_ledger', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_id')->nullable()->after('reference_id');
            $table->unsignedBigInteger('purchase_arm_serial_id')->nullable()->after('purchase_id');
            
            // Foreign key constraints for purchase references
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('set null');
            $table->foreign('purchase_arm_serial_id')->references('id')->on('purchase_arm_serials')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Remove purchase references from general_batches table
        Schema::table('general_batches', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropForeign(['purchase_line_id']);
            $table->dropIndex(['purchase_id', 'purchase_line_id']);
            $table->dropIndex(['batch_code']);
            $table->dropColumn(['purchase_id', 'purchase_line_id', 'batch_code', 'status']);
        });

        // Remove purchase references from arms table
        Schema::table('arms', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropForeign(['purchase_arm_serial_id']);
            $table->dropIndex(['purchase_id', 'purchase_arm_serial_id']);
            $table->dropColumn(['purchase_id', 'purchase_arm_serial_id']);
        });

        // Remove purchase references from general_items_stock_ledger table
        Schema::table('general_items_stock_ledger', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropForeign(['purchase_line_id']);
            $table->dropColumn(['purchase_id', 'purchase_line_id']);
        });

        // Remove purchase references from arms_stock_ledger table
        Schema::table('arms_stock_ledger', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropForeign(['purchase_arm_serial_id']);
            $table->dropColumn(['purchase_id', 'purchase_arm_serial_id']);
        });
    }
};
