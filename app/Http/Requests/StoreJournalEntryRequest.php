<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required','date'],
            'title' => ['required','string'],
            'content' => ['nullable','string'],
            'diary_category_id' => ['required','exists:diary_categories,id'],
            'hidden' => ['boolean'],
        ];
    }
}


