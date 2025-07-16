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
        Schema::create('okr_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('objective_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('key_result_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Progress tracking
            $table->decimal('previous_progress', 5, 2)->default(0);
            $table->decimal('current_progress', 5, 2)->default(0);
            $table->decimal('confidence_level', 3, 2)->comment('Confidence from 0.0 to 1.0');
            
            // Check-in content
            $table->text('progress_notes')->nullable();
            $table->text('challenges')->nullable();
            $table->text('next_steps')->nullable();
            $table->json('risk_factors')->nullable()->comment('Array of risk indicators');
            
            // Metadata
            $table->enum('check_in_type', ['weekly', 'bi_weekly', 'monthly', 'quarterly', 'ad_hoc'])->default('weekly');
            $table->date('check_in_date');
            $table->timestamps();
            
            $table->index(['check_in_date', 'check_in_type']);
            $table->index(['objective_id', 'check_in_date']);
            $table->index(['key_result_id', 'check_in_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('okr_check_ins');
    }
}; 