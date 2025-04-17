<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskFilterRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Building;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

   /**
     * Lista tarefas para um edifício com filtros opcionais.
     *
     * @param \App\Models\Building $building O edifício cujas tarefas serão listadas
     * @param \App\Http\Requests\TaskFilterRequest $request A requisição com os filtros
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection Coleção de recursos de tarefas
     */
    public function index(Building $building, TaskFilterRequest $request): AnonymousResourceCollection
    {
        $tasks = $this->taskService->getFilteredTasks($building, $request->validated());
        
        return TaskResource::collection($tasks);
    }

    /**
     * Cria uma nova tarefa.
     *
     * @param \App\Http\Requests\StoreTaskRequest $request A requisição validada
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a tarefa criada
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->validated());
        
        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Atualiza o status de uma tarefa.
     *
     * @param \App\Models\Task $task A tarefa a ser atualizada
     * @param \App\Http\Requests\UpdateTaskStatusRequest $request A requisição validada
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a tarefa atualizada
     */
    public function updateStatus(Task $task, UpdateTaskStatusRequest $request): JsonResponse
    {
        $updatedTask = $this->taskService->updateTaskStatus($task, $request->validated('status'));
        
        return (new TaskResource($updatedTask))
            ->response()
            ->setStatusCode(200);
    }    
}