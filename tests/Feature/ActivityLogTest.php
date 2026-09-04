<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
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
            'name' => 'Activity Test Project',
            'type' => 'new_development',
            'status' => 'pending',
            'priority' => 'high',
            'user_id' => $this->user->id,
        ]);

        $this->task = Task::create([
            'title' => 'Audit Trail Testing',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->user->id,
        ]);
    }

    public function test_creating_project_records_activity_log(): void
    {
        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'name' => 'Brand New Alpha Project',
            'type' => 'new_development',
            'status' => 'pending',
            'priority' => 'high',
        ]);

        $response->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'project_created',
        ]);
    }

    public function test_updating_project_status_records_activity_log(): void
    {
        $response = $this->actingAs($this->user)->put(route('projects.update', $this->project), [
            'name' => $this->project->name,
            'type' => $this->project->type,
            'status' => 'in_progress',
            'priority' => $this->project->priority,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'project_id' => $this->project->id,
            'action' => 'project_updated',
        ]);
    }

    public function test_creating_task_records_activity_log(): void
    {
        $response = $this->actingAs($this->user)->post(route('tasks.store'), [
            'title' => 'New Automated Task',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'high',
            'project_id' => $this->project->id,
            'assigned_to' => $this->user->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'project_id' => $this->project->id,
            'action' => 'task_created',
        ]);
    }

    public function test_updating_task_status_records_activity_log(): void
    {
        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->patchJson(route('tasks.update-status', $this->task), [
                'status' => 'completed',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'task_id' => $this->task->id,
            'action' => 'task_status_changed',
        ]);
    }

    public function test_posting_comment_records_activity_log(): void
    {
        $response = $this->actingAs($this->user)->post(route('tasks.comments.store', $this->task), [
            'comment' => 'This is a test comment for activity feed verification.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'task_id' => $this->task->id,
            'action' => 'comment_added',
        ]);
    }

    public function test_logging_time_records_activity_log(): void
    {
        $response = $this->actingAs($this->user)->post(route('tasks.time-logs.store', $this->task), [
            'hours' => 2.5,
            'logged_date' => now()->format('Y-m-d'),
            'note' => 'Worked on activity log tests.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'task_id' => $this->task->id,
            'action' => 'time_logged',
        ]);
    }

    public function test_completing_subtask_records_activity_log(): void
    {
        $item = TaskChecklist::create([
            'task_id' => $this->task->id,
            'title' => 'Subtask to complete',
            'is_completed' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->patchJson(route('tasks.checklists.toggle', $item));

        $response->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'task_id' => $this->task->id,
            'action' => 'checklist_completed',
        ]);
    }

    public function test_dashboard_displays_recent_activities(): void
    {
        ActivityLog::record(
            $this->project->id,
            'task_created',
            'Kiran created a task for testing dashboard',
            $this->task->id
        );

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Recent Team Activity');
        $response->assertSee('Kiran created a task for testing dashboard');
    }

    public function test_project_show_page_displays_activities(): void
    {
        ActivityLog::record(
            $this->project->id,
            'project_created',
            'Special project initialization logged',
            null
        );

        $response = $this->actingAs($this->user)->get(route('projects.show', $this->project));

        $response->assertOk();
        $response->assertSee('Activity History');
        $response->assertSee('Special project initialization logged');
    }
}
