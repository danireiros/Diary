<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes','required','string'],
            'content' => ['nullable','string'],
            'category_id' => ['sometimes','required','exists:categories,id'],
            'status' => ['sometimes','in:todo,pending,done'],
        ];
    }
}


