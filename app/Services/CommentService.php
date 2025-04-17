<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Task;

class CommentService
{
    /**
     * Create a new comment for a task
     */
    public function createComment(Task $task, array $data): Comment
    {
        $data['task_id'] = $task->id;
        $data['user_id'] = auth()->id();
        
        return Comment::create($data);
    }
}