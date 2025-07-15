<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Analytics snapshots for historical data
        Schema::create('analytics_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');
            $table->string('metric_type', 50); // 'success_rate', 'completion_time', 'engagement'
            $table->decimal('metric_value', 10, 2);
            $table->string('entity_type', 50); // 'user', 'team', 'department', 'company'
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamps();
            
            $table->index(['snapshot_date', 'metric_type']);
            $table->index(['entity_type', 'entity_id']);
        });

        // Performance metrics cache
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('objectives_assigned')->default(0);
            $table->integer('objectives_completed')->default(0);
            $table->integer('objectives_in_progress')->default(0);
            $table->integer('objectives_overdue')->default(0);
            $table->decimal('avg_completion_time_days', 8, 2)->nullable();
            $table->decimal('success_rate', 5, 2)->default(0);
            $table->integer('total_comments')->default(0);
            $table->integer('total_attachments')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'period_start', 'period_end']);
        });

        // Team performance aggregates
        Schema::create('team_performance', function (Blueprint $table) {
            $table->id();
            $table->string('team_identifier'); // department, custom team, etc.
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('team_size');
            $table->decimal('avg_success_rate', 5, 2);
            $table->decimal('objectives_per_person', 5, 2);
            $table->integer('collaborative_objectives')->default(0);
            $table->decimal('engagement_score', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['team_identifier', 'period_start', 'period_end']);
        });

        // Predictive analytics cache
        Schema::create('objective_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('objective_id')->constrained()->onDelete('cascade');
            $table->decimal('completion_probability', 5, 2); // 0-100
            $table->string('risk_level'); // 'low', 'medium', 'high'
            $table->json('risk_factors'); // Detailed analysis
            $table->date('prediction_date');
            $table->timestamps();
            
            $table->index(['risk_level', 'prediction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objective_predictions');
        Schema::dropIfExists('team_performance');
        Schema::dropIfExists('performance_metrics');
        Schema::dropIfExists('analytics_snapshots');
    }
}; 