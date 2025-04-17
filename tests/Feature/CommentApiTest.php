<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_comment_for_a_task(): void
    {
        $user = User::factory()->create();
        $building = Building::factory()->create();
        
        // Garante que o usuário tem acesso ao building
        $building->users()->attach($user);
        
        $task = Task::factory()->create([
            'building_id' => $building->id,
            'created_by' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $commentData = [
            'content' => 'This is a test comment',
        ];

        $response = $this->postJson("/api/tasks/{$task->id}/comments", $commentData);

        // Debug: mostra a resposta completa em caso de falha
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'task_id', 'user_id', 'content', 'created_at', 'updated_at',
                ],
            ])
            ->assertJsonPath('data.content', 'This is a test comment');

        $this->assertDatabaseHas('comments', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'content' => 'This is a test comment',
        ]);
    }
}