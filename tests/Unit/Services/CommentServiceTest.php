<?php
namespace Tests\Unit\Services;

use App\Models\Task;
use App\Models\User;
use App\Services\CommentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CommentService $commentService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commentService = new CommentService();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
      
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_comment(): void
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);
        
        $commentData = [
            'content' => 'Test comment content',
        ];
        
        $comment = $this->commentService->createComment($task, $commentData);
        
        $this->assertNotNull($comment);
        $this->assertEquals('Test comment content', $comment->content);
        $this->assertEquals($task->id, $comment->task_id);
        $this->assertEquals($this->user->id, $comment->user_id);
    }
}