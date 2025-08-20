<?php

namespace Database\Factories;

use App\Models\DiaryCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiaryCategoryFactory extends Factory
{
    protected $model = DiaryCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'color' => '#' . $this->faker->regexify('[0-9a-fA-F]{6}'),
        ];
    }
}


