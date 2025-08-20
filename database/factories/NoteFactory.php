<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraph(),
            'category_id' => Category::factory(),
            'status' => $this->faker->randomElement(['todo','pending','done']),
        ];
    }
}


