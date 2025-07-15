<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('objectives')->update(['progress' => 0]);
    }

    public function down()
    {
        // No rollback needed for this fix
    }
}; 