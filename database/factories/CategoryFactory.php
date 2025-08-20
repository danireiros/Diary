<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'color' => '#' . $this->faker->regexify('[0-9a-fA-F]{6}'),
        ];
    }
}


