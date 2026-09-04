<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskTimeLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeLogTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'developer']);

        $this->project = Project::create([
            'name' => 'Timesheet Test Project',
            'type' => 'new_development',
            'status' => 'in_progress',
            'priority' => 'high',
            'user_id' => $this->user->id,
        ]);

        $this->task = Task::create([
            'title' => 'Feature Implementation Task',
            'type' => 'feature',
            'status' => 'in_progress',
            'priority' => 'medium',
            'estimated_hours' => 10.0,
            'project_id' => $this->project->id,
            'assigned_to' => $this->user->id,
        ]);
    }

    public function test_user_can_log_time_on_task(): void
    {
        $response = $this->actingAs($this->user)->post(route('tasks.time-logs.store', $this->task), [
            'hours' => 3.5,
            'logged_date' => now()->format('Y-m-d'),
            'note' => 'Worked on controller logic and tests.',
        ]);

        $response->assertRedirect(route('tasks.show', $this->task));
        $this->assertDatabaseHas('task_time_logs', [
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'hours' => 3.5,
            'note' => 'Worked on controller logic and tests.',
        ]);
    }

    public function test_hours_validation_rules(): void
    {
        // Hours less than 0.1
        $response = $this->actingAs($this->user)->post(route('tasks.time-logs.store', $this->task), [
            'hours' => 0,
            'logged_date' => now()->format('Y-m-d'),
        ]);
        $response->assertSessionHasErrors('hours');

        // Hours greater than 24
        $response = $this->actingAs($this->user)->post(route('tasks.time-logs.store', $this->task), [
            'hours' => 25,
            'logged_date' => now()->format('Y-m-d'),
        ]);
        $response->assertSessionHasErrors('hours');
    }

    public function test_task_hours_calculation_and_progress_percentage(): void
    {
        TaskTimeLog::create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'hours' => 4.0,
            'logged_date' => now()->format('Y-m-d'),
        ]);

        TaskTimeLog::create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'hours' => 3.5,
            'logged_date' => now()->format('Y-m-d'),
        ]);

        $this->task->refresh();
        $this->assertEquals(7.5, $this->task->total_logged_hours);
        $this->assertEquals(75, $this->task->time_progress_percentage);
        $this->assertFalse($this->task->is_over_budget);

        // Add 4 more hours to exceed estimated 10
        TaskTimeLog::create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'hours' => 4.0,
            'logged_date' => now()->format('Y-m-d'),
        ]);

        $this->task->refresh();
        $this->assertEquals(11.5, $this->task->total_logged_hours);
        $this->assertEquals(100, $this->task->time_progress_percentage);
        $this->assertTrue($this->task->is_over_budget);
    }

    public function test_author_can_delete_work_log(): void
    {
        $log = TaskTimeLog::create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'hours' => 2.0,
            'logged_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->user)->delete(route('tasks.time-logs.destroy', $log));

        $response->assertRedirect();
        $this->assertDatabaseMissing('task_time_logs', ['id' => $log->id]);
    }

    public function test_unauthorized_user_cannot_delete_work_log(): void
    {
        $otherUser = User::factory()->create(['role' => 'developer']);
        $log = TaskTimeLog::create([
            'task_id' => $this->task->id,
            'user_id' => $otherUser->id,
            'hours' => 2.0,
            'logged_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->user)->delete(route('tasks.time-logs.destroy', $log));

        $response->assertForbidden();
        $this->assertDatabaseHas('task_time_logs', ['id' => $log->id]);
    }

    public function test_authenticated_user_can_view_timesheets_page(): void
    {
        TaskTimeLog::create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'hours' => 5.0,
            'logged_date' => now()->format('Y-m-d'),
            'note' => 'Worked on API design',
        ]);

        $response = $this->actingAs($this->user)->get(route('timesheets.index'));

        $response->assertOk();
        $response->assertSee('Team Timesheets & Work Logs', false);
        $response->assertSee('Worked on API design');
        $response->assertSee('5 hrs');
    }

    public function test_timesheets_can_be_filtered(): void
    {
        TaskTimeLog::create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'hours' => 5.0,
            'logged_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->user)->get(route('timesheets.index', [
            'period' => 'this_week',
            'project_id' => $this->project->id,
        ]));

        $response->assertOk();
        $response->assertSee('5 hrs');
    }
}
