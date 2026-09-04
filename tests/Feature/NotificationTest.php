<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskCommentNotification;
use App\Notifications\TaskStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $creator;
    private User $assignee;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->creator = User::factory()->create(['role' => 'manager']);
        $this->assignee = User::factory()->create(['role' => 'developer']);

        $this->project = Project::create([
            'name' => 'Notification Project',
            'type' => 'new_development',
            'status' => 'in_progress',
            'priority' => 'high',
            'user_id' => $this->creator->id,
        ]);
    }

    public function test_task_assignment_creates_notification_for_assignee(): void
    {
        $this->actingAs($this->creator)->post(route('tasks.store'), [
            'title' => 'Assigned Task Test',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->assignee->id,
        ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Assigned Task Test']);
        $this->assertEquals(1, $this->assignee->notifications()->count());
        $this->assertEquals('New Task Assigned', $this->assignee->notifications()->first()->data['title']);
    }

    public function test_task_comment_creates_notification_for_assignee(): void
    {
        $task = Task::create([
            'title' => 'Task for Comment Test',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->assignee->id,
        ]);

        $this->actingAs($this->creator)->post(route('tasks.comments.store', $task), [
            'comment' => 'Please check the requirements.',
        ]);

        $this->assertEquals(1, $this->assignee->notifications()->count());
        $this->assertEquals('New Comment on Task', $this->assignee->notifications()->first()->data['title']);
    }

    public function test_task_status_change_creates_notification(): void
    {
        $task = Task::create([
            'title' => 'Task for Status Test',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->assignee->id,
        ]);

        $this->actingAs($this->creator)->patch(route('tasks.update-status', $task), [
            'status' => 'completed',
        ]);

        $this->assertEquals(1, $this->assignee->notifications()->count());
        $this->assertEquals('Task Status Updated', $this->assignee->notifications()->first()->data['title']);
    }

    public function test_user_can_view_notifications_page(): void
    {
        $task = Task::create([
            'title' => 'Test Task',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->assignee->id,
        ]);

        $this->assignee->notify(new TaskAssignedNotification($task));

        $response = $this->actingAs($this->assignee)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertSee('Notification Center');
        $response->assertSee('New Task Assigned');
    }

    public function test_user_can_read_and_redirect_notification(): void
    {
        $task = Task::create([
            'title' => 'Test Task',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->assignee->id,
        ]);

        $this->assignee->notify(new TaskAssignedNotification($task));
        $notification = $this->assignee->unreadNotifications()->first();

        $response = $this->actingAs($this->assignee)->get(route('notifications.read', $notification->id));

        $response->assertRedirect(route('tasks.show', $task));
        $this->assertEquals(0, $this->assignee->fresh()->unreadNotifications()->count());
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $task = Task::create([
            'title' => 'Test Task',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->assignee->id,
        ]);

        $this->assignee->notify(new TaskAssignedNotification($task));
        $this->assertEquals(1, $this->assignee->unreadNotifications()->count());

        $response = $this->actingAs($this->assignee)->post(route('notifications.mark-all-read'));

        $response->assertRedirect();
        $this->assertEquals(0, $this->assignee->fresh()->unreadNotifications()->count());
    }

    public function test_user_can_delete_notification(): void
    {
        $task = Task::create([
            'title' => 'Test Task',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->assignee->id,
        ]);

        $this->assignee->notify(new TaskAssignedNotification($task));
        $notification = $this->assignee->notifications()->first();

        $response = $this->actingAs($this->assignee)->delete(route('notifications.destroy', $notification->id));

        $response->assertRedirect();
        $this->assertEquals(0, $this->assignee->fresh()->notifications()->count());
    }
}
