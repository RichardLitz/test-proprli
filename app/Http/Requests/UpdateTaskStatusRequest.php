<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskStatusRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     *
     * Verifica se o usuário tem acesso ao edifício da tarefa.
     *
     * @return bool True se o usuário tiver acesso ao edifício da tarefa, false caso contrário
     */
    public function authorize(): bool
    {
        $task = $this->route('task');
        
        // Verifica se o usuário tem acesso ao edifício da tarefa
        return $task->building->users()
            ->where('users.id', $this->user()->id)
            ->exists();
    }

    /**
     * Obtém as regras de validação aplicáveis à requisição.
     *
     * Valida que o status é obrigatório e deve ser um dos valores aceitos.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['open', 'in_progress', 'completed', 'rejected'])],
        ];
    }
}