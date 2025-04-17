<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Task;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Create a new comment for a task
     */
    public function store(Task $task, StoreCommentRequest $request): JsonResponse
    {
        $comment = $this->commentService->createComment($task, $request->validated());
        
        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }
}