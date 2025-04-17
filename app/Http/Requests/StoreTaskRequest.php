<?php

namespace App\Http\Requests;

use App\Models\Building;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!$this->filled('building_id')) {
            return false;
        }

        $building = Building::find($this->input('building_id'));
        
        // Verifica se o usuário tem acesso ao edifício
        return $building && $building->users()->where('users.id', $this->user()->id)->exists();
    }

    public function rules(): array
    {
        return [
            'building_id' => ['required', 'exists:buildings,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => [
                'nullable', 
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    // Verifica se o usuário atribuído pertence ao edifício
                    $buildingId = $this->input('building_id');
                    $userBelongsToBuildingTeam = User::find($value)
                        ->buildings()
                        ->where('buildings.id', $buildingId)
                        ->exists();
                    
                    if (!$userBelongsToBuildingTeam) {
                        $fail('O usuário atribuído deve fazer parte da equipe do edifício.');
                    }
                },
            ],
            'status' => ['nullable', Rule::in(['open', 'in_progress', 'completed', 'rejected'])],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'building_id.required' => 'O ID do edifício é obrigatório.',
            'building_id.exists' => 'O edifício selecionado não existe.',
            'title.required' => 'O título da tarefa é obrigatório.',
            'title.max' => 'O título não pode exceder 255 caracteres.',
            'assigned_to.exists' => 'O usuário atribuído não existe.',
            'due_date.after_or_equal' => 'A data de vencimento deve ser hoje ou uma data futura.',
        ];
    }
}