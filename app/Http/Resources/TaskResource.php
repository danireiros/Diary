<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'all_day' => $this->all_day,
            'start_at' => optional($this->start_at)?->clone()->timezone('UTC')->toISOString(),
            'end_at' => optional($this->end_at)?->clone()->timezone('UTC')->toISOString(),
            'duration_minutes' => $this->duration_minutes,
            'recurrence_json' => $this->recurrence_json,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


