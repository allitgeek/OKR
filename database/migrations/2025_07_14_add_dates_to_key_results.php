<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('key_results', function (Blueprint $table) {
            // Add both columns if they don't exist
            if (!Schema::hasColumn('key_results', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('key_results', 'due_date')) {
                $table->date('due_date')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('key_results', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'due_date']);
        });
    }
}; 