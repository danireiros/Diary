<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required','string'],
            'content' => ['nullable','string'],
            'category_id' => ['required','exists:categories,id'],
            'status' => ['nullable','in:todo,pending,done'],
        ];
    }
}


