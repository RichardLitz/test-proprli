<?php
namespace App\Services;

use App\Models\Building;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
/**
     * Obtém tarefas filtradas para um edifício específico.
     *
     * Aplica filtros opcionais como status, usuário atribuído, datas de criação
     * e datas de vencimento às tarefas do edifício especificado.
     *
     * @param \App\Models\Building $building O edifício cujas tarefas serão filtradas
     * @param array $filters Os filtros a serem aplicados à consulta
     * @return \Illuminate\Pagination\LengthAwarePaginator Uma coleção paginada de tarefas filtradas
     */
    public function getFilteredTasks(Building $building, array $filters): LengthAwarePaginator
    {
        $query = $building->tasks()
            ->with(['comments.user', 'creator', 'assignee'])
            ->when(isset($filters['status']), function (Builder $query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(isset($filters['assigned_to']), function (Builder $query) use ($filters) {
                $query->where('assigned_to', $filters['assigned_to']);
            })
            ->when(isset($filters['created_from']), function (Builder $query) use ($filters) {
                $query->whereDate('created_at', '>=', $filters['created_from']);
            })
            ->when(isset($filters['created_to']), function (Builder $query) use ($filters) {
                $query->whereDate('created_at', '<=', $filters['created_to']);
            })
            ->when(isset($filters['due_date_from']), function (Builder $query) use ($filters) {
                $query->whereDate('due_date', '>=', $filters['due_date_from']);
            })
            ->when(isset($filters['due_date_to']), function (Builder $query) use ($filters) {
                $query->whereDate('due_date', '<=', $filters['due_date_to']);
            });

        return $query->latest()->paginate(15);
    }

    /**
     * Cria uma nova tarefa.
     *
     * Define o usuário autenticado como o criador da tarefa
     * e atribui um status padrão 'open' se não for especificado.
     *
     * @param array $data Os dados da tarefa
     * @return \App\Models\Task A tarefa criada
     */
    public function createTask(array $data): Task
    {
        // Set the creator to the current user
        $data['created_by'] = auth()->id();
        
        // Default status to 'open' if not provided
        $data['status'] = $data['status'] ?? 'open';
        
        return Task::create($data);
    }

    /**
     * Atualiza o status de uma tarefa.
     *
     * @param \App\Models\Task $task A tarefa a ser atualizada
     * @param string $status O novo status da tarefa
     * @return \App\Models\Task A tarefa atualizada
     */    
    public function updateTaskStatus(Task $task, string $status): Task
    {
        $task->status = $status;
        $task->save();
        
        return $task;
    }    
}