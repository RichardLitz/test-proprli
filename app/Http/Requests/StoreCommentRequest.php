<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
        $userHasAccess = $task->building->users()
            ->where('users.id', $this->user()->id)
            ->exists();
            
        return $userHasAccess;
    }

    /**
     * Obtém as regras de validação aplicáveis à requisição.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    /**
     * Obtém as mensagens de erro personalizadas para as regras de validação.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.required' => 'O conteúdo do comentário é obrigatório.',
            'content.min' => 'O comentário deve ter pelo menos 3 caracteres.',
            'content.max' => 'O comentário não pode exceder 1000 caracteres.',
        ];
    }
}