<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

trait MigrationHelper
{
    protected function safeCreateTable(string $tableName, \Closure $callback)
    {
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, $callback);
        }
    }

    protected function safeDropTable(string $tableName)
    {
        if (Schema::hasTable($tableName)) {
            Schema::dropIfExists($tableName);
        }
    }

    protected function safeAddColumn(string $tableName, string $columnName, \Closure $callback)
    {
        if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, $callback);
        }
    }

    protected function safeDropColumn(string $tableName, string $columnName)
    {
        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->dropColumn($columnName);
            });
        }
    }

    protected function safeAlterColumn(string $tableName, string $columnName, \Closure $callback)
    {
        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, $callback);
        }
    }
}