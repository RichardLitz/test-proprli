<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_tasks_for_a_building(): void
    {
        $user = User::factory()->create();
        $building = Building::factory()->create();
        
        $building->users()->attach($user);
        
        $tasks = Task::factory()->count(3)->create([
            'building_id' => $building->id,
            'created_by' => $user->id,
        ]);
    
        Sanctum::actingAs($user);
    
        $response = $this->getJson("/api/buildings/{$building->id}/tasks");
    
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'title', 'description', 'status', 'building_id',
                        'created_by', 'assigned_to', 'created_at', 'updated_at',
                    ]
                ],
                'links',
                'meta',
            ]);
    }

    public function test_can_filter_tasks_by_status(): void
    {
        $user = User::factory()->create();
        $building = Building::factory()->create();
        
        $building->users()->attach($user);
        
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by' => $user->id,
            'status' => 'open',
        ]);
        
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by' => $user->id,
            'status' => 'in_progress',
        ]);
    
        Sanctum::actingAs($user);
    
        $response = $this->getJson("/api/buildings/{$building->id}/tasks?status=open");
    
        if ($response->status() !== 200) {
            dump($response->json());
        }
    
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'open');
    }

    public function test_can_create_a_task(): void
    {
        $user = User::factory()->create();
        $building = Building::factory()->create();
        
        $building->users()->attach($user);

        Sanctum::actingAs($user);

        $taskData = [
            'building_id' => $building->id,
            'title' => 'New Task',
            'description' => 'This is a test task',
            'status' => 'open',
            'due_date' => now()->addDays(7)->toDateString(),
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        if ($response->status() !== 201) {
            dump($response->json());
        }

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'title', 'description', 'status', 'building_id',
                    'created_by', 'assigned_to', 'due_date', 'created_at', 'updated_at',
                ],
            ])
            ->assertJsonPath('data.title', 'New Task')
            ->assertJsonPath('data.status', 'open');

        $this->assertDatabaseHas('tasks', [
            'building_id' => $building->id,
            'title' => 'New Task',
            'created_by' => $user->id,
        ]);
    }
}