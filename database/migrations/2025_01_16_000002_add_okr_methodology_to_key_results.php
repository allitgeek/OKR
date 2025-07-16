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
        Schema::table('key_results', function (Blueprint $table) {
            // OKR Scoring (0.0-1.0 scale)
            $table->decimal('okr_score', 3, 2)->nullable()->after('progress')->comment('Final KR score from 0.0 to 1.0');
            $table->decimal('confidence_level', 3, 2)->default(0.5)->after('okr_score')->comment('Confidence from 0.0 to 1.0');
            
            // Key Result Types
            $table->enum('kr_type', ['baseline', 'positive', 'negative', 'milestone'])->default('positive')->after('confidence_level');
            
            // Check-in Process
            $table->timestamp('last_check_in')->nullable()->after('kr_type');
            $table->text('last_check_in_notes')->nullable()->after('last_check_in');
            
            // Quality Validation
            $table->boolean('is_measurable')->default(true)->after('last_check_in_notes');
            $table->boolean('is_time_bound')->default(true)->after('is_measurable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('key_results', function (Blueprint $table) {
            $table->dropColumn([
                'okr_score',
                'confidence_level',
                'kr_type',
                'last_check_in',
                'last_check_in_notes',
                'is_measurable',
                'is_time_bound'
            ]);
        });
    }
}; 