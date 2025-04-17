<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->building = Building::factory()->create();
        
        $this->building->users()->attach($this->user);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_filter_by_assigned_user(): void
    {
        $assignee = User::factory()->create();
        
        // Create a task assigned to our specific user
        Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
            'assigned_to' => $assignee->id,
        ]);
        
        // Create another task assigned to someone else
        Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
            'assigned_to' => User::factory()->create()->id,
        ]);

        $response = $this->getJson("/api/buildings/{$this->building->id}/tasks?assigned_to={$assignee->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.assigned_to', $assignee->id);
    }

    public function test_can_filter_by_due_date_range(): void
    {
        // Create a task with a specific due date
        $task = Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
            'due_date' => now()->addDays(5)->toDateString(),
        ]);
        
        // Create another task with a later due date
        Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
            'due_date' => now()->addDays(15)->toDateString(),
        ]);

        $response = $this->getJson("/api/buildings/{$this->building->id}/tasks?" . 
            "due_date_from=" . now()->addDays(3)->toDateString() . 
            "&due_date_to=" . now()->addDays(10)->toDateString()
        );

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $task->id);
    }

    public function test_can_filter_by_created_date_range(): void
    {
        // Create a task with a specific created_at date
        $task = Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
        ]);
        
        // Manually update the created_at date
        $task->created_at = now()->subDays(5);
        $task->save();
        
        // Create another task with today's date
        Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->getJson("/api/buildings/{$this->building->id}/tasks?" . 
            "created_from=" . now()->subDays(7)->toDateString() . 
            "&created_to=" . now()->subDays(3)->toDateString()
        );

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $task->id);
    }

    public function test_can_apply_multiple_filters_simultaneously(): void
    {
        $assignee = User::factory()->create();
        
        // Create a task that matches all our filters
        $task = Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
            'assigned_to' => $assignee->id,
            'status' => 'in_progress',
            'due_date' => now()->addDays(5)->toDateString(),
        ]);
        
        // Create tasks that don't match all filters
        Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
            'assigned_to' => $assignee->id,
            'status' => 'open',
            'due_date' => now()->addDays(5)->toDateString(),
        ]);
        
        Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
            'assigned_to' => User::factory()->create()->id,
            'status' => 'in_progress',
            'due_date' => now()->addDays(5)->toDateString(),
        ]);

        $response = $this->getJson("/api/buildings/{$this->building->id}/tasks?" . 
            "status=in_progress" .
            "&assigned_to={$assignee->id}" .
            "&due_date_from=" . now()->addDays(3)->toDateString() . 
            "&due_date_to=" . now()->addDays(10)->toDateString()
        );

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $task->id);
    }
}