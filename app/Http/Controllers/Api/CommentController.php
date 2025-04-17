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
    /**
     * Comentários.
     *
     * @var \App\Services\CommentService
     */
    protected CommentService $commentService;

    /**
     * Cria uma nova instância do controlador.
     *
     * @param \App\Services\CommentService $commentService O serviço de comentários
     * @return void
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Cria um novo comentário para uma tarefa.
     *
     * @param \App\Models\Task $task A tarefa para a qual o comentário será criado
     * @param \App\Http\Requests\StoreCommentRequest $request A requisição validada
     * @return \Illuminate\Http\JsonResponse Resposta JSON com o comentário criado
     */
    public function store(Task $task, StoreCommentRequest $request): JsonResponse
    {
        $comment = $this->commentService->createComment($task, $request->validated());
        
        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }
}