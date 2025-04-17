<?php

namespace Tests\Unit;

use App\Models\Building;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaskService $taskService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskService = new TaskService();
    }

    public function test_can_filter_tasks_by_status(): void
    {
        $building = Building::factory()->create();
        $user = User::factory()->create();
        
        // Create tasks with different statuses
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by' => $user->id,
            'status' => 'open',
        ]);
        
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by' => $user->id,
            'status' => 'completed',
        ]);

        $filters = ['status' => 'open'];
        $tasks = $this->taskService->getFilteredTasks($building, $filters);

        $this->assertEquals(1, $tasks->count());
        $this->assertEquals('open', $tasks->first()->status);
    }

    public function test_can_filter_tasks_by_date_range(): void
    {
        $building = Building::factory()->create();
        $user = User::factory()->create();
        
        // Create a task with a specific created_at date
        $task = Task::factory()->create([
            'building_id' => $building->id,
            'created_by' => $user->id,
        ]);
        
        // Manually set the created_at date to test filtering
        $task->created_at = now()->subDays(5);
        $task->save();
        
        // Create another task with today's date
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by' => $user->id,
        ]);

        $filters = [
            'created_from' => now()->subDays(7)->toDateString(),
            'created_to' => now()->subDays(3)->toDateString(),
        ];
        
        $tasks = $this->taskService->getFilteredTasks($building, $filters);

        $this->assertEquals(1, $tasks->count());
        $this->assertEquals($task->id, $tasks->first()->id);
    }
}