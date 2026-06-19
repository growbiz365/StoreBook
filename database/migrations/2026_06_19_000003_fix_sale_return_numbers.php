<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix sale returns that received return_number=1 due to accessor bug in nextReturnNumberForBusiness.
     */
    public function up(): void
    {
        DB::statement('UPDATE sale_returns SET return_number = id WHERE return_number != id');
    }

    public function down(): void
    {
        // Cannot reliably restore incorrect values.
    }
};
