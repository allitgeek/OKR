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
        // Add indexes to objectives table
        Schema::table('objectives', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index(['creator_id', 'created_at']);
            $table->index(['status']);
            $table->index(['start_date', 'end_date']);
        });

        // Add indexes to key_results table
        Schema::table('key_results', function (Blueprint $table) {
            $table->index(['objective_id', 'created_at']);
            $table->index(['owner_id', 'created_at']);
            $table->index(['status']);
            $table->index(['progress']);
        });

        // Add indexes to tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['assignee_id', 'created_at']);
            $table->index(['creator_id', 'created_at']);
            $table->index(['key_result_id', 'created_at']);
            $table->index(['objective_id', 'created_at']);
            $table->index(['status']);
            $table->index(['due_date']);
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index(['is_active', 'created_at']);
            $table->index(['email_verified_at']);
        });

        // Add indexes to activity_log table if it exists
        if (Schema::hasTable('activity_log')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->index(['subject_type', 'subject_id']);
                $table->index(['causer_type', 'causer_id']);
                $table->index(['created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('objectives', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['creator_id', 'created_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['start_date', 'end_date']);
        });

        Schema::table('key_results', function (Blueprint $table) {
            $table->dropIndex(['objective_id', 'created_at']);
            $table->dropIndex(['owner_id', 'created_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['progress']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['assignee_id', 'created_at']);
            $table->dropIndex(['creator_id', 'created_at']);
            $table->dropIndex(['key_result_id', 'created_at']);
            $table->dropIndex(['objective_id', 'created_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['due_date']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'created_at']);
            $table->dropIndex(['email_verified_at']);
        });

        if (Schema::hasTable('activity_log')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->dropIndex(['subject_type', 'subject_id']);
                $table->dropIndex(['causer_type', 'causer_id']);
                $table->dropIndex(['created_at']);
            });
        }
    }
};
