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
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->timestampTz('start_time');
            $table->timestampTz('end_time')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->boolean('is_manual')->default(false);
            $table->index(['user_id', 'start_time']);
            $table->index(['project_id', 'start_time']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
