<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;

class IssueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Issue::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'journal_id' => Journal::factory(),
            'volume' => $this->faker->numberBetween(1, 10),
            'number' => $this->faker->numberBetween(1, 4),
            'year' => $this->faker->year,
            'title' => $this->faker->sentence,
            'is_published' => true,
            'published_at' => now(),
            'url_path' => $this->faker->slug,
        ];
    }
}
