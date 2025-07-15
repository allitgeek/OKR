<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('objectives', function (Blueprint $table) {
            $table->foreignId('creator_id')->after('user_id')->nullable()->constrained('users')->nullOnDelete();
        });

        // Update existing records to set creator_id as the current user_id
        DB::table('objectives')->update(['creator_id' => DB::raw('user_id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('objectives', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropColumn('creator_id');
        });
    }
};
