<?php

namespace Database\Factories;

use App\Models\Submission;
use App\Models\Journal;
use App\Models\User;
use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Submission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'journal_id' => Journal::factory(),
            'user_id' => User::factory(),
            'issue_id' => Issue::factory(),
            'title' => $this->faker->sentence,
            'status' => Submission::STATUS_PUBLISHED,
            'stage' => Submission::STAGE_PRODUCTION,
            'submitted_at' => now(),
            'published_at' => now(),
            'language' => 'en',
        ];
    }
}
