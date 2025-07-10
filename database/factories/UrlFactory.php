<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Url>
 */
class UrlFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'original_url' => $this->faker->word(),
            'value' => $this->faker->randomNumber(),
            'expires_at' => Carbon::tomorrow(),
        ];

    }

    public function setUser($user_id)
    {
        return $this->state(['user_id' => $user_id]);
    }
}
