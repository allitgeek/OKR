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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // Create category_objective pivot table
        Schema::create('category_objective', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('objective_id')->constrained()->onDelete('cascade');
            $table->primary(['category_id', 'objective_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_objective');
        Schema::dropIfExists('categories');
    }
};
