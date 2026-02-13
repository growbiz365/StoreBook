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
        // Add soft delete columns to sale_invoices table
        Schema::table('sale_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_invoices', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('sale_invoices', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('cancelled_by');
                // Foreign key constraint for deleted_by
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        // Add soft delete columns to sale_invoice_general_items table
        Schema::table('sale_invoice_general_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_invoice_general_items', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('sale_invoice_general_items', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('line_total');
                // Foreign key constraint for deleted_by
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        // Add soft delete columns to sale_invoice_arms table
        Schema::table('sale_invoice_arms', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_invoice_arms', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('sale_invoice_arms', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('line_total');
                // Foreign key constraint for deleted_by
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        // Add soft delete columns to sale_invoice_audit_logs table
        Schema::table('sale_invoice_audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_invoice_audit_logs', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('sale_invoice_audit_logs', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('user_id');
                // Foreign key constraint for deleted_by
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove soft delete columns from sale_invoice_audit_logs table
        Schema::table('sale_invoice_audit_logs', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });

        // Remove soft delete columns from sale_invoice_arms table
        Schema::table('sale_invoice_arms', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });

        // Remove soft delete columns from sale_invoice_general_items table
        Schema::table('sale_invoice_general_items', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });

        // Remove soft delete columns from sale_invoices table
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });
    }
};