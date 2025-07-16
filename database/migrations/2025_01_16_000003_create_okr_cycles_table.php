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
        Schema::create('okr_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Q1-2025", "Q2-2025"
            $table->integer('year');
            $table->integer('quarter');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('planning_start')->nullable()->comment('When planning phase begins');
            $table->date('mid_quarter_review')->nullable()->comment('Mid-quarter check-in date');
            $table->date('scoring_deadline')->nullable()->comment('When final scoring is due');
            $table->enum('status', ['planning', 'active', 'review', 'closed'])->default('planning');
            $table->text('description')->nullable();
            $table->json('cycle_metadata')->nullable()->comment('Additional cycle configuration');
            $table->timestamps();
            
            $table->unique(['year', 'quarter']);
            $table->index(['status', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('okr_cycles');
    }
}; 