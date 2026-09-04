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
        // 1. Add estimated_hours to tasks table if not exists
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'estimated_hours')) {
                $table->decimal('estimated_hours', 8, 2)->default(0)->after('description');
            }
        });

        // 2. Create task_time_logs table
        Schema::create('task_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('hours', 8, 2);
            $table->string('note', 500)->nullable();
            $table->date('logged_date');
            $table->timestamps();

            $table->index(['task_id', 'logged_date']);
            $table->index(['user_id', 'logged_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_time_logs');

        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'estimated_hours')) {
                $table->dropColumn('estimated_hours');
            }
        });
    }
};
