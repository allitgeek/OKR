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
        Schema::table('objectives', function (Blueprint $table) {
            // OKR Cycle Management
            $table->string('cycle_id')->nullable()->after('time_period')->comment('Q1-2025, Q2-2025, etc.');
            $table->integer('cycle_year')->nullable()->after('cycle_id');
            $table->integer('cycle_quarter')->nullable()->after('cycle_year');
            
            // OKR Alignment & Cascading
            $table->foreignId('parent_objective_id')->nullable()->after('team_id')->constrained('objectives')->nullOnDelete();
            $table->enum('level', ['company', 'team', 'individual'])->default('individual')->after('parent_objective_id');
            
            // OKR Scoring (0.0-1.0 scale)
            $table->decimal('okr_score', 3, 2)->nullable()->after('progress')->comment('Final OKR score from 0.0 to 1.0');
            $table->decimal('confidence_level', 3, 2)->default(0.5)->after('okr_score')->comment('Confidence from 0.0 to 1.0');
            
            // OKR Types
            $table->enum('okr_type', ['committed', 'aspirational'])->default('committed')->after('confidence_level');
            
            // Check-in Process
            $table->timestamp('last_check_in')->nullable()->after('okr_type');
            $table->text('last_check_in_notes')->nullable()->after('last_check_in');
            
            // OKR Quality
            $table->boolean('is_measurable')->default(true)->after('last_check_in_notes');
            $table->boolean('is_specific')->default(true)->after('is_measurable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('objectives', function (Blueprint $table) {
            $table->dropForeign(['parent_objective_id']);
            $table->dropColumn([
                'cycle_id',
                'cycle_year', 
                'cycle_quarter',
                'parent_objective_id',
                'level',
                'okr_score',
                'confidence_level',
                'okr_type',
                'last_check_in',
                'last_check_in_notes',
                'is_measurable',
                'is_specific'
            ]);
        });
    }
}; 