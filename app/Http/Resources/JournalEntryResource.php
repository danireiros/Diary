<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JournalEntryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'date' => optional($this->date)?->toDateString(),
            'title' => $this->title,
            'content' => $this->content,
            'diary_category_id' => $this->diary_category_id,
            'hidden' => $this->hidden,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


