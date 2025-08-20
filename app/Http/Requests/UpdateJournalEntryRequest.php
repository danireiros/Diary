<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['sometimes','required','date'],
            'title' => ['sometimes','required','string'],
            'content' => ['nullable','string'],
            'diary_category_id' => ['sometimes','required','exists:diary_categories,id'],
            'hidden' => ['sometimes','boolean'],
        ];
    }
}


