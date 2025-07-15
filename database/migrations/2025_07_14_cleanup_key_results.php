<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('key_results')->truncate();
    }

    public function down()
    {
        // No rollback needed for cleanup
    }
}; 