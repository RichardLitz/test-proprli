<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');
        
        // Verifica se o usuário tem acesso ao edifício da tarefa
        return $task->building->users()
            ->where('users.id', $this->user()->id)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['open', 'in_progress', 'completed', 'rejected'])],
        ];
    }
}