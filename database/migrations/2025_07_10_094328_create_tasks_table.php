<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('key_result_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('objective_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('assignee_id')->constrained('users');
            $table->date('start_date');
            $table->date('due_date');
            $table->string('status')->default('assigned');
            $table->float('progress')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
