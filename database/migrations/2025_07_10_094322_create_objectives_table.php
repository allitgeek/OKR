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
        Schema::create('objectives', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'archived'])->default('not_started');
            $table->enum('time_period', ['monthly', 'quarterly', 'yearly'])->default('quarterly');
            $table->integer('progress')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objectives');
    }
};
