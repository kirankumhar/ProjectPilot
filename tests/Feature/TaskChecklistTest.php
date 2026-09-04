<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskChecklistTest extends TestCase
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
            'name' => 'Checklist Test Project',
            'type' => 'new_development',
            'status' => 'in_progress',
            'priority' => 'high',
            'user_id' => $this->user->id,
        ]);

        $this->task = Task::create([
            'title' => 'Subtask Core Feature',
            'type' => 'feature',
            'status' => 'in_progress',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->user->id,
        ]);
    }

    public function test_user_can_add_checklist_item_via_standard_post(): void
    {
        $response = $this->actingAs($this->user)->post(route('tasks.checklists.store', $this->task), [
            'title' => 'Write database migrations',
        ]);

        $response->assertRedirect(route('tasks.show', $this->task));
        $this->assertDatabaseHas('task_checklists', [
            'task_id' => $this->task->id,
            'title' => 'Write database migrations',
            'is_completed' => false,
        ]);
    }

    public function test_user_can_add_checklist_item_via_ajax(): void
    {
        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('tasks.checklists.store', $this->task), [
                'title' => 'Implement AJAX controller endpoints',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'checklist' => [
                'title' => 'Implement AJAX controller endpoints',
                'is_completed' => false,
            ],
            'stats' => [
                'total' => 1,
                'completed' => 0,
                'percentage' => 0,
            ],
        ]);
    }

    public function test_checklist_title_validation(): void
    {
        $response = $this->actingAs($this->user)->post(route('tasks.checklists.store', $this->task), [
            'title' => '',
        ]);
        $response->assertSessionHasErrors('title');

        $responseTooLong = $this->actingAs($this->user)->post(route('tasks.checklists.store', $this->task), [
            'title' => str_repeat('a', 256),
        ]);
        $responseTooLong->assertSessionHasErrors('title');
    }

    public function test_user_can_toggle_checklist_item_completion(): void
    {
        $item = TaskChecklist::create([
            'task_id' => $this->task->id,
            'title' => 'Setup unit test harness',
            'is_completed' => false,
        ]);

        // Toggle to completed
        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->patchJson(route('tasks.checklists.toggle', $item));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_completed' => true,
            'completed_by_name' => $this->user->name,
            'stats' => [
                'total' => 1,
                'completed' => 1,
                'percentage' => 100,
            ],
        ]);

        $item->refresh();
        $this->assertTrue($item->is_completed);
        $this->assertNotNull($item->completed_at);
        $this->assertEquals($this->user->id, $item->completed_by);

        // Toggle back to incomplete
        $response2 = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->patchJson(route('tasks.checklists.toggle', $item));

        $response2->assertOk();
        $response2->assertJson([
            'success' => true,
            'is_completed' => false,
            'stats' => [
                'total' => 1,
                'completed' => 0,
                'percentage' => 0,
            ],
        ]);

        $item->refresh();
        $this->assertFalse($item->is_completed);
        $this->assertNull($item->completed_at);
        $this->assertNull($item->completed_by);
    }

    public function test_task_checklist_progress_percentage_calculation(): void
    {
        $item1 = TaskChecklist::create([
            'task_id' => $this->task->id,
            'title' => 'Item 1',
            'is_completed' => true,
        ]);

        $item2 = TaskChecklist::create([
            'task_id' => $this->task->id,
            'title' => 'Item 2',
            'is_completed' => false,
        ]);

        $this->task->refresh();
        $this->assertEquals(2, $this->task->checklist_total_count);
        $this->assertEquals(1, $this->task->checklist_completed_count);
        $this->assertEquals(50, $this->task->checklist_progress_percentage);

        // Complete item2
        $item2->update(['is_completed' => true]);
        $this->task->refresh();
        $this->assertEquals(100, $this->task->checklist_progress_percentage);
    }

    public function test_user_can_delete_checklist_item(): void
    {
        $item = TaskChecklist::create([
            'task_id' => $this->task->id,
            'title' => 'To be deleted item',
            'is_completed' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->deleteJson(route('tasks.checklists.destroy', $item));

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('task_checklists', ['id' => $item->id]);
    }

    public function test_unauthenticated_user_cannot_manage_checklists(): void
    {
        $response = $this->post(route('tasks.checklists.store', $this->task), [
            'title' => 'Unauthorized Item',
        ]);
        $response->assertRedirect(route('login'));
    }
}
