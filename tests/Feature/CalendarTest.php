<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
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
            'name' => 'Calendar Demo Project',
            'type' => 'new_development',
            'status' => 'in_progress',
            'priority' => 'high',
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'due_date' => now()->endOfMonth()->format('Y-m-d'),
            'user_id' => $this->user->id,
        ]);

        $this->task = Task::create([
            'title' => 'Scheduled Task for Calendar',
            'type' => 'feature',
            'status' => 'in_progress',
            'priority' => 'medium',
            'start_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'project_id' => $this->project->id,
            'assigned_to' => $this->user->id,
        ]);
    }

    public function test_authenticated_user_can_view_calendar_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('calendar.index'));

        $response->assertOk();
        $response->assertSee('Project & Task Calendar', false);
        $response->assertSee('Calendar Demo Project');
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('calendar.index'));

        $response->assertRedirect('/login');
    }

    public function test_calendar_events_endpoint_returns_json_events(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('calendar.events'));

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'start',
                'end',
                'allDay',
                'backgroundColor',
                'extendedProps' => [
                    'type',
                    'item_id',
                    'title',
                    'category',
                    'status',
                    'priority',
                    'view_url',
                ],
            ],
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data);

        $titles = collect($data)->pluck('title')->toArray();
        $this->assertTrue(collect($titles)->contains(fn ($t) => str_contains($t, 'Calendar Demo Project')));
        $this->assertTrue(collect($titles)->contains(fn ($t) => str_contains($t, 'Scheduled Task for Calendar')));
    }

    public function test_calendar_events_can_be_filtered_by_type(): void
    {
        // Tasks only
        $response = $this->actingAs($this->user)->getJson(route('calendar.events', ['type' => 'tasks']));
        $response->assertOk();
        $data = $response->json();
        $types = collect($data)->pluck('extendedProps.type')->unique()->toArray();
        $this->assertEquals(['task'], array_values($types));

        // Projects only
        $response = $this->actingAs($this->user)->getJson(route('calendar.events', ['type' => 'projects']));
        $response->assertOk();
        $data = $response->json();
        $types = collect($data)->pluck('extendedProps.type')->unique()->toArray();
        $this->assertEquals(['project'], array_values($types));
    }

    public function test_calendar_events_can_be_filtered_by_project(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('calendar.events', ['project_id' => $this->project->id]));
        $response->assertOk();
        $data = $response->json();
        $this->assertNotEmpty($data);
    }
}
