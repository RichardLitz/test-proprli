<?php
namespace App\Services;

use App\Models\Building;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    /**
     * Get filtered tasks for a building
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
     * Create a new task
     */
    public function createTask(array $data): Task
    {
        // Set the creator to the current user
        $data['created_by'] = auth()->id();
        
        // Default status to 'open' if not provided
        $data['status'] = $data['status'] ?? 'open';
        
        return Task::create($data);
    }
}