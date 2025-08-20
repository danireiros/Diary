<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes','required','string'],
            'category_id' => ['sometimes','required','exists:categories,id'],
            'status' => ['sometimes','in:todo,pending,done'],
            'all_day' => ['sometimes','boolean'],
            'start_at' => ['nullable','date_format:Y-m-d H:i:s'],
            'end_at' => ['nullable','date_format:Y-m-d H:i:s','after_or_equal:start_at'],
            'duration_minutes' => ['nullable','integer','min:1'],
            'recurrence_json' => ['nullable','array'],
            'recurrence_json.freq' => ['nullable','in:DAILY,WEEKLY,MONTHLY'],
            'recurrence_json.interval' => ['nullable','integer','min:1'],
            'recurrence_json.time' => ['nullable','regex:/^\\d{2}:\\d{2}$/'],
            'recurrence_json.byWeekday' => ['nullable','array'],
            'recurrence_json.byWeekday.*' => ['integer','between:0,6'],
            'recurrence_json.byMonthDay' => ['nullable','array'],
            'recurrence_json.byMonthDay.*' => ['integer','between:1,31'],
            'recurrence_json.until' => ['nullable','date'],
            'recurrence_json.count' => ['nullable','integer','min:1'],
        ];
    }
}


