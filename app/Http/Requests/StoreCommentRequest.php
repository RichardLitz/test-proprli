<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');
        
        // Verifica se o usuário tem acesso ao edifício da tarefa
        $userHasAccess = $task->building->users()
            ->where('users.id', $this->user()->id)
            ->exists();
            
        return $userHasAccess;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'O conteúdo do comentário é obrigatório.',
            'content.min' => 'O comentário deve ter pelo menos 3 caracteres.',
            'content.max' => 'O comentário não pode exceder 1000 caracteres.',
        ];
    }
}