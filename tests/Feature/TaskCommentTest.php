<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCommentTest extends TestCase
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
            'name' => 'Test Project',
            'type' => 'new_development',
            'status' => 'in_progress',
            'priority' => 'high',
            'user_id' => $this->user->id,
        ]);

        $this->task = Task::create([
            'title' => 'Test Task',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->user->id,
        ]);
    }

    public function test_user_can_view_task_details_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('tasks.show', $this->task));

        $response->assertOk();
        $response->assertSee('Test Task');
        $response->assertSee('Discussion & Comments', false);
    }

    public function test_user_can_post_comment_on_task(): void
    {
        $response = $this->actingAs($this->user)->post(route('tasks.comments.store', $this->task), [
            'comment' => 'Working on the authentication flow now.',
        ]);

        $response->assertRedirect(route('tasks.show', $this->task));
        $this->assertDatabaseHas('task_comments', [
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'comment' => 'Working on the authentication flow now.',
        ]);
    }

    public function test_author_can_delete_their_own_comment(): void
    {
        $comment = TaskComment::create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'comment' => 'This is a test comment to be deleted.',
        ]);

        $response = $this->actingAs($this->user)->delete(route('tasks.comments.destroy', $comment));

        $response->assertRedirect(route('tasks.show', $this->task->id));
        $this->assertDatabaseMissing('task_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_comment(): void
    {
        $otherUser = User::factory()->create(['role' => 'developer']);
        $comment = TaskComment::create([
            'task_id' => $this->task->id,
            'user_id' => $otherUser->id,
            'comment' => 'Comment by other user.',
        ]);

        $response = $this->actingAs($this->user)->delete(route('tasks.comments.destroy', $comment));

        $response->assertForbidden();
        $this->assertDatabaseHas('task_comments', [
            'id' => $comment->id,
        ]);
    }
}
