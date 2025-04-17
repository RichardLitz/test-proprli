<?php

namespace Tests\Unit;

use App\Models\Building;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaskService $taskService;
    protected User $user;
    protected Building $building; 

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskService = new TaskService();
        $this->user = User::factory()->create();
        $this->building = Building::factory()->create(); 
        $this->building->users()->attach($this->user);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_filter_tasks_by_status(): void
    {
        // Create tasks with different statuses
        Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
            'status' => 'open',
        ]);
        
        Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
            'status' => 'completed',
        ]);

        $filters = ['status' => 'open'];
        $tasks = $this->taskService->getFilteredTasks($this->building, $filters);

        $this->assertEquals(1, $tasks->count());
        $this->assertEquals('open', $tasks->first()->status);
    }

    public function test_can_filter_tasks_by_date_range(): void
    {
        // Create a task with a specific created_at date
        $task = Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
        ]);
        
        // Manually set the created_at date to test filtering
        $task->created_at = now()->subDays(5);
        $task->save();
        
        // Create another task with today's date
        Task::factory()->create([
            'building_id' => $this->building->id,
            'created_by' => $this->user->id,
        ]);

        $filters = [
            'created_from' => now()->subDays(7)->toDateString(),
            'created_to' => now()->subDays(3)->toDateString(),
        ];
        
        $tasks = $this->taskService->getFilteredTasks($this->building, $filters);

        $this->assertEquals(1, $tasks->count());
        $this->assertEquals($task->id, $tasks->first()->id);
    }
}