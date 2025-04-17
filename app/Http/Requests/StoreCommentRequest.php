<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Implement proper authorization later
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
        ];
    }
}