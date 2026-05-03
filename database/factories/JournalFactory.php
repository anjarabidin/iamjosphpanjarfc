<?php

namespace Database\Factories;

use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalFactory extends Factory
{
    protected $model = Journal::class;

    public function definition(): array
    {
        $name = $this->faker->company;
        $slug = $this->faker->unique()->slug;

        return [
            'name' => $name,
            'path' => $slug, // Using slug as path for consistency
            'slug' => $slug,
            'description' => $this->faker->paragraph,
            'enabled' => true,
            'visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
