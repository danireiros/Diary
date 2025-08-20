<?php

namespace Database\Factories;

use App\Models\DiaryCategory;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'title' => $this->faker->sentence(5),
            'content' => $this->faker->paragraph(),
            'diary_category_id' => DiaryCategory::factory(),
            'hidden' => $this->faker->boolean(20),
        ];
    }
}


