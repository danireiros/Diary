<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'category_id' => Category::factory(),
            'status' => $this->faker->randomElement(['todo','pending','done']),
            'all_day' => false,
            'start_at' => now()->addDays(rand(-5,5)),
            'end_at' => now()->addDays(rand(-5,5))->addHour(),
        ];
    }
}


